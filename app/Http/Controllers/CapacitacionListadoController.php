<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Capacitacion;
use App\Models\CapacitacionAsistencia;
use App\Models\CapacitacionAsistenciaRegistro;
use App\Models\User;
use App\Models\Servicio;
use Illuminate\Support\Facades\Schema;

class CapacitacionListadoController extends Controller
{
    public function index()
    {
        $hasSesiones = Schema::hasTable('capacitacion_sesiones');

        $query = Capacitacion::query();
        if ($hasSesiones) {
            $query->with('ultimaSesion');
        }
        $capacitaciones = $query->withCount(['asistencias', 'asistencias as asistieron_count' => function ($q) {
                $q->where('asistio', true);
            }])->orderBy('fecha', 'desc')->paginate(15);

        $activas = Capacitacion::where('activo', true)->where('fecha', '>=', now()->toDateString())->count();
        
        $capIds = Capacitacion::pluck('id');
        $totalAsistieron = ($hasSesiones && Schema::hasTable('capacitacion_asistencia_registros'))
            ? CapacitacionAsistenciaRegistro::whereIn('capacitacion_id', $capIds)->count()
            : CapacitacionAsistencia::whereIn('capacitacion_id', $capIds)->where('asistio', true)->count();

        return view('capacitaciones.index_user', compact('capacitaciones', 'activas', 'totalAsistieron', 'hasSesiones'));
    }

    public function edit(Capacitacion $capacitacion)
    {
        $capacitacion->load('asistencias');
        $usuarios = User::where('role', 'Usuario')->orderBy('name')->get();
        $asignadosIds = $capacitacion->asistencias->pluck('user_id')->toArray();

        // Obtener lista de servicios únicos para el filtro
        $servicios = User::where('role', 'Usuario')
            ->whereNotNull('servicio')
            ->where('servicio', '!=', '')
            ->distinct()
            ->orderBy('servicio')
            ->pluck('servicio');

        return view('capacitaciones.edit_user', compact('capacitacion', 'usuarios', 'asignadosIds', 'servicios'));
    }

    public function update(Request $request, Capacitacion $capacitacion)
    {
        $request->validate([
            'usuarios' => 'nullable|array',
            'usuarios.*' => 'exists:users,id',
        ]);

        $nuevosIds = $request->usuarios ?? [];
        $existentesIds = $capacitacion->asistencias()->pluck('user_id')->toArray();

        // Sincronizar citados
        $removidos = array_diff($existentesIds, $nuevosIds);
        if (!empty($removidos)) {
            $capacitacion->asistencias()->whereIn('user_id', $removidos)->delete();
        }
        $nuevos = array_diff($nuevosIds, $existentesIds);
        foreach ($nuevos as $userId) {
            CapacitacionAsistencia::create([
                'capacitacion_id' => $capacitacion->id,
                'user_id' => $userId,
                'asistio' => false,
            ]);
        }

        // Crear nueva sesión si cambiaron los citados
        if (Schema::hasTable('capacitacion_sesiones') && !empty(array_merge($removidos, $nuevos))) {
            \App\Models\CapacitacionSesion::create([
                'capacitacion_id' => $capacitacion->id,
                'token' => \Illuminate\Support\Str::random(32),
                'fecha' => $capacitacion->fecha,
                'hora_inicio' => $capacitacion->hora_inicio,
                'hora_fin' => $capacitacion->hora_fin,
                'citados_ids' => $nuevosIds,
            ]);
        }

        return redirect()->route('capacitaciones.index_user')
                         ->with('success', 'Asistentes actualizados exitosamente.');
    }
}
