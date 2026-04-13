<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Capacitacion;
use App\Models\CapacitacionAsistencia;
use App\Models\CapacitacionAsistenciaRegistro;
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
}
