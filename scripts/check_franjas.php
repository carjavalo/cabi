<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate exactly what the show endpoint returns (same eager loading)
$evento = App\Models\Evento::with(['eventoDias', 'eventoFranjas.inscripciones', 'eventoInscripciones.franja'])->findOrFail(1);
$json = json_decode($evento->toJson(), true);

echo "=== evento_inscripciones from JSON ===\n";
foreach ($json['evento_inscripciones'] as $ins) {
    $f = $ins['franja'] ?? null;
    echo "  INS id={$ins['id']} | franja_id={$ins['evento_franja_id']} | franja=" . ($f ? "id={$f['id']} dia={$f['dia_semana']} {$f['hora_inicio']}-{$f['hora_fin']}" : "NULL") . "\n";
}

echo "\n=== evento_franjas with inscripciones ===\n";
foreach ($json['evento_franjas'] as $f) {
    echo "  FRANJA id={$f['id']} dia={$f['dia_semana']} {$f['hora_inicio']}-{$f['hora_fin']} -> " . count($f['inscripciones'] ?? []) . " inscripciones\n";
    foreach ($f['inscripciones'] ?? [] as $ins) {
        echo "    -> INS id={$ins['id']} franja_id={$ins['evento_franja_id']}\n";
    }
}
