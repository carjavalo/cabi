@extends('layouts.app')

@section('title','Gestión de Servicios')
@section('header','Gestión de Servicios')

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
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="corporate-gradient rounded-3 shadow-lg mb-4 p-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white mb-1 fw-bold">
                    <i class="fas fa-briefcase me-2"></i>Gestión de Servicios
                </h2>
                <p class="text-white-50 mb-0 small">Administra los servicios y unidades del hospital</p>
            </div>
            <a href="{{ route('config.servicios.create') }}" class="btn btn-light btn-lg shadow-sm hover-scale">
                <i class="fas fa-plus me-2"></i>Crear Servicio
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search Filter -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('config.servicios.index') }}" class="row g-2 align-items-center">
                <div class="col-md-8 col-lg-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="buscar" class="form-control border-start-0" 
                               placeholder="Buscar servicio por nombre..." 
                               value="{{ request('buscar') }}">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Buscar
                    </button>
                    @if(request('buscar'))
                        <a href="{{ route('config.servicios.index') }}" class="btn btn-outline-secondary ms-1">
                            <i class="fas fa-times me-1"></i>Limpiar
                        </a>
                    @endif
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
                            <th class="px-4 py-3 text-muted fw-bold small" style="width: 100px;">#</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Nombre del Servicio</th>
                            <th class="px-4 py-3 text-muted fw-bold small text-center" style="width: 200px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($servicios as $s)
                        <tr class="border-bottom">
                            <td class="px-4 py-3">
                                <span class="badge bg-light text-dark fs-6">{{ $s->id }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-hospital text-primary"></i>
                                    </div>
                                    <span class="fw-semibold">{{ $s->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('config.servicios.edit',$s->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       data-bs-toggle="tooltip" 
                                       title="Editar servicio">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('config.servicios.destroy',$s->id) }}" 
                                          method="POST" 
                                          style="display:inline-block" 
                                          onsubmit="return confirm('¿Está seguro de eliminar este servicio?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="tooltip" 
                                                title="Eliminar servicio">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $servicios->links() }}
        </div>
    </div>
</div>
@endsection
