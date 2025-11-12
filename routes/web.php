<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Encargado\DashboardController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProveedorInsumoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaProductoController;
use App\Http\Controllers\RecetaController;
use App\Http\Middleware\EnsureUserIsEncargado;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Encargado\CompraController;
use App\Http\Controllers\Proveedor\PanelController as ProveedorPanelController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Rutas para el rol Encargado
Route::middleware(['auth', EnsureUserIsEncargado::class])->prefix('encargado')->name('encargado.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/compras', [DashboardController::class, 'compras'])->name('compras');
    // Proveedores: listado y CRUD + catálogo (reemplaza la ruta anterior)

    Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores');
    // Aseguramos que el parámetro de ruta sea `proveedor` para coincidir con las variables de las vistas
    Route::resource('proveedores', ProveedorController::class)->except(['index'])->parameters(['proveedores' => 'proveedor']);

    // Rutas para gestionar el catálogo (insumos que vende cada proveedor)
    Route::post('proveedores/{proveedor}/catalogo', [ProveedorInsumoController::class, 'store'])->name('proveedor.catalogo.store');
    Route::patch('proveedores/{proveedor}/catalogo/{insumo}', [ProveedorInsumoController::class, 'update'])->name('proveedor.catalogo.update');
    Route::delete('proveedores/{proveedor}/catalogo/{insumo}', [ProveedorInsumoController::class, 'destroy'])->name('proveedor.catalogo.destroy');
    // Producción (Productos y Recetas)
    Route::get('/produccion', [ProductoController::class, 'index'])->name('produccion');

    // Productos: crear/editar/borrar (el índice está expuesto en /encargado/produccion)
    Route::resource('productos', ProductoController::class)->except(['index'])->parameters(['productos' => 'producto']);

    // Categorías de productos
    Route::resource('categorias-productos', CategoriaProductoController::class)->parameters(['categorias-productos' => 'categorias_producto']);

    // Rutas para gestionar recetas e insumos de la receta
    Route::get('productos/{producto}/receta', [RecetaController::class, 'show'])->name('receta.show');
    Route::post('recetas/{receta}/insumos', [RecetaController::class, 'store'])->name('receta.insumo.store');
    Route::patch('recetas/{receta}/insumos/{insumo}', [RecetaController::class, 'update'])->name('receta.insumo.update');
    Route::delete('recetas/{receta}/insumos/{insumo}', [RecetaController::class, 'destroy'])->name('receta.insumo.destroy');
    Route::get('/stock', [\App\Http\Controllers\InsumoController::class, 'index'])->name('stock');
    
    // Gestión de categorías e insumos (Encargado)
    Route::resource('categorias-insumos', \App\Http\Controllers\CategoriaInsumoController::class);
    Route::resource('insumos', \App\Http\Controllers\InsumoController::class);

    // Stock actions
    Route::post('insumos/{insumo}/agregar-stock', [\App\Http\Controllers\StockController::class, 'store'])->name('stock.store');
    Route::patch('stock/lote/{lote}/ajustar', [\App\Http\Controllers\StockController::class, 'update'])->name('stock.update');

    // Compras (Órdenes de Compra)
    Route::get('compras', [CompraController::class, 'index'])->name('compras');
    Route::get('compras/create', [CompraController::class, 'create'])->name('compras.create');
    Route::post('compras', [CompraController::class, 'store'])->name('compras.store');
    Route::get('compras/{ordenDeCompra}', [CompraController::class, 'show'])->name('compras.show');
    Route::post('compras/{ordenDeCompra}/receive', [CompraController::class, 'receive'])->name('compras.receive');
});

// Dashboards mínimos para otros roles (protegidos por rol)
use App\Http\Middleware\EnsureRole;

Route::middleware(['auth', EnsureRole::class . ':administrador'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    // Ruta principal para gestionar usuarios y roles (index)
    Route::get('/usuarios-roles', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('usuarios_roles');
        Route::get('/sistema', [\App\Http\Controllers\Admin\DashboardController::class, 'sistema'])->name('sistema');
        Route::get('/notificaciones', [\App\Http\Controllers\Admin\DashboardController::class, 'notificaciones'])->name('notificaciones');
        Route::get('/reportes', [\App\Http\Controllers\Admin\DashboardController::class, 'reportes'])->name('reportes');
        Route::get('/auditoria', [\App\Http\Controllers\Admin\DashboardController::class, 'auditoria'])->name('auditoria');

        // Rutas de gestión de usuarios (base: /admin/usuarios-roles)
        Route::resource('usuarios-roles', \App\Http\Controllers\Admin\UserController::class)
            ->names('users')
            ->parameters(['usuarios-roles' => 'user']);
    });
});

Route::middleware(['auth', EnsureRole::class . ':vendedor'])->group(function () {
    Route::get('/vendedor', function () { return view('roles.vendedor'); })->name('vendedor.dashboard');
});

Route::middleware(['auth', EnsureRole::class . ':cliente'])->group(function () {
    Route::get('/cliente', function () { return view('roles.cliente'); })->name('cliente.dashboard');
});

/*
|--------------------------------------------------------------------------
| Rutas del Proveedor
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix' => 'proveedor', 
    'middleware' => ['auth', \App\Http\Middleware\EnsureRole::class . ':proveedor'], 
    'as' => 'proveedor.'
], function() {
    
    // Esta es la ruta que arregla tu problema:
    Route::get('/proveedor/dashboard', [ProveedorPanelController::class, 'dashboard'])->name('dashboard');
    
    // Estas son las otras rutas que ya creamos para las acciones:
    Route::post('/oc/{ordenDeCompra}/confirmar', [ProveedorPanelController::class, 'confirm'])->name('oc.confirm');
    Route::post('/oc/{ordenDeCompra}/rechazar', [ProveedorPanelController::class, 'reject'])->name('oc.reject');
    
});
