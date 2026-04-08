<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\Capacitacion;
use App\Models\CapacitacionAsistencia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CapacitacionController extends Controller
{
    public function index()
    {
        $capacitaciones = Capacitacion::withCount(['asistencias', 'asistencias as asistieron_count' => function ($q) {
            $q->where('asistio', true);
        }])->orderBy('fecha', 'desc')->paginate(15);

        return view('config.capacitaciones.index', compact('capacitaciones'));
    }

    public function create()
    {
        $usuarios = User::where('role', 'Usuario')->orderBy('name')->get();
        return view('config.capacitaciones.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'nullable|string|max:200',
            'instructor' => 'nullable|string|max:200',
            'fecha' => 'required|date',
            'hora_inicio' => 'nullable',
            'hora_fin' => 'nullable',
            'capacidad_maxima' => 'nullable|integer|min:0',
            'usuarios' => 'nullable|array',
            'usuarios.*' => 'exists:users,id',
        ]);

        $capacitacion = Capacitacion::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'ubicacion' => $request->ubicacion,
            'instructor' => $request->instructor,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'capacidad_maxima' => $request->capacidad_maxima ?? 0,
            'activo' => true,
            'created_by' => Auth::id(),
        ]);

        // Asignar usuarios citados
        if ($request->filled('usuarios')) {
            foreach ($request->usuarios as $userId) {
                CapacitacionAsistencia::create([
                    'capacitacion_id' => $capacitacion->id,
                    'user_id' => $userId,
                    'asistio' => false,
                ]);
            }
        }

        return redirect()->route('config.capacitaciones.index')
                         ->with('success', 'Capacitación creada exitosamente.');
    }

    public function show(Capacitacion $capacitacione)
    {
        $capacitacion = $capacitacione;
        $capacitacion->load(['asistencias.user']);
        $usuarios = User::where('role', 'Usuario')->orderBy('name')->get();

        // IDs ya asignados
        $asignadosIds = $capacitacion->asistencias->pluck('user_id')->toArray();

        return view('config.capacitaciones.show', compact('capacitacion', 'usuarios', 'asignadosIds'));
    }

    public function edit(Capacitacion $capacitacione)
    {
        $capacitacion = $capacitacione;
        $capacitacion->load('asistencias');
        $usuarios = User::where('role', 'Usuario')->orderBy('name')->get();
        $asignadosIds = $capacitacion->asistencias->pluck('user_id')->toArray();

        return view('config.capacitaciones.edit', compact('capacitacion', 'usuarios', 'asignadosIds'));
    }

    public function update(Request $request, Capacitacion $capacitacione)
    {
        $capacitacion = $capacitacione;

        $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'nullable|string|max:200',
            'instructor' => 'nullable|string|max:200',
            'fecha' => 'required|date',
            'hora_inicio' => 'nullable',
            'hora_fin' => 'nullable',
            'capacidad_maxima' => 'nullable|integer|min:0',
            'usuarios' => 'nullable|array',
            'usuarios.*' => 'exists:users,id',
        ]);

        $capacitacion->update([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'ubicacion' => $request->ubicacion,
            'instructor' => $request->instructor,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'capacidad_maxima' => $request->capacidad_maxima ?? 0,
        ]);

        // Sincronizar usuarios: mantener asistencias existentes, agregar nuevos, eliminar removidos
        $nuevosIds = $request->usuarios ?? [];
        $existentesIds = $capacitacion->asistencias()->pluck('user_id')->toArray();

        // Eliminar removidos
        $removidos = array_diff($existentesIds, $nuevosIds);
        if (!empty($removidos)) {
            $capacitacion->asistencias()->whereIn('user_id', $removidos)->delete();
        }

        // Agregar nuevos
        $nuevos = array_diff($nuevosIds, $existentesIds);
        foreach ($nuevos as $userId) {
            CapacitacionAsistencia::create([
                'capacitacion_id' => $capacitacion->id,
                'user_id' => $userId,
                'asistio' => false,
            ]);
        }

        return redirect()->route('config.capacitaciones.index')
                         ->with('success', 'Capacitación actualizada exitosamente.');
    }

    public function destroy(Capacitacion $capacitacione)
    {
        $capacitacione->delete();
        return redirect()->route('config.capacitaciones.index')
                         ->with('success', 'Capacitación eliminada.');
    }

    // ─── API: Marcar/desmarcar asistencia ───
    public function toggleAsistencia(Request $request)
    {
        $request->validate([
            'capacitacion_id' => 'required|exists:capacitaciones,id',
            'user_id' => 'required|exists:users,id',
            'asistio' => 'required|boolean',
        ]);

        $asistencia = CapacitacionAsistencia::where('capacitacion_id', $request->capacitacion_id)
            ->where('user_id', $request->user_id)
            ->first();

        if ($asistencia) {
            $asistencia->update(['asistio' => $request->asistio]);
            return response()->json(['ok' => true, 'asistio' => $asistencia->asistio]);
        }

        return response()->json(['ok' => false, 'error' => 'Registro no encontrado'], 404);
    }

    // ─── API: Agregar usuario a capacitación ───
    public function agregarUsuario(Request $request)
    {
        $request->validate([
            'capacitacion_id' => 'required|exists:capacitaciones,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $existe = CapacitacionAsistencia::where('capacitacion_id', $request->capacitacion_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($existe) {
            return response()->json(['ok' => false, 'error' => 'El usuario ya está asignado'], 422);
        }

        CapacitacionAsistencia::create([
            'capacitacion_id' => $request->capacitacion_id,
            'user_id' => $request->user_id,
            'asistio' => false,
        ]);

        return response()->json(['ok' => true]);
    }

    // ─── API: Remover usuario de capacitación ───
    public function removerUsuario(Request $request)
    {
        $request->validate([
            'capacitacion_id' => 'required|exists:capacitaciones,id',
            'user_id' => 'required|exists:users,id',
        ]);

        CapacitacionAsistencia::where('capacitacion_id', $request->capacitacion_id)
            ->where('user_id', $request->user_id)
            ->delete();

        return response()->json(['ok' => true]);
    }
}
