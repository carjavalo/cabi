@extends('layouts.app')

@section('title','Registrarse')
@section('header','Registrarse')

@section('content')
<div class="row justify-content-center mt-3">
    <div class="col-md-7">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header text-white text-center py-3" style="background: linear-gradient(135deg, #2e3a75 0%, #1a234f 100%); border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;">
                <h4 class="mb-0 font-weight-bold">Crear una cuenta</h4>
                <p class="mb-0 small text-white-50">Ingrese sus datos para registrarse en CABI</p>
            </div>
            <div class="card-body p-4 bg-white">
                <form method="POST" action="{{ url('/register') }}">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="name" class="text-secondary font-weight-bold small">Nombre</label>
                        <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus style="border-radius: 6px; border: 1px solid #ced4da;" placeholder="Ingrese su nombre" />
                        @error('name') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                    </div>

                    <div class="form-row mb-3">
                        <div class="form-group col-md-6 mb-2 mb-md-0">
                            <label for="apellido1" class="text-secondary font-weight-bold small">Primer Apellido</label>
                            <input id="apellido1" name="apellido1" type="text" class="form-control @error('apellido1') is-invalid @enderror" value="{{ old('apellido1') }}" style="border-radius: 6px; border: 1px solid #ced4da;" placeholder="Primer apellido" />
                            @error('apellido1') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="apellido2" class="text-secondary font-weight-bold small">Segundo Apellido</label>
                            <input id="apellido2" name="apellido2" type="text" class="form-control @error('apellido2') is-invalid @enderror" value="{{ old('apellido2') }}" style="border-radius: 6px; border: 1px solid #ced4da;" placeholder="Segundo apellido" />
                            @error('apellido2') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                        </div>
                    </div>

                    <div class="form-row mb-3">
                        <div class="form-group col-md-6 mb-2 mb-md-0">
                            <label for="identificacion" class="text-secondary font-weight-bold small">Identificación</label>
                            <input id="identificacion" name="identificacion" type="text" class="form-control @error('identificacion') is-invalid @enderror" value="{{ old('identificacion') }}" style="border-radius: 6px; border: 1px solid #ced4da;" placeholder="Número de documento" />
                            @error('identificacion') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="servicio_id" class="text-secondary font-weight-bold small">Servicio / Unidad</label>
                            <select id="servicio_id" name="servicio_id" class="form-control @error('servicio_id') is-invalid @enderror" style="border-radius: 6px; border: 1px solid #ced4da;">
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
                    <div class="form-row mb-3">
                        <div class="form-group col-md-6 mb-2 mb-md-0">
                            <label for="tipo_vinculacion_id" class="text-secondary font-weight-bold small">Tipo de vinculación</label>
                            <select id="tipo_vinculacion_id" name="tipo_vinculacion_id" class="form-control @error('tipo_vinculacion_id') is-invalid @enderror" style="border-radius: 6px; border: 1px solid #ced4da;">
                                <option value="">Seleccione vinculación</option>
                                @if(isset($vinculaciones) && $vinculaciones->count())
                                    @foreach($vinculaciones as $v)
                                        <option value="{{ $v->id }}" {{ old('tipo_vinculacion_id') == $v->id ? 'selected' : '' }}>{{ $v->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('tipo_vinculacion_id') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="cargo" class="text-secondary font-weight-bold small">Cargo</label>
                            <select id="cargo" name="cargo" class="form-control @error('cargo') is-invalid @enderror" style="border-radius: 6px; border: 1px solid #ced4da;">
                                <option value="">Seleccione cargo</option>
                                @if(isset($cargos) && $cargos->count())
                                    @foreach($cargos as $c)
                                        <option value="{{ $c->nombre }}" {{ old('cargo') == $c->nombre ? 'selected' : '' }}>{{ $c->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('cargo') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email" class="text-secondary font-weight-bold small">Correo Electrónico</label>
                        <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required style="border-radius: 6px; border: 1px solid #ced4da;" placeholder="ejemplo@correo.com" />
                        @error('email') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                    </div>

                    <div class="form-row mb-3">
                        <div class="form-group col-md-6 mb-2 mb-md-0">
                            <label for="password" class="text-secondary font-weight-bold small">Contraseña</label>
                            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required style="border-radius: 6px; border: 1px solid #ced4da;" placeholder="Min. 8 caracteres" />
                            @error('password') <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="password_confirmation" class="text-secondary font-weight-bold small">Confirmar contraseña</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required style="border-radius: 6px; border: 1px solid #ced4da;" placeholder="Repita la contraseña" />
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button class="btn btn-block text-white font-weight-bold py-2" style="background-color: #2e3a75; border-color: #2e3a75; border-radius: 6px; box-shadow: 0 4px 6px rgba(46,58,117,0.2);">
                            Completar Registro <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </form>
                <div class="mt-3 text-center small text-muted">
                    <i class="fas fa-envelope mr-1"></i> Se enviará un correo para verificar su dirección.
                </div>
                <hr class="mt-3 mb-3">
                <div class="text-center small">
                    <span class="text-muted">¿Ya tienes cuenta?</span> 
                    <a href="{{ url('/login') }}" class="font-weight-bold" style="color: #2e3a75; text-decoration: none;">Inicia sesión aquí</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
