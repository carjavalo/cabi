@extends('layouts.app')

@section('title', $capacitacion->titulo . ' - Asistencia')
@section('header','Detalle de Capacitación')

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .cap-card { background: #f5f2f9; border-radius: 1.5rem; padding: 2rem; }
    .cap-header { font-family: 'Manrope', sans-serif; }
    .cap-input {
        background: #e3e1e8; border: none; border-radius: 0.75rem;
        padding: 0.85rem 1rem; width: 100%; transition: box-shadow 0.2s;
    }
    .cap-input:focus { outline: none; box-shadow: 0 0 0 3px rgba(47,65,133,0.15); }
    .toggle-asistencia { cursor: pointer; user-select: none; }
    .toggle-dot {
        width: 2.5rem; height: 1.25rem; border-radius: 1rem; position: relative;
        transition: background 0.2s; display: inline-block; vertical-align: middle;
    }
    .toggle-dot::after {
        content: ''; position: absolute; top: 2px; left: 2px;
        width: 1rem; height: 1rem; border-radius: 50%; background: #fff; transition: transform 0.2s;
    }
    .toggle-dot.active { background: #2f4185; }
    .toggle-dot.active::after { transform: translateX(1.2rem); }
    .toggle-dot.inactive { background: #c5c5d2; }
    .user-initials {
        width: 2rem; height: 2rem; border-radius: 50%; display: flex; align-items: center;
        justify-content: center; font-size: 0.7rem; font-weight: 700; flex-shrink: 0;
    }
    .stat-mini {
        border-radius: 1.5rem; padding: 1.2rem 1.5rem; display: flex; flex-direction: column;
        justify-content: center;
    }
    .progress-ring { transform: rotate(-90deg); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
        <div>
            <h1 class="cap-header fw-bold" style="font-size:2rem;">{{ $capacitacion->titulo }}</h1>
            <p class="text-muted mb-0">{{ $capacitacion->descripcion ?: 'Sin descripción' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('config.capacitaciones.edit', $capacitacion->id) }}" class="btn btn-outline-warning">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
            <a href="{{ route('config.capacitaciones.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left: Info -->
        <div class="col-lg-5">
            <div class="cap-card mb-4">
                <h5 class="cap-header fw-bold mb-4" style="color:#2f4185;">
                    <i class="fas fa-info-circle me-2"></i>Información General
                </h5>
                <table class="table table-borderless mb-0">
                    <tr><td class="text-muted small fw-bold" style="width:40%;">Fecha</td><td>{{ $capacitacion->fecha->format('d/m/Y') }}</td></tr>
                    <tr>
                        <td class="text-muted small fw-bold">Horario</td>
                        <td>
                            @if($capacitacion->hora_inicio && $capacitacion->hora_fin)
                                {{ \Carbon\Carbon::parse($capacitacion->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($capacitacion->hora_fin)->format('H:i') }}
                            @else — @endif
                        </td>
                    </tr>
                    <tr><td class="text-muted small fw-bold">Ubicación</td><td>{{ $capacitacion->ubicacion ?: '—' }}</td></tr>
                    <tr><td class="text-muted small fw-bold">Instructor</td><td>{{ $capacitacion->instructor ?: '—' }}</td></tr>
                    <tr><td class="text-muted small fw-bold">Capacidad</td><td>{{ $capacitacion->capacidad_maxima ?: 'Sin límite' }}</td></tr>
                </table>
            </div>

            <!-- Stats -->
            @php
                $totalCitados = $capacitacion->asistencias->count();
                $asistieron = $capacitacion->asistencias->where('asistio', true)->count();
                $porcentaje = $totalCitados > 0 ? round(($asistieron / $totalCitados) * 100) : 0;
            @endphp
            <div class="row g-3">
                <div class="col-4">
                    <div class="stat-mini bg-white shadow-sm text-center">
                        <div class="h3 fw-bold mb-0" style="color:#2f4185;">{{ $totalCitados }}</div>
                        <small class="text-muted fw-bold text-uppercase" style="font-size:.65rem;">Citados</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-mini bg-white shadow-sm text-center">
                        <div class="h3 fw-bold mb-0 text-success">{{ $asistieron }}</div>
                        <small class="text-muted fw-bold text-uppercase" style="font-size:.65rem;">Asistieron</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-mini shadow-sm text-center" style="background:#ffdeac;">
                        <div class="position-relative d-inline-block" style="width:3.5rem;height:3.5rem;">
                            <svg class="progress-ring" width="56" height="56">
                                <circle cx="28" cy="28" r="22" stroke="#e3e1e8" stroke-width="5" fill="transparent"/>
                                <circle cx="28" cy="28" r="22" stroke="#2f4185" stroke-width="5" fill="transparent"
                                    stroke-dasharray="{{ 2 * 3.1416 * 22 }}"
                                    stroke-dashoffset="{{ 2 * 3.1416 * 22 * (1 - $porcentaje / 100) }}"
                                    stroke-linecap="round"/>
                            </svg>
                            <span class="position-absolute" style="top:50%;left:50%;transform:translate(-50%,-50%);font-size:0.7rem;font-weight:700;">{{ $porcentaje }}%</span>
                        </div>
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size:.6rem;">Cobertura</small>
                    </div>
                </div>
            </div>

            <!-- Agregar usuario -->
            <div class="cap-card mt-4">
                <h6 class="cap-header fw-bold mb-3" style="color:#2f4185;">
                    <i class="fas fa-user-plus me-2"></i>Agregar Participante
                </h6>
                <select id="addUserSelect" class="cap-input mb-2">
                    <option value="">Seleccione un usuario...</option>
                    @foreach($usuarios as $u)
                        @if(!in_array($u->id, $asignadosIds))
                        <option value="{{ $u->id }}">{{ $u->name }} {{ $u->apellido1 }} {{ $u->apellido2 }} — {{ $u->identificacion ?? 'Sin ID' }}</option>
                        @endif
                    @endforeach
                </select>
                <button type="button" id="btnAgregarUsuario" class="btn btn-sm w-100" style="background:#2f4185;color:#fff;border-radius:0.75rem;">
                    <i class="fas fa-plus me-1"></i> Agregar a la Capacitación
                </button>
            </div>
        </div>

        <!-- Right: Attendees Ledger -->
        <div class="col-lg-7">
            <div class="cap-card" style="min-height:100%;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="cap-header fw-bold mb-0" style="color:#2f4185;">
                        <i class="fas fa-clipboard-check me-2"></i>Registro de Asistencia
                    </h5>
                    <div class="position-relative" style="width:16rem;">
                        <i class="fas fa-search position-absolute" style="left:0.8rem;top:50%;transform:translateY(-50%);color:#757682;"></i>
                        <input type="text" id="searchAsistentes" class="cap-input" style="padding-left:2.3rem;font-size:0.85rem;" placeholder="Buscar participante...">
                    </div>
                </div>

                <div style="border-radius:1rem; overflow:hidden; border: 1px solid #e3e1e8;">
                    <table class="table table-hover align-middle mb-0" id="tablaAsistencia">
                        <thead style="background:#efedf3;">
                            <tr>
                                <th class="py-3 px-4 text-muted small fw-bold text-uppercase" style="font-size:0.7rem;letter-spacing:0.05em;">Nombre</th>
                                <th class="py-3 px-4 text-muted small fw-bold text-uppercase" style="font-size:0.7rem;letter-spacing:0.05em;">Servicio</th>
                                <th class="py-3 px-4 text-muted small fw-bold text-uppercase text-center" style="font-size:0.7rem;letter-spacing:0.05em;">Asistencia</th>
                                <th class="py-3 px-4 text-muted small fw-bold text-uppercase text-center" style="font-size:0.7rem;letter-spacing:0.05em;">Quitar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $colors = ['#dde1ff','#ffdeac','#d4edda','#f8d7da','#d6dbfd','#e3e1e8']; @endphp
                            @forelse($capacitacion->asistencias as $a)
                            <tr data-search="{{ mb_strtolower(($a->user->name ?? '').' '.($a->user->apellido1 ?? '').' '.($a->user->apellido2 ?? '').' '.($a->user->identificacion ?? '').' '.($a->user->servicio ?? '')) }}" id="row-{{ $a->user_id }}">
                                <td class="py-3 px-4">
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $initials = strtoupper(substr($a->user->name ?? '',0,1) . substr($a->user->apellido1 ?? '',0,1));
                                            $color = $colors[$a->user_id % count($colors)];
                                        @endphp
                                        <div class="user-initials" style="background:{{ $color }};color:#333;">{{ $initials }}</div>
                                        <span class="fw-semibold small">{{ $a->user->name ?? '?' }} {{ $a->user->apellido1 ?? '' }} {{ $a->user->apellido2 ?? '' }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="badge" style="background:#efedf3;color:#454650;font-size:0.72rem;border-radius:2rem;padding:0.3rem 0.7rem;">{{ $a->user->servicio ?? '—' }}</span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="toggle-asistencia" data-cap="{{ $capacitacion->id }}" data-user="{{ $a->user_id }}">
                                        <span class="toggle-dot {{ $a->asistio ? 'active' : 'inactive' }}"></span>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remover" data-cap="{{ $capacitacion->id }}" data-user="{{ $a->user_id }}" title="Quitar">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRow">
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                                    No se han citado usuarios para esta capacitación.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Toggle asistencia
    document.querySelectorAll('.toggle-asistencia').forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const dot = this.querySelector('.toggle-dot');
            const capId = this.dataset.cap;
            const userId = this.dataset.user;
            const nuevoEstado = !dot.classList.contains('active');

            fetch('{{ route("config.capacitaciones.toggle-asistencia") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ capacitacion_id: capId, user_id: userId, asistio: nuevoEstado })
            })
            .then(r => r.json())
            .then(function(data) {
                if (data.ok) {
                    dot.classList.toggle('active', nuevoEstado);
                    dot.classList.toggle('inactive', !nuevoEstado);
                }
            });
        });
    });

    // Buscar
    document.getElementById('searchAsistentes').addEventListener('input', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('#tablaAsistencia tbody tr[data-search]').forEach(function(row) {
            row.style.display = row.dataset.search.includes(term) ? '' : 'none';
        });
    });

    // Agregar usuario
    document.getElementById('btnAgregarUsuario').addEventListener('click', function() {
        const sel = document.getElementById('addUserSelect');
        const userId = sel.value;
        if (!userId) return;

        fetch('{{ route("config.capacitaciones.agregar-usuario") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ capacitacion_id: {{ $capacitacion->id }}, user_id: userId })
        })
        .then(r => r.json())
        .then(function(data) {
            if (data.ok) {
                location.reload();
            } else {
                alert(data.error || 'Error al agregar');
            }
        });
    });

    // Remover usuario
    document.querySelectorAll('.btn-remover').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!confirm('¿Quitar este usuario de la capacitación?')) return;
            const capId = this.dataset.cap;
            const userId = this.dataset.user;

            fetch('{{ route("config.capacitaciones.remover-usuario") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ capacitacion_id: capId, user_id: userId })
            })
            .then(r => r.json())
            .then(function(data) {
                if (data.ok) {
                    const row = document.getElementById('row-' + userId);
                    if (row) row.remove();
                }
            });
        });
    });
});
</script>
@endsection
