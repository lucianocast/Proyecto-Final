<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Encargado\DashboardController;
use App\Http\Middleware\EnsureUserIsEncargado;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
    Route::get('/proveedores', [DashboardController::class, 'proveedores'])->name('proveedores');
    Route::get('/produccion', [DashboardController::class, 'produccion'])->name('produccion');
    Route::get('/stock', [DashboardController::class, 'stock'])->name('stock');
});

// Dashboards mínimos para otros roles (protegidos por rol)
use App\Http\Middleware\EnsureRole;

Route::middleware(['auth', EnsureRole::class . ':administrador'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/usuarios-roles', [\App\Http\Controllers\Admin\DashboardController::class, 'usuariosRoles'])->name('usuarios_roles');
        Route::get('/sistema', [\App\Http\Controllers\Admin\DashboardController::class, 'sistema'])->name('sistema');
        Route::get('/notificaciones', [\App\Http\Controllers\Admin\DashboardController::class, 'notificaciones'])->name('notificaciones');
        Route::get('/reportes', [\App\Http\Controllers\Admin\DashboardController::class, 'reportes'])->name('reportes');
        Route::get('/auditoria', [\App\Http\Controllers\Admin\DashboardController::class, 'auditoria'])->name('auditoria');
    });
});

Route::middleware(['auth', EnsureRole::class . ':vendedor'])->group(function () {
    Route::get('/vendedor', function () { return view('roles.vendedor'); })->name('vendedor.dashboard');
});

Route::middleware(['auth', EnsureRole::class . ':cliente'])->group(function () {
    Route::get('/cliente', function () { return view('roles.cliente'); })->name('cliente.dashboard');
});

Route::middleware(['auth', EnsureRole::class . ':proveedor'])->group(function () {
    Route::get('/proveedor', function () { return view('roles.proveedor'); })->name('proveedor.dashboard');
});
