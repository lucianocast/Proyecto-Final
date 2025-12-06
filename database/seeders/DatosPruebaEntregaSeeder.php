<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\CategoriaProducto;
use App\Models\Insumo;
use App\Models\CategoriaInsumo;
use App\Models\Proveedor;
use App\Models\Receta;
use App\Models\Lote;
use App\Models\Pedido;
use App\Enums\UnidadMedida;
use Carbon\Carbon;

class DatosPruebaEntregaSeeder extends Seeder
{
    /**
     * Seed para crear datos de prueba completos para la entrega final.
     * 
     * Crea:
     * - Usuarios (admin, vendedor, cliente)
     * - 8 Clientes activos
     * - 3 Proveedores con sus catálogos
     * - 10 Insumos con stock
     * - 6 Productos con recetas completas
     * - 3 Pedidos confirmados listos para producir
     */
    public function run(): void
    {
        echo "\n🎯 Iniciando creación de datos de prueba para entrega final...\n\n";

        DB::transaction(function () {
            // 1. Usuarios
            $this->crearUsuarios();
            
            // 2. Clientes
            $clientes = $this->crearClientes();
            
            // 3. Categorías
            $categoriaInsumos = $this->crearCategoriasInsumos();
            $categoriaProductos = $this->crearCategoriasProductos();
            
            // 4. Proveedores
            $proveedores = $this->crearProveedores();
            
            // 5. Insumos con stock
            $insumos = $this->crearInsumos($categoriaInsumos, $proveedores);
            
            // 6. Productos con recetas (comentado - usar los existentes)
            // $productos = $this->crearProductos($categoriaProductos, $insumos);
            
            // 7. Pedidos de prueba (comentado - crear manualmente en la demo)
            // $this->crearPedidos($clientes, $productos);
        });

        echo "\n✅ Datos de prueba creados exitosamente!\n";
        echo "\n📋 Resumen:\n";
        echo "   - Usuarios: Admin (admin@test.com / password), Vendedor (vendedor@test.com / password)\n";
        echo "   - Clientes: 8 activos\n";
        echo "   - Proveedores: 3 con catálogos completos\n";
        echo "   - Insumos: 10 con stock disponible\n";
        echo "   - Productos y Pedidos: Usar los existentes o crear en la demo\n\n";
    }

    private function crearUsuarios(): void
    {
        echo "👤 Creando usuarios con roles de Spatie...\n";

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin Test',
                'password' => Hash::make('password'),
            ]
        );
        
        // Asignar rol usando Spatie Permission
        if (!$admin->hasRole('administrador')) {
            $admin->assignRole('administrador');
        }

        // Vendedor
        $vendedor = User::firstOrCreate(
            ['email' => 'vendedor@test.com'],
            [
                'name' => 'Vendedor Test',
                'password' => Hash::make('password'),
            ]
        );
        
        if (!$vendedor->hasRole('vendedor')) {
            $vendedor->assignRole('vendedor');
        }

        // Cliente (usuario web)
        $cliente = User::firstOrCreate(
            ['email' => 'cliente@test.com'],
            [
                'name' => 'Cliente Test',
                'password' => Hash::make('password'),
            ]
        );
        
        if (!$cliente->hasRole('cliente')) {
            $cliente->assignRole('cliente');
        }

        echo "   ✓ 3 usuarios creados\n";
    }

    private function crearClientes(): array
    {
        echo "👥 Creando clientes...\n";

        $clientes = [
            [
                'nombre' => 'María González',
                'email' => 'maria.gonzalez@email.com',
                'telefono' => '3515551234',
                'direccion' => 'Av. Colón 1234, Córdoba',
                'activo' => true,
            ],
            [
                'nombre' => 'Juan Pérez',
                'email' => 'juan.perez@email.com',
                'telefono' => '3515555678',
                'direccion' => 'Av. Vélez Sarsfield 890, Córdoba',
                'activo' => true,
            ],
            [
                'nombre' => 'Laura Martínez',
                'email' => 'laura.martinez@email.com',
                'telefono' => '3515559012',
                'direccion' => 'Bv. San Juan 456, Córdoba',
                'activo' => true,
            ],
            [
                'nombre' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@email.com',
                'telefono' => '3515553456',
                'direccion' => 'Av. Hipólito Yrigoyen 789, Córdoba',
                'activo' => true,
            ],
            [
                'nombre' => 'Ana Fernández',
                'email' => 'ana.fernandez@email.com',
                'telefono' => '3515557890',
                'direccion' => 'Av. General Paz 321, Córdoba',
                'activo' => true,
            ],
            [
                'nombre' => 'Diego Sánchez',
                'email' => 'diego.sanchez@email.com',
                'telefono' => '3515552345',
                'direccion' => 'Bv. Illia 654, Córdoba',
                'activo' => true,
            ],
            [
                'nombre' => 'Lucía López',
                'email' => 'lucia.lopez@email.com',
                'telefono' => '3515556789',
                'direccion' => 'Av. Rafael Núñez 987, Córdoba',
                'activo' => true,
            ],
            [
                'nombre' => 'Miguel Torres',
                'email' => 'miguel.torres@email.com',
                'telefono' => '3515550123',
                'direccion' => 'Av. Recta Martinolli 147, Córdoba',
                'activo' => true,
            ],
        ];

        $clientesCreados = [];
        foreach ($clientes as $clienteData) {
            $clientesCreados[] = Cliente::firstOrCreate(
                ['email' => $clienteData['email']],
                $clienteData
            );
        }

        echo "   ✓ " . count($clientesCreados) . " clientes creados\n";
        return $clientesCreados;
    }

    private function crearCategoriasInsumos(): array
    {
        echo "📦 Creando categorías de insumos...\n";

        $categorias = [
            ['nombre' => 'Harinas y Almidones', 'descripcion' => 'Harinas de todo tipo'],
            ['nombre' => 'Lácteos', 'descripcion' => 'Leche, manteca, crema'],
            ['nombre' => 'Endulzantes', 'descripcion' => 'Azúcar, miel, edulcorantes'],
            ['nombre' => 'Chocolates', 'descripcion' => 'Chocolate en todas sus formas'],
            ['nombre' => 'Frutas y Frutos Secos', 'descripcion' => 'Frutas frescas y secas'],
        ];

        $categoriasCreadas = [];
        foreach ($categorias as $cat) {
            $categoriasCreadas[] = CategoriaInsumo::firstOrCreate(
                ['nombre' => $cat['nombre']],
                $cat
            );
        }

        echo "   ✓ " . count($categoriasCreadas) . " categorías de insumos creadas\n";
        return $categoriasCreadas;
    }

    private function crearCategoriasProductos(): array
    {
        echo "🎂 Creando categorías de productos...\n";

        $categorias = [
            ['nombre' => 'Tortas', 'descripcion' => 'Tortas de todo tipo'],
            ['nombre' => 'Cupcakes', 'descripcion' => 'Cupcakes y muffins'],
            ['nombre' => 'Postres', 'descripcion' => 'Postres individuales'],
            ['nombre' => 'Galletas', 'descripcion' => 'Galletas y cookies'],
        ];

        $categoriasCreadas = [];
        foreach ($categorias as $cat) {
            $categoriasCreadas[] = CategoriaProducto::firstOrCreate(
                ['nombre' => $cat['nombre']],
                $cat
            );
        }

        echo "   ✓ " . count($categoriasCreadas) . " categorías de productos creadas\n";
        return $categoriasCreadas;
    }

    private function crearProveedores(): array
    {
        echo "🏢 Creando proveedores...\n";

        $proveedores = [
            [
                'nombre_empresa' => 'Distribuidora La Central',
                'nombre_contacto' => 'Roberto Gómez',
                'cuit' => '20-12345678-9',
                'email_pedidos' => 'ventas@lacentral.com',
                'telefono' => '3514001234',
                'direccion' => 'Av. Circunvalación 5678',
                'activo' => true,
            ],
            [
                'nombre_empresa' => 'Mayorista El Buen Precio',
                'nombre_contacto' => 'Sandra Díaz',
                'cuit' => '20-87654321-0',
                'email_pedidos' => 'contacto@buenprecio.com',
                'telefono' => '3514005678',
                'direccion' => 'Zona Industrial km 8',
                'activo' => true,
            ],
            [
                'nombre_empresa' => 'Insumos Premium SA',
                'nombre_contacto' => 'Jorge Morales',
                'cuit' => '30-11223344-5',
                'email_pedidos' => 'ventas@premium.com',
                'telefono' => '3514009012',
                'direccion' => 'Parque Industrial Sur',
                'activo' => true,
            ],
        ];

        $proveedoresCreados = [];
        foreach ($proveedores as $prov) {
            $proveedoresCreados[] = Proveedor::firstOrCreate(
                ['cuit' => $prov['cuit']],
                $prov
            );
        }

        echo "   ✓ " . count($proveedoresCreados) . " proveedores creados\n";
        return $proveedoresCreados;
    }

    private function crearInsumos(array $categorias, array $proveedores): array
    {
        echo "🥚 Creando insumos con stock...\n";

        $insumos = [
            [
                'nombre' => 'Harina 0000',
                'descripcion' => 'Harina de trigo refinada',
                'categoria' => 'Harinas y Almidones',
                'unidad' => UnidadMedida::GRAMO,
                'stock_minimo' => 5000,
                'stock_inicial' => 15000,
                'proveedor' => 'Distribuidora La Central',
                'precio' => 150.00,
                'unidad_compra' => UnidadMedida::KILOGRAMO,
                'cantidad_por_bulto' => 1000,
            ],
            [
                'nombre' => 'Azúcar',
                'descripcion' => 'Azúcar blanca refinada',
                'categoria' => 'Endulzantes',
                'unidad' => UnidadMedida::GRAMO,
                'stock_minimo' => 3000,
                'stock_inicial' => 10000,
                'proveedor' => 'Distribuidora La Central',
                'precio' => 120.00,
                'unidad_compra' => UnidadMedida::KILOGRAMO,
                'cantidad_por_bulto' => 1000,
            ],
            [
                'nombre' => 'Manteca',
                'descripcion' => 'Manteca sin sal',
                'categoria' => 'Lácteos',
                'unidad' => UnidadMedida::GRAMO,
                'stock_minimo' => 2000,
                'stock_inicial' => 5000,
                'proveedor' => 'Mayorista El Buen Precio',
                'precio' => 450.00,
                'unidad_compra' => UnidadMedida::GRAMO,
                'cantidad_por_bulto' => 1,
            ],
            [
                'nombre' => 'Huevos',
                'descripcion' => 'Huevos frescos de gallina',
                'categoria' => 'Lácteos',
                'unidad' => UnidadMedida::UNIDAD,
                'stock_minimo' => 50,
                'stock_inicial' => 120,
                'proveedor' => 'Mayorista El Buen Precio',
                'precio' => 25.00,
                'unidad_compra' => UnidadMedida::UNIDAD,
                'cantidad_por_bulto' => 1,
            ],
            [
                'nombre' => 'Chocolate para Repostería',
                'descripcion' => 'Chocolate cobertura al 70%',
                'categoria' => 'Chocolates',
                'unidad' => UnidadMedida::GRAMO,
                'stock_minimo' => 1000,
                'stock_inicial' => 3000,
                'proveedor' => 'Insumos Premium SA',
                'precio' => 800.00,
                'unidad_compra' => UnidadMedida::GRAMO,
                'cantidad_por_bulto' => 1,
            ],
            [
                'nombre' => 'Leche Entera',
                'descripcion' => 'Leche entera larga vida',
                'categoria' => 'Lácteos',
                'unidad' => UnidadMedida::MILILITRO,
                'stock_minimo' => 2000,
                'stock_inicial' => 5000,
                'proveedor' => 'Mayorista El Buen Precio',
                'precio' => 180.00,
                'unidad_compra' => UnidadMedida::LITRO,
                'cantidad_por_bulto' => 1000,
            ],
            [
                'nombre' => 'Esencia de Vainilla',
                'descripcion' => 'Esencia natural de vainilla',
                'categoria' => 'Endulzantes',
                'unidad' => UnidadMedida::MILILITRO,
                'stock_minimo' => 100,
                'stock_inicial' => 500,
                'proveedor' => 'Insumos Premium SA',
                'precio' => 350.00,
                'unidad_compra' => UnidadMedida::MILILITRO,
                'cantidad_por_bulto' => 1,
            ],
            [
                'nombre' => 'Cacao en Polvo',
                'descripcion' => 'Cacao puro sin azúcar',
                'categoria' => 'Chocolates',
                'unidad' => UnidadMedida::GRAMO,
                'stock_minimo' => 500,
                'stock_inicial' => 2000,
                'proveedor' => 'Insumos Premium SA',
                'precio' => 650.00,
                'unidad_compra' => UnidadMedida::GRAMO,
                'cantidad_por_bulto' => 1,
            ],
            [
                'nombre' => 'Crema de Leche',
                'descripcion' => 'Crema de leche para batir',
                'categoria' => 'Lácteos',
                'unidad' => UnidadMedida::MILILITRO,
                'stock_minimo' => 1000,
                'stock_inicial' => 3000,
                'proveedor' => 'Mayorista El Buen Precio',
                'precio' => 280.00,
                'unidad_compra' => UnidadMedida::MILILITRO,
                'cantidad_por_bulto' => 1,
            ],
            [
                'nombre' => 'Polvo de Hornear',
                'descripcion' => 'Levadura química',
                'categoria' => 'Harinas y Almidones',
                'unidad' => UnidadMedida::GRAMO,
                'stock_minimo' => 200,
                'stock_inicial' => 800,
                'proveedor' => 'Distribuidora La Central',
                'precio' => 95.00,
                'unidad_compra' => UnidadMedida::GRAMO,
                'cantidad_por_bulto' => 1,
            ],
        ];

        $insumosCreados = [];
        foreach ($insumos as $insumoData) {
            $categoria = collect($categorias)->firstWhere('nombre', $insumoData['categoria']);
            $proveedor = collect($proveedores)->firstWhere('nombre_empresa', $insumoData['proveedor']);

            $insumo = Insumo::firstOrCreate(
                ['nombre' => $insumoData['nombre']],
                [
                    'descripcion' => $insumoData['descripcion'],
                    'categoria_insumo_id' => $categoria->id,
                    'unidad_de_medida' => $insumoData['unidad'],
                    'stock_minimo' => $insumoData['stock_minimo'],
                    'activo' => true,
                ]
            );

            // Vincular con proveedor
            if (!$insumo->proveedores()->where('proveedor_id', $proveedor->id)->exists()) {
                $insumo->proveedores()->attach($proveedor->id, [
                    'precio' => $insumoData['precio'],
                    'unidad_compra' => $insumoData['unidad_compra']->value,
                    'cantidad_por_bulto' => $insumoData['cantidad_por_bulto'],
                    'tiempo_entrega_dias' => 3,
                ]);
            }

            // Crear lote inicial de stock
            if (!Lote::where('insumo_id', $insumo->id)->exists()) {
                Lote::create([
                    'insumo_id' => $insumo->id,
                    'cantidad_inicial' => $insumoData['stock_inicial'],
                    'cantidad_actual' => $insumoData['stock_inicial'],
                    'codigo_lote' => 'INICIAL-' . $insumo->id,
                    'fecha_vencimiento' => Carbon::now()->addMonths(6),
                ]);
            }

            $insumosCreados[] = $insumo;
        }

        echo "   ✓ " . count($insumosCreados) . " insumos creados con stock\n";
        return $insumosCreados;
    }

    private function crearProductos(array $categorias, array $insumos): array
    {
        echo "🍰 Creando productos con recetas...\n";

        $productos = [
            [
                'nombre' => 'Torta de Chocolate',
                'descripcion' => 'Torta de chocolate húmeda con cobertura',
                'categoria' => 'Tortas',
                'precio' => 5500.00,
                'tiempo_preparacion' => 120,
                'receta' => [
                    ['insumo' => 'Harina 0000', 'cantidad' => 300],
                    ['insumo' => 'Azúcar', 'cantidad' => 250],
                    ['insumo' => 'Cacao en Polvo', 'cantidad' => 80],
                    ['insumo' => 'Manteca', 'cantidad' => 150],
                    ['insumo' => 'Huevos', 'cantidad' => 3],
                    ['insumo' => 'Leche Entera', 'cantidad' => 200],
                    ['insumo' => 'Esencia de Vainilla', 'cantidad' => 10],
                ],
            ],
            [
                'nombre' => 'Cupcakes de Vainilla (x6)',
                'descripcion' => 'Set de 6 cupcakes con buttercream',
                'categoria' => 'Cupcakes',
                'precio' => 2400.00,
                'tiempo_preparacion' => 60,
                'receta' => [
                    ['insumo' => 'Harina 0000', 'cantidad' => 200],
                    ['insumo' => 'Azúcar', 'cantidad' => 150],
                    ['insumo' => 'Manteca', 'cantidad' => 100],
                    ['insumo' => 'Huevos', 'cantidad' => 2],
                    ['insumo' => 'Leche Entera', 'cantidad' => 100],
                    ['insumo' => 'Esencia de Vainilla', 'cantidad' => 5],
                ],
            ],
            [
                'nombre' => 'Brownie con Nueces',
                'descripcion' => 'Brownie denso de chocolate con nueces',
                'categoria' => 'Postres',
                'precio' => 3200.00,
                'tiempo_preparacion' => 45,
                'receta' => [
                    ['insumo' => 'Chocolate para Repostería', 'cantidad' => 200],
                    ['insumo' => 'Manteca', 'cantidad' => 120],
                    ['insumo' => 'Azúcar', 'cantidad' => 180],
                    ['insumo' => 'Huevos', 'cantidad' => 3],
                    ['insumo' => 'Harina 0000', 'cantidad' => 80],
                ],
            ],
            [
                'nombre' => 'Torta de Vainilla',
                'descripcion' => 'Torta clásica de vainilla con relleno de dulce de leche',
                'categoria' => 'Tortas',
                'precio' => 4800.00,
                'tiempo_preparacion' => 100,
                'receta' => [
                    ['insumo' => 'Harina 0000', 'cantidad' => 350],
                    ['insumo' => 'Azúcar', 'cantidad' => 280],
                    ['insumo' => 'Manteca', 'cantidad' => 180],
                    ['insumo' => 'Huevos', 'cantidad' => 4],
                    ['insumo' => 'Leche Entera', 'cantidad' => 250],
                    ['insumo' => 'Esencia de Vainilla', 'cantidad' => 15],
                    ['insumo' => 'Polvo de Hornear', 'cantidad' => 20],
                ],
            ],
            [
                'nombre' => 'Mousse de Chocolate',
                'descripcion' => 'Mousse suave de chocolate belga',
                'categoria' => 'Postres',
                'precio' => 1800.00,
                'tiempo_preparacion' => 30,
                'receta' => [
                    ['insumo' => 'Chocolate para Repostería', 'cantidad' => 150],
                    ['insumo' => 'Crema de Leche', 'cantidad' => 200],
                    ['insumo' => 'Huevos', 'cantidad' => 2],
                    ['insumo' => 'Azúcar', 'cantidad' => 80],
                ],
            ],
            [
                'nombre' => 'Galletas de Chocolate (x12)',
                'descripcion' => 'Docena de galletas con chips de chocolate',
                'categoria' => 'Galletas',
                'precio' => 1500.00,
                'tiempo_preparacion' => 40,
                'receta' => [
                    ['insumo' => 'Harina 0000', 'cantidad' => 250],
                    ['insumo' => 'Manteca', 'cantidad' => 130],
                    ['insumo' => 'Azúcar', 'cantidad' => 150],
                    ['insumo' => 'Chocolate para Repostería', 'cantidad' => 100],
                    ['insumo' => 'Huevos', 'cantidad' => 1],
                    ['insumo' => 'Esencia de Vainilla', 'cantidad' => 5],
                ],
            ],
        ];

        $productosCreados = [];
        foreach ($productos as $prodData) {
            $categoria = collect($categorias)->firstWhere('nombre', $prodData['categoria']);

            $producto = Producto::firstOrCreate(
                ['nombre' => $prodData['nombre']],
                [
                    'descripcion' => $prodData['descripcion'],
                    'categoria_producto_id' => $categoria->id,
                    'precio_venta' => $prodData['precio'],
                    'activo' => true,
                    'visible_en_catalogo' => true,
                ]
            );

            // Crear receta
            $receta = Receta::firstOrCreate(
                ['producto_id' => $producto->id],
                ['nombre' => 'Receta ' . $producto->nombre]
            );

            // Agregar insumos a la receta
            foreach ($prodData['receta'] as $insumoReceta) {
                $insumo = collect($insumos)->firstWhere('nombre', $insumoReceta['insumo']);
                
                if (!$receta->insumos()->where('insumo_id', $insumo->id)->exists()) {
                    $receta->insumos()->attach($insumo->id, [
                        'cantidad' => $insumoReceta['cantidad'],
                    ]);
                }
            }

            $productosCreados[] = $producto;
        }

        echo "   ✓ " . count($productosCreados) . " productos con recetas creados\n";
        return $productosCreados;
    }

    private function crearPedidos(array $clientes, array $productos): void
    {
        echo "📝 Creando pedidos de prueba...\n";

        $vendedor = User::where('role', 'vendedor')->first();
        if (!$vendedor) {
            $vendedor = User::where('role', 'administrador')->first();
        }

        $pedidosData = [
            [
                'cliente' => 0,
                'fecha_entrega' => Carbon::now()->addDays(3),
                'items' => [
                    ['producto' => 'Torta de Chocolate', 'cantidad' => 1],
                    ['producto' => 'Cupcakes de Vainilla (x6)', 'cantidad' => 2],
                ],
            ],
            [
                'cliente' => 1,
                'fecha_entrega' => Carbon::now()->addDays(4),
                'items' => [
                    ['producto' => 'Brownie con Nueces', 'cantidad' => 2],
                    ['producto' => 'Mousse de Chocolate', 'cantidad' => 4],
                ],
            ],
            [
                'cliente' => 2,
                'fecha_entrega' => Carbon::now()->addDays(5),
                'items' => [
                    ['producto' => 'Torta de Vainilla', 'cantidad' => 1],
                    ['producto' => 'Galletas de Chocolate (x12)', 'cantidad' => 3],
                ],
            ],
        ];

        foreach ($pedidosData as $pedidoData) {
            $cliente = $clientes[$pedidoData['cliente']];
            
            $pedido = Pedido::create([
                'cliente_id' => $cliente->id,
                'vendedor_id' => $vendedor->id,
                'fecha_entrega' => $pedidoData['fecha_entrega'],
                'status' => 'confirmado',
                'total_calculado' => 0, // Se calcula después
                'monto_abonado' => 0,
                'saldo_pendiente' => 0,
            ]);

            $total = 0;
            foreach ($pedidoData['items'] as $itemData) {
                $producto = collect($productos)->firstWhere('nombre', $itemData['producto']);
                $subtotal = $producto->precio_venta * $itemData['cantidad'];
                $total += $subtotal;

                $pedido->items()->create([
                    'producto_id' => $producto->id,
                    'cantidad' => $itemData['cantidad'],
                    'precio_unitario' => $producto->precio_venta,
                    'subtotal' => $subtotal,
                ]);
            }

            $pedido->update([
                'total_calculado' => $total,
                'saldo_pendiente' => $total,
            ]);
        }

        echo "   ✓ 3 pedidos confirmados creados\n";
    }
}
