<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictInstructorGym
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role === 'Instructor GYM') {
            // Check if the route is one of the allowed ones
            $allowedRoutes = [
                'bienestar.listados',
                'bienestar.observaciones.get',
                'bienestar.observaciones.store',
                'bienestar.asistencia.cargar',
                'bienestar.asistencia.guardar',
                'bienestar.asistencia.consultar',
                'logout'
            ];
            
            // Allow POSTs to logout
            if ($request->is('logout')) {
                return $next($request);
            }
            
            // Allow direct home '/' maybe? Better strictly restrict
            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('bienestar.listados');
            }
        }
        
        return $next($request);
    }
}
