<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\EventoDia;
use App\Models\EventoFranja;
use App\Models\EventoInscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventoController extends Controller
{
    public function index()
    {
        return view('config.eventos.index');
    }

    public function data()
    {
        $eventos = Evento::orderBy('fecha_inicio', 'desc')->get();
        return response()->json($eventos);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'nullable|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'capacidad_maxima' => 'required|integer|min:1',
            'max_inscripciones_dia' => 'nullable|integer|min:0',
            'max_inscripciones_semana' => 'nullable|integer|min:0',
            'imagen' => 'nullable|image|max:5120',
            'dias_activos' => 'nullable|string',
            'franjas_horarias' => 'nullable|string',
        ]);

        // Decodificar JSON de días activos y franjas horarias
        if (isset($data['dias_activos'])) {
            $data['dias_activos'] = json_decode($data['dias_activos'], true);
        }
        if (isset($data['franjas_horarias'])) {
            $data['franjas_horarias'] = json_decode($data['franjas_horarias'], true);
        }

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $folder = public_path('img/eventos');
            if (!is_dir($folder)) mkdir($folder, 0755, true);
            $name = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\\.]/', '', $file->getClientOriginalName());
            $file->move($folder, $name);
            $data['imagen'] = url('img/eventos/'.$name);
        }

        $data['created_by'] = auth()->id() ?? null;
        $data['activo'] = 1;
        $data['inscritos'] = 0;

        $evento = Evento::create($data);

        // Guardar dias en tabla relacionada
        if (isset($data['dias_activos']) && is_array($data['dias_activos'])) {
            foreach ($data['dias_activos'] as $dia) {
                EventoDia::create([
                    'evento_id' => $evento->id,
                    'dia_semana' => $dia
                ]);
            }
        }

        // Guardar franjas en tabla relacionada (con dia_semana)
        if (isset($data['franjas_horarias']) && is_array($data['franjas_horarias'])) {
            foreach ($data['franjas_horarias'] as $franja) {
                EventoFranja::create([
                    'evento_id' => $evento->id,
                    'dia_semana' => $franja['dia_semana'] ?? null,
                    'hora_inicio' => $franja['hora_inicio'],
                    'hora_fin' => $franja['hora_fin'],
                    'capacidad_maxima' => $franja['capacidad_maxima'] ?? 0
                ]);
            }
        }

        return response()->json(['success' => true, 'evento' => $evento]);
    }

    public function show($id)
    {
        $evento = Evento::with(['eventoDias', 'eventoFranjas.inscripciones', 'eventoInscripciones.franja'])->findOrFail($id);
        return response()->json($evento);
    }

    public function update(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);
        
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'nullable|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'capacidad_maxima' => 'required|integer|min:1',
            'max_inscripciones_dia' => 'nullable|integer|min:0',
            'max_inscripciones_semana' => 'nullable|integer|min:0',
            'imagen' => 'nullable|image|max:5120',
            'dias_activos' => 'nullable|string',
            'franjas_horarias' => 'nullable|string',
        ]);

        // Decodificar JSON de días activos y franjas horarias
        if (isset($data['dias_activos'])) {
            $data['dias_activos'] = json_decode($data['dias_activos'], true);
        }
        if (isset($data['franjas_horarias'])) {
            $data['franjas_horarias'] = json_decode($data['franjas_horarias'], true);
        }

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $folder = public_path('img/eventos');
            if (!is_dir($folder)) mkdir($folder, 0755, true);
            $name = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\\.]/', '', $file->getClientOriginalName());
            $file->move($folder, $name);
            $data['imagen'] = url('img/eventos/'.$name);
        }

        $evento->update($data);

        // Actualizar dias relacionados
        $evento->eventoDias()->delete();
        if (isset($data['dias_activos']) && is_array($data['dias_activos'])) {
            foreach ($data['dias_activos'] as $dia) {
                EventoDia::create([
                    'evento_id' => $evento->id,
                    'dia_semana' => $dia
                ]);
            }
        }

        // Actualizar franjas relacionadas (con dia_semana)
        $evento->eventoFranjas()->delete();
        if (isset($data['franjas_horarias']) && is_array($data['franjas_horarias'])) {
            foreach ($data['franjas_horarias'] as $franja) {
                EventoFranja::create([
                    'evento_id' => $evento->id,
                    'dia_semana' => $franja['dia_semana'] ?? null,
                    'hora_inicio' => $franja['hora_inicio'],
                    'hora_fin' => $franja['hora_fin'],
                    'capacidad_maxima' => $franja['capacidad_maxima'] ?? 0
                ]);
            }
        }

        return response()->json(['success' => true, 'evento' => $evento]);
    }

    public function destroy($id)
    {
        if (in_array(auth()->user()->role, ['Operador', 'Administrador'])) {
            return response()->json(['success' => false, 'message' => 'No autorizado para eliminar.'], 403);
        }
        $evento = Evento::findOrFail($id);
        $evento->delete();
        return response()->json(['success' => true]);
    }

    public function inscripcion($id)
    {
        $evento = Evento::with(['eventoDias', 'eventoFranjas.inscripciones'])->findOrFail($id);
        $servicios = \App\Models\Servicio::orderBy('nombre')->get();

        // Mapear nombre de días
        $diasMap = [0 => 'DOMINGO', 1 => 'LUNES', 2 => 'MARTES', 3 => 'MIÉRCOLES', 4 => 'JUEVES', 5 => 'VIERNES', 6 => 'SÁBADO'];

        return view('config.eventos.inscripcion_publica', compact('evento', 'servicios', 'diasMap'));
    }

    public function guardarInscripcion(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);
        
        $request->validate([
            'nombres' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'identificacion' => 'required|string|max:255',
            'servicio' => 'nullable|string|max:255',
            'dia_semana' => 'required|integer',
            'evento_franja_id' => 'required|exists:evento_franjas,id'
        ]);

        // Construir nombre completo
        $nombreCompleto = trim($request->nombres . ' ' . $request->primer_apellido . ' ' . ($request->segundo_apellido ?? ''));

        // Calcular la próxima fecha que corresponda al día seleccionado
        $diaSemana = (int) $request->dia_semana;
        $hoy = now();
        $fechaReserva = $hoy->copy();
        while ($fechaReserva->dayOfWeek !== $diaSemana) {
            $fechaReserva->addDay();
        }

        // Verificar capacidad
        $franja = EventoFranja::find($request->evento_franja_id);
        $inscritosFranja = EventoInscripcion::where('evento_id', $id)
            ->where('evento_franja_id', $request->evento_franja_id)
            ->count();
            
        if ($franja && $franja->capacidad_maxima > 0 && $inscritosFranja >= $franja->capacidad_maxima) {
            return back()->with('error', 'El cupo para esta franja ya está lleno.')->withInput();
        }

        // Verificar límite diario por usuario
        $maxDia = (int) $evento->max_inscripciones_dia;
        if ($maxDia > 0) {
            // Contar inscripciones del usuario en exactamente la misma fecha de reserva
            $inscripcionesDia = EventoInscripcion::where('evento_id', $id)
                ->where('identificacion', $request->identificacion)
                ->whereDate('fecha_reserva', $fechaReserva->toDateString())
                ->count();

            if ($inscripcionesDia >= $maxDia) {
                $msg = $maxDia === 1
                    ? 'Ya te encuentras inscrito en esta fecha. Solo se permite una inscripción por día.'
                    : "Ya alcanzaste el máximo de {$maxDia} inscripciones permitidas para este día.";
                return back()->with('error', $msg)->withInput();
            }
        }

        // Verificar límite semanal por usuario
        $maxSemana = (int) $evento->max_inscripciones_semana;
        if ($maxSemana > 0) {
            // Contar inscripciones en la misma semana calendario de la fecha de reserva
            $inicioSemana = $fechaReserva->copy()->startOfWeek();
            $finSemana = $fechaReserva->copy()->endOfWeek();

            $inscripcionesSemana = EventoInscripcion::where('evento_id', $id)
                ->where('identificacion', $request->identificacion)
                ->whereBetween('fecha_reserva', [$inicioSemana->toDateString(), $finSemana->toDateString()])
                ->count();

            if ($inscripcionesSemana >= $maxSemana) {
                $msg = $maxSemana === 1
                    ? 'Ya te encuentras inscrito esta semana. Solo se permite una inscripción por semana.'
                    : "Ya alcanzaste el máximo de {$maxSemana} inscripciones permitidas por semana.";
                return back()->with('error', $msg)->withInput();
            }
        }

        // Verificar inscripción duplicada en la misma franja exacta
        $yaInscritoFranja = EventoInscripcion::where('evento_id', $id)
            ->where('identificacion', $request->identificacion)
            ->where('evento_franja_id', $request->evento_franja_id)
            ->whereDate('fecha_reserva', $fechaReserva->toDateString())
            ->exists();

        if ($yaInscritoFranja) {
            return back()->with('error', 'Ya te encuentras inscrito en esta franja horaria.')->withInput();
        }

        EventoInscripcion::create([
            'evento_id' => $id,
            'evento_franja_id' => $request->evento_franja_id,
            'user_id' => auth()->id() ?? null,
            'nombre_completo' => $nombreCompleto,
            'identificacion' => $request->identificacion,
            'fecha_reserva' => $fechaReserva->toDateString(),
            'asistencia' => 0
        ]);

        // Incrementar inscritos totales del evento
        $evento->increment('inscritos');

        return redirect()->route('eventos.inscripcion', $id)->with('success', '¡Inscripción realizada con éxito!');
    }

    /**
     * Eliminar inscripciones seleccionadas o todas las de un evento.
     */
    public function eliminarInscripciones(Request $request)
    {
        $request->validate([
            'evento_id' => 'required|integer|exists:eventos,id',
            'ids' => 'nullable|array',
            'ids.*' => 'integer|exists:evento_inscripciones,id',
            'all' => 'nullable|boolean',
        ]);

        $eventoId = $request->evento_id;

        if ($request->boolean('all')) {
            // Eliminar todas las inscripciones del evento
            $count = EventoInscripcion::where('evento_id', $eventoId)->count();
            EventoInscripcion::where('evento_id', $eventoId)->delete();
        } else {
            $ids = $request->input('ids', []);
            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'No se seleccionaron inscripciones.'], 422);
            }
            $count = EventoInscripcion::where('evento_id', $eventoId)
                ->whereIn('id', $ids)
                ->count();
            EventoInscripcion::where('evento_id', $eventoId)
                ->whereIn('id', $ids)
                ->delete();
        }

        // Actualizar contador de inscritos del evento
        $evento = Evento::find($eventoId);
        if ($evento) {
            $evento->inscritos = EventoInscripcion::where('evento_id', $eventoId)->count();
            $evento->save();
        }

        return response()->json(['success' => true, 'deleted' => $count, 'message' => "Se eliminaron {$count} inscripción(es)."]);
    }

    public function consultarUsuario($identificacion)
    {
        // Consultar en la tabla inscripgym (tiene nombres, apellidos, servicio_unidad)
        $usuario = \Illuminate\Support\Facades\DB::table('inscripgym')
            ->where('identificacion', $identificacion)
            ->first();

        if ($usuario) {
            if (!$usuario->autorizado) {
                 return response()->json([
                    'found' => false,
                    'error_msg' => 'El usuario se encuentra registrado, pero aún no tiene la autorización de agendamiento.'
                 ]);
            }
            return response()->json([
                'found' => true,
                'nombres' => $usuario->nombres ?? '',
                'primer_apellido' => $usuario->primer_apellido ?? '',
                'segundo_apellido' => $usuario->segundo_apellido ?? '',
                'servicio_unidad' => $usuario->servicio_unidad ?? ''
            ]);
        }

        return response()->json(['found' => false]);
    }
}
