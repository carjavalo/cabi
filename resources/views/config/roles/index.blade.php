@extends('layouts.app')

@section('title','Gestión de Roles')
@section('header','Gestión de Roles')

@push('head')
<style>
    .corporate-gradient { background: linear-gradient(135deg, #2e3a75 0%, #1e2a55 100%); }
    .hover-scale { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-scale:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(46,58,117,0.3); }
    .stat-card { border-radius: 12px; padding: 20px; color: #fff; position: relative; overflow: hidden; }
    .stat-card .stat-icon { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); font-size: 3rem; opacity: 0.15; }
    .table-card { border: none; border-radius: 12px; overflow: hidden; }
    .search-box { position: relative; }
    .search-box .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #adb5bd; }
    .search-box input { padding-left: 40px; border-radius: 25px; border: 2px solid #e0e0e0; transition: border-color 0.3s; }
    .search-box input:focus { border-color: #2e3a75; box-shadow: 0 0 0 0.2rem rgba(46,58,117,0.15); }
    .filter-label { font-size: 0.75rem; font-weight: 700; color: #6c757d; text-transform: uppercase; margin-bottom: 4px; }
    .table thead th { border-top: none; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
    .table thead th.sortable { cursor: pointer; user-select: none; }
    .table thead th.sortable:hover { color: #2e3a75 !important; }
    .table tbody tr { transition: background-color 0.15s ease; }
    .table tbody tr:hover { background-color: rgba(46,58,117,0.04); }
    .btn-corporate { background: linear-gradient(135deg, #2e3a75 0%, #3d4f9f 100%); color: #fff; border: none; border-radius: 8px; padding: 10px 24px; font-weight: 600; transition: all 0.3s; }
    .btn-corporate:hover { background: linear-gradient(135deg, #1e2a55 0%, #2e3a75 100%); color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(46,58,117,0.35); }
    .modal-header.corporate { background: linear-gradient(135deg, #2e3a75 0%, #1e2a55 100%); color: #fff; border-bottom: none; }
    .modal-header.corporate .close { color: #fff; text-shadow: none; opacity: 0.9; }
    .pagination { margin-bottom: 0; }
    .pagination .page-link { color: #2e3a75; }
    .pagination .page-item.active .page-link { background: #2e3a75; border-color: #2e3a75; color: #fff; }
    .btn-export { background: #1b7a3d; color: #fff; border: none; border-radius: 8px; padding: 8px 18px; font-weight: 600; }
    .btn-export:hover { background: #15612f; color: #fff; }
    .fade-in { animation: fadeIn 0.3s ease-in; }
    @keyframes fadeIn { from { opacity:0; transform: translateY(8px); } to { opacity:1; transform: translateY(0); } }
    .detail-label { font-weight: 700; color: #2e3a75; font-size: 0.85rem; text-transform: uppercase; }
    .detail-value { font-size: 1rem; color: #333; }
    #rolesTableBody.loading { opacity: 0.5; pointer-events: none; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 fade-in">
    <!-- Encabezado -->
    <div class="corporate-gradient rounded shadow-lg mb-4 p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2 class="text-white mb-1" style="font-weight:700;">
                    <i class="fas fa-user-shield mr-2"></i>Gestión de Roles
                </h2>
                <p class="mb-0 small" style="color:rgba(255,255,255,0.7);">Administra los roles que se asignan a los usuarios del aplicativo</p>
            </div>
            @if($puedeEditar)
            <button class="btn btn-light btn-lg shadow-sm hover-scale" id="btnNuevoRol" style="font-weight:600;">
                <i class="fas fa-plus mr-2"></i>Nuevo Rol
            </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <!-- Indicadores -->
    <div class="row mb-4">
        <div class="col-lg-3 col-sm-6 mb-3">
            <div class="stat-card hover-scale shadow" style="background: linear-gradient(135deg, #2e3a75,#3d4f9f);">
                <div style="font-size:0.8rem;opacity:0.8;">Total Roles</div>
                <div style="font-size:2rem;font-weight:700;">{{ $stats['total'] }}</div>
                <i class="fas fa-user-tag stat-icon"></i>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-3">
            <div class="stat-card hover-scale shadow" style="background: linear-gradient(135deg, #1b7a3d,#28a745);">
                <div style="font-size:0.8rem;opacity:0.8;">Roles Activos</div>
                <div style="font-size:2rem;font-weight:700;">{{ $stats['activos'] }}</div>
                <i class="fas fa-check-circle stat-icon"></i>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-3">
            <div class="stat-card hover-scale shadow" style="background: linear-gradient(135deg, #6f42c1,#8e5ed6);">
                <div style="font-size:0.8rem;opacity:0.8;">Roles del Sistema</div>
                <div style="font-size:2rem;font-weight:700;">{{ $stats['sistema'] }}</div>
                <i class="fas fa-lock stat-icon"></i>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-3">
            <div class="stat-card hover-scale shadow" style="background: linear-gradient(135deg, #e67e22,#f39c12);">
                <div style="font-size:0.8rem;opacity:0.8;">Usuarios con Rol</div>
                <div style="font-size:2rem;font-weight:700;">{{ $stats['usuarios'] }}</div>
                <i class="fas fa-users stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
        <div class="card-body py-3">
            <form id="formFiltros" onsubmit="return false;">
                <div class="row align-items-end">
                    <div class="col-lg-4 col-md-6 mb-2">
                        <div class="filter-label">Buscar</div>
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="searchInput" name="search" class="form-control" placeholder="Nombre o descripción..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-2">
                        <div class="filter-label">Estado</div>
                        <select name="estado" class="form-control filtro">
                            <option value="">Todos</option>
                            <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                            <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-2">
                        <div class="filter-label">Tipo</div>
                        <select name="tipo" class="form-control filtro">
                            <option value="">Todos</option>
                            <option value="sistema" {{ request('tipo') === 'sistema' ? 'selected' : '' }}>Sistema</option>
                            <option value="personalizado" {{ request('tipo') === 'personalizado' ? 'selected' : '' }}>Personalizado</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-2">
                        <div class="filter-label">Usuarios</div>
                        <select name="usuarios" class="form-control filtro">
                            <option value="">Todos</option>
                            <option value="con" {{ request('usuarios') === 'con' ? 'selected' : '' }}>Con usuarios</option>
                            <option value="sin" {{ request('usuarios') === 'sin' ? 'selected' : '' }}>Sin usuarios</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-2">
                        <div class="filter-label">Por página</div>
                        <select name="per_page" class="form-control filtro">
                            @foreach([10,25,50,100] as $n)
                            <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="sort" id="sortField" value="{{ request('sort', 'id') }}">
                    <input type="hidden" name="direction" id="sortDirection" value="{{ request('direction', 'asc') }}">
                </div>
                <div class="d-flex justify-content-end flex-wrap mt-2" style="gap:10px;">
                    <button type="button" class="btn btn-outline-secondary" id="btnLimpiar"><i class="fas fa-eraser mr-1"></i> Limpiar filtros</button>
                    <a href="{{ route('config.roles.export', request()->query()) }}" class="btn btn-export" id="btnExportExcel">
                        <i class="fas fa-file-excel mr-1"></i> Exportar Excel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card table-card shadow-lg" style="border-radius:12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f0f2f8;">
                        <tr>
                            <th class="px-4 py-3 text-muted small sortable" data-sort="id" style="width:80px; font-weight:700;"># <i class="fas fa-sort"></i></th>
                            <th class="px-4 py-3 text-muted small sortable" data-sort="nombre" style="font-weight:700;">Rol <i class="fas fa-sort"></i></th>
                            <th class="px-4 py-3 text-muted small" style="font-weight:700;">Descripción</th>
                            <th class="px-4 py-3 text-muted small sortable" data-sort="es_sistema" style="font-weight:700;">Tipo <i class="fas fa-sort"></i></th>
                            <th class="px-4 py-3 text-muted small sortable" data-sort="activo" style="font-weight:700;">Estado <i class="fas fa-sort"></i></th>
                            <th class="px-4 py-3 text-muted small text-center sortable" data-sort="users_count" style="font-weight:700;">Usuarios <i class="fas fa-sort"></i></th>
                            <th class="px-4 py-3 text-muted small text-center" style="width:160px; font-weight:700;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="rolesTableBody">
                        @include('config.roles._table', ['roles' => $roles, 'puedeEditar' => $puedeEditar])
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap">
            <div class="text-muted small" id="resumenPaginacion">
                Mostrando {{ $roles->firstItem() ?? 0 }} a {{ $roles->lastItem() ?? 0 }} de {{ $roles->total() }} registros
            </div>
            <div id="paginationLinks">{{ $roles->links() }}</div>
        </div>
    </div>
</div>

@if($puedeEditar)
<!-- MODAL: Crear / Editar Rol -->
<div class="modal fade" id="modalRol" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none;">
            <div class="modal-header corporate">
                <h5 class="modal-title" id="modalRolTitle"><i class="fas fa-user-tag mr-2"></i>Nuevo Rol</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="formRol" method="POST">
                @csrf
                <input type="hidden" id="rolMethod" name="_method" value="POST">
                <div class="modal-body px-4 py-4">
                    <div class="alert alert-info small d-none" id="avisoSistema">
                        <i class="fas fa-lock mr-1"></i> Es un rol del sistema: el aplicativo usa su nombre para los permisos, por lo que solo se puede modificar la descripción.
                    </div>
                    <div class="alert alert-warning small d-none" id="avisoRenombrar">
                        <i class="fas fa-info-circle mr-1"></i> Si cambia el nombre, se actualizará automáticamente en todos los usuarios que tienen asignado este rol.
                    </div>
                    <div class="form-group mb-3">
                        <label for="rolNombre" class="detail-label">Nombre del Rol <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="rolNombre" name="nombre" maxlength="60" required placeholder="Ej: Auxiliar Administrativo">
                        <small class="text-muted"><span id="charCountNombre">0</span>/60 caracteres</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="rolDescripcion" class="detail-label">Descripción</label>
                        <textarea class="form-control" id="rolDescripcion" name="descripcion" maxlength="255" rows="3" placeholder="Descripción breve del rol (opcional)"></textarea>
                        <small class="text-muted"><span id="charCountDesc">0</span>/255 caracteres</small>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" class="custom-control-input" id="rolActivo" name="activo" value="1" checked>
                        <label class="custom-control-label" for="rolActivo">Activo (disponible para asignar a usuarios)</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #eee;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-corporate" id="btnGuardarRol"><i class="fas fa-save mr-1"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Confirmar Eliminación -->
<div class="modal fade" id="modalEliminarRol" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#dc3545,#c82333); color:#fff; border-bottom:none;">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body px-4 py-4">
                <p class="mb-1">¿Está seguro de que desea eliminar el rol:</p>
                <p class="mb-0" style="font-weight:700; font-size:1.1rem;" id="eliminarRolNombre"></p>
                <p class="text-muted small mt-2 mb-0"><i class="fas fa-info-circle mr-1"></i>Esta acción no se puede deshacer. Solo se pueden eliminar roles sin usuarios asignados.</p>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #eee;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar"><i class="fas fa-trash-alt mr-1"></i> Eliminar</button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- MODAL: Ver Detalle -->
<div class="modal fade" id="modalVerRol" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none;">
            <div class="modal-header corporate">
                <h5 class="modal-title"><i class="fas fa-eye mr-2"></i>Detalle del Rol</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body px-4 py-4">
                <div class="row">
                    <div class="col-6 mb-3"><div class="detail-label mb-1">Nombre</div><div class="detail-value" id="verNombre">—</div></div>
                    <div class="col-6 mb-3"><div class="detail-label mb-1">Tipo / Estado</div><div class="detail-value" id="verTipo">—</div></div>
                    <div class="col-12 mb-3"><div class="detail-label mb-1">Descripción</div><div class="detail-value" id="verDescripcion">—</div></div>
                    <div class="col-6 mb-3"><div class="detail-label mb-1">Usuarios asignados</div><div class="detail-value" id="verUsuariosCount">—</div></div>
                    <div class="col-6 mb-3"><div class="detail-label mb-1">Fecha de Creación</div><div class="detail-value" id="verFecha">—</div></div>
                    <div class="col-12">
                        <div class="detail-label mb-1">Usuarios (primeros 10)</div>
                        <ul class="list-unstyled small mb-0" id="verUsuarios"></ul>
                        <a href="#" id="verTodosUsuarios" class="small d-inline-block mt-2"><i class="fas fa-external-link-alt mr-1"></i>Ver todos en Gestión de Usuarios</a>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #eee;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var baseUrl = "{{ route('config.roles.index') }}";
    var exportUrl = "{{ route('config.roles.export') }}";
    var usuariosUrl = "{{ url('/configuracion/usuarios') }}";
    var csrf = $('meta[name="csrf-token"]').attr('content');
    var searchTimer;

    function escapeHtml(s) { return $('<div>').text(s == null ? '' : s).html(); }

    // ---------- Listado con filtros, orden y paginación (AJAX) ----------
    function queryString() { return $('#formFiltros').serialize(); }

    function actualizarIconosOrden() {
        var sort = $('#sortField').val(), dir = $('#sortDirection').val();
        $('th.sortable i').attr('class', 'fas fa-sort');
        $('th.sortable[data-sort="' + sort + '"] i').attr('class', 'fas fa-sort-' + (dir === 'asc' ? 'up' : 'down'));
    }

    function loadTable(url) {
        var qs = queryString();
        url = url || (baseUrl + '?' + qs);
        $('#btnExportExcel').attr('href', exportUrl + '?' + qs);
        $('#rolesTableBody').addClass('loading');
        $.ajax({
            url: url,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(resp) {
                $('#rolesTableBody').html(resp.html);
                $('#paginationLinks').html(resp.pagination);
                $('#resumenPaginacion').text(resp.resumen);
                if (window.history && history.replaceState) {
                    history.replaceState(null, '', url.replace(/([?&])page=1(&|$)/, '$1').replace(/[?&]$/, ''));
                }
            },
            error: function() { showAlert('danger', 'No se pudo cargar el listado.'); },
            complete: function() { $('#rolesTableBody').removeClass('loading'); }
        });
    }

    $('#searchInput').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() { loadTable(); }, 400);
    });
    $('.filtro').on('change', function() { loadTable(); });

    $('th.sortable').on('click', function() {
        var campo = $(this).data('sort');
        if ($('#sortField').val() === campo) {
            $('#sortDirection').val($('#sortDirection').val() === 'asc' ? 'desc' : 'asc');
        } else {
            $('#sortField').val(campo);
            $('#sortDirection').val('asc');
        }
        actualizarIconosOrden();
        loadTable();
    });

    $(document).on('click', '#paginationLinks a.page-link', function(e) {
        e.preventDefault();
        loadTable($(this).attr('href'));
    });

    $('#btnLimpiar').on('click', function() {
        $('#formFiltros')[0].reset();
        $('#searchInput').val('');
        $('#formFiltros select').val('');
        $('select[name="per_page"]').val('10');
        $('#sortField').val('id');
        $('#sortDirection').val('asc');
        actualizarIconosOrden();
        loadTable(baseUrl);
    });

    actualizarIconosOrden();

    // ---------- Ver detalle ----------
    $(document).on('click', '.btn-ver-rol', function() {
        $.get(baseUrl + '/' + $(this).data('id'), function(rol) {
            $('#verNombre').text(rol.nombre);
            $('#verTipo').html((rol.es_sistema ? 'Sistema' : 'Personalizado') + ' · ' + (rol.activo ? '<span class="text-success">Activo</span>' : '<span class="text-secondary">Inactivo</span>'));
            $('#verDescripcion').text(rol.descripcion || 'Sin descripción');
            $('#verUsuariosCount').text(rol.users_count);
            $('#verFecha').text(rol.created_at ? new Date(rol.created_at).toLocaleString('es-CO') : '—');
            var lista = (rol.usuarios || []).map(function(u) {
                return '<li><i class="fas fa-user text-muted mr-1"></i>' + escapeHtml((u.name || '') + ' ' + (u.apellido1 || '')) + ' <span class="text-muted">— ' + escapeHtml(u.email) + '</span></li>';
            });
            $('#verUsuarios').html(lista.length ? lista.join('') : '<li class="text-muted">Ningún usuario tiene este rol.</li>');
            $('#verTodosUsuarios').attr('href', usuariosUrl + '?role_filter=' + encodeURIComponent(rol.nombre)).toggle(rol.users_count > 0);
            $('#modalVerRol').modal('show');
        }).fail(function() { showAlert('danger', 'No se pudo cargar el detalle.'); });
    });

    @if($puedeEditar)
    // ---------- Crear / Editar ----------
    $('#rolNombre').on('input', function() { $('#charCountNombre').text($(this).val().length); });
    $('#rolDescripcion').on('input', function() { $('#charCountDesc').text($(this).val().length); });

    var nombreOriginal = '';
    $('#rolNombre').on('input', function() {
        $('#avisoRenombrar').toggleClass('d-none', !nombreOriginal || $(this).val().trim() === nombreOriginal);
    });

    function prepararModal(titulo, accion, metodo, datos) {
        $('#modalRolTitle').html(titulo);
        $('#formRol').attr('action', accion);
        $('#rolMethod').val(metodo);
        $('#rolNombre').val(datos.nombre).prop('readonly', datos.sistema);
        $('#rolDescripcion').val(datos.descripcion);
        $('#rolActivo').prop('checked', datos.activo).prop('disabled', datos.sistema);
        $('#avisoSistema').toggleClass('d-none', !datos.sistema);
        $('#avisoRenombrar').addClass('d-none');
        $('#charCountNombre').text(datos.nombre.length);
        $('#charCountDesc').text(datos.descripcion.length);
        $('#modalRol').modal('show');
    }

    $('#btnNuevoRol').on('click', function() {
        nombreOriginal = '';
        prepararModal('<i class="fas fa-user-tag mr-2"></i>Nuevo Rol', baseUrl, 'POST', { nombre: '', descripcion: '', activo: true, sistema: false });
    });

    $(document).on('click', '.btn-editar-rol', function() {
        var b = $(this);
        var sistema = String(b.data('sistema')) === '1';
        var usuarios = parseInt(b.closest('tr').find('.badge-light').text(), 10) || 0;
        nombreOriginal = (sistema || usuarios === 0) ? '' : String(b.data('nombre'));
        prepararModal('<i class="fas fa-edit mr-2"></i>Editar Rol', baseUrl + '/' + b.data('id'), 'PUT', {
            nombre: String(b.data('nombre')), descripcion: String(b.data('descripcion') || ''),
            activo: String(b.data('activo')) === '1', sistema: sistema
        });
    });

    $('#formRol').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#btnGuardarRol').prop('disabled', true);
        // El checkbox deshabilitado (roles del sistema) no se envía: se fuerza activo=1
        var data = $(this).serializeArray();
        if ($('#rolActivo').prop('disabled')) {
            data = data.filter(function(f) { return f.name !== 'activo'; });
            data.push({ name: 'activo', value: '1' });
        }
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $.param(data),
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            success: function(resp) {
                $('#modalRol').modal('hide');
                showAlert('success', resp.message || 'Operación exitosa.');
                setTimeout(function() { window.location.reload(); }, 800);
            },
            error: function(xhr) { showAlert('danger', mensajeError(xhr)); },
            complete: function() { btn.prop('disabled', false); }
        });
    });

    // ---------- Eliminar ----------
    var deleteId = null;
    $(document).on('click', '.btn-eliminar-rol', function() {
        var usuarios = parseInt($(this).data('usuarios'), 10) || 0;
        if (usuarios > 0) {
            showAlert('warning', 'El rol "' + escapeHtml($(this).data('nombre')) + '" está asignado a ' + usuarios + ' usuario(s). Reasígnelos o desactive el rol en lugar de eliminarlo.');
            return;
        }
        deleteId = $(this).data('id');
        $('#eliminarRolNombre').text($(this).data('nombre'));
        $('#modalEliminarRol').modal('show');
    });

    $('#btnConfirmarEliminar').on('click', function() {
        if (!deleteId) return;
        $.ajax({
            url: baseUrl + '/' + deleteId,
            method: 'POST',
            data: { _token: csrf, _method: 'DELETE' },
            headers: { 'Accept': 'application/json' },
            success: function(resp) {
                $('#modalEliminarRol').modal('hide');
                showAlert('success', resp.message || 'Rol eliminado.');
                setTimeout(function() { window.location.reload(); }, 800);
            },
            error: function(xhr) {
                $('#modalEliminarRol').modal('hide');
                showAlert('danger', mensajeError(xhr));
            }
        });
    });
    @endif

    function mensajeError(xhr) {
        var r = xhr.responseJSON || {};
        if (xhr.status === 422 && r.errors) {
            var msg = [];
            $.each(r.errors, function(k, v) { msg.push(v.join(', ')); });
            return escapeHtml(msg.join(' '));
        }
        return escapeHtml(r.message || 'Error al procesar la solicitud.');
    }

    function showAlert(type, msg) {
        var icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
        var html = '<div class="alert alert-' + type + ' alert-dismissible fade show shadow-sm" role="alert" style="position:fixed;top:70px;right:20px;z-index:9999;min-width:300px;max-width:420px;border-radius:8px;">' +
            '<i class="fas fa-' + icon + ' mr-2"></i>' + msg +
            '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>';
        var $a = $(html).appendTo('body');
        setTimeout(function() { $a.alert('close'); }, 5000);
    }
});
</script>
@endpush
