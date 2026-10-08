<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Support\GestorPermisos;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller implements HasMiddleware
{
    /** Roles que pueden ver el módulo (mismos que ven el menú Configuración). */
    private const PUEDEN_VER = ['Super Admin', 'Administrador', 'Operador'];

    /** Roles que pueden crear, editar y eliminar roles. */
    private const PUEDEN_EDITAR = ['Super Admin', 'Administrador'];

    public static function middleware(): array
    {
        return [
            function (Request $request, Closure $next) {
                $rol = Auth::user()->role ?? null;
                if (!in_array($rol, self::PUEDEN_VER, true)) {
                    abort(403, 'No autorizado.');
                }
                if (!$request->isMethod('get') && !in_array($rol, self::PUEDEN_EDITAR, true)) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => 'No autorizado para modificar roles.'], 403);
                    }
                    abort(403, 'No autorizado para modificar roles.');
                }
                return $next($request);
            },
        ];
    }

    private function filtrar(Request $request)
    {
        $query = Role::query()->select('roles.*')->selectSub(
            User::selectRaw('count(*)')->whereColumn('users.role', 'roles.nombre'),
            'users_count'
        );

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado') && in_array($request->estado, ['1', '0'], true)) {
            $query->where('activo', (int) $request->estado);
        }

        if ($request->filled('tipo')) {
            if ($request->tipo === 'sistema') {
                $query->where('es_sistema', true);
            } elseif ($request->tipo === 'personalizado') {
                $query->where('es_sistema', false);
            }
        }

        if ($request->filled('usuarios')) {
            $asignados = fn ($q) => $q->select(DB::raw(1))->from('users')->whereColumn('users.role', 'roles.nombre');
            if ($request->usuarios === 'con') {
                $query->whereExists($asignados);
            } elseif ($request->usuarios === 'sin') {
                $query->whereNotExists($asignados);
            }
        }

        $sort = $request->get('sort', 'id');
        $allowedSorts = ['id', 'nombre', 'nivel', 'activo', 'es_sistema', 'users_count', 'created_at'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }
        $direction = strtolower((string) $request->get('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sort, $direction);
    }

    public function index(Request $request)
    {
        Role::asegurarTabla();
        $conAsignables = GestorPermisos::asegurarTablas();

        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $query = $this->filtrar($request);
        if ($conAsignables) {
            $query->with(['asignables' => fn ($q) => $q->select('roles.id', 'roles.nombre', 'roles.nivel')->orderBy('roles.nivel')->orderBy('roles.id')]);
        }
        $roles = $query->paginate($perPage)->withQueryString();
        $puedeEditar = in_array(Auth::user()->role, self::PUEDEN_EDITAR, true);
        $soySuper = Auth::user()->role === 'Super Admin';

        if ($request->ajax()) {
            return response()->json([
                'html'       => view('config.roles._table', compact('roles', 'puedeEditar', 'soySuper'))->render(),
                'pagination' => $roles->links()->toHtml(),
                'resumen'    => 'Mostrando ' . ($roles->firstItem() ?? 0) . ' a ' . ($roles->lastItem() ?? 0) . ' de ' . $roles->total() . ' registros',
            ]);
        }

        $stats = [
            'total'      => Role::count(),
            'activos'    => Role::where('activo', true)->count(),
            'sistema'    => Role::where('es_sistema', true)->count(),
            'usuarios'   => User::whereIn('role', Role::pluck('nombre'))->count(),
        ];

        // Catálogo completo para configurar "roles que puede asignar"
        $todosRoles = Role::orderBy('nivel')->orderBy('id')->get(['id', 'nombre', 'nivel', 'activo']);

        return view('config.roles.index', compact('roles', 'stats', 'puedeEditar', 'soySuper', 'todosRoles'));
    }

    /** Reglas comunes de nivel y roles asignables. */
    private function reglasExtra(): array
    {
        return [
            'nivel'          => ['nullable', 'integer', 'min:1', 'max:10'],
            'config_asig'    => ['nullable', 'boolean'],
            'asignables'     => ['nullable', 'array'],
            'asignables.*'   => ['integer', 'exists:roles,id'],
        ];
    }

    /** Guarda los roles asignables si el formulario los envió. Devuelve mensaje de error o null. */
    private function guardarAsignables(Request $request, Role $role): ?string
    {
        if (!$request->boolean('config_asig') || $role->nombre === 'Super Admin') {
            return null;
        }
        return GestorPermisos::guardarAsignables($role, $request->input('asignables', []), Auth::user()->role);
    }

    public function store(Request $request)
    {
        Role::asegurarTabla();

        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:60', Rule::unique('roles', 'nombre')],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'activo'      => ['nullable', 'boolean'],
        ] + $this->reglasExtra(), ['nombre.unique' => 'Ya existe un rol con ese nombre.', 'nombre.required' => 'El nombre del rol es obligatorio.'], ['nombre' => 'nombre del rol']);

        $role = Role::create([
            'nombre'      => trim($data['nombre']),
            'descripcion' => $data['descripcion'] ?? null,
            'activo'      => $request->boolean('activo', true),
            'es_sistema'  => false,
            'nivel'       => $data['nivel'] ?? Role::NIVEL_POR_DEFECTO,
        ]);

        // Gestión de Permisos: el rol nuevo inicia con los permisos del rol "Usuario"
        GestorPermisos::inicializarRol($role);

        if ($error = $this->guardarAsignables($request, $role)) {
            return $this->error($request, 'Rol creado, pero no se guardaron los roles asignables: ' . $error);
        }

        return $this->responder($request, 'Rol creado correctamente.');
    }

    public function show(Role $role)
    {
        $role->users_count = User::where('role', $role->nombre)->count();
        $role->usuarios = User::where('role', $role->nombre)
            ->orderBy('name')->limit(10)
            ->get(['id', 'name', 'apellido1', 'email']);

        return response()->json($role);
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:60', Rule::unique('roles', 'nombre')->ignore($role->id)],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'activo'      => ['nullable', 'boolean'],
        ] + $this->reglasExtra(), ['nombre.unique' => 'Ya existe un rol con ese nombre.', 'nombre.required' => 'El nombre del rol es obligatorio.'], ['nombre' => 'nombre del rol']);

        $nuevoNombre = trim($data['nombre']);
        $nivel = $data['nivel'] ?? $role->nivel;

        if ($role->es_sistema) {
            // Los roles del sistema solo permiten cambiar la descripción, el nivel y los roles que asignan
            if ($nuevoNombre !== $role->nombre || !$request->boolean('activo', true)) {
                return $this->error($request, 'Los roles del sistema no se pueden renombrar ni desactivar.');
            }
            if ($role->nombre === 'Super Admin') {
                $nivel = 1;
            }
            $role->update(['descripcion' => $data['descripcion'] ?? null, 'nivel' => $nivel]);
            if ($error = $this->guardarAsignables($request, $role)) {
                return $this->error($request, $error);
            }
            GestorPermisos::limpiarCache();
            return $this->responder($request, 'Rol actualizado correctamente.');
        }

        if ($error = $this->guardarAsignables($request, $role)) {
            return $this->error($request, $error);
        }

        DB::transaction(function () use ($role, $data, $nuevoNombre, $nivel, $request) {
            $anterior = $role->nombre;

            $role->update([
                'nombre'      => $nuevoNombre,
                'descripcion' => $data['descripcion'] ?? null,
                'activo'      => $request->boolean('activo', true),
                'nivel'       => $nivel,
            ]);

            // Mantener sincronizados a los usuarios que tienen asignado el rol
            if ($anterior !== $nuevoNombre) {
                User::where('role', $anterior)->update(['role' => $nuevoNombre]);
            }
        });

        \App\Support\GestorPermisos::limpiarCache();
        return $this->responder($request, 'Rol actualizado correctamente.');
    }

    public function destroy(Request $request, Role $role)
    {
        if ($role->es_sistema) {
            return $this->error($request, 'Los roles del sistema no se pueden eliminar.');
        }

        $asignados = User::where('role', $role->nombre)->count();
        if ($asignados > 0) {
            return $this->error($request, "No se puede eliminar: el rol está asignado a {$asignados} usuario(s). Reasígnelos o desactive el rol.");
        }

        $role->delete();

        \App\Support\GestorPermisos::limpiarCache();
        return $this->responder($request, 'Rol eliminado correctamente.');
    }

    public function exportExcel(Request $request)
    {
        Role::asegurarTabla();
        GestorPermisos::asegurarTablas();
        $roles = $this->filtrar($request)->with(['asignables' => fn ($q) => $q->select('roles.id', 'roles.nombre')])->get();

        $filename = 'roles_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($roles) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['ID', 'Nombre', 'Nivel', 'Descripción', 'Tipo', 'Estado', 'Usuarios', 'Puede asignar', 'Fecha de Creación'], ';');
            foreach ($roles as $r) {
                fputcsv($file, [
                    $r->id,
                    $r->nombre,
                    $r->nivel,
                    $r->descripcion ?? '',
                    $r->es_sistema ? 'Sistema' : 'Personalizado',
                    $r->activo ? 'Activo' : 'Inactivo',
                    $r->users_count,
                    $r->nombre === 'Super Admin' ? 'Todos' : $r->asignables->pluck('nombre')->implode(', '),
                    $r->created_at ? $r->created_at->format('d/m/Y H:i') : '',
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function responder(Request $request, string $mensaje)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $mensaje]);
        }
        return redirect()->route('config.roles.index')->with('success', $mensaje);
    }

    private function error(Request $request, string $mensaje)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $mensaje], 422);
        }
        return redirect()->route('config.roles.index')->with('error', $mensaje);
    }
}
