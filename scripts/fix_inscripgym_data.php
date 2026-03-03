<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Inscripgym;

$user = Inscripgym::first();
if ($user) {
    echo "Updating user ID 1...\n";
    $user->identificacion = '111222333';
    $user->save();
    echo "Updated! New data: " . $user->toJson() . "\n";
} else {
    echo "User not found.\n";
}
