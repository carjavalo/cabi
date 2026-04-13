@extends('layouts.app')

@section('title','Capacitaciones')
@section('header','Capacitaciones')

@push('head')
<style>
    .cap-gradient { background: linear-gradient(135deg, #2f4185 0%, #48599e 100%); }
    .stat-card { border-radius: 1.5rem; padding: 1.5rem; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-3px); }
    .badge-activo { background: #d4edda; color: #155724; }
    .badge-finalizado { background: #f8d7da; color: #721c24; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="cap-gradient rounded-3 shadow-lg mb-4 p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2 class="text-white mb-1 fw-bold">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Capacitaciones
                </h2>
                <p class="text-white-50 mb-0 small">Consulta el listado de capacitaciones y sus detalles</p>
            </div>
            <!-- Botón Nueva Capacitación eliminado -->
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="stat-card bg-white shadow-sm border-0">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;background:#dde1ff;">
                        <i class="fas fa-chalkboard-teacher text-primary"></i>
                    </div>
                    <div>
                        <div class="h3 mb-0 fw-bold">{{ $capacitaciones->total() }}</div>
                        <small class="text-muted fw-bold text-uppercase">Total Capacitaciones</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-white shadow-sm border-0">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;background:#d4edda;">
                        <i class="fas fa-calendar-check text-success"></i>
                    </div>
                    <div>
                        <div class="h3 mb-0 fw-bold">{{ $activas }}</div>
                        <small class="text-muted fw-bold text-uppercase">Activas / Próximas</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-white shadow-sm border-0">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;background:#ffdeac;">
                        <i class="fas fa-user-check" style="color:#5e4000;"></i>
                    </div>
                    <div>
                        <div class="h3 mb-0 fw-bold">{{ $totalAsistieron }}</div>
                        <small class="text-muted fw-bold text-uppercase">Asistencias Registradas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-lg rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="px-4 py-3 text-muted fw-bold small">#</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Título</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Fecha</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Horario</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Ubicación</th>
                            <th class="px-4 py-3 text-muted fw-bold small">Instructor</th>
                            <th class="px-4 py-3 text-muted fw-bold small text-center">Citados</th>
                            <th class="px-4 py-3 text-muted fw-bold small text-center">Asistieron</th>
                            <th class="px-4 py-3 text-muted fw-bold small text-center">Estado</th>
                            <th class="px-4 py-3 text-muted fw-bold small text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($capacitaciones as $cap)
                        <tr>
                            <td class="px-4 py-3"><span class="badge bg-light text-dark">{{ $cap->id }}</span></td>
                            <td class="px-4 py-3 fw-semibold">{{ $cap->titulo }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($cap->fecha)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-muted small">
                                @if($cap->hora_inicio && $cap->hora_fin)
                                    {{ \Carbon\Carbon::parse($cap->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($cap->hora_fin)->format('H:i') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 small">{{ $cap->ubicacion ?: '—' }}</td>
                            <td class="px-4 py-3 small">{{ $cap->instructor ?: '—' }}</td>
                            
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-info bg-opacity-10 text-info" style="font-size:0.85rem;">{{ $cap->asistencias_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-success bg-opacity-10 text-success" style="font-size:0.85rem;">{{ $cap->asistieron_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($cap->activo && \Carbon\Carbon::parse($cap->fecha)->toDateString() >= now()->toDateString())
                                    <span class="badge badge-activo"><i class="fas fa-circle me-1" style="font-size:0.5rem;"></i>Activa</span>
                                @else
                                    <span class="badge badge-finalizado"><i class="fas fa-circle me-1" style="font-size:0.5rem;"></i>Finalizada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('config.capacitaciones.show', $cap->id) }}" class="btn btn-sm btn-outline-primary" title="Ver Detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('capacitaciones.edit_user', $cap->id) }}" class="btn btn-sm btn-outline-warning" title="Asignar Participantes">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open mb-3" style="font-size:3rem; opacity:0.5"></i>
                                <h5>No hay capacitaciones registradas</h5>
                                <p class="mb-0">Aún no se han creado capacitaciones.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($capacitaciones->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $capacitaciones->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
