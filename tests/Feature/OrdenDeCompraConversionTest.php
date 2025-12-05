<?php

namespace Tests\Feature;

use App\Enums\UnidadMedida;
use App\Helpers\ConversionHelper;
use App\Models\CategoriaInsumo;
use App\Models\Insumo;
use App\Models\Lote;
use App\Models\OrdenDeCompra;
use App\Models\OrdenDeCompraItem;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdenDeCompraConversionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Proveedor $proveedor;
    protected Insumo $harina;
    protected CategoriaInsumo $categoria;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear usuario
        $this->user = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
        ]);

        // Crear categoría de insumo
        $this->categoria = CategoriaInsumo::create([
            'nombre' => 'Harinas',
            'descripcion' => 'Categoría de harinas',
        ]);

        // Crear proveedor
        $this->proveedor = Proveedor::create([
            'nombre_empresa' => 'Distribuidora Test',
            'cuit' => '20-12345678-9',
            'nombre_contacto' => 'Juan Pérez',
            'email_pedidos' => 'pedidos@test.com',
            'telefono' => '123456789',
            'activo' => true,
        ]);

        // Crear insumo con unidad base en gramos
        $this->harina = Insumo::create([
            'nombre' => 'Harina 0000',
            'descripcion' => 'Harina para pastelería',
            'categoria_insumo_id' => $this->categoria->id,
            'unidad_de_medida' => UnidadMedida::GRAMO,
            'activo' => true,
        ]);
    }

    /** @test */
    public function puede_crear_orden_con_insumo_en_kilogramos_y_convertir_a_gramos()
    {
        // Configurar relación proveedor-insumo
        // El proveedor vende en KILOGRAMOS, pero el insumo se mide en GRAMOS
        $this->proveedor->insumos()->attach($this->harina->id, [
            'precio' => 1500,
            'unidad_compra' => UnidadMedida::KILOGRAMO->value,
            'cantidad_por_bulto' => 1, // 1 bulto = 1 kg
            'tiempo_entrega_dias' => 3,
        ]);

        // Crear orden de compra
        $orden = OrdenDeCompra::create([
            'proveedor_id' => $this->proveedor->id,
            'fecha_emision' => now(),
            'fecha_entrega_estimada' => now()->addDays(3),
            'status' => 'aprobada',
            'total' => 15000, // 10 kg × $1500
        ]);

        // Agregar item: compramos 10 kilogramos
        $item = OrdenDeCompraItem::create([
            'orden_de_compra_id' => $orden->id,
            'insumo_id' => $this->harina->id,
            'cantidad' => 10, // 10 kg
            'precio_unitario' => 1500,
            'subtotal' => 15000,
        ]);

        // Simular recepción de stock con conversión
        $proveedorData = $this->harina->proveedores()
            ->where('proveedor_id', $this->proveedor->id)
            ->first();

        $unidadCompra = UnidadMedida::from($proveedorData->pivot->unidad_compra);
        $unidadBase = $this->harina->unidad_de_medida;

        // Validar que son compatibles
        $this->assertTrue(ConversionHelper::sonCompatibles($unidadCompra, $unidadBase));

        // Convertir
        $cantidadReal = ConversionHelper::convertirABase(
            cantidad: $item->cantidad * $proveedorData->pivot->cantidad_por_bulto,
            unidadCompra: $unidadCompra,
            unidadBase: $unidadBase
        );

        // 10 kg × 1 × factor(kg->g) = 10,000 gramos
        $this->assertEquals(10000, $cantidadReal);

        // Crear lote
        $lote = Lote::create([
            'insumo_id' => $this->harina->id,
            'cantidad_inicial' => $cantidadReal,
            'cantidad_actual' => $cantidadReal,
            'fecha_vencimiento' => now()->addMonths(6),
            'codigo_lote' => 'TEST-001',
        ]);

        // Verificar que el lote se creó con la cantidad correcta
        $this->assertEquals(10000, $lote->cantidad_actual);
        
        // Verificar que el stock total es correcto
        $stockTotal = Lote::where('insumo_id', $this->harina->id)
            ->sum('cantidad_actual');
        
        $this->assertEquals(10000, $stockTotal);
    }

    /** @test */
    public function no_permite_conversion_incompatible()
    {
        // Crear insumo con unidad base en LITROS (volumen)
        $aceite = Insumo::create([
            'nombre' => 'Aceite de Girasol',
            'descripcion' => 'Aceite para cocina',
            'categoria_insumo_id' => $this->categoria->id,
            'unidad_de_medida' => UnidadMedida::LITRO,
            'activo' => true,
        ]);

        // Intentar configurar en KILOGRAMOS (peso) - debe fallar
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Conversión incompatible');

        ConversionHelper::convertirABase(
            cantidad: 1,
            unidadCompra: UnidadMedida::KILOGRAMO,
            unidadBase: UnidadMedida::LITRO
        );
    }

    /** @test */
    public function puede_comprar_en_misma_unidad_sin_conversion()
    {
        // Crear insumo que ya se compra en su unidad base
        $huevos = Insumo::create([
            'nombre' => 'Huevos',
            'descripcion' => 'Huevos frescos',
            'categoria_insumo_id' => $this->categoria->id,
            'unidad_de_medida' => UnidadMedida::UNIDAD,
            'activo' => true,
        ]);

        $this->proveedor->insumos()->attach($huevos->id, [
            'precio' => 50,
            'unidad_compra' => UnidadMedida::UNIDAD->value,
            'cantidad_por_bulto' => 1,
            'tiempo_entrega_dias' => 1,
        ]);

        $cantidad = ConversionHelper::convertirABase(
            cantidad: 30,
            unidadCompra: UnidadMedida::UNIDAD,
            unidadBase: UnidadMedida::UNIDAD
        );

        // No debe haber conversión
        $this->assertEquals(30, $cantidad);
    }

    /** @test */
    public function valida_tipos_de_unidades_correctamente()
    {
        // Peso
        $this->assertEquals('peso', ConversionHelper::getTipoUnidad(UnidadMedida::GRAMO));
        $this->assertEquals('peso', ConversionHelper::getTipoUnidad(UnidadMedida::KILOGRAMO));

        // Volumen
        $this->assertEquals('volumen', ConversionHelper::getTipoUnidad(UnidadMedida::LITRO));
        $this->assertEquals('volumen', ConversionHelper::getTipoUnidad(UnidadMedida::MILILITRO));

        // Unidad
        $this->assertEquals('unidad', ConversionHelper::getTipoUnidad(UnidadMedida::UNIDAD));
    }

    /** @test */
    public function calcula_factor_de_conversion_automatico()
    {
        // kg -> g = 1000
        $factor = ConversionHelper::calcularFactorConversion(
            UnidadMedida::KILOGRAMO,
            UnidadMedida::GRAMO
        );
        $this->assertEquals(1000, $factor);

        // L -> ml = 1000
        $factor = ConversionHelper::calcularFactorConversion(
            UnidadMedida::LITRO,
            UnidadMedida::MILILITRO
        );
        $this->assertEquals(1000, $factor);

        // g -> kg = 0.001
        $factor = ConversionHelper::calcularFactorConversion(
            UnidadMedida::GRAMO,
            UnidadMedida::KILOGRAMO
        );
        $this->assertEquals(0.001, $factor);
    }
}
