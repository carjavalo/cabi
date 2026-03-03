@extends('layouts.app')

@section('title','Iniciar sesión')
@section('header','Iniciar sesión')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ url('/login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Correo</label>
                        <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus />
                        @error('email') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required />
                        @error('password') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                    </div>

                    <div class="form-group d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Recuérdame</label>
                        </div>
                        <button class="btn btn-primary" type="submit">Iniciar sesión</button>
                    </div>

                    <div class="mt-3">
                        <a href="{{ url('/forgot-password') }}">¿Olvidó su contraseña?</a>
                    </div>
                </form>
                <div class="mt-3 text-center">
                    ¿No tienes una cuenta? <a href="{{ url('/register') }}">Regístrate</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
