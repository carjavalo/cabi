@extends('layouts.app')

@section('title','Registrarse')
@section('header','Registrarse')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ url('/register') }}">
                    @csrf

                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus />
                        @error('name') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="apellido1">Apellido 1</label>
                            <input id="apellido1" name="apellido1" type="text" class="form-control @error('apellido1') is-invalid @enderror" value="{{ old('apellido1') }}" />
                            @error('apellido1') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="apellido2">Apellido 2</label>
                            <input id="apellido2" name="apellido2" type="text" class="form-control @error('apellido2') is-invalid @enderror" value="{{ old('apellido2') }}" />
                            @error('apellido2') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="identificacion">Identificación</label>
                            <input id="identificacion" name="identificacion" type="text" class="form-control @error('identificacion') is-invalid @enderror" value="{{ old('identificacion') }}" />
                            @error('identificacion') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="servicio_id">Servicio / Unidad</label>
                            <select id="servicio_id" name="servicio_id" class="form-control @error('servicio_id') is-invalid @enderror">
                                <option value="">Seleccione servicio</option>
                                @if(isset($servicios) && $servicios->count())
                                    @foreach($servicios as $s)
                                        <option value="{{ $s->id }}" {{ old('servicio_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('servicio_id') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tipo_vinculacion_id">Tipo de vinculación</label>
                        <select id="tipo_vinculacion_id" name="tipo_vinculacion_id" class="form-control @error('tipo_vinculacion_id') is-invalid @enderror">
                            <option value="">Seleccione vinculación</option>
                            @if(isset($vinculaciones) && $vinculaciones->count())
                                @foreach($vinculaciones as $v)
                                    <option value="{{ $v->id }}" {{ old('tipo_vinculacion_id') == $v->id ? 'selected' : '' }}>{{ $v->nombre }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('tipo_vinculacion_id') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Correo</label>
                        <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required />
                        @error('email') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required />
                        @error('password') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirmar contraseña</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required />
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary">Registrarse</button>
                    </div>
                </form>
                <div class="mt-3 text-center text-muted" style="font-size:0.9rem">
                    Se enviará un correo electrónico para verificar su dirección.
                </div>
                <div class="mt-2 text-center">
                    ¿Ya tienes cuenta? <a href="{{ url('/login') }}">Inicia sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
