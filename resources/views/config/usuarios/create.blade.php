@extends('layouts.app')

@section('title','Crear Usuario')
@section('header','Crear Usuario')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('config.usuarios.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="name">Nombre</label>
                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required />
                    @error('name')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required />
                    @error('email')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="apellido1">Apellido 1</label>
                    <input id="apellido1" name="apellido1" type="text" class="form-control" value="{{ old('apellido1') }}" />
                </div>
                <div class="form-group col-md-6">
                    <label for="apellido2">Apellido 2</label>
                    <input id="apellido2" name="apellido2" type="text" class="form-control" value="{{ old('apellido2') }}" />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="identificacion">Identificación</label>
                    <input id="identificacion" name="identificacion" type="text" class="form-control" value="{{ old('identificacion') }}" />
                </div>
                <div class="form-group col-md-6">
                    <label for="servicio_id">Servicio</label>
                    <select id="servicio_id" name="servicio_id" class="form-control">
                        <option value="">Seleccione</option>
                        @if(isset($servicios) && $servicios->count())
                            @foreach($servicios as $s)
                                <option value="{{ $s->id }}" {{ old('servicio_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                            @endforeach
                        @endif
                    </select>
                    <small class="form-text text-muted">Seleccione el servicio correspondiente.</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="tipo_vinculacion_id">Tipo de vinculación</label>
                    <select id="tipo_vinculacion_id" name="tipo_vinculacion_id" class="form-control">
                        <option value="">Seleccione</option>
                        @if(isset($vinculaciones) && $vinculaciones->count())
                            @foreach($vinculaciones as $v)
                                <option value="{{ $v->id }}" {{ old('tipo_vinculacion_id')==$v->id ? 'selected' : '' }}>{{ $v->nombre }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="cargo">Cargo</label>
                    <select id="cargo" name="cargo" class="form-control">
                        <option value="">Seleccione cargo</option>
                        @if(isset($cargos) && $cargos->count())
                            @foreach($cargos as $c)
                                <option value="{{ $c->nombre }}" {{ old('cargo') == $c->nombre ? 'selected' : '' }}>{{ $c->nombre }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="role">Rol</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="">Seleccione</option>
                        @if(Auth::check() && Auth::user()->role == 'Super Admin')
                        <option value="Super Admin" {{ old('role')=='Super Admin' ? 'selected' : '' }}>Super Admin</option>
                        @endif
                        @if(Auth::check() && in_array(Auth::user()->role,['Super Admin','Administrador']))
                        <option value="Administrador" {{ old('role')=='Administrador' ? 'selected' : '' }}>Administrador</option>
                        @endif
                        <option value="Instructor GYM" {{ old('role')=='Instructor GYM' ? 'selected' : '' }}>Instructor GYM</option>
                        <option value="Operador" {{ old('role')=='Operador' ? 'selected' : '' }}>Operador</option>
                        <option value="Usuario" {{ old('role')=='Usuario' ? 'selected' : '' }}>Usuario</option>
                    </select>
                    <small class="form-text text-muted">Seleccione el rol que tendrá el usuario al crearlo.</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="password">Contraseña</label>
                    <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required />
                    @error('password')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="password_confirmation">Confirmar contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required />
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('config.usuarios.index') }}" class="btn btn-secondary mr-2">Cancelar</a>
                <button class="btn btn-primary">Crear</button>
            </div>
        </form>
    </div>
</div>
@endsection
