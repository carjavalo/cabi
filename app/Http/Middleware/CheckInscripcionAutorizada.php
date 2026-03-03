<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class CheckInscripcionAutorizada
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Require identification parameter. Si no hay en la ruta o get, intentemos recuperarlo de auth o sesion.
        // Como la ruta de inscripción pública (/eventos/inscripcion/1) se abre por url, necesitamos preguntar al usuario su identificación antes si no está logeado.
        // Podríamos redirigirlo a un prompt de "Ingresa tu identificación para verificar si estás autorizado".
        // Para que se integre más fácil, podemos checarlo por un parámetro get ej: ?identificacion=123
        $identificacion = $request->query('identificacion');

        if (!$identificacion) {
            // Permitimos pasar a la vista, pero la vista es la que valida si digita la cedula.
            // Para bloquear de entrada, exigimos la identificacion.
            return redirect('/bienestar/gym/inscripcion')->with('error', 'Debes estar pre-registrado y autorizado para acceder a eventos.');
        }

        $usuario = DB::table('inscripgym')->where('identificacion', $identificacion)->first();

        if (!$usuario || !$usuario->autorizado) {
            return redirect('/bienestar/gym/inscripcion')->with('error', 'Lo sentimos, no estás autorizado para agendar en eventos. Debes ser autorizado primero.');
        }

        return $next($request);
    }
}
