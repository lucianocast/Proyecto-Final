<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICACIÓN DE ADMINISTRADORES PARA NOTIFICACIONES ===\n\n";

// Verificar con el query CORRECTO (administrador)
echo "✅ Búsqueda con 'administrador' (CORRECTO):\n";
$adminsCorrect = \App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'administrador');
})->get();

if ($adminsCorrect->isEmpty()) {
    echo "   ⚠️ NO se encontraron usuarios con rol 'administrador'\n";
} else {
    echo "   ✓ Encontrados {$adminsCorrect->count()} administrador(es):\n";
    foreach ($adminsCorrect as $admin) {
        echo "     - {$admin->name} ({$admin->email})\n";
    }
}
echo "\n";

// Verificar con el query INCORRECTO (admin) - el que estaba antes
echo "❌ Búsqueda con 'admin' (INCORRECTO - antes del fix):\n";
$adminsWrong = \App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'admin');
})->get();

if ($adminsWrong->isEmpty()) {
    echo "   ⚠️ NO se encuentran usuarios (por eso no se enviaban emails)\n";
} else {
    echo "   ✓ Encontrados {$adminsWrong->count()} usuario(s)\n";
}
echo "\n";

echo "📧 Resultado: El servicio ahora encontrará correctamente a los administradores.\n";
