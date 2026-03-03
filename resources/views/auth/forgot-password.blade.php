@extends('layouts.app')

@section('title','Restablecer contraseña')
@section('header','Restablecer contraseña')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-5 col-lg-4 mx-auto">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-3">Ingrese su correo y le enviaremos un enlace para restablecer su contraseña.</p>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus />
                        @error('email') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary">Enviar enlace de restablecimiento</button>
                    </div>
                </form>

                <div class="mt-3 text-center">
                    <a href="{{ url('/login') }}">Volver a iniciar sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
