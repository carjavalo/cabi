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

        return view('capacitaciones.asistencia_publica', compact('sesion', 'capacitacion', 'esUltimaSesion'));
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
            'apellidos' => 'required|string|max:200',
            'identificacion' => 'required|string|max:50',
            'area_servicio' => 'required|string|max:200',
            'cargo' => 'required|string|max:200',
            'tipo_contrato' => 'required|string|max:100',
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
            'apellidos' => $request->apellidos,
            'identificacion' => $request->identificacion,
            'area_servicio' => $request->area_servicio,
            'cargo' => $request->cargo,
            'tipo_contrato' => $request->tipo_contrato,
            'correo' => $user?->email,
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
            'nombre' => $user->name ?? '',
            'apellidos' => trim(($user->apellido1 ?? '') . ' ' . ($user->apellido2 ?? '')),
            'area_servicio' => $user->servicio ?? '',
            'cargo' => $user->cargo ?? '',
            'tipo_vinculacion' => $user->tipo_vinculacion ?? '',
        ]);
    }
}
