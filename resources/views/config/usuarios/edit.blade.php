@extends('layouts.app')

@section('title','Editar Usuario')
@section('header','Editar Usuario')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('config.usuarios.update',$user->id) }}">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="name">Nombre</label>
                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$user->name) }}" required />
                    @error('name')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email',$user->email) }}" required />
                    @error('email')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="apellido1">Apellido 1</label>
                    <input id="apellido1" name="apellido1" type="text" class="form-control" value="{{ old('apellido1',$user->apellido1) }}" />
                </div>
                <div class="form-group col-md-6">
                    <label for="apellido2">Apellido 2</label>
                    <input id="apellido2" name="apellido2" type="text" class="form-control" value="{{ old('apellido2',$user->apellido2) }}" />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="identificacion">Identificación</label>
                    <input id="identificacion" name="identificacion" type="text" class="form-control" value="{{ old('identificacion',$user->identificacion) }}" />
                </div>
                <div class="form-group col-md-6">
                    <label for="servicio_id">Servicio</label>
                    <select id="servicio_id" name="servicio_id" class="form-control">
                        <option value="">Seleccione</option>
                        @if(isset($servicios) && $servicios->count())
                            @foreach($servicios as $s)
                                <option value="{{ $s->id }}" {{ old('servicio_id',$user->servicio_id) == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                            @endforeach
                        @endif
                    </select>
                    <small class="form-text text-muted">Seleccione el servicio correspondiente.</small>
                </div>
            </div>

            <div class="form-group">
                <label for="tipo_vinculacion_id">Tipo de vinculación</label>
                <select id="tipo_vinculacion_id" name="tipo_vinculacion_id" class="form-control">
                    <option value="">Seleccione</option>
                    @if(isset($vinculaciones) && $vinculaciones->count())
                        @foreach($vinculaciones as $v)
                            <option value="{{ $v->id }}" {{ old('tipo_vinculacion_id',$user->tipo_vinculacion_id)==$v->id ? 'selected' : '' }}>{{ $v->nombre }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="role">Rol</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="">Seleccione</option>
                        @if(Auth::check() && Auth::user()->role == 'Super Admin')
                        <option value="Super Admin" {{ old('role',$user->role)=='Super Admin' ? 'selected' : '' }}>Super Admin</option>
                        @endif
                        @if(Auth::check() && in_array(Auth::user()->role,['Super Admin','Administrador']))
                        <option value="Administrador" {{ old('role',$user->role)=='Administrador' ? 'selected' : '' }}>Administrador</option>
                        @endif
                        <option value="Instructor GYM" {{ old('role',$user->role)=='Instructor GYM' ? 'selected' : '' }}>Instructor GYM</option>
                        <option value="Operador" {{ old('role',$user->role)=='Operador' ? 'selected' : '' }}>Operador</option>
                        <option value="Usuario" {{ old('role',$user->role)=='Usuario' ? 'selected' : '' }}>Usuario</option>
                    </select>
                    <small class="form-text text-muted">Cambie el rol del usuario si es necesario.</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="password">Contraseña (dejar vacío para mantener actual)</label>
                    <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" />
                    @error('password')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="password_confirmation">Confirmar contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" />
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('config.usuarios.index') }}" class="btn btn-secondary mr-2">Cancelar</a>
                <button class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>
@endsection
