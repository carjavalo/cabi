<?php

namespace App\Http\Controllers\SaludOcupacional;

use App\Http\Controllers\Controller;
use App\Models\Afp;
use App\Models\Arl;
use App\Models\Eps;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

/**
 * CRUD dinámico de los catálogos de seguridad social (EPS, AFP y ARL) que
 * alimentan el paso 3 del Concepto Médico Ocupacional.
 *
 * Un solo controlador atiende los tres catálogos: el segmento {tipo} de la ruta
 * decide sobre qué tabla se opera.
 */
class EntidadAfiliacionController extends Controller implements HasMiddleware
{
    /** Catálogos soportados: tipo de la ruta => [modelo, etiqueta singular, etiqueta plural]. */
    private const CATALOGOS = [
        'eps' => [Eps::class, 'EPS', 'EPS'],
        'afp' => [Afp::class, 'AFP', 'AFP'],
        'arl' => [Arl::class, 'ARL', 'ARL'],
    ];

    /**
     * Mismo control de acceso que el módulo de Salud Ocupacional: por ahora,
     * exclusivo del rol "Super Admin".
     */
    public static function middleware(): array
    {
        return [
            function (Request $request, Closure $next) {
                $user = Auth::user();
                if (!$user || $user->role !== 'Super Admin') {
                    abort(403, 'Acceso restringido al módulo de Salud Ocupacional.');
                }
                return $next($request);
            },
        ];
    }

    /**
     * Lista las entidades de un catálogo, con búsqueda opcional (?q=).
     */
    public function index(Request $request, string $tipo)
    {
        return $this->responder(function () use ($request, $tipo) {
            [$modelo, , $plural] = $this->catalogo($tipo);
            $tabla = (new $modelo)->getTable();

            // En cPanel las migraciones suelen quedar pendientes: se crean e
            // inicializan las tablas del catálogo al vuelo (operación idempotente).
            if (!Schema::hasTable($tabla)) {
                $this->provisionar($tabla);
            }

            if (!Schema::hasTable($tabla)) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'El catálogo de ' . $plural . ' no existe en la base de datos y no se pudo crear '
                        . 'automáticamente. Abre /salud-ocupacional/concepto/migrar para ejecutar las migraciones.',
                    'items'   => [],
                ]);
            }

            $q = trim((string) $request->query('q', ''));

            $items = $modelo::query()
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($sub) use ($q) {
                        $sub->where('nombre', 'like', '%' . $q . '%')
                            ->orWhere('codigo', 'like', '%' . $q . '%');
                    });
                })
                ->orderBy('nombre')
                ->get()
                ->map(fn (Model $e) => $this->payload($e));

            return response()->json([
                'ok'    => true,
                'tipo'  => $tipo,
                'label' => $plural,
                'items' => $items,
            ]);
        });
    }

    /**
     * Diagnóstico del CRUD de catálogos (solo Super Admin).
     *
     * Reporta en JSON por qué falla el módulo en el servidor sin necesidad de
     * acceso a terminal ni a los logs, igual que el diagnóstico del concepto.
     */
    public function diagnostico()
    {
        $out = [
            'php'       => PHP_VERSION,
            'app_env'   => config('app.env'),
            'app_debug' => config('app.debug'),
        ];

        try {
            DB::connection()->getPdo();
            $out['db'] = 'conectada (' . config('database.connections.' . config('database.default') . '.database') . ')';
        } catch (\Throwable $e) {
            $out['db'] = 'ERROR: ' . $e->getMessage();
        }

        foreach (self::CATALOGOS as $tipo => [$modelo, , $plural]) {
            $tabla = (new $modelo)->getTable();
            $info  = [
                'modelo'       => class_exists($modelo) ? $modelo . ' (cargado)' : $modelo . ' — CLASE NO ENCONTRADA',
                'tabla'        => $tabla,
                'tabla_existe' => Schema::hasTable($tabla) ? 'sí' : 'NO',
            ];

            if ($info['tabla_existe'] === 'sí') {
                foreach (['id', 'nombre', 'codigo', 'nit', 'activo'] as $col) {
                    $info['columna_' . $col] = Schema::hasColumn($tabla, $col) ? 'existe' : 'FALTA';
                }
                try {
                    $info['registros'] = $modelo::count();
                } catch (\Throwable $e) {
                    $info['registros'] = 'ERROR: ' . $e->getMessage();
                }
            }

            $out['catalogo_' . $tipo] = $info;
        }

        $out['controlador'] = static::class;
        $out['migracion']   = DB::table('migrations')
            ->where('migration', 'like', '%entidades_afiliacion%')
            ->pluck('migration')
            ->all() ?: 'NO registrada en la tabla migrations';

        return response()->json($out, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Crea una entidad nueva en el catálogo.
     */
    public function store(Request $request, string $tipo)
    {
        return $this->responder(function () use ($request, $tipo) {
            [$modelo, $singular] = $this->catalogo($tipo);

            $tabla = (new $modelo)->getTable();
            if (!Schema::hasTable($tabla)) {
                $this->provisionar($tabla);
            }

            $data = $this->validar($request, $modelo);
            $data['activo'] = $request->boolean('activo', true);

            $entidad = $modelo::create($data);

            return response()->json([
                'ok'      => true,
                'message' => $singular . ' "' . $entidad->nombre . '" creada correctamente.',
                'item'    => $this->payload($entidad),
            ]);
        });
    }

    /**
     * Actualiza una entidad existente.
     */
    public function update(Request $request, string $tipo, int $id)
    {
        return $this->responder(function () use ($request, $tipo, $id) {
            [$modelo, $singular] = $this->catalogo($tipo);

            $entidad  = $modelo::findOrFail($id);
            $anterior = $entidad->nombre;

            $data = $this->validar($request, $modelo, $entidad->id);

            $entidad->fill($data);
            if ($request->has('activo')) {
                $entidad->activo = $request->boolean('activo');
            }
            $entidad->save();

            return response()->json([
                'ok'       => true,
                'message'  => $singular . ' actualizada correctamente.',
                'item'     => $this->payload($entidad),
                'anterior' => $anterior,
            ]);
        });
    }

    /**
     * Elimina una entidad del catálogo.
     *
     * No se elimina si ya está referenciada por conceptos médicos o por
     * trabajadores: en ese caso se sugiere desactivarla, para no perder la
     * trazabilidad de las atenciones ya registradas.
     */
    public function destroy(string $tipo, int $id)
    {
        return $this->responder(function () use ($tipo, $id) {
            [$modelo, $singular] = $this->catalogo($tipo);

            $entidad = $modelo::findOrFail($id);
            $usos    = $this->contarUsos($tipo, $entidad->nombre);

            if ($usos > 0) {
                return response()->json([
                    'ok'      => false,
                    'en_uso'  => true,
                    'usos'    => $usos,
                    'message' => 'No se puede eliminar: "' . $entidad->nombre . '" está en uso en '
                        . $usos . ' registro(s). Puedes desactivarla para que deje de aparecer en el listado.',
                ], 409);
            }

            $nombre = $entidad->nombre;
            $entidad->delete();

            return response()->json([
                'ok'      => true,
                'message' => $singular . ' "' . $nombre . '" eliminada correctamente.',
            ]);
        });
    }

    // --------------------------- Helpers ---------------------------

    /**
     * Ejecuta la acción y traduce cualquier fallo a JSON.
     *
     * Sin esto, un error en el servidor devuelve la página HTML de error 500 de
     * Laravel; el front intenta parsearla como JSON, falla y solo puede mostrar
     * "fallo de conexión", que oculta la causa real. Aquí el motivo viaja en el
     * cuerpo de la respuesta y además queda en el log.
     */
    private function responder(\Closure $accion)
    {
        try {
            return $accion();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // La validación ya produce su propia respuesta 422 en JSON.
            throw $e;
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            // abort() intencional (404 de catálogo no soportado, 403 de acceso).
            throw $e;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'La entidad ya no existe en el catálogo. Actualiza el listado.',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Catálogo de afiliación: ' . $e->getMessage(), [
                'excepcion' => get_class($e),
                'archivo'   => $e->getFile() . ':' . $e->getLine(),
            ]);

            return response()->json([
                'ok'        => false,
                'message'   => 'Error en el servidor: ' . $e->getMessage(),
                'excepcion' => class_basename($e),
                'donde'     => basename($e->getFile()) . ':' . $e->getLine(),
            ], 500);
        }
    }

    /**
     * Crea la tabla del catálogo si falta y la inicializa con las entidades
     * de uso corriente en Colombia.
     *
     * En cPanel no hay terminal para lanzar `php artisan migrate`, así que el
     * módulo se autoabastece la primera vez que se usa. Es idempotente.
     */
    private function provisionar(string $tabla): void
    {
        $semilla = [
            'eps' => [
                'Nueva EPS', 'EPS Sura', 'EPS Sanitas', 'Salud Total', 'Compensar', 'Famisanar',
                'Coosalud', 'Emssanar', 'Servicio Occidental de Salud (SOS)', 'Comfenalco Valle', 'Asmet Salud',
            ],
            'afps' => ['Porvenir', 'Protección', 'Colfondos', 'Skandia', 'Colpensiones'],
            'arls' => [
                'ARL Sura', 'Positiva', 'Colmena Seguros', 'Seguros Bolívar', 'AXA Colpatria',
                'La Equidad Seguros', 'Mapfre',
            ],
        ];

        if (!isset($semilla[$tabla])) {
            return;
        }

        try {
            if (!Schema::hasTable($tabla)) {
                Schema::create($tabla, function (Blueprint $table) {
                    $table->id();
                    $table->string('nombre', 150)->unique();
                    $table->string('codigo', 40)->nullable();
                    $table->string('nit', 40)->nullable();
                    $table->boolean('activo')->default(true);
                    $table->timestamps();
                });

                $ahora = now();
                foreach ($semilla[$tabla] as $nombre) {
                    DB::table($tabla)->insert([
                        'nombre'     => $nombre,
                        'activo'     => true,
                        'created_at' => $ahora,
                        'updated_at' => $ahora,
                    ]);
                }

                Log::info('Catálogo de afiliación: tabla "' . $tabla . '" creada e inicializada automáticamente.');
            }
        } catch (\Throwable $e) {
            Log::error('Catálogo de afiliación: no se pudo crear la tabla "' . $tabla . '": ' . $e->getMessage());
        }
    }

    /** Resuelve el catálogo a partir del segmento {tipo} de la ruta. */
    private function catalogo(string $tipo): array
    {
        $tipo = strtolower($tipo);
        abort_unless(isset(self::CATALOGOS[$tipo]), 404, 'Catálogo no soportado.');

        return self::CATALOGOS[$tipo];
    }

    private function validar(Request $request, string $modelo, ?int $ignoreId = null): array
    {
        $tabla = (new $modelo)->getTable();

        $unico = Rule::unique($tabla, 'nombre');
        if ($ignoreId) {
            $unico->ignore($ignoreId);
        }

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150', $unico],
            'codigo' => ['nullable', 'string', 'max:40'],
            'nit'    => ['nullable', 'string', 'max:40'],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique'   => 'Ya existe una entidad con ese nombre en el catálogo.',
        ]);

        $data['nombre'] = trim($data['nombre']);

        return $data;
    }

    /** Cuenta cuántos registros del sistema usan la entidad (por nombre). */
    private function contarUsos(string $tipo, string $nombre): int
    {
        $total = 0;

        foreach (['conceptos_medicos', 'users'] as $tabla) {
            try {
                if (Schema::hasTable($tabla) && Schema::hasColumn($tabla, $tipo)) {
                    $total += DB::table($tabla)->where($tipo, $nombre)->count();
                }
            } catch (\Throwable $e) {
                // Si el conteo falla, no se bloquea la eliminación por ese motivo.
            }
        }

        return $total;
    }

    private function payload(Model $e): array
    {
        return [
            'id'     => $e->id,
            'nombre' => $e->nombre,
            'codigo' => $e->codigo,
            'nit'    => $e->nit,
            'activo' => (bool) $e->activo,
        ];
    }
}
