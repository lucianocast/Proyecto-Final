<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICACIÓN DE ROLES Y USUARIOS ===\n\n";

// 1. Verificar roles
echo "📋 Roles en la base de datos:\n";
$roles = \Spatie\Permission\Models\Role::all();
if ($roles->isEmpty()) {
    echo "   ⚠️ NO HAY ROLES CREADOS\n\n";
} else {
    foreach ($roles as $role) {
        echo "   - {$role->name} (guard: {$role->guard_name})\n";
    }
    echo "\n";
}

// 2. Verificar usuarios y sus roles
echo "👥 Usuarios y roles asignados:\n";
$users = \App\Models\User::with('roles')->get();
foreach ($users as $user) {
    $userRoles = $user->roles->pluck('name')->toArray();
    $rolesStr = empty($userRoles) ? '❌ SIN ROLES' : implode(', ', $userRoles);
    echo "   - {$user->email}: {$rolesStr}\n";
}
echo "\n";

// 3. Verificar campo 'role' (antiguo sistema)
echo "🔧 Campo 'role' directo (sistema antiguo):\n";
foreach ($users as $user) {
    $roleField = $user->role ?? 'NULL';
    echo "   - {$user->email}: role = {$roleField}\n";
}
echo "\n";

// 4. Verificar usuarios con rol 'admin' según whereHas
echo "🔍 Usuarios encontrados con whereHas('roles', 'admin'):\n";
$admins = \App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'admin');
})->get();

if ($admins->isEmpty()) {
    echo "   ⚠️ NO SE ENCONTRARON ADMINISTRADORES (esto explica por qué no se envían emails)\n";
} else {
    foreach ($admins as $admin) {
        echo "   - {$admin->email}\n";
    }
}
echo "\n";

echo "✅ Verificación completa\n";
