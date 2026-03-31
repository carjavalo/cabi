@foreach($cargos as $cargo)
<tr class="border-bottom cargo-row" data-id="{{ $cargo->id }}">
    <td class="px-4 py-3">
        <span class="badge" style="background:#e8eaf6; color:#2e3a75; font-size:0.85rem;">{{ $cargo->id }}</span>
    </td>
    <td class="px-4 py-3">
        <div class="d-flex align-items-center">
            <div style="width:36px;height:36px;background:rgba(46,58,117,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                <i class="fas fa-id-badge" style="color:#2e3a75;"></i>
            </div>
            <span class="fw-semibold" style="font-weight:600;">{{ $cargo->nombre }}</span>
        </div>
    </td>
    <td class="px-4 py-3 text-muted">
        {{ $cargo->descripcion ?? '—' }}
    </td>
    <td class="px-4 py-3 text-center">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-info btn-ver-cargo" data-id="{{ $cargo->id }}" title="Ver detalle">
                <i class="fas fa-eye"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary btn-editar-cargo" data-id="{{ $cargo->id }}" data-nombre="{{ $cargo->nombre }}" data-descripcion="{{ $cargo->descripcion }}" title="Editar">
                <i class="fas fa-edit"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-cargo" data-id="{{ $cargo->id }}" data-nombre="{{ $cargo->nombre }}" title="Eliminar">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    </td>
</tr>
@endforeach

@if($cargos->isEmpty())
<tr>
    <td colspan="4" class="text-center py-5 text-muted">
        <i class="fas fa-inbox fa-3x mb-3 d-block" style="opacity:0.3;"></i>
        No se encontraron cargos registrados.
    </td>
</tr>
@endif
