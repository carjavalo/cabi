<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HorarioGim;
use App\Models\Observacion;
use App\Models\AsistenciaGym;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BienestarListadoController extends Controller
{
    private function verificarRolAsistencia()
    {
        $rolesPermitidos = ['Super Admin', 'Administrador', 'Operador', 'Instructor GYM'];
        if (!Auth::check() || !in_array(Auth::user()->role, $rolesPermitidos)) {
            return false;
        }
        return true;
    }

    public function index(Request $request)
    {
        // Permitir acceso únicamente a roles específicos
        if (!$this->verificarRolAsistencia()) {
            return redirect('/')->with('error', 'Acceso denegado. No tiene permisos para ver los Listados de Bienestar.');
        }

        $query = DB::table('evento_inscripciones')
            ->leftJoin('evento_franjas', 'evento_inscripciones.evento_franja_id', '=', 'evento_franjas.id')
            ->leftJoin('eventos', 'evento_inscripciones.evento_id', '=', 'eventos.id')
            ->leftJoin('inscripgym', function($join) {
                $join->on(DB::raw('evento_inscripciones.identificacion COLLATE utf8mb4_unicode_ci'), '=', DB::raw('inscripgym.identificacion COLLATE utf8mb4_unicode_ci'));
            })
            ->select(
                'evento_inscripciones.id',
                'evento_inscripciones.identificacion',
                'evento_inscripciones.nombre_completo as nombre_completo',
                'evento_franjas.dia_semana',
                'evento_franjas.hora_inicio',
                'evento_franjas.hora_fin',
                'evento_inscripciones.fecha_reserva as fecha_inscripcion',
                'inscripgym.contacto_emergencia'
            )
            ->orderBy('evento_inscripciones.id', 'desc');

        // Extract allowed dates/days/slots for filters
        $diasUnicos = DB::table('evento_franjas')->select('dia_semana')->whereNotNull('dia_semana')->distinct()->pluck('dia_semana');
        $franjasUnicasMap = DB::table('evento_franjas')->select('hora_inicio', 'hora_fin')->distinct()->get();
        $franjasUnicas = [];
        foreach($franjasUnicasMap as $f) {
            $franjaStr = substr($f->hora_inicio, 0, 5) . ' a ' . substr($f->hora_fin, 0, 5);
            if (!in_array($franjaStr, $franjasUnicas)) {
                $franjasUnicas[] = $franjaStr;
            }
        }

        // Apply filters
        if ($request->filled('identificacion')) {
            $query->where('evento_inscripciones.identificacion', 'like', '%' . $request->identificacion . '%');
        }
        if ($request->filled('dia')) {
            $query->where('evento_franjas.dia_semana', $request->dia);
        }
        if ($request->filled('franja')) {
            // Split franja back
            $parts = explode(' a ', $request->franja);
            if (count($parts) == 2) {
                $query->where('evento_franjas.hora_inicio', 'like', $parts[0] . '%')
                      ->where('evento_franjas.hora_fin', 'like', $parts[1] . '%');
            }
        }

        $listados = $query->paginate(15)->appends($request->all());

        return view('bienestar.listados', compact('listados', 'diasUnicos', 'franjasUnicas'));
    }

    public function getObservaciones($identificacion)
    {
        $observaciones = Observacion::where('identificacion', $identificacion)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($observaciones);
    }

    public function storeObservacion(Request $request)
    {
        $request->validate([
            'identificacion' => 'required|string',
            'observacion' => 'required|string'
        ]);

        $obs = Observacion::create([
            'identificacion' => $request->identificacion,
            'observacion' => $request->observacion,
            'creado_por' => Auth::user()->name ?? 'Sistema'
        ]);

        return response()->json(['success' => true, 'data' => $obs]);
    }

    public function cargarAsistencia(Request $request)
    {
        if (!$this->verificarRolAsistencia()) {
            return response()->json(['error' => 'No tiene permisos para esta acción.'], 403);
        }

        $fecha = $request->get('fecha'); // e.g., '2026-03-24'
        $franja = $request->get('franja'); // e.g., '17:00 a 18:00'
        
        if (!$fecha || !$franja) {
            return response()->json(['error' => 'Falta fecha o franja'], 400);
        }

        // Obtener el día de la semana (1 = Lunes, ..., 7 = Domingo)
        $diaSemana = date('N', strtotime($fecha));

        $parts = explode(' a ', $franja);
        if (count($parts) != 2) {
            return response()->json(['error' => 'Formato de franja inválido'], 400);
        }

        // Filtrar inscritos por ese día y franja
        $users = DB::table('evento_inscripciones')
            ->leftJoin('evento_franjas', 'evento_inscripciones.evento_franja_id', '=', 'evento_franjas.id')
            ->where('evento_franjas.dia_semana', $diaSemana)
            ->where('evento_franjas.hora_inicio', 'like', $parts[0] . '%')
            ->where('evento_franjas.hora_fin', 'like', $parts[1] . '%')
            ->select('evento_inscripciones.identificacion', 'evento_inscripciones.nombre_completo')
            ->distinct()
            ->get();

        // Obtener la asistencia actual para este fecha y franja
        $asistencias = AsistenciaGym::where('fecha', $fecha)
                                    ->where('franja', $franja)
                                    ->pluck('asistio', 'identificacion')->toArray();

        // Combinar datos
        $datos = clone $users;
        $datos = $datos->map(function ($item) use ($asistencias) {
            $item->asistio = isset($asistencias[$item->identificacion]) ? $asistencias[$item->identificacion] : false;
            return $item;
        });

        return response()->json(['success' => true, 'data' => $datos]);
    }

    public function guardarAsistencia(Request $request)
    {
        if (!$this->verificarRolAsistencia()) {
            return response()->json(['success' => false, 'message' => 'No tiene permisos para esta acción.'], 403);
        }

        $fecha = $request->post('fecha');
        $franja = $request->post('franja');
        $asistencias = $request->post('asistencias', []); // formato: [identificacion => true/false]

        if (!$fecha || !$franja) {
            return response()->json(['success' => false, 'message' => 'Faltan parámetros de fecha o franja.']);
        }

        foreach ($asistencias as $identificacion => $asistio) {
            AsistenciaGym::updateOrCreate(
                ['identificacion' => $identificacion, 'fecha' => $fecha, 'franja' => $franja],
                ['asistio' => $asistio]
            );
        }

        return response()->json(['success' => true, 'message' => 'Asistencia guardada correctamente.']);
    }

    public function consultarAsistencia(Request $request)
    {
        if (!$this->verificarRolAsistencia()) {
            return response()->json(['error' => 'No tiene permisos para esta acción.'], 403);
        }

        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        $franja = $request->get('franja');
        $identificacion = $request->get('identificacion');

        if (!$fechaDesde || !$fechaHasta) {
            return response()->json(['error' => 'Debe indicar rango de fechas.'], 400);
        }

        $query = AsistenciaGym::whereBetween('fecha', [$fechaDesde, $fechaHasta]);

        if ($franja) {
            $query->where('franja', $franja);
        }
        if ($identificacion) {
            $query->where('identificacion', 'like', '%' . $identificacion . '%');
        }

        $registros = $query->orderBy('fecha', 'desc')->orderBy('franja')->get();

        // Enriquecer con nombre completo
        $identificaciones = $registros->pluck('identificacion')->unique()->toArray();
        $nombres = DB::table('evento_inscripciones')
            ->whereIn('identificacion', $identificaciones)
            ->select('identificacion', 'nombre_completo')
            ->distinct()
            ->pluck('nombre_completo', 'identificacion')
            ->toArray();

        $data = $registros->map(function ($r) use ($nombres) {
            return [
                'identificacion' => $r->identificacion,
                'nombre_completo' => $nombres[$r->identificacion] ?? 'N/A',
                'fecha' => $r->fecha,
                'franja' => $r->franja,
                'asistio' => $r->asistio,
            ];
        });

        // Resumen estadístico
        $total = $data->count();
        $asistieron = $data->where('asistio', true)->count();
        $noAsistieron = $total - $asistieron;

        return response()->json([
            'success' => true,
            'data' => $data->values(),
            'resumen' => [
                'total' => $total,
                'asistieron' => $asistieron,
                'no_asistieron' => $noAsistieron,
                'porcentaje' => $total > 0 ? round(($asistieron / $total) * 100, 1) : 0
            ]
        ]);
    }
}
