<?php
/**
 * Script de migración para tablas de sesiones de capacitaciones en producción (cPanel)
 *
 * INSTRUCCIONES:
 * 1. Subir este archivo a /public/ en el servidor
 * 2. Acceder desde el navegador: https://cabi.huv.gov.co/fix_capacitaciones_produccion.php?token=fix_cabi_2026_capacitaciones
 * 3. Primero ejecutar con action=diagnostico (por defecto) para ver el estado
 * 4. Luego ejecutar con action=migrar para crear las tablas
 * 5. ELIMINAR ESTE ARCHIVO después de usar
 *
 * SEGURIDAD: Protegido con token temporal
 */

// ── Token de seguridad ──
$SECURITY_TOKEN = 'fix_cabi_2026_capacitaciones';

$providedToken = $_GET['token'] ?? '';
if ($providedToken !== $SECURITY_TOKEN) {
    http_response_code(403);
    echo "<h1>Acceso denegado</h1>";
    echo "<p>Uso: <code>fix_capacitaciones_produccion.php?token={$SECURITY_TOKEN}</code></p>";
    exit;
}

$basePath = dirname(__DIR__);
require $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

$action = $_GET['action'] ?? 'diagnostico';
$results = [];

// ══════════════════════════════════════════════
// DIAGNÓSTICO
// ══════════════════════════════════════════════
if ($action === 'diagnostico') {
    $results[] = '🔍 DIAGNÓSTICO DE TABLAS DE CAPACITACIONES';
    $results[] = str_repeat('─', 50);

    // Tabla capacitaciones
    $results[] = Schema::hasTable('capacitaciones')
        ? '✅ Tabla capacitaciones: EXISTE'
        : '❌ Tabla capacitaciones: NO EXISTE (requerida)';

    // Columna token en capacitaciones
    if (Schema::hasTable('capacitaciones')) {
        $results[] = Schema::hasColumn('capacitaciones', 'token')
            ? '✅ Columna token en capacitaciones: EXISTE'
            : '⚠️ Columna token en capacitaciones: NO EXISTE (se creará)';

        $totalCap = DB::table('capacitaciones')->count();
        $results[] = "   Capacitaciones existentes: {$totalCap}";

        if (Schema::hasColumn('capacitaciones', 'token')) {
            $sinToken = DB::table('capacitaciones')->whereNull('token')->count();
            $results[] = "   Sin token: {$sinToken}";
        }
    }

    // Tabla capacitacion_sesiones
    $results[] = Schema::hasTable('capacitacion_sesiones')
        ? '✅ Tabla capacitacion_sesiones: EXISTE'
        : '⚠️ Tabla capacitacion_sesiones: NO EXISTE (se creará)';

    if (Schema::hasTable('capacitacion_sesiones')) {
        $totalSes = DB::table('capacitacion_sesiones')->count();
        $results[] = "   Sesiones existentes: {$totalSes}";
    }

    // Tabla capacitacion_asistencia_registros
    $results[] = Schema::hasTable('capacitacion_asistencia_registros')
        ? '✅ Tabla capacitacion_asistencia_registros: EXISTE'
        : '⚠️ Tabla capacitacion_asistencia_registros: NO EXISTE (se creará)';

    if (Schema::hasTable('capacitacion_asistencia_registros')) {
        $totalReg = DB::table('capacitacion_asistencia_registros')->count();
        $results[] = "   Registros existentes: {$totalReg}";
    }

    $results[] = '';
    $results[] = '📋 ACCIONES DISPONIBLES:';
    $results[] = "  → Migrar: fix_capacitaciones_produccion.php?token={$SECURITY_TOKEN}&action=migrar";
}

// ══════════════════════════════════════════════
// MIGRAR
// ══════════════════════════════════════════════
if ($action === 'migrar') {
    $results[] = '🚀 EJECUTANDO MIGRACIÓN';
    $results[] = str_repeat('─', 50);

    try {
        // 1. Agregar columna token a capacitaciones si no existe
        if (Schema::hasTable('capacitaciones') && !Schema::hasColumn('capacitaciones', 'token')) {
            Schema::table('capacitaciones', function ($table) {
                $table->string('token', 64)->nullable()->unique()->after('created_by');
            });
            $results[] = '✅ Columna token agregada a capacitaciones';
        } else {
            $results[] = '⏭️ Columna token ya existe en capacitaciones';
        }

        // Generar tokens para capacitaciones que no tengan
        if (Schema::hasColumn('capacitaciones', 'token')) {
            $sinToken = DB::table('capacitaciones')->whereNull('token')->get();
            foreach ($sinToken as $cap) {
                DB::table('capacitaciones')->where('id', $cap->id)->update(['token' => Str::random(32)]);
            }
            $results[] = '✅ Tokens generados para ' . $sinToken->count() . ' capacitaciones';
        }

        // 2. Crear tabla capacitacion_sesiones
        if (!Schema::hasTable('capacitacion_sesiones')) {
            Schema::create('capacitacion_sesiones', function ($table) {
                $table->id();
                $table->unsignedBigInteger('capacitacion_id');
                $table->string('token', 64)->unique();
                $table->date('fecha');
                $table->time('hora_inicio')->nullable();
                $table->time('hora_fin')->nullable();
                $table->json('citados_ids')->nullable();
                $table->timestamps();

                $table->foreign('capacitacion_id')->references('id')->on('capacitaciones')->cascadeOnDelete();
            });
            $results[] = '✅ Tabla capacitacion_sesiones creada';
        } else {
            $results[] = '⏭️ Tabla capacitacion_sesiones ya existe';
        }

        // 3. Crear tabla capacitacion_asistencia_registros
        if (!Schema::hasTable('capacitacion_asistencia_registros')) {
            Schema::create('capacitacion_asistencia_registros', function ($table) {
                $table->id();
                $table->unsignedBigInteger('sesion_id');
                $table->unsignedBigInteger('capacitacion_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('nombre', 200);
                $table->string('identificacion', 50);
                $table->string('tipo_contrato', 100)->nullable();
                $table->string('correo', 200)->nullable();
                $table->boolean('autoriza_firma')->default(false);
                $table->boolean('es_citado')->default(false);
                $table->timestamps();

                $table->foreign('sesion_id')->references('id')->on('capacitacion_sesiones')->cascadeOnDelete();
                $table->foreign('capacitacion_id')->references('id')->on('capacitaciones')->cascadeOnDelete();
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
                $table->unique(['sesion_id', 'identificacion']);
            });
            $results[] = '✅ Tabla capacitacion_asistencia_registros creada';
        } else {
            $results[] = '⏭️ Tabla capacitacion_asistencia_registros ya existe';
        }

        // 4. Crear sesión inicial para capacitaciones existentes que no tengan sesión
        if (Schema::hasTable('capacitacion_sesiones')) {
            $capsIds = DB::table('capacitacion_sesiones')->pluck('capacitacion_id')->unique()->toArray();
            $capsSinSesion = DB::table('capacitaciones')->whereNotIn('id', $capsIds)->get();

            foreach ($capsSinSesion as $cap) {
                $citadosIds = DB::table('capacitacion_asistencias')
                    ->where('capacitacion_id', $cap->id)
                    ->pluck('user_id')->toArray();

                DB::table('capacitacion_sesiones')->insert([
                    'capacitacion_id' => $cap->id,
                    'token' => Str::random(32),
                    'fecha' => $cap->fecha,
                    'hora_inicio' => $cap->hora_inicio,
                    'hora_fin' => $cap->hora_fin,
                    'citados_ids' => json_encode($citadosIds),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $results[] = '✅ Sesiones iniciales creadas para ' . $capsSinSesion->count() . ' capacitaciones';
        }

        $results[] = '';
        $results[] = '🎉 MIGRACIÓN COMPLETADA EXITOSAMENTE';
        $results[] = '';
        $results[] = '⚠️ IMPORTANTE: Eliminar este archivo del servidor después de verificar.';

    } catch (\Exception $e) {
        $results[] = '❌ ERROR: ' . $e->getMessage();
        $results[] = '   Archivo: ' . $e->getFile() . ':' . $e->getLine();
    }
}

// ══════════════════════════════════════════════
// SALIDA HTML
// ══════════════════════════════════════════════
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Capacitaciones - Producción</title>
    <style>
        body { font-family: monospace; padding: 2rem; background: #1a1a2e; color: #e0e0e0; }
        pre { background: #16213e; padding: 1.5rem; border-radius: 8px; white-space: pre-wrap; line-height: 1.8; }
        h1 { color: #4fc3f7; }
        a { color: #4fc3f7; text-decoration: underline; }
    </style>
</head>
<body>
    <h1>Fix Capacitaciones - Producción</h1>
    <pre><?php echo implode("\n", $results); ?></pre>
    <p>
        <a href="?token=<?php echo $SECURITY_TOKEN; ?>&action=diagnostico">🔍 Diagnóstico</a> |
        <a href="?token=<?php echo $SECURITY_TOKEN; ?>&action=migrar">🚀 Migrar</a>
    </p>
</body>
</html>
