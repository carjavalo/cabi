@extends('layouts.app')

@section('title','Asignar Participantes')
@section('header','Asignar Participantes')

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .cap-card { background: #f5f2f9; border-radius: 1.5rem; padding: 2rem; }
    .cap-input {
        background: #e3e1e8; border: none; border-radius: 0.75rem;
        padding: 0.85rem 1rem; width: 100%; transition: box-shadow 0.2s;
    }
    .cap-input:focus { outline: none; box-shadow: 0 0 0 3px rgba(47,65,133,0.15); }
    .cap-input-readonly {
        background: #d5d3da; border: none; border-radius: 0.75rem;
        padding: 0.85rem 1rem; width: 100%; color: #555; cursor: default;
    }
    .cap-label { font-size: 0.8rem; font-weight: 600; color: #454650; margin-bottom: 0.35rem; display: block; margin-left: 0.25rem; }
    .cap-header { font-family: 'Manrope', sans-serif; }
    .user-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.75rem; border-radius: 0.75rem; transition: background 0.15s; cursor: pointer; }
    .user-item:hover { background: #efedf3; }
    .user-item input[type=checkbox] { width: 1.1rem; height: 1.1rem; accent-color: #2f4185; cursor: pointer; }
    .user-initials {
        width: 2rem; height: 2rem; border-radius: 50%; display: flex; align-items: center;
        justify-content: center; font-size: 0.7rem; font-weight: 700; flex-shrink: 0;
    }
    .selected-count { background: #2f4185; color: #fff; border-radius: 2rem; padding: 0.15rem 0.7rem; font-size: 0.75rem; font-weight: 700; }
    .cap-gradient-btn {
        background: linear-gradient(135deg, #2f4185 0%, #48599e 100%);
        color: #fff; border: none; border-radius: 0.75rem; padding: 0.85rem 2.5rem;
        font-weight: 700; font-size: 1rem; cursor: pointer; transition: box-shadow 0.2s;
    }
    .cap-gradient-btn:hover { box-shadow: 0 6px 24px rgba(47,65,133,0.3); color: #fff; }
    .filter-row { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .filter-row .filter-input { flex: 1; min-width: 120px; }
    .filter-row .filter-select { flex: 1; min-width: 150px; }
    .readonly-notice {
        background: #fff3cd; color: #856404; border-radius: 0.5rem; padding: 0.5rem 0.75rem;
        font-size: 0.78rem; font-weight: 600; text-align: center; margin-top: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h1 class="cap-header fw-bold" style="font-size:2rem;">Asignar Participantes</h1>
            <p class="text-muted">Selecciona los asistentes para la capacitación. La información general no es editable.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <form method="POST" action="{{ route('capacitaciones.update_user', $capacitacion->id) }}" id="formCapacitacion">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Left Column: General Info (READ-ONLY) -->
            <div class="col-lg-5">
                <div class="cap-card mb-4">
                    <h5 class="cap-header fw-bold mb-4" style="color:#2f4185;">
                        <i class="fas fa-info-circle me-2"></i>Información General
                    </h5>
                    <div class="mb-3">
                        <label class="cap-label">Nombre de la Capacitación</label>
                        <div class="cap-input-readonly">{{ $capacitacion->titulo }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="cap-label">Descripción</label>
                        <div class="cap-input-readonly" style="min-height:80px;">{{ $capacitacion->descripcion ?: '—' }}</div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="cap-label">Fecha</label>
                            <div class="cap-input-readonly">{{ \Carbon\Carbon::parse($capacitacion->fecha)->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-6">
                            <label class="cap-label">Ubicación</label>
                            <div class="cap-input-readonly">{{ $capacitacion->ubicacion ?: '—' }}</div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="cap-label">Hora Inicio</label>
                            <div class="cap-input-readonly">{{ $capacitacion->hora_inicio ? \Carbon\Carbon::parse($capacitacion->hora_inicio)->format('h:i A') : '—' }}</div>
                        </div>
                        <div class="col-6">
                            <label class="cap-label">Hora Fin</label>
                            <div class="cap-input-readonly">{{ $capacitacion->hora_fin ? \Carbon\Carbon::parse($capacitacion->hora_fin)->format('h:i A') : '—' }}</div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="cap-label">Instructor</label>
                            <div class="cap-input-readonly">{{ $capacitacion->instructor ?: '—' }}</div>
                        </div>
                        <div class="col-6">
                            <label class="cap-label">Capacidad Máxima</label>
                            <div class="cap-input-readonly">{{ $capacitacion->capacidad_maxima ?: '—' }}</div>
                        </div>
                    </div>
                    <div class="readonly-notice">
                        <i class="fas fa-lock me-1"></i> Información de solo lectura. Para modificar estos datos use la sección de Configuración.
                    </div>
                </div>
            </div>

            <!-- Right Column: Attendees with filters -->
            <div class="col-lg-7">
                <div class="cap-card" style="min-height:100%;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="cap-header fw-bold mb-0" style="color:#2f4185;">
                            <i class="fas fa-users me-2"></i>Asistentes (Usuarios)
                        </h5>
                        <span class="selected-count" id="selectedCount">0 seleccionados</span>
                    </div>

                    <!-- Filtros -->
                    <div class="filter-row mb-3">
                        <div class="filter-input">
                            <div class="position-relative">
                                <i class="fas fa-search position-absolute" style="left:0.8rem;top:50%;transform:translateY(-50%);color:#757682;"></i>
                                <input type="text" id="searchNombre" class="cap-input" style="padding-left: 2.3rem; font-size:0.85rem;" placeholder="Buscar por nombre...">
                            </div>
                        </div>
                        <div class="filter-input">
                            <div class="position-relative">
                                <i class="fas fa-id-card position-absolute" style="left:0.8rem;top:50%;transform:translateY(-50%);color:#757682;"></i>
                                <input type="text" id="searchIdentificacion" class="cap-input" style="padding-left: 2.3rem; font-size:0.85rem;" placeholder="Identificación...">
                            </div>
                        </div>
                        <div class="filter-select">
                            <select id="filterServicio" class="cap-input" style="font-size:0.85rem;">
                                <option value="">Todas las Áreas/Servicios</option>
                                @foreach($servicios as $s)
                                    <option value="{{ mb_strtolower($s) }}">{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-2 px-2">
                        <input type="checkbox" id="selectAll" style="width:1.1rem;height:1.1rem;accent-color:#2f4185;cursor:pointer;">
                        <label for="selectAll" class="mb-0 small fw-bold text-muted" style="cursor:pointer;">Seleccionar todos los visibles</label>
                    </div>

                    <div id="usersList" style="max-height: 420px; overflow-y: auto; border: 1px solid #e3e1e8; border-radius: 1rem; padding: 0.5rem;">
                        @php $colors = ['#dde1ff','#ffdeac','#d4edda','#f8d7da','#d6dbfd','#e3e1e8']; @endphp
                        @foreach($usuarios as $u)
                        <label class="user-item w-100 mb-0"
                               data-nombre="{{ mb_strtolower($u->name.' '.$u->apellido1.' '.$u->apellido2) }}"
                               data-identificacion="{{ mb_strtolower($u->identificacion ?? '') }}"
                               data-servicio="{{ mb_strtolower($u->servicio ?? '') }}">
                            <input type="checkbox" name="usuarios[]" value="{{ $u->id }}"
                                {{ in_array($u->id, old('usuarios', $asignadosIds)) ? 'checked' : '' }}>
                            @php
                                $initials = strtoupper(substr($u->name,0,1) . substr($u->apellido1 ?? '',0,1));
                                $color = $colors[$u->id % count($colors)];
                            @endphp
                            <div class="user-initials" style="background:{{ $color }};color:#333;">{{ $initials }}</div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">{{ $u->name }} {{ $u->apellido1 }} {{ $u->apellido2 }}</div>
                                <div class="text-muted" style="font-size:0.72rem;">
                                    {{ $u->identificacion ?? 'Sin ID' }}
                                    @if($u->servicio) · {{ $u->servicio }} @endif
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="mt-3 text-muted small">
                        <i class="fas fa-info-circle me-1"></i> Se listan únicamente los usuarios con rol <strong>Usuario</strong>.
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="d-flex justify-content-end mt-4 mb-4">
            <a href="{{ route('capacitaciones.index_user') }}" class="btn btn-outline-secondary me-2 px-4">Cancelar</a>
            <button type="submit" class="cap-gradient-btn">
                <i class="fas fa-save me-2"></i>Guardar Asistentes
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchNombre = document.getElementById('searchNombre');
    var searchId = document.getElementById('searchIdentificacion');
    var filterServicio = document.getElementById('filterServicio');
    var items = document.querySelectorAll('#usersList .user-item');
    var selectAll = document.getElementById('selectAll');
    var countEl = document.getElementById('selectedCount');

    function updateCount() {
        var checked = document.querySelectorAll('#usersList input[name="usuarios[]"]:checked').length;
        countEl.textContent = checked + ' seleccionado' + (checked !== 1 ? 's' : '');
    }

    function applyFilters() {
        var nombre = searchNombre.value.toLowerCase();
        var identificacion = searchId.value.toLowerCase();
        var servicio = filterServicio.value.toLowerCase();

        items.forEach(function(item) {
            var matchNombre = !nombre || item.getAttribute('data-nombre').indexOf(nombre) !== -1;
            var matchId = !identificacion || item.getAttribute('data-identificacion').indexOf(identificacion) !== -1;
            var matchServicio = !servicio || item.getAttribute('data-servicio') === servicio;

            item.style.display = (matchNombre && matchId && matchServicio) ? '' : 'none';
        });
    }

    searchNombre.addEventListener('input', applyFilters);
    searchId.addEventListener('input', applyFilters);
    filterServicio.addEventListener('change', applyFilters);

    selectAll.addEventListener('change', function() {
        document.querySelectorAll('#usersList .user-item:not([style*="display: none"]) input[name="usuarios[]"]')
            .forEach(function(cb) { cb.checked = selectAll.checked; });
        updateCount();
    });

    document.querySelectorAll('#usersList input[name="usuarios[]"]').forEach(function(cb) {
        cb.addEventListener('change', updateCount);
    });

    updateCount();
});
</script>
@endsection
