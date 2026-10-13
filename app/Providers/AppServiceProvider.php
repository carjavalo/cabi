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
        // @puede('a', 'b') exige todas las claves (p. ej. formulario de creación + guardar).
        Blade::if('puede', fn (string ...$claves) => collect($claves)->every(fn ($c) => GestorPermisos::puede(Auth::user(), $c)));
        Blade::if('puedeUrl', fn (string $url) => GestorPermisos::puedeUrl(Auth::user(), $url));
    }
}
