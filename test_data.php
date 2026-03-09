<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$eventos = \App\Models\Evento::orderBy('fecha_inicio', 'desc')->get();
echo json_encode($eventos);
