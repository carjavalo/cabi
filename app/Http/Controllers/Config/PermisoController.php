<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use App\Models\Role;
use App\Models\User;
use App\Support\GestorPermisos;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PermisoController extends Controller implements HasMiddleware
{
    /** Roles que pueden administrar los permisos (además del permiso de ruta correspondiente). */
    private const PUEDEN_ADMINISTRAR = ['Super Admin', 'Administrador'];

    public static function middleware(): array
    {
        return [
            function (Request $request, Closure $next) {
                if (!in_array(Auth::user()->role ?? null, self::PUEDEN_ADMINISTRAR, true)) {
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
                    }
                    abort(403, 'No autorizado.');
                }
                GestorPermisos::asegurarTablas();
                return $next($request);
            },
        ];
    }

    public function index()
    {
        $sync = GestorPermisos::sincronizar();

        return view('config.permisos.index', array_merge($this->datos(), ['sync' => $sync]));
    }

    /** Datos completos para la vista (también se usan para refrescar por AJAX). */
    public function datos(): array
    {
        $conteoUsuarios = User::select('role', DB::raw('count(*) as c'))->groupBy('role')->pluck('c', 'role');
        $permisosPorRol = DB::table('permiso_role')->get(['role_id', 'permiso_id'])->groupBy('role_id')
            ->map(fn ($g) => $g->pluck('permiso_id')->values());
        $asignablesPorRol = DB::table('role_asignable')->get(['role_id', 'asignable_id'])->groupBy('role_id')
            ->map(fn ($g) => $g->pluck('asignable_id')->values());

        $roles = Role::orderByDesc('es_sistema')->orderBy('id')->get()->map(fn ($r) => [
            'id'         => $r->id,
            'nombre'     => $r->nombre,
            'descripcion'=> $r->descripcion,
            'es_sistema' => (bool) $r->es_sistema,
            'activo'     => (bool) $r->activo,
            'total'      => $r->nombre === 'Super Admin',
            'usuarios'   => (int) ($conteoUsuarios[$r->nombre] ?? 0),
            'permisos'   => $permisosPorRol[$r->id] ?? [],
            'asignables' => $asignablesPorRol[$r->id] ?? [],
        ])->values();

        $permisos = Permiso::orderBy('modulo')->orderBy('tipo', 'desc')->orderBy('nombre')->get([
            'id', 'clave', 'nombre', 'modulo', 'descripcion', 'tipo', 'metodo', 'uri', 'origen', 'activo', 'nuevo', 'huerfano',
        ]);

        return [
            'roles'      => $roles,
            'permisos'   => $permisos,
            'soySuper'   => Auth::user()->role === 'Super Admin',
        ];
    }

    public function refrescar()
    {
        return response()->json($this->datos());
    }

    public function sincronizar()
    {
        $r = GestorPermisos::sincronizar();
        $msg = "Sincronización completa: {$r['total']} funciones detectadas, {$r['nuevos']} nuevas"
            . ($r['huerfanos'] ? ", {$r['huerfanos']} ya no existen" : '') . '.';
        return response()->json(['success' => true, 'message' => $msg, 'resultado' => $r] + $this->datos());
    }

    /** Asignar o quitar uno o varios permisos a un rol. */
    public function asignar(Request $request)
    {
        $data = $request->validate([
            'role_id'       => ['required', 'integer', 'exists:roles,id'],
            'permiso_ids'   => ['required', 'array', 'min:1'],
            'permiso_ids.*' => ['integer', 'exists:permisos,id'],
            'valor'         => ['required', 'boolean'],
        ]);

        $role = Role::findOrFail($data['role_id']);
        if ($role->nombre === 'Super Admin') {
            return response()->json(['success' => false, 'message' => 'El rol Super Admin siempre tiene acceso total.'], 422);
        }
        if ($role->nombre === 'Administrador' && Auth::user()->role !== 'Super Admin') {
            return response()->json(['success' => false, 'message' => 'Solo el Super Admin puede cambiar los permisos del rol Administrador.'], 403);
        }

        $ids = array_unique($data['permiso_ids']);
        if ($request->boolean('valor')) {
            $now = now();
            DB::table('permiso_role')->insertOrIgnore(array_map(fn ($pid) => [
                'permiso_id' => $pid, 'role_id' => $role->id, 'created_at' => $now, 'updated_at' => $now,
            ], $ids));
        } else {
            DB::table('permiso_role')->where('role_id', $role->id)->whereIn('permiso_id', $ids)->delete();
        }
        GestorPermisos::limpiarCache();

        $n = count($ids);
        return response()->json([
            'success'  => true,
            'message'  => ($request->boolean('valor') ? 'Asignado' : 'Retirado') . ($n > 1 ? " ({$n} permisos)" : '') . " · {$role->nombre}",
            'permisos' => DB::table('permiso_role')->where('role_id', $role->id)->pluck('permiso_id'),
        ]);
    }

    /** Copiar todos los permisos de un rol a otro. */
    public function copiar(Request $request)
    {
        $data = $request->validate([
            'desde' => ['required', 'integer', 'exists:roles,id'],
            'hacia' => ['required', 'integer', 'exists:roles,id', 'different:desde'],
        ]);
        $hacia = Role::findOrFail($data['hacia']);
        if ($hacia->nombre === 'Super Admin') {
            return response()->json(['success' => false, 'message' => 'El rol Super Admin siempre tiene acceso total.'], 422);
        }
        if ($hacia->nombre === 'Administrador' && Auth::user()->role !== 'Super Admin') {
            return response()->json(['success' => false, 'message' => 'Solo el Super Admin puede cambiar los permisos del rol Administrador.'], 403);
        }
        GestorPermisos::copiarPermisos($data['desde'], $hacia->id);

        return response()->json([
            'success'  => true,
            'message'  => 'Permisos copiados a ' . $hacia->nombre . '.',
            'permisos' => DB::table('permiso_role')->where('role_id', $hacia->id)->pluck('permiso_id'),
        ]);
    }

    /** Definir qué roles puede asignar un rol al crear/editar usuarios. */
    public function asignables(Request $request)
    {
        $data = $request->validate([
            'role_id'        => ['required', 'integer', 'exists:roles,id'],
            'asignables'     => ['nullable', 'array'],
            'asignables.*'   => ['integer', 'exists:roles,id'],
        ]);
        $role = Role::findOrFail($data['role_id']);
        if ($role->nombre === 'Super Admin') {
            return response()->json(['success' => false, 'message' => 'El Super Admin siempre puede asignar todos los roles.'], 422);
        }

        $superId = Role::where('nombre', 'Super Admin')->value('id');
        $ids = collect($data['asignables'] ?? [])->unique()->reject(fn ($id) => (int) $id === (int) $superId)->values();

        DB::transaction(function () use ($role, $ids) {
            $now = now();
            DB::table('role_asignable')->where('role_id', $role->id)->delete();
            if ($ids->isNotEmpty()) {
                DB::table('role_asignable')->insert($ids->map(fn ($id) => [
                    'role_id' => $role->id, 'asignable_id' => $id, 'created_at' => $now, 'updated_at' => $now,
                ])->all());
            }
            $role->forceFill(['config_asignables' => true])->save();
        });

        return response()->json(['success' => true, 'message' => "Roles asignables de {$role->nombre} actualizados.", 'asignables' => $ids]);
    }

    /** Crear un permiso manual (función que no corresponde a una ruta, para usar con @puede en las vistas). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'clave'       => ['required', 'string', 'max:191', 'regex:/^[a-z0-9_.\-]+$/', Rule::unique('permisos', 'clave')],
            'nombre'      => ['required', 'string', 'max:150'],
            'modulo'      => ['required', 'string', 'max:120'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ], [
            'clave.regex'  => 'La clave solo puede tener minúsculas, números, puntos, guiones y guion bajo (ej: usuarios.exportar).',
            'clave.unique' => 'Ya existe un permiso con esa clave.',
        ]);

        $p = Permiso::create($data + ['tipo' => 'personalizado', 'origen' => 'manual', 'activo' => true, 'editado' => true]);
        // Igual que las funciones nuevas: disponible para todos los roles hasta que se restrinja
        $now = now();
        DB::table('permiso_role')->insertOrIgnore(Role::pluck('id')->map(fn ($rid) => [
            'permiso_id' => $p->id, 'role_id' => $rid, 'created_at' => $now, 'updated_at' => $now,
        ])->all());
        GestorPermisos::limpiarCache();

        return response()->json(['success' => true, 'message' => 'Permiso creado.'] + $this->datos());
    }

    /** Editar nombre, módulo, descripción o estado de un permiso. */
    public function update(Request $request, Permiso $permiso)
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:150'],
            'modulo'      => ['required', 'string', 'max:120'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'activo'      => ['required', 'boolean'],
        ]);

        $permiso->fill($data + ['nuevo' => false]);
        if ($permiso->isDirty(['nombre', 'modulo'])) {
            $permiso->editado = true;
        }
        $permiso->save();
        GestorPermisos::limpiarCache();

        return response()->json(['success' => true, 'message' => 'Permiso actualizado.', 'permiso' => $permiso]);
    }

    public function destroy(Permiso $permiso)
    {
        if ($permiso->origen === 'ruta' && !$permiso->huerfano) {
            return response()->json(['success' => false, 'message' => 'Este permiso corresponde a una función activa del aplicativo. Puede desactivarlo, pero no eliminarlo.'], 422);
        }
        $permiso->delete();
        GestorPermisos::limpiarCache();

        return response()->json(['success' => true, 'message' => 'Permiso eliminado.']);
    }

    /** Marcar como revisadas las funciones nuevas. */
    public function revisados()
    {
        Permiso::where('nuevo', true)->update(['nuevo' => false]);
        return response()->json(['success' => true, 'message' => 'Funciones nuevas marcadas como revisadas.']);
    }
}
