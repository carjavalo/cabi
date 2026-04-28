<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaHorarioController;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Config\EncuestaController;

// ─── Rutas protegidas: requieren autenticación Y verificación de correo ───
use App\Models\Servicio;
use App\Models\Vinculacion;
use App\Http\Controllers\BienestarListadoController;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', function () {
        // Obtener registros de la tabla 'publicidad' que estén activos y dentro del rango de fechas
        $publicidad = [];
        try {
            $now = now();
            $publicidad = \App\Models\Publicidad::where('activo', 1)
                ->where(function($query) use ($now) {
                    $query->where(function($q) use ($now) {
                        $q->whereNull('fecha_inicio')
                          ->orWhere('fecha_inicio', '<=', $now);
                    })
                    ->where(function($q) use ($now) {
                        $q->whereNull('fecha_fin')
                          ->orWhere('fecha_fin', '>', $now);
                    });
                })
                ->orderBy('prioridad', 'desc')
                ->orderBy('id', 'desc')
                ->get();
        } catch (\Throwable $e) {
            $publicidad = collect();
        }
        return view('dashboard', compact('publicidad'));
    })->name('dashboard');

    // Bienestar - GYM routes
    Route::get('/bienestar/gym/inscripcion', function () {
        $servicios = Servicio::orderBy('nombre')->get();
        $vinculaciones = Vinculacion::orderBy('nombre')->get();
        return view('bienestar.gym.inscripcion', compact('servicios','vinculaciones'));
    });

    // Bienestar - Listados
    Route::get('/bienestar/listados', [BienestarListadoController::class, 'index'])->name('bienestar.listados');
    Route::get('/bienestar/observaciones/{identificacion}', [BienestarListadoController::class, 'getObservaciones'])->name('bienestar.observaciones.get');
    Route::post('/bienestar/observaciones', [BienestarListadoController::class, 'storeObservacion'])->name('bienestar.observaciones.store');

    // Capacitaciones - Listado User
    Route::get('/capacitaciones', [\App\Http\Controllers\CapacitacionListadoController::class, 'index'])->name('capacitaciones.index_user');
    Route::get('/capacitaciones/{capacitacion}/editar', [\App\Http\Controllers\CapacitacionListadoController::class, 'edit'])->name('capacitaciones.edit_user');
    Route::put('/capacitaciones/{capacitacion}', [\App\Http\Controllers\CapacitacionListadoController::class, 'update'])->name('capacitaciones.update_user');

    // Rutas de asistencia
    Route::get('/bienestar/asistencia/cargar', [BienestarListadoController::class, 'cargarAsistencia'])->name('bienestar.asistencia.cargar');
    Route::post('/bienestar/asistencia/guardar', [BienestarListadoController::class, 'guardarAsistencia'])->name('bienestar.asistencia.guardar');
    Route::get('/bienestar/asistencia/consultar', [BienestarListadoController::class, 'consultarAsistencia'])->name('bienestar.asistencia.consultar');
    Route::post('/bienestar/gym/inscripcion', [AgendaHorarioController::class, 'storeInscription'])->name('inscripcion.store');

    // Agenda - usar controlador para mostrar y guardar
    Route::get('/bienestar/gym/agenda', [AgendaHorarioController::class, 'index'])->name('agenda.horario');
    Route::post('/bienestar/gym/agenda', [AgendaHorarioController::class, 'store'])->name('agenda.horario.store');

    // API endpoint: fetch inscription data by identificacion
    Route::get('/bienestar/gym/inscrip-by-id/{identificacion}', [AgendaHorarioController::class, 'fetchInscripById'])->name('agenda.fetchInscrip');

    // Inscritos Autorizaciones AJAX API
    Route::get('/api/inscritos', [\App\Http\Controllers\Api\InscritoController::class, 'getInscritos'])->name('api.inscritos.lista');
    Route::post('/api/inscritos/{id}/autorizar', [\App\Http\Controllers\Api\InscritoController::class, 'toggleAutorizacion'])->name('api.inscritos.autorizar');
    Route::put('/api/inscritos/{id}', [\App\Http\Controllers\Api\InscritoController::class, 'updateInscrito'])->name('api.inscritos.update');
    Route::delete('/api/inscritos/{id}', [\App\Http\Controllers\Api\InscritoController::class, 'deleteInscrito'])->name('api.inscritos.delete');

    // Configuración - rutas placeholder para gestión
    Route::get('/configuracion/usuarios', function () { return view('config.usuarios'); });
    Route::get('/configuracion/vinculaciones', function () { return view('config.vinculaciones'); });
    Route::get('/configuracion/cursos', function () { return view('config.cursos'); });
    Route::get('/otros/ciau1', function () { return view('otros.ciau1'); })->name('otros.ciau1');

}); // Fin del grupo auth + verified

// Load authentication routes (login, register, password reset)
require __DIR__.'/auth.php';

// Gestión de Usuarios (CRUD básico) - protegido con auth + verified
use App\Http\Controllers\Config\UserController;
use App\Http\Controllers\Config\ServicioController;
use App\Http\Controllers\Config\VinculacionController;
use App\Http\Controllers\Config\CargoController;
Route::prefix('configuracion')->name('config.')->middleware(['auth', 'verified'])->group(function () {
    Route::resource('usuarios', UserController::class)->except(['show']);
    Route::post('usuarios/{id}/verify-email', [UserController::class, 'verifyEmail'])->name('usuarios.verify-email');
    Route::resource('servicios', ServicioController::class)->except(['show']);
    Route::get('servicios-buscar', [ServicioController::class, 'buscar'])->name('servicios.buscar');
    Route::resource('vinculaciones', VinculacionController::class)->except(['show']);
    Route::get('cargos/export', [CargoController::class, 'exportExcel'])->name('cargos.export');
    Route::resource('cargos', CargoController::class);
    Route::get('encuestas/data', [EncuestaController::class, 'data'])->name('encuestas.data');
    Route::get('encuestas/{id}/edit-data', [EncuestaController::class, 'show'])->name('encuestas.show');
    Route::get('encuestas/{id}/respuestas', [EncuestaController::class, 'getRespuestas'])->name('encuestas.respuestas');
    Route::post('encuestas/{encuestaId}/respuestas/{respuestaId}/permitir-repetir', [EncuestaController::class, 'permitirRepetir'])->name('encuestas.permitir-repetir');
    Route::resource('encuestas', EncuestaController::class)->except(['show']);
    // Publicidad CRUD
    Route::get('publicidad/data', [App\Http\Controllers\Config\PublicidadController::class, 'data'])->name('publicidad.data');
    Route::resource('publicidad', App\Http\Controllers\Config\PublicidadController::class);
    // Eventos CRUD
    Route::get('eventos/data', [App\Http\Controllers\Config\EventoController::class, 'data'])->name('eventos.data');
    Route::resource('eventos', App\Http\Controllers\Config\EventoController::class);
    // Capacitaciones CRUD
    Route::resource('capacitaciones', App\Http\Controllers\Config\CapacitacionController::class);
    Route::post('capacitaciones/toggle-asistencia', [App\Http\Controllers\Config\CapacitacionController::class, 'toggleAsistencia'])->name('capacitaciones.toggle-asistencia');
    Route::post('capacitaciones/agregar-usuario', [App\Http\Controllers\Config\CapacitacionController::class, 'agregarUsuario'])->name('capacitaciones.agregar-usuario');
    Route::post('capacitaciones/remover-usuario', [App\Http\Controllers\Config\CapacitacionController::class, 'removerUsuario'])->name('capacitaciones.remover-usuario');
    Route::get('capacitaciones/{capacitacione}/informes', [App\Http\Controllers\Config\CapacitacionController::class, 'informes'])->name('capacitaciones.informes');
    Route::get('capacitaciones/{capacitacione}/sesiones/{sesion}/excel', [App\Http\Controllers\Config\CapacitacionController::class, 'exportarSesion'])->name('capacitaciones.exportar-sesion');
    Route::resource('publicidad', App\Http\Controllers\Config\PublicidadController::class);
});

// Rutas públicas para responder encuestas y eventos
Route::get('/encuestas/responder/{id}', [EncuestaController::class, 'mostrarEncuesta'])->name('encuestas.mostrar');
Route::post('/encuestas/guardar', [EncuestaController::class, 'guardarRespuesta'])->name('encuestas.guardar');

Route::get('/eventos/inscripcion/{id}', [App\Http\Controllers\Config\EventoController::class, 'inscripcion'])->name('eventos.inscripcion');

// Ruta pública para marcar asistencia a capacitaciones vía QR/link
Route::get('/capacitaciones/asistencia/buscar-usuario/{identificacion}', [App\Http\Controllers\CapacitacionAsistenciaPublicaController::class, 'buscarUsuario'])->name('capacitaciones.asistencia.buscar-usuario');
Route::get('/capacitaciones/asistencia/{token}', [App\Http\Controllers\CapacitacionAsistenciaPublicaController::class, 'mostrar'])->name('capacitaciones.asistencia.publica');
Route::post('/capacitaciones/asistencia/{token}', [App\Http\Controllers\CapacitacionAsistenciaPublicaController::class, 'marcar'])->name('capacitaciones.asistencia.marcar');

Route::get('/eventos/api/usuario/{identificacion}', [App\Http\Controllers\Config\EventoController::class, 'consultarUsuario'])->name('eventos.api.usuario');
Route::post('/eventos/inscripcion/{id}', [App\Http\Controllers\Config\EventoController::class, 'guardarInscripcion'])->name('eventos.inscripcion.guardar');
Route::post('/eventos/inscripciones/eliminar', [App\Http\Controllers\Config\EventoController::class, 'eliminarInscripciones'])->name('eventos.inscripciones.eliminar')->middleware(['auth', 'verified']);
Route::post('/eventos/inscripciones/actualizar', [App\Http\Controllers\Config\EventoController::class, 'actualizarInscripcion'])->name('eventos.inscripciones.actualizar')->middleware(['auth', 'verified']);

// ─── Ruta de diagnóstico de correo (TEMPORAL - eliminar después de verificar) ───
Route::get('/test-email-diagnostico', function () {
    if (!auth()->check() || auth()->user()->role !== 'Super Admin') {
        abort(403, 'Solo Super Admin puede acceder a esta ruta.');
    }

    $diagnostico = [];

    // 1. Verificar configuración SMTP actual
    $diagnostico['config_actual'] = [
        'MAIL_MAILER' => config('mail.default'),
        'MAIL_HOST' => config('mail.mailers.smtp.host'),
        'MAIL_PORT' => config('mail.mailers.smtp.port'),
        'MAIL_SCHEME' => config('mail.mailers.smtp.scheme') ?: '(vacío/null - correcto para puerto 587)',
        'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
        'MAIL_PASSWORD' => config('mail.mailers.smtp.password') ? '***SET***' : '***VACÍA***',
        'MAIL_FROM' => config('mail.from.address'),
        'APP_URL' => config('app.url'),
    ];

    // 2. Verificar conectividad al puerto 587
    $diagnostico['test_puerto_587'] = 'Probando...';
    $conn587 = @fsockopen('smtp.gmail.com', 587, $errno, $errstr, 10);
    if ($conn587) {
        $diagnostico['test_puerto_587'] = 'CONECTADO - Puerto 587 abierto';
        fclose($conn587);
    } else {
        $diagnostico['test_puerto_587'] = "BLOQUEADO - Error $errno: $errstr";
    }

    // 3. Verificar conectividad al puerto 465
    $conn465 = @fsockopen('smtp.gmail.com', 465, $errno2, $errstr2, 10);
    if ($conn465) {
        $diagnostico['test_puerto_465'] = 'CONECTADO - Puerto 465 abierto';
        fclose($conn465);
    } else {
        $diagnostico['test_puerto_465'] = "BLOQUEADO - Error $errno2: $errstr2";
    }

    // 4. Verificar extensiones PHP
    $diagnostico['php_extensions'] = [
        'openssl' => extension_loaded('openssl') ? 'OK' : 'FALTA',
        'sockets' => extension_loaded('sockets') ? 'OK' : 'FALTA',
        'php_version' => PHP_VERSION,
    ];

    // 5. Verificar logo
    $logoPath = public_path('img/logocorreo.jpeg');
    $diagnostico['logo'] = [
        'public_path' => $logoPath,
        'existe' => file_exists($logoPath) ? 'SÍ' : 'NO',
        'url_logo' => rtrim(config('app.url'), '/') . '/img/logocorreo.jpeg',
    ];

    // 6. Intentar enviar correo con configuración actual
    try {
        \Illuminate\Support\Facades\Mail::raw(
            'Prueba SMTP desde CABI. Fecha: ' . now()->toDateTimeString(),
            function ($message) {
                $message->to(auth()->user()->email)
                        ->subject('CABI - Test SMTP ' . now()->format('H:i:s'));
            }
        );
        $diagnostico['envio_config_actual'] = 'ÉXITO - Correo enviado a ' . auth()->user()->email;
    } catch (\Exception $e) {
        $diagnostico['envio_config_actual'] = 'ERROR: ' . $e->getMessage();

        // 7. Si falla con puerto 587, intentar con puerto 465 + smtps
        $diagnostico['intento_puerto_465'] = 'Probando con puerto 465 (SSL)...';
        try {
            config([
                'mail.mailers.smtp.host' => 'smtp.gmail.com',
                'mail.mailers.smtp.port' => 465,
                'mail.mailers.smtp.scheme' => 'smtps',
            ]);

            // Purgar mailer cacheado
            app()->forgetInstance('mail.manager');

            \Illuminate\Support\Facades\Mail::raw(
                'Prueba SMTP puerto 465 desde CABI. Fecha: ' . now()->toDateTimeString(),
                function ($message) {
                    $message->to(auth()->user()->email)
                            ->subject('CABI - Test SMTP 465 ' . now()->format('H:i:s'));
                }
            );
            $diagnostico['intento_puerto_465'] = 'ÉXITO CON PUERTO 465 - Debes cambiar tu .env a MAIL_PORT=465 y agregar MAIL_SCHEME=smtps';
        } catch (\Exception $e2) {
            $diagnostico['intento_puerto_465'] = 'TAMBIÉN FALLÓ: ' . $e2->getMessage();
        }
    }

    return response()->json($diagnostico, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
})->middleware('auth')->name('test.email.diagnostico');
