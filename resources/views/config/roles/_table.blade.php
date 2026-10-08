@foreach($roles as $rol)
@php
    $asignablesIds = $rol->relationLoaded('asignables') ? $rol->asignables->pluck('id')->values() : collect();
    $configurableAsig = $rol->nombre !== 'Super Admin' && ($rol->nombre !== 'Administrador' || ($soySuper ?? false));
@endphp
<tr class="border-bottom rol-row" data-id="{{ $rol->id }}">
    <td class="px-4 py-3">
        <span class="badge" style="background:#e8eaf6; color:#2e3a75; font-size:0.85rem;">{{ $rol->id }}</span>
    </td>
    <td class="px-4 py-3">
        <div class="d-flex align-items-center">
            <div style="width:36px;height:36px;background:rgba(46,58,117,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                <i class="fas {{ $rol->es_sistema ? 'fa-user-shield' : 'fa-user-tag' }}" style="color:#2e3a75;"></i>
            </div>
            <span style="font-weight:600;">{{ $rol->nombre }}</span>
        </div>
    </td>
    <td class="px-4 py-3 text-center">
        <span class="badge badge-pill" style="background:#eef0f8;color:#2e3a75;font-size:.85rem;" title="1 = nivel más alto">{{ $rol->nivel ?? '—' }}</span>
    </td>
    <td class="px-4 py-3 text-muted">{{ $rol->descripcion ?? '—' }}</td>
    <td class="px-4 py-3">
        @if($rol->es_sistema)
            <span class="badge badge-pill" style="background:#2e3a75;color:#fff;"><i class="fas fa-lock mr-1"></i>Sistema</span>
        @else
            <span class="badge badge-pill badge-info">Personalizado</span>
        @endif
    </td>
    <td class="px-4 py-3">
        @if($rol->activo)
            <span class="badge badge-pill badge-success">Activo</span>
        @else
            <span class="badge badge-pill badge-secondary">Inactivo</span>
        @endif
    </td>
    <td class="px-4 py-3" style="min-width:200px; max-width:320px;">
        @if($rol->nombre === 'Super Admin')
            <span class="badge badge-pill" style="background:#fff4d6;color:#b7791f;"><i class="fas fa-crown mr-1"></i>Todos los roles</span>
        @elseif($rol->relationLoaded('asignables'))
            @forelse($rol->asignables as $asig)
                <span class="badge badge-pill mb-1" style="background:#e8f5e9;color:#1b7a3d;font-weight:600;">{{ $asig->nombre }}</span>
            @empty
                <span class="text-muted small"><i class="fas fa-ban mr-1"></i>Ninguno</span>
            @endforelse
        @else
            <span class="text-muted small">—</span>
        @endif
    </td>
    <td class="px-4 py-3 text-center">
        <a href="{{ url('/configuracion/usuarios') }}?role_filter={{ urlencode($rol->nombre) }}" class="badge badge-light" style="font-size:0.9rem;" title="Ver usuarios con este rol">
            <i class="fas fa-users mr-1"></i>{{ $rol->users_count }}
        </a>
    </td>
    <td class="px-4 py-3 text-center">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-info btn-ver-rol" data-id="{{ $rol->id }}" title="Ver detalle">
                <i class="fas fa-eye"></i>
            </button>
            @if($puedeEditar)
            <button type="button" class="btn btn-sm btn-outline-primary btn-editar-rol"
                data-id="{{ $rol->id }}" data-nombre="{{ $rol->nombre }}" data-descripcion="{{ $rol->descripcion }}"
                data-activo="{{ $rol->activo ? 1 : 0 }}" data-sistema="{{ $rol->es_sistema ? 1 : 0 }}"
                data-nivel="{{ $rol->nivel }}" data-asignables="{{ $asignablesIds->toJson() }}"
                data-asig-config="{{ $configurableAsig ? 1 : 0 }}" title="Editar">
                <i class="fas fa-edit"></i>
            </button>
            @if($configurableAsig)
            <button type="button" class="btn btn-sm btn-outline-success btn-asignables-rol" data-id="{{ $rol->id }}" title="Roles que puede asignar al crear usuarios">
                <i class="fas fa-user-plus"></i>
            </button>
            @endif
            @if(!$rol->es_sistema)
            <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-rol"
                data-id="{{ $rol->id }}" data-nombre="{{ $rol->nombre }}" data-usuarios="{{ $rol->users_count }}" title="Eliminar">
                <i class="fas fa-trash-alt"></i>
            </button>
            @endif
            @endif
        </div>
    </td>
</tr>
@endforeach

@if($roles->isEmpty())
<tr>
    <td colspan="10" class="text-center py-5 text-muted">
        <i class="fas fa-inbox fa-3x mb-3 d-block" style="opacity:0.3;"></i>
        No se encontraron roles con los filtros aplicados.
    </td>
</tr>
@endif
