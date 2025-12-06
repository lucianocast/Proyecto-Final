<?php

namespace App\Livewire\Frontend;

use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CheckoutView extends Component
{
    // NO PONGAS $cartItems NI $total aquí arriba
    
    // Propiedades del Formulario
    public string $fecha_entrega = '';
    public string $forma_entrega = 'retiro';
    public string $direccion_envio = '';
    public string $observaciones = '';

    protected function rules()
    {
        // ... tus reglas (esto estaba bien)
        return [
            'fecha_entrega' => 'required|date|after_or_equal:today',
            'forma_entrega' => 'required|in:retiro,envio',
            'direccion_envio' => 'required_if:forma_entrega,envio|string|max:255',
            'observaciones' => 'nullable|string',
        ];
    }

    public function mount()
    {
        // En mount, SÓLO validamos
        if (\Cart::isEmpty()) {
            return redirect()->route('catalogo.index');
        }
        
        // $this->direccion_envio = Auth::user()->cliente->direccion ?? '';
    }

    public function saveOrder()
    {
        $this->validate();
        
        // Obtenemos el total FRESCO antes de guardar
        $total = \Cart::getTotal(); 
        $cartItems = \Cart::getContent();

        $user = Auth::user();
        $clienteId = $user->id; // Sigue ajustando esto a tu lógica

        try {
            DB::beginTransaction();

            $pedido = Pedido::create([
                'cliente_id' => $clienteId,
                'status' => 'pendiente',
                'fecha_entrega' => $this->fecha_entrega,
                'forma_entrega' => $this->forma_entrega,
                'direccion_envio' => $this->forma_entrega == 'envio' ? $this->direccion_envio : null,
                'metodo_pago' => 'mercadopago',
                'monto_abonado' => 0,
                'total' => $total, // Usar la variable fresca
                'observaciones' => $this->observaciones,
            ]);

            foreach ($cartItems as $item) { // Usar la variable fresca
                $pedido->items()->create([
                    'producto_variante_id' => $item->id,
                    'cantidad' => $item->quantity,
                    'precio_unitario' => $item->price,
                    'subtotal' => $item->getPriceSum()
                ]);
            }

            $pago = Pago::create([
                'pedido_id' => $pedido->id,
                'monto' => $total, // Usar la variable fresca
                'metodo' => 'mercadopago',
                'estado' => 'pendiente',
            ]);

            DB::commit();
            \Cart::clear();
            $this->dispatch('cartUpdated');
            return redirect()->route('pago.iniciar', ['pedido' => $pedido->id, 'pago' => $pago->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            // ¡Importante! Loguea el error real para ti
            \Log::error('Error al guardar pedido: ' . $e->getMessage()); 
            $this->addError('general', 'Hubo un error al procesar tu pedido. Por favor, intenta de nuevo.');
        }
    }

    /**
     * Manejar token de pago de Google Pay (UC-09)
     * 
     * Este método recibe el token desde el frontend y procesa el pago
     * 
     * @param string $paymentToken Token de pago de Google Pay
     * @param array $additionalData Datos adicionales (email, billing_address, etc.)
     */
    public function handlePaymentToken(string $paymentToken, array $additionalData = [])
    {
        try {
            // Validar que exista un pedido activo (usar el último pedido del usuario)
            $user = Auth::user();
            $pedido = Pedido::where('cliente_id', $user->id)
                ->where('status', 'pendiente')
                ->where('saldo_pendiente', '>', 0)
                ->latest()
                ->first();

            if (!$pedido) {
                $this->dispatch('error', message: 'No se encontró un pedido pendiente de pago.');
                return;
            }

            // Preparar datos del token
            $tokenData = array_merge([
                'token' => $paymentToken,
            ], $additionalData);

            // Inyectar y usar PaymentService
            $paymentService = app(\App\Services\PaymentService::class);

            // Procesar el pago (monto = saldo pendiente)
            $pago = $paymentService->processPayment(
                pedido: $pedido,
                monto: $pedido->saldo_pendiente,
                tokenData: $tokenData,
                methodName: 'google_pay'
            );

            // Limpiar carrito después del pago exitoso
            \Cart::clear();
            $this->dispatch('cartUpdated');

            // Emitir evento de éxito con datos del pago
            $this->dispatch('paymentSuccess', [
                'message' => '¡Pago procesado exitosamente!',
                'pago_id' => $pago->id,
                'transaction_id' => $pago->referencia_externa,
                'pedido_id' => $pedido->id,
            ]);

            // Redirigir a página de confirmación
            return redirect()->route('pedido.confirmacion', ['pedido' => $pedido->id]);

        } catch (\Exception $e) {
            // Registrar error en logs
            \Log::error('Error al procesar pago de Google Pay', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            // Emitir evento de error
            $this->dispatch('paymentError', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        // ¡ESTA ES LA MAGIA!
        // Cargamos los datos del carrito FRESCOS en CADA RENDER.
        // Así nunca usamos un estado "roto" o "deshidratado".
        $cartItems = \Cart::getContent();
        $total = \Cart::getTotal();

        return view('livewire.frontend.checkout-view', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }
}