@extends('layouts.app')

@section('title','Crear Usuario')
@section('header','Crear Usuario')

@section('content')

<!-- Modal de errores de validación -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);">
                <h5 class="modal-title" id="errorModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Error al crear usuario
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <i class="fas fa-user-times text-danger" style="font-size: 3rem;"></i>
                </div>
                <p class="text-center text-muted mb-3">No se pudo crear el usuario por los siguientes motivos:</p>
                <ul class="list-group list-group-flush" id="errorList">
                    @foreach($errors->all() as $error)
                        <li class="list-group-item border-0 py-2">
                            <i class="fas fa-times-circle text-danger mr-2"></i>
                            @if(str_contains($error, 'identificacion') || str_contains($error, 'identificación'))
                                <strong>Cédula duplicada:</strong> Ya existe un usuario registrado con este número de identificación.
                            @elseif(str_contains($error, 'email') || str_contains($error, 'correo'))
                                <strong>Correo duplicado:</strong> Ya existe un usuario registrado con este correo electrónico.
                            @else
                                {{ $error }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">
                    <i class="fas fa-pencil-alt mr-1"></i> Corregir datos
                </button>
            </div>
        </div>
    </div>
</div>
<div class="row justify-content-center mb-5">
    <div class="col-lg-10 col-xl-9" style="zoom: 0.98;">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header text-white text-center py-3" style="background: linear-gradient(135deg, #2e3a75 0%, #1a234f 100%); border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;">
                <h4 class="mb-0 font-weight-bold"><i class="fas fa-user-plus mr-2"></i> Nuevo Usuario</h4>
                <p class="mb-0 small text-white-50">Ingrese la información para registrar a un nuevo usuario en el sistema</p>
            </div>
            
            <div class="card-body p-4 bg-white">
                <form method="POST" action="{{ route('config.usuarios.store') }}">
                    @csrf
                    
                    <h6 class="text-secondary border-bottom pb-2 mb-3"><i class="fas fa-address-card mr-1"></i> Información Personal</h6>
                    
                    <div class="form-row mb-3">
                        <div class="form-group col-md-6 mb-2 mb-md-0">
                            <label for="name" class="text-secondary font-weight-bold small">Nombres <span class="text-danger">*</span></label>
                            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Nombres completos" style="border-radius: 6px; border: 1px solid #ced4da;"/>
                            @error('name')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="identificacion" class="text-secondary font-weight-bold small">Número de Identificación</label>
                            <input id="identificacion" name="identificacion" type="text" class="form-control" value="{{ old('identificacion') }}" placeholder="Documento de identidad" style="border-radius: 6px; border: 1px solid #ced4da;"/>
                        </div>
                    </div>

                    <div class="form-row mb-3">
                        <div class="form-group col-md-6 mb-2 mb-md-0">
                            <label for="apellido1" class="text-secondary font-weight-bold small">Primer Apellido</label>
                            <input id="apellido1" name="apellido1" type="text" class="form-control" value="{{ old('apellido1') }}" placeholder="Primer apellido" style="border-radius: 6px; border: 1px solid #ced4da;"/>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="apellido2" class="text-secondary font-weight-bold small">Segundo Apellido</label>
                            <input id="apellido2" name="apellido2" type="text" class="form-control" value="{{ old('apellido2') }}" placeholder="Segundo apellido" style="border-radius: 6px; border: 1px solid #ced4da;"/>
                        </div>
                    </div>

                    <h6 class="text-secondary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-briefcase mr-1"></i> Información Institucional</h6>

                    <div class="form-row mb-3">
                        <div class="form-group col-md-6 mb-2 mb-md-0">
                            <label for="servicio_id" class="text-secondary font-weight-bold small">Unidad / Servicio</label>
                            <select id="servicio_id" name="servicio_id" class="form-control" style="border-radius: 6px; border: 1px solid #ced4da;">
                                <option value="">Seleccione el servicio</option>
                                @if(isset($servicios) && $servicios->count())
                                    @foreach($servicios as $s)
                                        <option value="{{ $s->id }}" {{ old('servicio_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="role" class="text-secondary font-weight-bold small">Perfil de Acceso (Rol) <span class="text-danger">*</span></label>
                            <select id="role" name="role" class="form-control" required style="border-radius: 6px; border: 1px solid #ced4da;">
                                <option value="">Seleccione el rol</option>
                                @if(Auth::check() && Auth::user()->role == 'Super Admin')
                                <option value="Super Admin" {{ old('role')=='Super Admin' ? 'selected' : '' }}>Super Admin</option>
                                @endif
                                @if(Auth::check() && in_array(Auth::user()->role,['Super Admin','Administrador']))
                                <option value="Administrador" {{ old('role')=='Administrador' ? 'selected' : '' }}>Administrador</option>
                                @endif
                                <option value="Instructor GYM" {{ old('role')=='Instructor GYM' ? 'selected' : '' }}>Instructor GYM</option>
                                <option value="Coordinador" {{ old('role')=='Coordinador' ? 'selected' : '' }}>Coordinador</option>
                                <option value="Operador" {{ old('role')=='Operador' ? 'selected' : '' }}>Operador</option>
                                <option value="Usuario" {{ old('role')=='Usuario' ? 'selected' : '' }}>Usuario</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row mb-3">
                        <div class="form-group col-md-6 mb-2 mb-md-0">
                            <label for="tipo_vinculacion_id" class="text-secondary font-weight-bold small">Tipo de vinculación</label>
                            <select id="tipo_vinculacion_id" name="tipo_vinculacion_id" class="form-control" style="border-radius: 6px; border: 1px solid #ced4da;">
                                <option value="">Seleccione vinculación</option>
                                @if(isset($vinculaciones) && $vinculaciones->count())
                                    @foreach($vinculaciones as $v)
                                        <option value="{{ $v->id }}" {{ old('tipo_vinculacion_id')==$v->id ? 'selected' : '' }}>{{ $v->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="cargo" class="text-secondary font-weight-bold small">Cargo Institucional</label>
                            <select id="cargo" name="cargo" class="form-control" style="border-radius: 6px; border: 1px solid #ced4da;">
                                <option value="">Seleccione cargo</option>
                                @if(isset($cargos) && $cargos->count())
                                    @foreach($cargos as $c)
                                        <option value="{{ $c->nombre }}" {{ old('cargo') == $c->nombre ? 'selected' : '' }}>{{ $c->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <h6 class="text-secondary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-lock mr-1"></i> Credenciales de Acceso</h6>

                    <div class="form-row mb-3">
                        <div class="form-group col-md-12 mb-2">
                            <label for="email" class="text-secondary font-weight-bold small">Correo Electrónico <span class="text-danger">*</span></label>
                            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="ejemplo@correo.com" style="border-radius: 6px; border: 1px solid #ced4da;"/>
                            @error('email')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                    </div>

                    <div class="form-row mb-3">
                        <div class="form-group col-md-6 mb-2 mb-md-0">
                            <label for="password" class="text-secondary font-weight-bold small">Contraseña <span class="text-danger">*</span></label>
                            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Mínimo 8 caracteres" style="border-radius: 6px; border: 1px solid #ced4da;"/>
                            @error('password')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="password_confirmation" class="text-secondary font-weight-bold small">Confirmar Contraseña <span class="text-danger">*</span></label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required placeholder="Repita la contraseña" style="border-radius: 6px; border: 1px solid #ced4da;"/>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <a href="{{ route('config.usuarios.index') }}" class="btn btn-light shadow-sm border mr-2 font-weight-bold" style="border-radius: 6px; padding: 10px 20px;">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn text-white shadow-sm font-weight-bold" style="background-color: #2e3a75; border-radius: 6px; padding: 10px 25px;">
                            <i class="fas fa-save mr-1"></i> Guardar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if($errors->any())
        $('#errorModal').modal('show');
    @endif
});
</script>
@endpush
