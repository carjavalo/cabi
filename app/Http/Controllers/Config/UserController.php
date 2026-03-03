<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vinculacion;
use App\Models\Servicio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Show rules:
        // - Super Admin sees all
        // - Administrador sees all except users with role 'Super Admin'
        // - Operador sees users except 'Super Admin' and 'Administrador'
        if (!Auth::check()) {
            $users = User::whereNotIn('role',['Super Admin','Administrador'])->orderBy('id','desc')->paginate(15);
        } else {
            $current = Auth::user()->role;
            if ($current === 'Super Admin') {
                $users = User::orderBy('id','desc')->paginate(15);
            } elseif ($current === 'Administrador') {
                $users = User::where('role','!=','Super Admin')->orderBy('id','desc')->paginate(15);
            } elseif ($current === 'Operador') {
                $users = User::whereNotIn('role',['Super Admin','Administrador'])->orderBy('id','desc')->paginate(15);
            } else {
                // default: hide super admin and administrador
                $users = User::whereNotIn('role',['Super Admin','Administrador'])->orderBy('id','desc')->paginate(15);
            }
        }
        return view('config.usuarios.index', compact('users'));
    }

    public function create()
    {
        $vinculaciones = Vinculacion::orderBy('nombre')->get();
        $servicios = Servicio::orderBy('nombre')->get();
        return view('config.usuarios.create', compact('vinculaciones','servicios'));
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
            'role' => ['required','string','in:Super Admin,Administrador,Operador,Usuario'],
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

        $user = User::create([
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
        ]);

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
        return view('config.usuarios.edit', compact('user','vinculaciones','servicios'));
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
            'role' => ['required','string','in:Super Admin,Administrador,Operador,Usuario'],
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
        $user->tipo_vinculacion = $data['tipo_vinculacion'] ?? null;
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
}
