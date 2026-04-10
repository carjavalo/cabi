<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\Capacitacion;
use App\Models\CapacitacionAsistencia;
use App\Models\CapacitacionSesion;
use App\Models\CapacitacionAsistenciaRegistro;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CapacitacionController extends Controller
{
    public function index()
    {
        $capacitaciones = Capacitacion::with('ultimaSesion')
            ->withCount(['asistencias', 'asistencias as asistieron_count' => function ($q) {
                $q->where('asistio', true);
            }])->orderBy('fecha', 'desc')->paginate(15);

        $activas = Capacitacion::where('activo', true)->where('fecha', '>=', now()->toDateString())->count();
        $totalAsistieron = CapacitacionAsistenciaRegistro::count();

        return view('config.capacitaciones.index', compact('capacitaciones', 'activas', 'totalAsistieron'));
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

        $usuariosIds = $request->usuarios ?? [];

        if (!empty($usuariosIds)) {
            foreach ($usuariosIds as $userId) {
                CapacitacionAsistencia::create([
                    'capacitacion_id' => $capacitacion->id,
                    'user_id' => $userId,
                    'asistio' => false,
                ]);
            }
        }

        // Crear primera sesión
        CapacitacionSesion::create([
            'capacitacion_id' => $capacitacion->id,
            'token' => Str::random(32),
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'citados_ids' => $usuariosIds,
        ]);

        return redirect()->route('config.capacitaciones.index')
                         ->with('success', 'Capacitación creada exitosamente.');
    }

    public function show(Capacitacion $capacitacione)
    {
        $capacitacion = $capacitacione;
        $capacitacion->load(['asistencias.user']);
        $usuarios = User::where('role', 'Usuario')->orderBy('name')->get();
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

        // Detectar si cambió fecha/hora o citados para crear nueva sesión
        $fechaCambio = $capacitacion->fecha->format('Y-m-d') !== $request->fecha
            || $capacitacion->hora_inicio !== $request->hora_inicio
            || $capacitacion->hora_fin !== $request->hora_fin;

        $nuevosIds = $request->usuarios ?? [];
        $existentesIds = $capacitacion->asistencias()->pluck('user_id')->toArray();
        sort($nuevosIds);
        sort($existentesIds);
        $citadosCambio = $nuevosIds !== $existentesIds;

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

        // Si cambiaron fechas/hora o citados, crear nueva sesión con nuevo token
        if ($fechaCambio || $citadosCambio) {
            CapacitacionSesion::create([
                'capacitacion_id' => $capacitacion->id,
                'token' => Str::random(32),
                'fecha' => $request->fecha,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'citados_ids' => $nuevosIds,
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

    // ─── API: Obtener datos de informes por sesiones ───
    public function informes(Capacitacion $capacitacione)
    {
        $capacitacion = $capacitacione;
        $sesiones = $capacitacion->sesiones()->with('registros')->orderBy('created_at', 'desc')->get();

        $data = [];
        foreach ($sesiones as $sesion) {
            $citadosIds = $sesion->citados_ids ?? [];
            $registros = $sesion->registros;

            // Citados que asistieron
            $citadosAsistieron = $registros->where('es_citado', true);
            // No citados que asistieron
            $noCitadosAsistieron = $registros->where('es_citado', false);
            // Citados que NO asistieron
            $idsAsistieron = $registros->where('es_citado', true)->pluck('user_id')->filter()->toArray();
            $citadosNoAsistieron = [];
            foreach ($citadosIds as $uid) {
                if (!in_array($uid, $idsAsistieron)) {
                    $user = User::find($uid);
                    if ($user) {
                        $citadosNoAsistieron[] = [
                            'nombre' => $user->name . ' ' . ($user->apellido1 ?? '') . ' ' . ($user->apellido2 ?? ''),
                            'identificacion' => $user->identificacion ?? '—',
                        ];
                    }
                }
            }

            $data[] = [
                'id' => $sesion->id,
                'fecha' => $sesion->fecha->format('d/m/Y'),
                'hora_inicio' => $sesion->hora_inicio ? \Carbon\Carbon::parse($sesion->hora_inicio)->format('H:i') : '—',
                'hora_fin' => $sesion->hora_fin ? \Carbon\Carbon::parse($sesion->hora_fin)->format('H:i') : '—',
                'created_at' => $sesion->created_at->format('d/m/Y H:i'),
                'total_citados' => count($citadosIds),
                'citados_asistieron' => $citadosAsistieron->map(fn($r) => [
                    'nombre' => $r->nombre,
                    'identificacion' => $r->identificacion,
                    'tipo_contrato' => $r->tipo_contrato,
                    'correo' => $r->correo,
                    'hora_registro' => $r->created_at->format('d/m/Y H:i'),
                ])->values(),
                'no_citados_asistieron' => $noCitadosAsistieron->map(fn($r) => [
                    'nombre' => $r->nombre,
                    'identificacion' => $r->identificacion,
                    'tipo_contrato' => $r->tipo_contrato,
                    'correo' => $r->correo,
                    'hora_registro' => $r->created_at->format('d/m/Y H:i'),
                ])->values(),
                'citados_no_asistieron' => $citadosNoAsistieron,
            ];
        }

        return response()->json(['ok' => true, 'capacitacion' => $capacitacion->titulo, 'sesiones' => $data]);
    }

    // ─── Exportar Excel (CSV) de una sesión ───
    public function exportarSesion(Capacitacion $capacitacione, CapacitacionSesion $sesion)
    {
        if ($sesion->capacitacion_id !== $capacitacione->id) {
            abort(404);
        }

        $registros = $sesion->registros()->orderBy('created_at')->get();
        $citadosIds = $sesion->citados_ids ?? [];

        // Citados que NO asistieron
        $idsAsistieron = $registros->where('es_citado', true)->pluck('user_id')->filter()->toArray();
        $ausentes = [];
        foreach ($citadosIds as $uid) {
            if (!in_array($uid, $idsAsistieron)) {
                $user = User::find($uid);
                if ($user) {
                    $ausentes[] = $user;
                }
            }
        }

        $filename = 'asistencia_' . Str::slug($capacitacione->titulo) . '_sesion_' . $sesion->fecha->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($registros, $ausentes, $capacitacione, $sesion) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Encabezado del reporte
            fputcsv($file, ['REPORTE DE ASISTENCIA'], ';');
            fputcsv($file, ['Capacitación:', $capacitacione->titulo], ';');
            fputcsv($file, ['Fecha sesión:', $sesion->fecha->format('d/m/Y')], ';');
            fputcsv($file, ['Horario:', ($sesion->hora_inicio ?? '—') . ' - ' . ($sesion->hora_fin ?? '—')], ';');
            fputcsv($file, ['Instructor:', $capacitacione->instructor ?? '—'], ';');
            fputcsv($file, [], ';');

            // Asistentes
            fputcsv($file, ['=== ASISTENTES ==='], ';');
            fputcsv($file, ['Nombre', 'Identificación', 'Tipo Contrato', 'Correo', 'Citado', 'Autoriza Firma', 'Fecha/Hora Registro'], ';');
            foreach ($registros as $r) {
                fputcsv($file, [
                    $r->nombre,
                    $r->identificacion,
                    $r->tipo_contrato ?? '—',
                    $r->correo ?? '—',
                    $r->es_citado ? 'Sí' : 'No',
                    $r->autoriza_firma ? 'Sí' : 'No',
                    $r->created_at->format('d/m/Y H:i:s'),
                ], ';');
            }

            fputcsv($file, [], ';');
            fputcsv($file, ['=== CITADOS AUSENTES ==='], ';');
            fputcsv($file, ['Nombre', 'Identificación'], ';');
            foreach ($ausentes as $u) {
                fputcsv($file, [
                    $u->name . ' ' . ($u->apellido1 ?? '') . ' ' . ($u->apellido2 ?? ''),
                    $u->identificacion ?? '—',
                ], ';');
            }

            fputcsv($file, [], ';');
            fputcsv($file, ['Generado:', now()->format('d/m/Y H:i:s')], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
