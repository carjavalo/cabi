@extends('layouts.app')

@section('title','Gestión de Cargos')
@section('header','Gestión de Cargos')

@push('head')
<style>
    .corporate-gradient {
        background: linear-gradient(135deg, #2e3a75 0%, #1e2a55 100%);
    }
    .hover-scale {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-scale:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46,58,117,0.3);
    }
    .stat-card {
        border-radius: 12px;
        padding: 20px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .stat-card .stat-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 3rem;
        opacity: 0.15;
    }
    .table-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }
    .search-box {
        position: relative;
    }
    .search-box .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #adb5bd;
    }
    .search-box input {
        padding-left: 40px;
        border-radius: 25px;
        border: 2px solid #e0e0e0;
        transition: border-color 0.3s;
    }
    .search-box input:focus {
        border-color: #2e3a75;
        box-shadow: 0 0 0 0.2rem rgba(46,58,117,0.15);
    }
    .table thead th {
        border-top: none;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table tbody tr {
        transition: background-color 0.15s ease;
    }
    .table tbody tr:hover {
        background-color: rgba(46,58,117,0.04);
    }
    .btn-corporate {
        background: linear-gradient(135deg, #2e3a75 0%, #3d4f9f 100%);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 24px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-corporate:hover {
        background: linear-gradient(135deg, #1e2a55 0%, #2e3a75 100%);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(46,58,117,0.35);
    }
    .modal-header.corporate {
        background: linear-gradient(135deg, #2e3a75 0%, #1e2a55 100%);
        color: #fff;
        border-bottom: none;
    }
    .modal-header.corporate .close {
        color: #fff;
        text-shadow: none;
        opacity: 0.9;
    }
    .pagination .page-link {
        color: #2e3a75;
    }
    .pagination .page-item.active .page-link {
        background: #2e3a75;
        border-color: #2e3a75;
        color: #fff;
    }
    .toolbar-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }
    .btn-export {
        background: #1b7a3d;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 8px 18px;
        font-weight: 600;
    }
    .btn-export:hover {
        background: #15612f;
        color: #fff;
    }
    .fade-in { animation: fadeIn 0.3s ease-in; }
    @keyframes fadeIn { from { opacity:0; transform: translateY(8px); } to { opacity:1; transform: translateY(0); } }
    .detail-label { font-weight: 700; color: #2e3a75; font-size: 0.85rem; text-transform: uppercase; }
    .detail-value { font-size: 1rem; color: #333; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 fade-in">
    <!-- Header Section -->
    <div class="corporate-gradient rounded shadow-lg mb-4 p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2 class="text-white mb-1" style="font-weight:700;">
                    <i class="fas fa-id-badge mr-2"></i>Gestión de Cargos
                </h2>
                <p class="mb-0 small" style="color:rgba(255,255,255,0.7);">Administra los cargos y posiciones del hospital</p>
            </div>
            <button class="btn btn-light btn-lg shadow-sm hover-scale" id="btnNuevoCargo" style="font-weight:600;">
                <i class="fas fa-plus mr-2"></i>Nuevo Cargo
            </button>
        </div>
    </div>

    <!-- Stats row -->
    <div class="row mb-4">
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="stat-card hover-scale shadow" style="background: linear-gradient(135deg, #2e3a75,#3d4f9f);">
                <div>
                    <div style="font-size:0.8rem;opacity:0.8;">Total Cargos</div>
                    <div style="font-size:2rem;font-weight:700;" id="totalCargos">{{ $cargos->total() }}</div>
                </div>
                <i class="fas fa-briefcase stat-icon"></i>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="stat-card hover-scale shadow" style="background: linear-gradient(135deg, #1b7a3d,#28a745);">
                <div>
                    <div style="font-size:0.8rem;opacity:0.8;">Con Descripción</div>
                    <div style="font-size:2rem;font-weight:700;">{{ $cargos->filter(fn($c) => $c->descripcion)->count() }}</div>
                </div>
                <i class="fas fa-file-alt stat-icon"></i>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="stat-card hover-scale shadow" style="background: linear-gradient(135deg, #e67e22,#f39c12);">
                <div>
                    <div style="font-size:0.8rem;opacity:0.8;">En esta página</div>
                    <div style="font-size:2rem;font-weight:700;">{{ $cargos->count() }}</div>
                </div>
                <i class="fas fa-list stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="card border-0 shadow-sm rounded mb-3" style="border-radius:12px;">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="search-box" style="min-width:280px; flex:1; max-width:400px;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre o descripción..." value="{{ request('search') }}">
                </div>
                <div class="toolbar-actions mt-2 mt-md-0">
                    <a href="{{ route('config.cargos.export', ['search' => request('search')]) }}" class="btn btn-export" id="btnExportExcel">
                        <i class="fas fa-file-excel mr-1"></i> Exportar Excel
                    </a>
                    <button class="btn btn-outline-secondary" id="btnRefresh" title="Refrescar">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card table-card shadow-lg" style="border-radius:12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaCargos">
                    <thead style="background-color: #f0f2f8;">
                        <tr>
                            <th class="px-4 py-3 text-muted small" style="width:80px; font-weight:700;">#</th>
                            <th class="px-4 py-3 text-muted small" style="font-weight:700;">Nombre del Cargo</th>
                            <th class="px-4 py-3 text-muted small" style="font-weight:700;">Descripción</th>
                            <th class="px-4 py-3 text-muted small text-center" style="width:180px; font-weight:700;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cargosTableBody">
                        @include('config.cargos._table', ['cargos' => $cargos])
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center" id="paginationContainer">
            <div class="text-muted small">
                Mostrando {{ $cargos->firstItem() ?? 0 }} a {{ $cargos->lastItem() ?? 0 }} de {{ $cargos->total() }} registros
            </div>
            <div>
                {{ $cargos->appends(['search' => request('search')])->links() }}
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Crear / Editar Cargo -->
<div class="modal fade" id="modalCargo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none;">
            <div class="modal-header corporate">
                <h5 class="modal-title" id="modalCargoTitle">
                    <i class="fas fa-id-badge mr-2"></i>Nuevo Cargo
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCargo" method="POST">
                @csrf
                <input type="hidden" id="cargoMethod" name="_method" value="POST">
                <input type="hidden" id="cargoId" name="id" value="">
                <div class="modal-body px-4 py-4">
                    <div class="form-group mb-3">
                        <label for="cargoNombre" class="detail-label">Nombre del Cargo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cargoNombre" name="nombre" maxlength="80" required placeholder="Ej: Médico General">
                        <small class="text-muted"><span id="charCountNombre">0</span>/80 caracteres</small>
                    </div>
                    <div class="form-group mb-0">
                        <label for="cargoDescripcion" class="detail-label">Descripción</label>
                        <textarea class="form-control" id="cargoDescripcion" name="descripcion" maxlength="200" rows="3" placeholder="Descripción breve del cargo (opcional)"></textarea>
                        <small class="text-muted"><span id="charCountDesc">0</span>/200 caracteres</small>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #eee;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-corporate" id="btnGuardarCargo">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Ver Detalle -->
<div class="modal fade" id="modalVerCargo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none;">
            <div class="modal-header corporate">
                <h5 class="modal-title"><i class="fas fa-eye mr-2"></i>Detalle del Cargo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4 py-4">
                <div class="mb-3">
                    <div class="detail-label mb-1">ID</div>
                    <div class="detail-value" id="verCargoId">—</div>
                </div>
                <div class="mb-3">
                    <div class="detail-label mb-1">Nombre</div>
                    <div class="detail-value" id="verCargoNombre">—</div>
                </div>
                <div class="mb-3">
                    <div class="detail-label mb-1">Descripción</div>
                    <div class="detail-value" id="verCargoDescripcion">—</div>
                </div>
                <div class="mb-0">
                    <div class="detail-label mb-1">Fecha de Creación</div>
                    <div class="detail-value" id="verCargoFecha">—</div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #eee;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Confirmar Eliminación -->
<div class="modal fade" id="modalEliminarCargo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#dc3545,#c82333); color:#fff; border-bottom:none;">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4 py-4">
                <p class="mb-1">¿Está seguro de que desea eliminar el cargo:</p>
                <p class="mb-0" style="font-weight:700; font-size:1.1rem;" id="eliminarCargoNombre"></p>
                <p class="text-muted small mt-2 mb-0"><i class="fas fa-info-circle mr-1"></i>Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #eee;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">
                    <i class="fas fa-trash-alt mr-1"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var searchTimer;
    var baseUrl = "{{ route('config.cargos.index') }}";
    var exportUrl = "{{ route('config.cargos.export') }}";

    // -- Búsqueda dinámica --
    $('#searchInput').on('keyup', function() {
        clearTimeout(searchTimer);
        var val = $(this).val();
        searchTimer = setTimeout(function() {
            // Actualizar enlace de exportación con el filtro
            $('#btnExportExcel').attr('href', exportUrl + '?search=' + encodeURIComponent(val));
            loadTable(val);
        }, 400);
    });

    function loadTable(search) {
        $.ajax({
            url: baseUrl,
            data: { search: search },
            success: function(resp) {
                $('#cargosTableBody').html(resp.html);
                $('#paginationContainer > div:last-child').html(resp.pagination);
            }
        });
    }

    // -- Refresh --
    $('#btnRefresh').on('click', function() {
        $('#searchInput').val('');
        window.location.href = baseUrl;
    });

    // -- Contadores de caracteres --
    $('#cargoNombre').on('input', function() {
        $('#charCountNombre').text($(this).val().length);
    });
    $('#cargoDescripcion').on('input', function() {
        $('#charCountDesc').text($(this).val().length);
    });

    // -- Nuevo Cargo --
    $('#btnNuevoCargo').on('click', function() {
        $('#modalCargoTitle').html('<i class="fas fa-id-badge mr-2"></i>Nuevo Cargo');
        $('#formCargo')[0].reset();
        $('#cargoId').val('');
        $('#cargoMethod').val('POST');
        $('#formCargo').attr('action', baseUrl);
        $('#charCountNombre').text('0');
        $('#charCountDesc').text('0');
        $('#modalCargo').modal('show');
    });

    // -- Editar Cargo --
    $(document).on('click', '.btn-editar-cargo', function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        var descripcion = $(this).data('descripcion') || '';
        $('#modalCargoTitle').html('<i class="fas fa-edit mr-2"></i>Editar Cargo');
        $('#cargoId').val(id);
        $('#cargoNombre').val(nombre);
        $('#cargoDescripcion').val(descripcion);
        $('#cargoMethod').val('PUT');
        $('#formCargo').attr('action', baseUrl + '/' + id);
        $('#charCountNombre').text(nombre.length);
        $('#charCountDesc').text(descripcion.length);
        $('#modalCargo').modal('show');
    });

    // -- Guardar (crear/editar) --
    $('#formCargo').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var method = $('#cargoMethod').val();

        $.ajax({
            url: url,
            method: method === 'PUT' ? 'POST' : 'POST',
            data: form.serialize(),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(resp) {
                $('#modalCargo').modal('hide');
                showAlert('success', resp.message || 'Operación exitosa.');
                setTimeout(function() { window.location.reload(); }, 800);
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var msg = '';
                    $.each(errors, function(k, v) { msg += v.join(', ') + '\n'; });
                    showAlert('danger', msg);
                } else {
                    showAlert('danger', 'Error al procesar la solicitud.');
                }
            }
        });
    });

    // -- Ver Detalle --
    $(document).on('click', '.btn-ver-cargo', function() {
        var id = $(this).data('id');
        $.ajax({
            url: baseUrl + '/' + id,
            method: 'GET',
            success: function(cargo) {
                $('#verCargoId').text(cargo.id);
                $('#verCargoNombre').text(cargo.nombre);
                $('#verCargoDescripcion').text(cargo.descripcion || 'Sin descripción');
                var fecha = cargo.created_at ? new Date(cargo.created_at).toLocaleDateString('es-CO', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' }) : '—';
                $('#verCargoFecha').text(fecha);
                $('#modalVerCargo').modal('show');
            }
        });
    });

    // -- Eliminar --
    var deleteId = null;
    $(document).on('click', '.btn-eliminar-cargo', function() {
        deleteId = $(this).data('id');
        $('#eliminarCargoNombre').text($(this).data('nombre'));
        $('#modalEliminarCargo').modal('show');
    });

    $('#btnConfirmarEliminar').on('click', function() {
        if (!deleteId) return;
        $.ajax({
            url: baseUrl + '/' + deleteId,
            method: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
            success: function(resp) {
                $('#modalEliminarCargo').modal('hide');
                showAlert('success', resp.message || 'Cargo eliminado.');
                setTimeout(function() { window.location.reload(); }, 800);
            },
            error: function() {
                showAlert('danger', 'Error al eliminar el cargo.');
            }
        });
    });

    // -- Alert helper --
    function showAlert(type, msg) {
        var html = '<div class="alert alert-' + type + ' alert-dismissible fade show shadow-sm" role="alert" style="position:fixed;top:70px;right:20px;z-index:9999;min-width:300px;border-radius:8px;">' +
            '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + ' mr-2"></i>' + msg +
            '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>';
        $('body').append(html);
        setTimeout(function() { $('.alert').alert('close'); }, 4000);
    }
});
</script>
@endpush
