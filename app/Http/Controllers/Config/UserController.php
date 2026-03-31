<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vinculacion;
use App\Models\Servicio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Show rules:
        // - Super Admin sees all
        // - Administrador sees all except users with role 'Super Admin'
        // - Operador sees users except 'Super Admin' and 'Administrador'
        if (!Auth::check()) {
            $query->whereNotIn('role', ['Super Admin', 'Administrador']);
        } else {
            $current = Auth::user()->role;
            if ($current === 'Administrador') {
                $query->where('role', '!=', 'Super Admin');
            } elseif ($current !== 'Super Admin') {
                // Para Operador, Instructor GYM, Usuario y otros
                $query->whereNotIn('role', ['Super Admin', 'Administrador']);
            }
        }

        // Búsqueda por texto (nombre, apellidos, email, identificacion)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('apellido1', 'like', "%{$search}%")
                  ->orWhere('apellido2', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('identificacion', 'like', "%{$search}%");
            });
        }

        // Filtro por rol
        if ($request->filled('role_filter')) {
            $query->where('role', $request->role_filter);
        }

        $users = $query->orderBy('id', 'desc')->paginate(15)->appends($request->all());

        return view('config.usuarios.index', compact('users'));
    }

    public function create()
    {
        $vinculaciones = Vinculacion::orderBy('nombre')->get();
        $servicios = Servicio::orderBy('nombre')->get();

        // Auto-crear tabla cargos si no existe
        if (!Schema::hasTable('cargos')) {
            Schema::create('cargos', function ($table) {
                $table->id();
                $table->string('nombre', 80);
                $table->string('descripcion', 200)->nullable();
                $table->timestamps();
            });
        }
        // Auto-crear columna cargo en users si no existe
        if (!Schema::hasColumn('users', 'cargo')) {
            Schema::table('users', function ($table) {
                $table->string('cargo', 100)->default('')->after('password');
            });
        }

        $cargos = \App\Models\Cargo::orderBy('nombre')->get();
        return view('config.usuarios.create', compact('vinculaciones','servicios','cargos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'apellido1' => ['nullable','string','max:255'],
            'apellido2' => ['nullable','string','max:255'],
            'identificacion' => ['nullable','string','max:100'],
            'servicio_id' => ['nullable','integer','exists:servicios,id'],
            'tipo_vinculacion_id' => ['nullable','integer','exists:vinculaciones,id'],
            'cargo' => ['nullable', 'string', 'max:100'],
            'role' => ['required','string','in:Super Admin,Administrador,Operador,Usuario,Instructor GYM'],
            'email' => ['required','email','max:255','unique:'.User::class],
            'password' => ['required','confirmed','min:6'],
        ]);

        // Prevent non-super-admins from assigning Super Admin role
        if (($data['role'] ?? '') === 'Super Admin' && (!Auth::check() || Auth::user()->role !== 'Super Admin')) {
            return redirect()->back()->withInput()->with('error', 'No autorizado para asignar rol Super Admin.');
        }

        // Prevent Operador from assigning Super Admin or Administrador
        if (Auth::check() && Auth::user()->role === 'Operador' && in_array($data['role'] ?? '', ['Super Admin','Administrador'])) {
            return redirect()->back()->withInput()->with('error', 'No autorizado para asignar un rol superior.');
        }

        $userdata = [
            'name'=>$data['name'],
            'apellido1'=>$data['apellido1'] ?? null,
            'apellido2'=>$data['apellido2'] ?? null,
            'identificacion'=>$data['identificacion'] ?? null,
            'servicio_id'=>$data['servicio_id'] ?? null,
            'servicio'=> isset($data['servicio_id']) ? Servicio::find($data['servicio_id'])->nombre ?? null : null,
            'tipo_vinculacion_id'=>$data['tipo_vinculacion_id'] ?? null,
            'tipo_vinculacion'=> isset($data['tipo_vinculacion_id']) ? Vinculacion::find($data['tipo_vinculacion_id'])->nombre ?? null : null,
            'email'=>$data['email'],
            'password'=>Hash::make($data['password']),
            'role'=>$data['role'] ?? 'Usuario',
        ];

        if (Schema::hasColumn('users', 'cargo')) {
            $userdata['cargo'] = $data['cargo'] ?? '';
        }

        $user = User::create($userdata);

        return redirect()->route('config.usuarios.index')->with('success','Usuario creado correctamente.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        // Prevent non-super-admins from viewing/editing Super Admin users
        if ($user->role === 'Super Admin' && (!Auth::check() || Auth::user()->role !== 'Super Admin')) {
            return redirect()->route('config.usuarios.index')->with('error', 'No autorizado para ver o editar este usuario.');
        }
        // Prevent Operador from viewing/editing Administrador users
        if ($user->role === 'Administrador' && Auth::check() && Auth::user()->role === 'Operador') {
            return redirect()->route('config.usuarios.index')->with('error', 'No autorizado para ver o editar este usuario.');
        }
        $vinculaciones = Vinculacion::orderBy('nombre')->get();
        $servicios = Servicio::orderBy('nombre')->get();

        try {
            $cargos = \App\Models\Cargo::orderBy('nombre')->get();
        } catch (\Exception $e) {
            $cargos = collect();
        }

        return view('config.usuarios.edit', compact('user','vinculaciones','servicios', 'cargos'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent non-super-admins from updating Super Admin users
        if ($user->role === 'Super Admin' && (!Auth::check() || Auth::user()->role !== 'Super Admin')) {
            return redirect()->route('config.usuarios.index')->with('error', 'No autorizado para modificar este usuario.');
        }

        // Prevent Operador from updating Administrador users
        if ($user->role === 'Administrador' && Auth::check() && Auth::user()->role === 'Operador') {
            return redirect()->route('config.usuarios.index')->with('error', 'No autorizado para modificar este usuario.');
        }

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'apellido1' => ['nullable','string','max:255'],
            'apellido2' => ['nullable','string','max:255'],
            'identificacion' => ['nullable','string','max:100'],
            'servicio_id' => ['nullable','integer','exists:servicios,id'],
            'tipo_vinculacion_id' => ['nullable','integer','exists:vinculaciones,id'],
            'cargo' => ['nullable', 'string', 'max:100'],
            'role' => ['required','string','in:Super Admin,Administrador,Operador,Usuario,Instructor GYM'],
            'email' => ['required','email','max:255','unique:users,email,'.$user->id],
            'password' => ['nullable','confirmed','min:6'],
        ]);

        $user->name = $data['name'];
        $user->apellido1 = $data['apellido1'] ?? null;
        $user->apellido2 = $data['apellido2'] ?? null;
        $user->identificacion = $data['identificacion'] ?? null;
        $user->servicio_id = $data['servicio_id'] ?? null;
        $user->servicio = isset($data['servicio_id']) ? Servicio::find($data['servicio_id'])->nombre ?? null : null;
        $user->tipo_vinculacion_id = $data['tipo_vinculacion_id'] ?? null;
        $user->tipo_vinculacion = isset($data['tipo_vinculacion_id']) ? Vinculacion::find($data['tipo_vinculacion_id'])->nombre ?? null : null;
        if (Schema::hasColumn('users', 'cargo')) {
            $user->cargo = $data['cargo'] ?? '';
        }
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        // Prevent non-super-admins from assigning Super Admin role
        if (!empty($data['role'])) {
            if ($data['role'] === 'Super Admin' && (!Auth::check() || Auth::user()->role !== 'Super Admin')) {
                return redirect()->back()->withInput()->with('error', 'No autorizado para asignar rol Super Admin.');
            }
            // Prevent Operador from assigning Administrador or Super Admin
            if (Auth::check() && Auth::user()->role === 'Operador' && in_array($data['role'], ['Super Admin','Administrador'])) {
                return redirect()->back()->withInput()->with('error', 'No autorizado para asignar un rol superior.');
            }
            $user->role = $data['role'];
        }
        $user->save();

        return redirect()->route('config.usuarios.index')->with('success','Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        // Prevent Operador from deleting any user
        if (Auth::check() && Auth::user()->role === 'Operador') {
            return redirect()->route('config.usuarios.index')->with('error', 'No autorizado para eliminar usuarios.');
        }

        // Also protect Super Admin from deletion by non-super-admins
        if ($user->role === 'Super Admin' && (!Auth::check() || Auth::user()->role !== 'Super Admin')) {
            return redirect()->route('config.usuarios.index')->with('error', 'No autorizado para eliminar este usuario.');
        }

        $user->delete();
        return redirect()->route('config.usuarios.index')->with('success','Usuario eliminado.');
    }

    /**
     * Verificar manualmente el email de un usuario (solo Super Admin).
     */
    public function verifyEmail($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'Super Admin') {
            return redirect()->route('config.usuarios.index')->with('error', 'No autorizado para realizar esta acción.');
        }

        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('config.usuarios.index')->with('info', 'El usuario ya tiene el correo verificado.');
        }

        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('config.usuarios.index')->with('success', 'Correo del usuario "'.$user->name.'" verificado exitosamente.');
    }
}
