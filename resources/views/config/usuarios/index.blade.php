@extends('layouts.app')

@section('title','Gestión de Usuarios')
@section('header','Gestión de Usuarios')

@push('head')
<style>
    .corporate-gradient {
        background: linear-gradient(135deg, #2e3a75 0%, #1e2a55 100%);
    }
    .hover-scale {
        transition: transform 0.2s ease;
    }
    .hover-scale:hover {
        transform: translateY(-2px);
    }
    .badge-modern {
        padding: 0.35rem 0.75rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="corporate-gradient rounded-3 shadow-lg mb-4 p-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white mb-1 fw-bold">
                    <i class="fas fa-users me-2"></i>Gestión de Usuarios
                </h2>
                <p class="text-white-50 mb-0 small">Administra los usuarios del sistema</p>
            </div>
            <a href="{{ route('config.usuarios.create') }}" class="btn btn-light btn-lg shadow-sm hover-scale">
                <i class="fas fa-user-plus me-2"></i>Crear Usuario
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search Form -->
    <div class="card mb-4 border-0 shadow-sm" style="max-width: 800px;">
        <div class="card-body">
            <form method="GET" action="{{ route('config.usuarios.index') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label for="search" class="form-label text-muted small fw-bold mb-1">Buscar por nombre, identificación...</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control form-control-sm" id="search" name="search" value="{{ request('search') }}" placeholder="Ej: Juan, 12345678">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="role_filter" class="form-label text-muted small fw-bold mb-1">Filtrar por Rol</label>
                    <select id="role_filter" name="role_filter" class="form-select form-select-sm text-secondary">
                        <option value="">Todos los roles</option>
                        @if(Auth::check() && Auth::user()->role == 'Super Admin')
                        <option value="Super Admin" {{ request('role_filter') == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                        @endif
                        @if(Auth::check() && in_array(Auth::user()->role, ['Super Admin','Administrador']))
                        <option value="Administrador" {{ request('role_filter') == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                        @endif
                        <option value="Operador" {{ request('role_filter') == 'Operador' ? 'selected' : '' }}>Operador</option>
                        <option value="Instructor GYM" {{ request('role_filter') == 'Instructor GYM' ? 'selected' : '' }}>Instructor GYM</option>
                        <option value="Usuario" {{ request('role_filter') == 'Usuario' ? 'selected' : '' }}>Usuario</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-primary shadow-sm hover-scale me-1">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('config.usuarios.index') }}" class="btn btn-sm btn-light border shadow-sm hover-scale">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-lg rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="px-4 py-3 text-muted fw-bold small">#</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Nombre</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Apellidos</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Email</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Servicio</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Vinculación</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Cargo</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Rol</th>
                            <th class="px-4 py-3 text-muted fw-bold small text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                        <tr class="border-bottom">
                            <td class="px-4 py-3">
                                <span class="badge bg-light text-dark">{{ $u->id }}</span>
                            </td>
                            <td class="px-4 py-3 fw-semibold">{{ $u->name }}</td>
                            <td class="px-4 py-3">{{ $u->apellido1 }} {{ $u->apellido2 }}</td>
                            <td class="px-4 py-3">
                                <small class="text-muted">
                                    <i class="fas fa-envelope me-1"></i>{{ $u->email }}
                                </small>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-info bg-opacity-10 text-info badge-modern">
                                    {{ $u->servicio }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary badge-modern">
                                    {{ $u->tipo_vinculacion }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted small">
                                {{ $u->cargo ?: 'N/A' }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $roleClass = 'bg-secondary';
                                    $roleIcon = 'user';
                                    if(trim($u->role) == 'Super Admin') {
                                        $roleClass = 'bg-danger';
                                        $roleIcon = 'user-shield';
                                    } elseif(trim($u->role) == 'Administrador') {
                                        $roleClass = 'bg-warning';
                                        $roleIcon = 'user-cog';
                                    } elseif(trim($u->role) == 'Operador') {
                                        $roleClass = 'bg-info';
                                        $roleIcon = 'user-tie';
                                    } elseif(trim($u->role) == 'Instructor GYM') {
                                        $roleClass = 'bg-success';
                                        $roleIcon = 'dumbbell';
                                    }
                                @endphp
                                <span class="badge {{ $roleClass }} badge-modern">
                                    <i class="fas fa-{{ $roleIcon }} me-1"></i>{{ $u->role }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('config.usuarios.edit',$u->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       data-bs-toggle="tooltip" 
                                       title="Editar usuario">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(Auth::check() && Auth::user()->role === 'Super Admin' && !$u->hasVerifiedEmail())
                                    <form action="{{ route('config.usuarios.verify-email', $u->id) }}" 
                                          method="POST" 
                                          style="display:inline-block" 
                                          onsubmit="return confirm('¿Aprobar verificación de correo para {{ $u->name }}?');">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success" 
                                                data-bs-toggle="tooltip" 
                                                title="Aprobar verificación de correo">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if(Auth::check() && Auth::user()->role === 'Super Admin' && $u->hasVerifiedEmail())
                                    <span class="btn btn-sm btn-success disabled" 
                                          data-bs-toggle="tooltip" 
                                          title="Correo verificado">
                                        <i class="fas fa-envelope-open-text"></i>
                                    </span>
                                    @endif
                                    @if(!(Auth::check() && Auth::user()->role == 'Operador'))
                                    <form action="{{ route('config.usuarios.destroy',$u->id) }}" 
                                          method="POST" 
                                          style="display:inline-block" 
                                          onsubmit="return confirm('¿Está seguro de eliminar este usuario?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="tooltip" 
                                                title="Eliminar usuario">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
