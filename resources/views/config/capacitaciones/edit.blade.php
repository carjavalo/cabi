@extends('layouts.app')

@section('title','Editar Capacitación')
@section('header','Editar Capacitación')

@push('head')
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .cap-card { background: #f5f2f9; border-radius: 1.5rem; padding: 2rem; }
    .cap-input {
        background: #e3e1e8; border: none; border-radius: 0.75rem;
        padding: 0.85rem 1rem; width: 100%; transition: box-shadow 0.2s;
    }
    .cap-input:focus { outline: none; box-shadow: 0 0 0 3px rgba(47,65,133,0.15); }
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
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h1 class="cap-header fw-bold" style="font-size:2rem;">Editar Capacitación</h1>
            <p class="text-muted">Modifica los datos de la sesión y los participantes.</p>
        </div>
        <a href="{{ route('config.capacitaciones.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form method="POST" action="{{ route('config.capacitaciones.update', $capacitacion->id) }}" id="formCapacitacion">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Left Column: General Info -->
            <div class="col-lg-5">
                <div class="cap-card mb-4">
                    <h5 class="cap-header fw-bold mb-4" style="color:#2f4185;">
                        <i class="fas fa-info-circle me-2"></i>Información General
                    </h5>
                    <div class="mb-3">
                        <label class="cap-label">Nombre de la Capacitación <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="cap-input" value="{{ old('titulo', $capacitacion->titulo) }}" required>
                        @error('titulo') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="cap-label">Descripción</label>
                        <textarea name="descripcion" class="cap-input" rows="3">{{ old('descripcion', $capacitacion->descripcion) }}</textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="cap-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" class="cap-input" value="{{ old('fecha', $capacitacion->fecha->format('Y-m-d')) }}" required>
                            @error('fecha') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-6">
                            <label class="cap-label">Ubicación</label>
                            <input type="text" name="ubicacion" class="cap-input" value="{{ old('ubicacion', $capacitacion->ubicacion) }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="cap-label">Hora Inicio</label>
                            <input type="time" name="hora_inicio" class="cap-input" value="{{ old('hora_inicio', $capacitacion->hora_inicio) }}">
                        </div>
                        <div class="col-6">
                            <label class="cap-label">Hora Fin</label>
                            <input type="time" name="hora_fin" class="cap-input" value="{{ old('hora_fin', $capacitacion->hora_fin) }}">
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="cap-label">Instructor</label>
                            <input type="text" name="instructor" class="cap-input" value="{{ old('instructor', $capacitacion->instructor) }}">
                        </div>
                        <div class="col-6">
                            <label class="cap-label">Capacidad Máxima</label>
                            <input type="number" name="capacidad_maxima" class="cap-input" min="0" value="{{ old('capacidad_maxima', $capacitacion->capacidad_maxima) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Attendees -->
            <div class="col-lg-7">
                <div class="cap-card" style="min-height:100%;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="cap-header fw-bold mb-0" style="color:#2f4185;">
                            <i class="fas fa-users me-2"></i>Asistentes (Usuarios)
                        </h5>
                        <span class="selected-count" id="selectedCount">0 seleccionados</span>
                    </div>

                    <div class="position-relative mb-3">
                        <i class="fas fa-search position-absolute" style="left:1rem;top:50%;transform:translateY(-50%);color:#757682;"></i>
                        <input type="text" id="searchUsuarios" class="cap-input" style="padding-left: 2.5rem;" placeholder="Buscar participante...">
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-2 px-2">
                        <input type="checkbox" id="selectAll" style="width:1.1rem;height:1.1rem;accent-color:#2f4185;cursor:pointer;">
                        <label for="selectAll" class="mb-0 small fw-bold text-muted" style="cursor:pointer;">Seleccionar todos los visibles</label>
                    </div>

                    <div id="usersList" style="max-height: 420px; overflow-y: auto; border: 1px solid #e3e1e8; border-radius: 1rem; padding: 0.5rem;">
                        @php $colors = ['#dde1ff','#ffdeac','#d4edda','#f8d7da','#d6dbfd','#e3e1e8']; @endphp
                        @foreach($usuarios as $u)
                        <label class="user-item w-100 mb-0" data-search="{{ mb_strtolower($u->name.' '.$u->apellido1.' '.$u->apellido2.' '.$u->identificacion.' '.$u->servicio) }}">
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
            <a href="{{ route('config.capacitaciones.index') }}" class="btn btn-outline-secondary me-2 px-4">Cancelar</a>
            <button type="submit" class="cap-gradient-btn">
                <i class="fas fa-save me-2"></i>Actualizar Capacitación
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const search = document.getElementById('searchUsuarios');
    const items = document.querySelectorAll('#usersList .user-item');
    const selectAll = document.getElementById('selectAll');
    const countEl = document.getElementById('selectedCount');

    function updateCount() {
        const checked = document.querySelectorAll('#usersList input[name="usuarios[]"]:checked').length;
        countEl.textContent = checked + ' seleccionado' + (checked !== 1 ? 's' : '');
    }

    search.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        items.forEach(function(item) {
            item.style.display = item.getAttribute('data-search').includes(term) ? '' : 'none';
        });
    });

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
