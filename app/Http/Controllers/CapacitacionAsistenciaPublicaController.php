<?php

namespace App\Http\Controllers;

use App\Models\Capacitacion;
use App\Models\CapacitacionAsistencia;
use App\Models\CapacitacionAsistenciaRegistro;
use App\Models\CapacitacionSesion;
use App\Models\User;
use Illuminate\Http\Request;

class CapacitacionAsistenciaPublicaController extends Controller
{
    /**
     * Mostrar formulario público para marcar asistencia vía token de sesión.
     */
    public function mostrar(string $token)
    {
        $sesion = CapacitacionSesion::where('token', $token)->firstOrFail();
        $capacitacion = $sesion->capacitacion;

        // Verificar que sea la sesión más reciente (las anteriores están cerradas)
        $ultimaSesion = $capacitacion->sesiones()->latest()->first();
        $esUltimaSesion = $ultimaSesion && $ultimaSesion->id === $sesion->id;

        $vinculaciones = \App\Models\Vinculacion::orderBy('nombre')->pluck('nombre');

        return view('capacitaciones.asistencia_publica', compact('sesion', 'capacitacion', 'esUltimaSesion', 'vinculaciones'));
    }

    /**
     * Registrar asistencia de un usuario.
     */
    public function marcar(Request $request, string $token)
    {
        $sesion = CapacitacionSesion::where('token', $token)->firstOrFail();
        $capacitacion = $sesion->capacitacion;

        // Verificar que sea la sesión más reciente
        $ultimaSesion = $capacitacion->sesiones()->latest()->first();
        if (!$ultimaSesion || $ultimaSesion->id !== $sesion->id) {
            return back()->with('error', 'Este enlace ya no está vigente. Se ha generado una nueva sesión.');
        }

        // Verificar ventana de tiempo
        if (!$sesion->estaAbierta()) {
            return back()->with('error', 'El registro de asistencia no está disponible en este momento. Solo se permite durante la fecha y horario de la capacitación.');
        }

        $request->validate([
            'nombre' => 'required|string|max:200',
            'identificacion' => 'required|string|max:50',
            'tipo_contrato' => 'nullable|string|max:100',
            'correo' => 'nullable|email|max:200',
            'autoriza_firma' => 'required|accepted',
        ], [
            'autoriza_firma.required' => 'Debe autorizar con su firma para registrar asistencia.',
            'autoriza_firma.accepted' => 'Debe autorizar con su firma para registrar asistencia.',
        ]);

        // Verificar si ya registró asistencia en esta sesión
        $yaRegistro = CapacitacionAsistenciaRegistro::where('sesion_id', $sesion->id)
            ->where('identificacion', $request->identificacion)
            ->exists();

        if ($yaRegistro) {
            return back()->with('info', 'Ya registró su asistencia en esta sesión.')->withInput();
        }

        // Determinar si es citado
        $user = User::where('identificacion', $request->identificacion)->first();
        $citadosIds = $sesion->citados_ids ?? [];
        $esCitado = $user && in_array($user->id, $citadosIds);

        CapacitacionAsistenciaRegistro::create([
            'sesion_id' => $sesion->id,
            'capacitacion_id' => $capacitacion->id,
            'user_id' => $user?->id,
            'nombre' => $request->nombre,
            'identificacion' => $request->identificacion,
            'tipo_contrato' => $request->tipo_contrato,
            'correo' => $request->correo,
            'autoriza_firma' => true,
            'es_citado' => $esCitado,
        ]);

        // Marcar asistencia en la tabla de citados también (si aplica)
        if ($esCitado && $user) {
            CapacitacionAsistencia::where('capacitacion_id', $capacitacion->id)
                ->where('user_id', $user->id)
                ->update(['asistio' => true]);
        }

        return back()->with('success', '¡Asistencia registrada exitosamente para ' . $request->nombre . '!');
    }

    /**
     * API: Buscar datos de usuario por identificación (para autocompletar).
     */
    public function buscarUsuario(string $identificacion)
    {
        $user = User::where('identificacion', $identificacion)->first();

        if (!$user) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'nombre' => trim($user->name . ' ' . ($user->apellido1 ?? '') . ' ' . ($user->apellido2 ?? '')),
            'correo' => $user->email,
            'tipo_contrato' => $user->tipo_vinculacion ?? '',
        ]);
    }
}
