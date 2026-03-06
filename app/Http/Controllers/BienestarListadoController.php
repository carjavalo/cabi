<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HorarioGim;
use App\Models\Observacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BienestarListadoController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['Super Admin', 'Administrador', 'Operador', 'Instructor GYM'])) {
            return redirect('/')->with('error', 'No tiene permisos para acceder a esta sección.');
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
        if (!Auth::check() || !in_array(Auth::user()->role, ['Super Admin', 'Administrador', 'Operador', 'Instructor GYM'])) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $observaciones = Observacion::where('identificacion', $identificacion)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($observaciones);
    }

    public function storeObservacion(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['Super Admin', 'Administrador', 'Operador', 'Instructor GYM'])) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

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
}
