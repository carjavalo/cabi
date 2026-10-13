<?php

namespace App\Http\Middleware;

use App\Support\GestorPermisos;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Aplica Gestión de Permisos a cada ruta autenticada.
 * Además detecta rutas nuevas y las agrega al catálogo de permisos automáticamente.
 */
class VerificarPermiso
{
    public function handle(Request $request, Closure $next): Response
    {
        GestorPermisos::sincronizarSiCambio();

        $user = Auth::user();
        if ($user && !GestorPermisos::puedeRuta($user, $request->route())) {
            $mensaje = 'Su perfil no cuenta con autorización para realizar esta acción. Si la necesita, solicítela al administrador del sistema.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $mensaje], 403);
            }
            abort(403, $mensaje);
        }

        return $next($request);
    }
}
