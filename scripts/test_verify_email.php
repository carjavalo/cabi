<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST NOTIFICACIÓN DE VERIFICACIÓN ===\n\n";

// Buscar un usuario para probar
$user = \App\Models\User::first();
if (!$user) {
    echo "No hay usuarios en la base de datos.\n";
    exit(1);
}

echo "Usuario: {$user->name} ({$user->email})\n\n";

echo "Enviando notificación de verificación...\n";
try {
    $notification = new \App\Notifications\CustomVerifyEmail();
    $user->notify($notification);
    echo "RESULTADO: ÉXITO - Notificación enviada a {$user->email}\n";
} catch (Exception $e) {
    echo "RESULTADO: ERROR - " . $e->getMessage() . "\n";
    echo "TRACE: " . $e->getTraceAsString() . "\n";
}
