<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaHorarioController;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Config\EncuestaController;

Route::get('/', function () {
    // Obtener registros de la tabla 'publicidad' que estén activos y dentro del rango de fechas
    $publicidad = [];
    try {
        $now = now();
        $publicidad = \App\Models\Publicidad::where('activo', 1)
            ->where(function($query) use ($now) {
                $query->where(function($q) use ($now) {
                    // Si tiene fecha_inicio, debe ser menor o igual a ahora
                    $q->whereNull('fecha_inicio')
                      ->orWhere('fecha_inicio', '<=', $now);
                })
                ->where(function($q) use ($now) {
                    // Si tiene fecha_fin, debe ser mayor a ahora
                    $q->whereNull('fecha_fin')
                      ->orWhere('fecha_fin', '>', $now);
                });
            })
            ->orderBy('prioridad', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    } catch (\Throwable $e) {
        // Si la tabla no existe o hay un error, pasar colección vacía
        $publicidad = collect();
    }
    return view('dashboard', compact('publicidad'));
})->name('dashboard');

// Bienestar - GYM routes
use App\Models\Servicio;
use App\Models\Vinculacion;

Route::get('/bienestar/gym/inscripcion', function () {
    $servicios = Servicio::orderBy('nombre')->get();
    $vinculaciones = Vinculacion::orderBy('nombre')->get();
    return view('bienestar.gym.inscripcion', compact('servicios','vinculaciones'));
});
// Bienestar - Listados (placeholder view)
use App\Http\Controllers\BienestarListadoController;
Route::get('/bienestar/listados', [BienestarListadoController::class, 'index'])->name('bienestar.listados');
Route::get('/bienestar/observaciones/{identificacion}', [BienestarListadoController::class, 'getObservaciones'])->name('bienestar.observaciones.get');
Route::post('/bienestar/observaciones', [BienestarListadoController::class, 'storeObservacion'])->name('bienestar.observaciones.store');

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
use App\Http\Controllers\Api\InscritoController;
Route::get('/api/inscritos', [InscritoController::class, 'getInscritos'])->name('api.inscritos.lista');
Route::post('/api/inscritos/{id}/autorizar', [InscritoController::class, 'toggleAutorizacion'])->name('api.inscritos.autorizar');
Route::put('/api/inscritos/{id}', [InscritoController::class, 'updateInscrito'])->name('api.inscritos.update');
Route::delete('/api/inscritos/{id}', [InscritoController::class, 'deleteInscrito'])->name('api.inscritos.delete');

// Load authentication routes (login, register, password reset)
require __DIR__.'/auth.php';

// Configuración - rutas placeholder para gestión
Route::get('/configuracion/usuarios', function () { return view('config.usuarios'); });
Route::get('/configuracion/servicios', function () { return view('config.servicios'); });
Route::get('/configuracion/vinculaciones', function () { return view('config.vinculaciones'); });
Route::get('/configuracion/cursos', function () { return view('config.cursos'); });
// Route handled by resource controller for eventos and encuestas

// Gestión de Usuarios (CRUD básico)
use App\Http\Controllers\Config\UserController;
use App\Http\Controllers\Config\ServicioController;
use App\Http\Controllers\Config\VinculacionController;
Route::prefix('configuracion')->name('config.')->group(function () {
    Route::resource('usuarios', UserController::class)->except(['show']);
    Route::resource('servicios', ServicioController::class)->except(['show']);
    Route::resource('vinculaciones', VinculacionController::class)->except(['show']);
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
    Route::resource('publicidad', App\Http\Controllers\Config\PublicidadController::class);
});

// Rutas públicas para responder encuestas y eventos
Route::get('/encuestas/responder/{id}', [EncuestaController::class, 'mostrarEncuesta'])->name('encuestas.mostrar');
Route::post('/encuestas/guardar', [EncuestaController::class, 'guardarRespuesta'])->name('encuestas.guardar');

Route::get('/eventos/inscripcion/{id}', [App\Http\Controllers\Config\EventoController::class, 'inscripcion'])->name('eventos.inscripcion');
Route::get('/eventos/api/usuario/{identificacion}', [App\Http\Controllers\Config\EventoController::class, 'consultarUsuario'])->name('eventos.api.usuario');
Route::post('/eventos/inscripcion/{id}', [App\Http\Controllers\Config\EventoController::class, 'guardarInscripcion'])->name('eventos.inscripcion.guardar');
Route::post('/eventos/inscripciones/eliminar', [App\Http\Controllers\Config\EventoController::class, 'eliminarInscripciones'])->name('eventos.inscripciones.eliminar')->middleware('auth');
Route::post('/eventos/inscripciones/actualizar', [App\Http\Controllers\Config\EventoController::class, 'actualizarInscripcion'])->name('eventos.inscripciones.actualizar')->middleware('auth');
