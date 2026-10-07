<?php

namespace App\Support;

use App\Models\Permiso;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Núcleo de Gestión de Permisos.
 *
 * - Descubre automáticamente las funciones del aplicativo a partir de las rutas
 *   protegidas con "auth": cualquier vista u opción nueva aparece sola en el catálogo.
 * - Resuelve si un usuario puede ejecutar una función (Super Admin siempre puede).
 * - Resuelve qué roles puede asignar cada rol al crear/editar usuarios.
 *
 * Reglas para no alterar el funcionamiento actual:
 * - Las funciones nuevas se asignan a todos los roles; el administrador las restringe después.
 * - Si las tablas no existen o una función no está en el catálogo, no se bloquea nada.
 * - Los controles de rol que ya existen en el código siguen aplicando.
 */
class GestorPermisos
{
    /** Rutas esenciales que nunca se controlan (sesión, verificación, perfil, inicio). */
    private const EXCLUIDAS_NOMBRE = ['dashboard', 'logout'];
    private const EXCLUIDAS_PREFIJO = ['password.', 'verification.', 'profile.', 'sanctum.', 'ignition.', 'livewire.'];
    private const EXCLUIDAS_URI = ['confirm-password', 'up', '/'];

    private const ROL_TOTAL = 'Super Admin';

    private static ?bool $tablasOk = null;
    private static array $memo = [];

    // ------------------------------------------------------------------
    // Tablas
    // ------------------------------------------------------------------

    public static function asegurarTablas(): bool
    {
        if (self::$tablasOk === true) {
            return true;
        }

        try {
            Role::asegurarTabla();

            if (!Schema::hasColumn('roles', 'config_asignables')) {
                Schema::table('roles', function (Blueprint $t) {
                    $t->boolean('config_asignables')->default(false)->after('es_sistema');
                });
            }

            if (!Schema::hasTable('permisos')) {
                Schema::create('permisos', function (Blueprint $t) {
                    $t->id();
                    $t->string('clave', 191)->unique();
                    $t->string('nombre', 150);
                    $t->string('modulo', 120)->index();
                    $t->string('descripcion', 255)->nullable();
                    $t->string('tipo', 20)->default('vista');      // vista | accion | personalizado
                    $t->string('metodo', 30)->nullable();
                    $t->string('uri', 255)->nullable();
                    $t->string('origen', 10)->default('ruta');     // ruta | manual
                    $t->boolean('activo')->default(true);          // inactivo = no se controla
                    $t->boolean('nuevo')->default(false);          // descubierto y aún no revisado
                    $t->boolean('huerfano')->default(false);       // su ruta ya no existe
                    $t->boolean('editado')->default(false);        // nombre/módulo ajustado a mano
                    $t->timestamps();
                });
            }

            if (!Schema::hasTable('permiso_role')) {
                Schema::create('permiso_role', function (Blueprint $t) {
                    $t->id();
                    $t->foreignId('permiso_id')->constrained('permisos')->cascadeOnDelete();
                    $t->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
                    $t->timestamps();
                    $t->unique(['permiso_id', 'role_id']);
                });
            }

            if (!Schema::hasTable('role_asignable')) {
                Schema::create('role_asignable', function (Blueprint $t) {
                    $t->id();
                    $t->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
                    $t->foreignId('asignable_id')->constrained('roles')->cascadeOnDelete();
                    $t->timestamps();
                    $t->unique(['role_id', 'asignable_id']);
                });
            }

            return self::$tablasOk = true;
        } catch (\Throwable $e) {
            report($e);
            return self::$tablasOk = false;
        }
    }

    private static function tablasListas(): bool
    {
        if (self::$tablasOk !== null) {
            return self::$tablasOk;
        }
        try {
            self::$tablasOk = Schema::hasTable('permisos') && Schema::hasTable('permiso_role');
        } catch (\Throwable $e) {
            self::$tablasOk = false;
        }
        return self::$tablasOk;
    }

    // ------------------------------------------------------------------
    // Descubrimiento de funciones (rutas)
    // ------------------------------------------------------------------

    /** Clave de permiso de una ruta: su nombre, o "MÉTODO uri" si no tiene nombre. */
    public static function claveRuta(LaravelRoute $route): string
    {
        if ($nombre = $route->getName()) {
            return $nombre;
        }
        $metodos = array_values(array_diff($route->methods(), ['HEAD']));
        return implode('|', $metodos) . ' ' . $route->uri();
    }

    public static function rutaControlable(LaravelRoute $route): bool
    {
        $mw = $route->gatherMiddleware();
        $autenticada = collect($mw)->contains(fn ($m) => is_string($m) && (
            $m === 'auth' || str_starts_with($m, 'auth:') || str_contains($m, 'Authenticate')
        ));
        if (!$autenticada) {
            return false;
        }

        $nombre = (string) $route->getName();
        if (in_array($nombre, self::EXCLUIDAS_NOMBRE, true) || in_array($route->uri(), self::EXCLUIDAS_URI, true)) {
            return false;
        }
        foreach (self::EXCLUIDAS_PREFIJO as $p) {
            if ($nombre !== '' && str_starts_with($nombre, $p)) {
                return false;
            }
        }
        return true;
    }

    /** @return array<string,array> clave => datos descubiertos */
    public static function descubrir(): array
    {
        $out = [];
        foreach (Route::getRoutes() as $route) {
            if (!self::rutaControlable($route)) {
                continue;
            }
            $clave = self::claveRuta($route);
            $metodos = array_values(array_diff($route->methods(), ['HEAD']));
            $esVista = in_array('GET', $metodos, true);
            $out[$clave] = [
                'clave'  => $clave,
                'nombre' => self::nombreLegible($route, $esVista),
                'modulo' => self::moduloLegible($route->uri()),
                'tipo'   => $esVista ? 'vista' : 'accion',
                'metodo' => implode('|', $metodos),
                'uri'    => '/' . ltrim($route->uri(), '/'),
            ];
        }
        ksort($out);
        return $out;
    }

    /** Huella del conjunto de rutas controlables: cambia cuando se agrega o quita una opción. */
    public static function huella(): string
    {
        return md5(implode("\n", array_keys(self::descubrir())));
    }

    /**
     * Sincroniza el catálogo con las rutas actuales.
     * @return array{nuevos:int, actualizados:int, huerfanos:int, total:int}
     */
    public static function sincronizar(): array
    {
        if (!self::asegurarTablas()) {
            return ['nuevos' => 0, 'actualizados' => 0, 'huerfanos' => 0, 'total' => 0];
        }

        $descubiertos = self::descubrir();
        $existentes = Permiso::where('origen', 'ruta')->get()->keyBy('clave');
        $roles = Role::pluck('id')->all();
        $nuevos = $actualizados = $huerfanos = 0;
        $primeraVez = Permiso::count() === 0;

        DB::transaction(function () use ($descubiertos, $existentes, $roles, $primeraVez, &$nuevos, &$actualizados, &$huerfanos) {
            $now = now();
            foreach ($descubiertos as $clave => $d) {
                $p = $existentes->get($clave);
                if (!$p) {
                    $p = Permiso::create($d + ['origen' => 'ruta', 'activo' => true, 'nuevo' => !$primeraVez]);
                    // Para no alterar el funcionamiento: la función nueva queda disponible para todos los roles
                    $filas = array_map(fn ($rid) => ['permiso_id' => $p->id, 'role_id' => $rid, 'created_at' => $now, 'updated_at' => $now], $roles);
                    if ($filas) {
                        DB::table('permiso_role')->insertOrIgnore($filas);
                    }
                    $nuevos++;
                    continue;
                }

                $cambios = ['metodo' => $d['metodo'], 'uri' => $d['uri'], 'tipo' => $d['tipo'], 'huerfano' => false];
                if (!$p->editado) {
                    $cambios['nombre'] = $d['nombre'];
                    $cambios['modulo'] = $d['modulo'];
                }
                $p->fill($cambios);
                if ($p->isDirty()) {
                    $p->save();
                    $actualizados++;
                }
            }

            $faltantes = $existentes->keys()->diff(array_keys($descubiertos));
            if ($faltantes->isNotEmpty()) {
                $huerfanos = Permiso::where('origen', 'ruta')->whereIn('clave', $faltantes)->where('huerfano', false)->update(['huerfano' => true]);
            }
        });

        self::sembrarAsignables();
        self::limpiarCache();
        Cache::forever('permisos.huella', md5(implode("\n", array_keys($descubiertos))));

        return ['nuevos' => $nuevos, 'actualizados' => $actualizados, 'huerfanos' => $huerfanos, 'total' => count($descubiertos)];
    }

    /**
     * Revisa (como máximo una vez por minuto) si cambiaron las rutas y, si es así, sincroniza.
     * Así toda opción o vista nueva se agrega sola al catálogo.
     */
    public static function sincronizarSiCambio(): void
    {
        try {
            if (!self::tablasListas() || Cache::has('permisos.revisado')) {
                return;
            }
            Cache::put('permisos.revisado', 1, 60);
            if (Cache::get('permisos.huella') !== self::huella()) {
                self::sincronizar();
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    // ------------------------------------------------------------------
    // Verificación de permisos
    // ------------------------------------------------------------------

    private static function version(): int
    {
        return (int) Cache::get('permisos.version', 1);
    }

    public static function limpiarCache(): void
    {
        Cache::forever('permisos.version', self::version() + 1);
        self::$memo = [];
    }

    /** @return array<string,int> clave => id de los permisos controlados (activos y vigentes) */
    private static function mapaControlados(): array
    {
        $k = 'permisos.mapa.' . self::version();
        return self::$memo[$k] ??= Cache::remember($k, 3600, fn () =>
            Permiso::where('activo', true)->where('huerfano', false)->pluck('id', 'clave')->all()
        );
    }

    /** @return array<int,true> ids de permisos del rol */
    private static function permisosDeRol(string $rol): array
    {
        $k = 'permisos.rol.' . md5($rol) . '.' . self::version();
        return self::$memo[$k] ??= Cache::remember($k, 3600, function () use ($rol) {
            $roleId = Role::where('nombre', $rol)->value('id');
            if (!$roleId) {
                return [];
            }
            return array_fill_keys(DB::table('permiso_role')->where('role_id', $roleId)->pluck('permiso_id')->all(), true);
        });
    }

    public static function puede(?User $user, string $clave): bool
    {
        if (!$user) {
            return false;
        }
        if ($user->role === self::ROL_TOTAL) {
            return true;
        }
        try {
            if (!self::tablasListas()) {
                return true;
            }
            $mapa = self::mapaControlados();
            if (!isset($mapa[$clave])) {
                return true; // función no controlada o aún no sincronizada
            }
            return isset(self::permisosDeRol((string) $user->role)[$mapa[$clave]]);
        } catch (\Throwable $e) {
            report($e);
            return true;
        }
    }

    public static function puedeRuta(?User $user, ?LaravelRoute $route): bool
    {
        if (!$route || !self::rutaControlable($route)) {
            return true;
        }
        return self::puede($user, self::claveRuta($route));
    }

    /** Para el menú: ¿puede el usuario abrir esta URL (GET)? */
    public static function puedeUrl(?User $user, string $url): bool
    {
        if ($user && $user->role === self::ROL_TOTAL) {
            return true;
        }
        try {
            $route = Route::getRoutes()->match(\Illuminate\Http\Request::create($url, 'GET'));
            return self::puedeRuta($user, $route);
        } catch (\Throwable $e) {
            return true;
        }
    }

    // ------------------------------------------------------------------
    // Roles asignables
    // ------------------------------------------------------------------

    /** Regla original del aplicativo (se usa para sembrar y como respaldo). */
    public static function asignablesPorJerarquia(string $rol, array $nombres): array
    {
        return Role::filtrarPorJerarquia($nombres, $rol);
    }

    /** Siembra la configuración de asignables de los roles que aún no la tienen, según la jerarquía original. */
    public static function sembrarAsignables(): void
    {
        if (!Schema::hasTable('role_asignable')) {
            return;
        }
        $roles = Role::all();
        $porNombre = $roles->pluck('id', 'nombre');
        $now = now();
        foreach ($roles as $r) {
            if ($r->config_asignables) {
                continue;
            }
            $nombres = self::asignablesPorJerarquia($r->nombre, $roles->pluck('nombre')->all());
            $filas = [];
            foreach ($nombres as $n) {
                $filas[] = ['role_id' => $r->id, 'asignable_id' => $porNombre[$n], 'created_at' => $now, 'updated_at' => $now];
            }
            DB::table('role_asignable')->where('role_id', $r->id)->delete();
            if ($filas) {
                DB::table('role_asignable')->insert($filas);
            }
            $r->forceFill(['config_asignables' => true])->save();
        }
    }

    /**
     * Filtra la lista de roles a los que el rol actual puede asignar.
     * "Super Admin" solo lo puede asignar un Super Admin, sin importar la configuración.
     *
     * @param array<int,string> $nombres
     * @return array<int,string>
     */
    public static function rolesAsignables(array $nombres, ?string $rolActual): array
    {
        if ($rolActual === self::ROL_TOTAL) {
            return array_values($nombres);
        }

        $permitidos = null;
        try {
            if ($rolActual && Schema::hasTable('role_asignable') && Schema::hasColumn('roles', 'config_asignables')) {
                $rol = Role::where('nombre', $rolActual)->first();
                if ($rol && $rol->config_asignables) {
                    $permitidos = Role::whereIn('id', DB::table('role_asignable')->where('role_id', $rol->id)->pluck('asignable_id'))
                        ->pluck('nombre')->all();
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        if ($permitidos === null) {
            return Role::filtrarPorJerarquia($nombres, $rolActual);
        }

        return array_values(array_filter($nombres, fn ($n) => $n !== self::ROL_TOTAL && in_array($n, $permitidos, true)));
    }

    /** Al crear un rol nuevo, hereda los permisos del rol "Usuario" (mismo acceso que tenía antes). */
    public static function inicializarRol(Role $role, string $desde = 'Usuario'): void
    {
        if (!self::asegurarTablas()) {
            return;
        }
        $origen = Role::where('nombre', $desde)->first();
        if ($origen) {
            self::copiarPermisos($origen->id, $role->id);
        }
        self::sembrarAsignables();
    }

    public static function copiarPermisos(int $desdeId, int $haciaId): void
    {
        $now = now();
        DB::transaction(function () use ($desdeId, $haciaId, $now) {
            DB::table('permiso_role')->where('role_id', $haciaId)->delete();
            $filas = DB::table('permiso_role')->where('role_id', $desdeId)->pluck('permiso_id')
                ->map(fn ($pid) => ['permiso_id' => $pid, 'role_id' => $haciaId, 'created_at' => $now, 'updated_at' => $now])->all();
            foreach (array_chunk($filas, 500) as $lote) {
                DB::table('permiso_role')->insert($lote);
            }
        });
        self::limpiarCache();
    }

    // ------------------------------------------------------------------
    // Nombres legibles
    // ------------------------------------------------------------------

    private const PALABRAS = [
        'configuracion' => 'Configuración', 'salud-ocupacional' => 'Salud Ocupacional', 'vinculaciones' => 'Vinculaciones',
        'capacitaciones' => 'Capacitaciones', 'publicidad' => 'Publicidad', 'gym' => 'GYM', 'api' => 'API',
        'ciau1' => 'CIAU1', 'concepto' => 'Concepto Médico', 'agenda' => 'Agenda Médica', 'roles' => 'Roles',
        'permisos' => 'Permisos', 'usuarios' => 'Usuarios', 'servicios' => 'Servicios', 'servicios-buscar' => 'Servicios',
        'encuestas' => 'Encuestas', 'eventos' => 'Eventos', 'cargos' => 'Cargos', 'cursos' => 'Cursos',
        'recaudo' => 'Recaudo', 'bienestar' => 'Bienestar', 'inscritos' => 'Inscritos', 'otros' => 'Otros',
        'entidades' => 'Entidades de Afiliación', 'listados' => 'Listados', 'asistencia' => 'Asistencia',
        'observaciones' => 'Observaciones', 'test-email-diagnostico' => 'Diagnóstico de Correo',
    ];

    private const ACCIONES = [
        'index' => 'Ver listado', 'create' => 'Ver formulario de creación', 'store' => 'Crear registro',
        'show' => 'Ver detalle', 'edit' => 'Ver formulario de edición', 'update' => 'Actualizar registro',
        'destroy' => 'Eliminar registro', 'export' => 'Exportar', 'exportExcel' => 'Exportar a Excel',
        'data' => 'Consultar datos de la tabla', 'buscar' => 'Buscar', 'diagnostico' => 'Diagnóstico',
        'migrar' => 'Ejecutar migraciones', 'edit_user' => 'Ver formulario de edición', 'update_user' => 'Actualizar registro',
        'index_user' => 'Ver listado',
    ];

    private static function palabra(string $seg): string
    {
        return self::PALABRAS[$seg] ?? Str::title(str_replace(['-', '_'], ' ', $seg));
    }

    public static function moduloLegible(string $uri): string
    {
        $segs = array_values(array_filter(explode('/', trim($uri, '/')), fn ($s) => $s !== '' && !str_starts_with($s, '{')));
        if (!$segs) {
            return 'General';
        }
        $principal = self::palabra($segs[0]);
        if (isset($segs[1]) && in_array($segs[0], ['configuracion', 'salud-ocupacional', 'bienestar', 'api', 'otros'], true)) {
            return $principal . ' › ' . self::palabra($segs[1]);
        }
        return $principal;
    }

    private static function nombreLegible(LaravelRoute $route, bool $esVista): string
    {
        $nombre = (string) $route->getName();
        if ($nombre !== '') {
            $ultimo = Str::afterLast($nombre, '.');
            if (isset(self::ACCIONES[$ultimo])) {
                return self::ACCIONES[$ultimo];
            }
            // nombres compuestos: "salud.concepto.paciente.buscar" -> "Paciente: buscar"
            $partes = explode('.', $nombre);
            $texto = count($partes) >= 3
                ? Str::title(str_replace(['-', '_'], ' ', $partes[count($partes) - 2])) . ': ' . str_replace(['-', '_'], ' ', $ultimo)
                : str_replace(['-', '_'], ' ', $ultimo);
            return Str::ucfirst(trim($texto));
        }

        $segs = array_values(array_filter(explode('/', trim($route->uri(), '/')), fn ($s) => !str_starts_with($s, '{')));
        $ultimo = $segs ? self::palabra(end($segs)) : 'Inicio';
        return ($esVista ? 'Ver ' : 'Acción en ') . $ultimo;
    }
}
