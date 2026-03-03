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
