<?php

namespace App\Http\Controllers\SaludOcupacional;

use App\Http\Controllers\Controller;
use App\Models\Afp;
use App\Models\Arl;
use App\Models\Eps;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        [$modelo, , $plural] = $this->catalogo($tipo);

        if (!Schema::hasTable((new $modelo)->getTable())) {
            return response()->json([
                'ok'      => false,
                'message' => 'El catálogo de ' . $plural . ' aún no está creado. Ejecuta las migraciones pendientes.',
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
    }

    /**
     * Crea una entidad nueva en el catálogo.
     */
    public function store(Request $request, string $tipo)
    {
        [$modelo, $singular] = $this->catalogo($tipo);

        $data = $this->validar($request, $modelo);
        $data['activo'] = $request->boolean('activo', true);

        $entidad = $modelo::create($data);

        return response()->json([
            'ok'      => true,
            'message' => $singular . ' "' . $entidad->nombre . '" creada correctamente.',
            'item'    => $this->payload($entidad),
        ]);
    }

    /**
     * Actualiza una entidad existente.
     */
    public function update(Request $request, string $tipo, int $id)
    {
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
    }

    // --------------------------- Helpers ---------------------------

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
