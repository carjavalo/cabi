@extends('layouts.app')

@section('title','Editar Usuario')
@section('header','Editar Usuario')

@section('content')
<div class="row justify-content-center mb-5">
    <div class="col-lg-10 col-xl-9" style="zoom: 0.98;">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header text-white text-center py-3" style="background: linear-gradient(135deg, #2e3a75 0%, #1a234f 100%); border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;">
                <h4 class="mb-0 font-weight-bold"><i class="fas fa-user-edit mr-2"></i> Editar Usuario</h4>
                <p class="mb-0 small text-white-50">Actualice la informaci�n del usuario en el sistema</p>
            </div>
            
            <div class="card-body p-4 bg-white">
                <form method="POST" action="{{ route('config.usuarios.update',$user->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <h6 class="text-secondary border-bottom pb-2 mb-3"><i class="fas fa-address-card mr-1"></i> Informaci�n Personal</h6>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6 mb-3">
                            <label for="name" class="small text-muted font-weight-bold">Nombres <span class="text-danger">*</span></label>
                            <input id="name" name="name" type="text" class="form-control bg-light @error('name') is-invalid @enderror" value="{{ old('name',$user->name) }}" required placeholder="Nombres completos" style="border: 1px solid #ced4da; border-radius: 6px;" />
                            @error('name')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label for="identificacion" class="small text-muted font-weight-bold">N�mero de Identificaci�n</label>
                            <input id="identificacion" name="identificacion" type="text" class="form-control bg-light" value="{{ old('identificacion',$user->identificacion) }}" placeholder="Documento de identidad" style="border: 1px solid #ced4da; border-radius: 6px;" />
                        </div>
                    </div>

                    <div class="form-row mb-4">
                        <div class="form-group col-md-6 mb-3">
                            <label for="apellido1" class="small text-muted font-weight-bold">Primer Apellido</label>
                            <input id="apellido1" name="apellido1" type="text" class="form-control bg-light" value="{{ old('apellido1',$user->apellido1) }}" placeholder="Primer apellido" style="border: 1px solid #ced4da; border-radius: 6px;" />
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label for="apellido2" class="small text-muted font-weight-bold">Segundo Apellido</label>
                            <input id="apellido2" name="apellido2" type="text" class="form-control bg-light" value="{{ old('apellido2',$user->apellido2) }}" placeholder="Segundo apellido" style="border: 1px solid #ced4da; border-radius: 6px;" />
                        </div>
                    </div>

                    <h6 class="text-secondary border-bottom pb-2 mb-3"><i class="fas fa-briefcase mr-1"></i> Informaci�n Institucional</h6>

                    <div class="form-row">
                        <div class="form-group col-md-6 mb-3">
                            <label for="servicio_id" class="small text-muted font-weight-bold">Unidad / Servicio</label>
                            <select id="servicio_id" name="servicio_id" class="form-control custom-select bg-light" style="border: 1px solid #ced4da; border-radius: 6px;">
                                <option value="">Seleccione el servicio</option>
                                @if(isset($servicios) && $servicios->count())
                                    @foreach($servicios as $s)
                                        <option value="{{ $s->id }}" {{ old('servicio_id',$user->servicio_id) == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label for="role" class="small text-muted font-weight-bold">Perfil de Acceso (Rol) <span class="text-danger">*</span></label>
                            <select id="role" name="role" class="form-control custom-select bg-light" required style="border: 1px solid #ced4da; border-radius: 6px;">
                                <option value="">Seleccione el rol</option>
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
                        </div>
                    </div>

                    <div class="form-row mb-4">
                        <div class="form-group col-md-6 mb-3">
                            <label for="tipo_vinculacion_id" class="small text-muted font-weight-bold">Tipo de vinculaci�n</label>
                            <select id="tipo_vinculacion_id" name="tipo_vinculacion_id" class="form-control custom-select bg-light" style="border: 1px solid #ced4da; border-radius: 6px;">
                                <option value="">Seleccione vinculaci�n</option>
                                @if(isset($vinculaciones) && $vinculaciones->count())   
                                    @foreach($vinculaciones as $v)
                                        <option value="{{ $v->id }}" {{ old('tipo_vinculacion_id',$user->tipo_vinculacion_id)==$v->id ? 'selected' : '' }}>{{ $v->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label for="cargo" class="small text-muted font-weight-bold">Cargo Institucional</label>
                            <select id="cargo" name="cargo" class="form-control custom-select bg-light" style="border: 1px solid #ced4da; border-radius: 6px;">
                                <option value="">Seleccione cargo</option>
                                @if(isset($cargos) && $cargos->count())
                                    @foreach($cargos as $c)
                                        <option value="{{ $c->nombre }}" {{ old('cargo', $user->cargo) == $c->nombre ? 'selected' : '' }}>{{ $c->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <h6 class="text-secondary border-bottom pb-2 mb-3"><i class="fas fa-lock mr-1"></i> Credenciales de Acceso</h6>

                    <div class="form-row mb-2">
                        <div class="form-group col-12 mb-3">
                            <label for="email" class="small text-muted font-weight-bold">Correo Electr�nico <span class="text-danger">*</span></label>
                            <input id="email" name="email" type="email" class="form-control bg-light @error('email') is-invalid @enderror" value="{{ old('email',$user->email) }}" required placeholder="ejemplo@correo.com" style="border: 1px solid #ced4da; border-radius: 6px;" />
                            @error('email')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                    </div>

                    <div class="form-row mb-4">
                        <div class="form-group col-md-6 mb-3">
                            <label for="password" class="small text-muted font-weight-bold">Nueva Contrase�a <span class="text-info font-italic font-weight-normal">(dejar vac�o para mantener)</span></label>
                            <input id="password" name="password" type="password" class="form-control bg-light @error('password') is-invalid @enderror" placeholder="Dejar en blanco si no cambia" style="border: 1px solid #ced4da; border-radius: 6px;" />
                            @error('password')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label for="password_confirmation" class="small text-muted font-weight-bold">Confirmar nueva contrase�a</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control bg-light" placeholder="Repita la contrase�a si la cambia" style="border: 1px solid #ced4da; border-radius: 6px;" />
                        </div>
                    </div>

                    <div class="d-flex justify-content-end bg-light p-3 rounded" style="border: 1px solid #e9ecef;">
                        <a href="{{ route('config.usuarios.index') }}" class="btn btn-outline-secondary font-weight-bold mr-3 rounded-pill px-4" style="border-width: 2px;">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn text-white font-weight-bold rounded-pill px-4 shadow-sm" style="background-color: #2e3a75;">
                            <i class="fas fa-save mr-1"></i> Actualizar Usuario
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

