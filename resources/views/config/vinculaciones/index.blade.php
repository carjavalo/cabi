@extends('layouts.app')

@section('title','Gestión de Vinculaciones')
@section('header','Gestión de Vinculaciones')

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
                    <i class="fas fa-link me-2"></i>Gestión de Vinculaciones
                </h2>
                <p class="text-white-50 mb-0 small">Administra los tipos de vinculación laboral</p>
            </div>
            <a href="{{ route('config.vinculaciones.create') }}" class="btn btn-light btn-lg shadow-sm hover-scale">
                <i class="fas fa-plus me-2"></i>Crear Vinculación
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
                            <th class="px-4 py-3 text-muted fw-bold small" style="width: 100px;">#</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Tipo de Vinculación</th>
                            <th class="px-4 py-3 text-muted fw-bold small text-center" style="width: 200px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vinculaciones as $v)
                        <tr class="border-bottom">
                            <td class="px-4 py-3">
                                <span class="badge bg-light text-dark fs-6">{{ $v->id }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-user-tag text-success"></i>
                                    </div>
                                    <span class="fw-semibold">{{ $v->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('config.vinculaciones.edit',$v->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       data-bs-toggle="tooltip" 
                                       title="Editar vinculación">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('config.vinculaciones.destroy',$v->id) }}" 
                                          method="POST" 
                                          style="display:inline-block" 
                                          onsubmit="return confirm('¿Está seguro de eliminar esta vinculación?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="tooltip" 
                                                title="Eliminar vinculación">
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
            {{ $vinculaciones->links() }}
        </div>
    </div>
</div>
@endsection
