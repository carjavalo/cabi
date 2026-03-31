<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Servicio;
use App\Models\Vinculacion;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $servicios = Servicio::orderBy('nombre')->get();
        $vinculaciones = Vinculacion::orderBy('nombre')->get();
        $cargos = \App\Models\Cargo::orderBy('nombre')->get();
        return view('auth.register', compact('servicios','vinculaciones','cargos'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'apellido1' => ['nullable','string','max:255'],
            'apellido2' => ['nullable','string','max:255'],
            'identificacion' => ['nullable','string','max:100'],
            'servicio_id' => ['nullable','integer','exists:servicios,id'],
            'tipo_vinculacion_id' => ['nullable','integer','exists:vinculaciones,id'],
            'cargo' => ['nullable','string','max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $servicioNombre = null;
        $tipoNombre = null;
        if ($request->filled('servicio_id')) {
            $s = Servicio::find($request->servicio_id);
            $servicioNombre = $s->nombre ?? null;
        }
        if ($request->filled('tipo_vinculacion_id')) {
            $v = Vinculacion::find($request->tipo_vinculacion_id);
            $tipoNombre = $v->nombre ?? null;
        }

        $user = User::create([
            'name' => $request->name,
            'apellido1' => $request->apellido1,
            'apellido2' => $request->apellido2,
            'identificacion' => $request->identificacion,
            'servicio_id' => $request->servicio_id ?? null,
            'servicio' => $servicioNombre,
            'tipo_vinculacion_id' => $request->tipo_vinculacion_id ?? null,
            'tipo_vinculacion' => $tipoNombre,
            'cargo' => $request->cargo ?? '',
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'Usuario',
        ]);

        event(new Registered($user));

        // Disparar notificación de verificación de correo
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            // Silenciar si no hay configuración de mail en entorno de desarrollo
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
