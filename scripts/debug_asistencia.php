<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== EVENTO_FRANJAS (primeros 10) ===" . PHP_EOL;
$franjas = DB::table('evento_franjas')->select('id','dia_semana','hora_inicio','hora_fin')->limit(10)->get();
foreach ($franjas as $f) {
    echo "  ID={$f->id}  dia_semana={$f->dia_semana}  hora_inicio={$f->hora_inicio}  hora_fin={$f->hora_fin}" . PHP_EOL;
}

echo PHP_EOL . "=== EVENTO_INSCRIPCIONES (primeros 5) ===" . PHP_EOL;
$inscritos = DB::table('evento_inscripciones')->select('id','identificacion','nombre_completo','evento_franja_id','evento_id')->limit(5)->get();
foreach ($inscritos as $i) {
    echo "  ID={$i->id}  identificacion={$i->identificacion}  nombre={$i->nombre_completo}  franja_id={$i->evento_franja_id}  evento_id={$i->evento_id}" . PHP_EOL;
}

echo PHP_EOL . "=== TEST: Cargar asistencia para hoy (Martes=2), franja 07:00 a 08:00 ===" . PHP_EOL;
$fecha = date('Y-m-d');
$diaSemana = date('N', strtotime($fecha));
echo "Fecha: {$fecha}, Dia semana PHP: {$diaSemana}" . PHP_EOL;

$users = DB::table('evento_inscripciones')
    ->leftJoin('evento_franjas', 'evento_inscripciones.evento_franja_id', '=', 'evento_franjas.id')
    ->where('evento_franjas.dia_semana', $diaSemana)
    ->select('evento_inscripciones.identificacion', 'evento_inscripciones.nombre_completo', 'evento_franjas.dia_semana', 'evento_franjas.hora_inicio', 'evento_franjas.hora_fin')
    ->distinct()
    ->limit(10)
    ->get();

echo "Resultados encontrados: " . count($users) . PHP_EOL;
foreach ($users as $u) {
    echo "  {$u->identificacion} - {$u->nombre_completo} | dia={$u->dia_semana} hora={$u->hora_inicio}-{$u->hora_fin}" . PHP_EOL;
}

echo PHP_EOL . "=== Tipos de dato de dia_semana ===" . PHP_EOL;
$tipos = DB::select("SHOW COLUMNS FROM evento_franjas LIKE 'dia_semana'");
echo json_encode($tipos) . PHP_EOL;

$tipos2 = DB::select("SHOW COLUMNS FROM evento_franjas LIKE 'hora_inicio'");
echo json_encode($tipos2) . PHP_EOL;
