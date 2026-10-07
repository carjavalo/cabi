<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use App\Support\GestorPermisos;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Gestión de Permisos en vistas: @puede('clave.permiso') ... @endpuede / @puedeUrl('/ruta') ... @endpuedeUrl
        Blade::if('puede', fn (string $clave) => GestorPermisos::puede(Auth::user(), $clave));
        Blade::if('puedeUrl', fn (string $url) => GestorPermisos::puedeUrl(Auth::user(), $url));
    }
}
