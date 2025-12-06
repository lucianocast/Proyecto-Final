<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Pago;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\Gateways\GooglePayGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentService $paymentService;
    protected User $user;
    protected Cliente $cliente;
    protected Pedido $pedido;

    protected function setUp(): void
    {
        parent::setUp();

        // Configurar usuario y cliente de prueba
        $this->user = User::factory()->create();
        $this->cliente = Cliente::factory()->create(['user_id' => $this->user->id]);

        // Crear pedido de prueba
        $this->pedido = Pedido::create([
            'cliente_id' => $this->cliente->id,
            'user_id' => $this->user->id,
            'status' => 'pendiente',
            'fecha_entrega' => now()->addDays(2),
            'forma_entrega' => 'retiro',
            'metodo_pago' => 'google_pay',
            'monto_abonado' => 0,
            'total_calculado' => 1000,
            'saldo_pendiente' => 1000,
        ]);

        // Configurar PaymentService con GooglePayGateway
        $gateway = new GooglePayGateway();
        $this->paymentService = new PaymentService($gateway);
    }

    /** @test */
    public function puede_procesar_pago_exitoso()
    {
        // Simular respuesta exitosa de Google Pay API
        Http::fake([
            '*/charges' => Http::response([
                'status' => 'completed',
                'transaction_id' => 'GPAY_TEST_12345',
            ], 200),
        ]);

        // Procesar pago
        $pago = $this->paymentService->processPayment(
            pedido: $this->pedido,
            monto: 500.00,
            tokenData: ['token' => 'test_token_123'],
            methodName: 'google_pay'
        );

        // Verificar que el pago se creó
        $this->assertInstanceOf(Pago::class, $pago);
        $this->assertEquals(500.00, $pago->monto);
        $this->assertEquals('google_pay', $pago->metodo);
        $this->assertEquals('confirmado', $pago->estado);

        // Verificar que el pedido se actualizó
        $this->pedido->refresh();
        $this->assertEquals(500.00, $this->pedido->monto_abonado);
        $this->assertEquals(500.00, $this->pedido->saldo_pendiente);
    }

    /** @test */
    public function actualiza_estado_pedido_cuando_pago_completo()
    {
        Http::fake([
            '*/charges' => Http::response([
                'status' => 'completed',
                'transaction_id' => 'GPAY_TEST_12345',
            ], 200),
        ]);

        // Pagar el total del pedido
        $pago = $this->paymentService->processPayment(
            pedido: $this->pedido,
            monto: 1000.00,
            tokenData: ['token' => 'test_token_123'],
            methodName: 'google_pay'
        );

        // Verificar que el pedido cambió de estado
        $this->pedido->refresh();
        $this->assertEquals(0, $this->pedido->saldo_pendiente);
        $this->assertEquals('en_produccion', $this->pedido->status);
    }

    /** @test */
    public function no_permite_pago_mayor_al_saldo_pendiente()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('excede el saldo pendiente');

        $this->paymentService->processPayment(
            pedido: $this->pedido,
            monto: 1500.00, // Mayor al saldo
            tokenData: ['token' => 'test_token_123'],
            methodName: 'google_pay'
        );
    }

    /** @test */
    public function no_permite_pago_negativo_o_cero()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('debe ser mayor a cero');

        $this->paymentService->processPayment(
            pedido: $this->pedido,
            monto: 0,
            tokenData: ['token' => 'test_token_123'],
            methodName: 'google_pay'
        );
    }

    /** @test */
    public function no_permite_pago_en_pedido_cancelado()
    {
        $this->pedido->update(['status' => 'cancelado']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('pedido cancelado');

        $this->paymentService->processPayment(
            pedido: $this->pedido,
            monto: 500.00,
            tokenData: ['token' => 'test_token_123'],
            methodName: 'google_pay'
        );
    }

    /** @test */
    public function verifica_si_pedido_esta_completamente_pagado()
    {
        $this->assertFalse($this->paymentService->isFullyPaid($this->pedido));

        Http::fake([
            '*/charges' => Http::response([
                'status' => 'completed',
                'transaction_id' => 'GPAY_TEST_12345',
            ], 200),
        ]);

        // Pagar el total
        $this->paymentService->processPayment(
            pedido: $this->pedido,
            monto: 1000.00,
            tokenData: ['token' => 'test_token_123'],
            methodName: 'google_pay'
        );

        $this->pedido->refresh();
        $this->assertTrue($this->paymentService->isFullyPaid($this->pedido));
    }

    /** @test */
    public function obtiene_saldo_pendiente_correcto()
    {
        $saldo = $this->paymentService->getPendingBalance($this->pedido);
        $this->assertEquals(1000.00, $saldo);
    }
}
