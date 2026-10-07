@extends('layouts.app')

@section('title','Gestión de Permisos')
@section('header','Gestión de Permisos')

@push('head')
<style>
    :root { --gp-primary:#2e3a75; --gp-primary-2:#3d4f9f; --gp-soft:#eef0f8; --gp-ok:#1b9e55; --gp-warn:#f39c12; --gp-danger:#dc3545; --gp-border:#e4e7f1; }
    .gp-hero { background: radial-gradient(1200px 300px at 10% -40%, rgba(255,255,255,.18), transparent), linear-gradient(135deg,#2e3a75 0%,#1e2a55 60%,#16204a 100%); border-radius:16px; color:#fff; padding:24px 28px; position:relative; overflow:hidden; }
    .gp-hero:after { content:""; position:absolute; right:-60px; top:-60px; width:240px; height:240px; border-radius:50%; background:rgba(255,255,255,.06); }
    .gp-hero h2 { font-weight:800; margin:0; letter-spacing:.2px; }
    .gp-chip-stat { background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.18); border-radius:12px; padding:8px 14px; min-width:110px; backdrop-filter: blur(4px); }
    .gp-chip-stat b { display:block; font-size:1.4rem; line-height:1.1; }
    .gp-chip-stat span { font-size:.72rem; text-transform:uppercase; opacity:.8; letter-spacing:.5px; }
    .gp-btn-hero { border-radius:10px; font-weight:700; padding:9px 16px; }
    .gp-card { background:#fff; border:1px solid var(--gp-border); border-radius:14px; box-shadow:0 4px 18px rgba(30,42,85,.06); }
    .gp-banner { border-radius:12px; border:1px dashed var(--gp-warn); background:#fff8ec; padding:10px 16px; }
    /* Roles */
    .gp-roles { max-height: calc(100vh - 260px); overflow:auto; padding:6px; }
    .gp-role { display:flex; align-items:center; gap:12px; padding:12px; border-radius:12px; cursor:pointer; border:2px solid transparent; transition:all .15s; margin-bottom:6px; }
    .gp-role:hover { background:var(--gp-soft); }
    .gp-role.active { background:linear-gradient(135deg,rgba(46,58,117,.10),rgba(61,79,159,.06)); border-color:var(--gp-primary); }
    .gp-avatar { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; flex-shrink:0; font-size:1rem; }
    .gp-role .nombre { font-weight:700; color:#1e2a55; font-size:.92rem; line-height:1.15; }
    .gp-role .meta { font-size:.72rem; color:#7a809b; }
    .gp-mini-bar { height:5px; background:#e9ecf5; border-radius:5px; overflow:hidden; margin-top:5px; }
    .gp-mini-bar > i { display:block; height:100%; background:linear-gradient(90deg,var(--gp-primary),#5b6fd6); border-radius:5px; transition:width .35s; }
    /* Tabs */
    .gp-tabs { border-bottom:none; gap:6px; flex-wrap:wrap; }
    .gp-tabs .nav-link { border:none; border-radius:10px; color:#5a6185; font-weight:700; padding:9px 16px; }
    .gp-tabs .nav-link.active { background:var(--gp-primary); color:#fff; box-shadow:0 4px 12px rgba(46,58,117,.3); }
    .gp-tabs .nav-link .badge { font-size:.68rem; }
    /* Encabezado rol */
    .gp-role-head { display:flex; align-items:center; gap:16px; flex-wrap:wrap; }
    .gp-ring { --p:0; width:64px; height:64px; border-radius:50%; background:conic-gradient(var(--gp-primary) calc(var(--p)*1%), #e6e9f4 0); display:flex; align-items:center; justify-content:center; transition: background .4s; }
    .gp-ring > span { width:50px; height:50px; border-radius:50%; background:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.85rem; color:var(--gp-primary); }
    /* Filtros tipo chip */
    .gp-chips { display:flex; gap:6px; flex-wrap:wrap; }
    .gp-chip { border:1px solid var(--gp-border); background:#fff; color:#4a5175; border-radius:20px; padding:5px 12px; font-size:.78rem; font-weight:700; cursor:pointer; user-select:none; transition:all .15s; }
    .gp-chip:hover { border-color:var(--gp-primary); }
    .gp-chip.on { background:var(--gp-primary); border-color:var(--gp-primary); color:#fff; }
    .gp-search { position:relative; }
    .gp-search i { position:absolute; left:13px; top:50%; transform:translateY(-50%); color:#a3a9c2; }
    .gp-search input { padding-left:36px; border-radius:22px; border:2px solid var(--gp-border); }
    .gp-search input:focus { border-color:var(--gp-primary); box-shadow:none; }
    /* Módulos */
    .gp-modulo { border:1px solid var(--gp-border); border-radius:14px; margin-bottom:12px; overflow:hidden; background:#fff; transition: box-shadow .2s; }
    .gp-modulo:hover { box-shadow:0 6px 18px rgba(30,42,85,.08); }
    .gp-mod-head { display:flex; align-items:center; gap:12px; padding:12px 16px; cursor:pointer; background:linear-gradient(180deg,#fafbff,#f4f6fc); }
    .gp-mod-icon { width:38px; height:38px; border-radius:10px; background:rgba(46,58,117,.1); color:var(--gp-primary); display:flex; align-items:center; justify-content:center; }
    .gp-mod-title { font-weight:800; color:#1e2a55; font-size:.95rem; }
    .gp-mod-count { font-size:.75rem; color:#7a809b; }
    .gp-mod-body { padding:6px 10px 10px; display:grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap:8px; }
    .gp-modulo.collapsed .gp-mod-body { display:none; }
    .gp-modulo .gp-caret { transition: transform .2s; color:#9aa0bb; }
    .gp-modulo.collapsed .gp-caret { transform: rotate(-90deg); }
    .gp-perm { display:flex; align-items:flex-start; gap:10px; padding:10px 12px; border-radius:10px; border:1px solid #edf0f7; transition: all .15s; background:#fff; }
    .gp-perm:hover { border-color:#cfd5ea; background:#fbfcff; }
    .gp-perm.on { border-color:rgba(27,158,85,.35); background:linear-gradient(135deg,rgba(27,158,85,.06),#fff); }
    .gp-perm .p-nombre { font-weight:700; font-size:.86rem; color:#273058; line-height:1.2; }
    .gp-perm code { font-size:.7rem; color:#6c7393; background:#f3f5fb; padding:1px 6px; border-radius:5px; word-break:break-all; }
    .gp-badge { font-size:.62rem; font-weight:800; padding:2px 7px; border-radius:6px; text-transform:uppercase; letter-spacing:.3px; display:inline-block; }
    .gp-b-get { background:#e3f2fd; color:#1565c0; } .gp-b-post { background:#e8f5e9; color:#2e7d32; }
    .gp-b-put { background:#fff3e0; color:#ef6c00; } .gp-b-delete { background:#fdecea; color:#c62828; }
    .gp-b-custom { background:#f3e5f5; color:#7b1fa2; } .gp-b-new { background:#fff4d6; color:#b7791f; }
    .gp-b-off { background:#eceff1; color:#607d8b; } .gp-b-orf { background:#fdecea; color:#c62828; }
    /* Switch */
    .gp-switch { position:relative; display:inline-block; width:40px; height:22px; flex-shrink:0; margin:0; }
    .gp-switch input { opacity:0; width:0; height:0; }
    .gp-switch span { position:absolute; inset:0; background:#cfd4e3; border-radius:22px; transition:.2s; cursor:pointer; }
    .gp-switch span:before { content:""; position:absolute; width:16px; height:16px; left:3px; top:3px; background:#fff; border-radius:50%; transition:.2s; box-shadow:0 1px 3px rgba(0,0,0,.2); }
    .gp-switch input:checked + span { background:var(--gp-ok); }
    .gp-switch input:checked + span:before { transform:translateX(18px); }
    .gp-switch input:indeterminate + span { background:var(--gp-warn); }
    .gp-switch input:indeterminate + span:before { transform:translateX(9px); }
    .gp-switch input:disabled + span { opacity:.55; cursor:not-allowed; }
    /* Asignables */
    .gp-asig-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(220px,1fr)); gap:12px; }
    .gp-asig { border:2px solid var(--gp-border); border-radius:14px; padding:14px; cursor:pointer; display:flex; gap:12px; align-items:center; transition:all .15s; user-select:none; }
    .gp-asig:hover { border-color:#b9c1e0; }
    .gp-asig.on { border-color:var(--gp-ok); background:linear-gradient(135deg,rgba(27,158,85,.08),#fff); }
    .gp-asig.locked { opacity:.55; cursor:not-allowed; }
    .gp-asig .check { margin-left:auto; width:24px; height:24px; border-radius:50%; border:2px solid #cfd4e3; display:flex; align-items:center; justify-content:center; color:#fff; font-size:.75rem; }
    .gp-asig.on .check { background:var(--gp-ok); border-color:var(--gp-ok); }
    /* Matriz */
    .gp-matriz-wrap { max-height: calc(100vh - 300px); overflow:auto; border:1px solid var(--gp-border); border-radius:12px; }
    .gp-matriz { border-collapse:separate; border-spacing:0; width:100%; font-size:.82rem; }
    .gp-matriz th, .gp-matriz td { padding:8px 10px; border-bottom:1px solid #eef0f6; background:#fff; }
    .gp-matriz thead th { position:sticky; top:0; z-index:3; background:#f0f2f8; color:#4a5175; font-size:.72rem; text-transform:uppercase; text-align:center; white-space:nowrap; }
    .gp-matriz thead th:first-child { left:0; z-index:4; text-align:left; }
    .gp-matriz td:first-child, .gp-matriz th:first-child { position:sticky; left:0; z-index:2; min-width:280px; }
    .gp-matriz tr.grupo td { background:#f7f8fc; font-weight:800; color:#1e2a55; }
    .gp-matriz td.c { text-align:center; cursor:pointer; }
    .gp-matriz td.c:hover { background:#f2f5ff; }
    .gp-dot { width:22px; height:22px; border-radius:7px; display:inline-flex; align-items:center; justify-content:center; border:2px solid #d3d8e8; color:#fff; font-size:.7rem; transition:all .15s; }
    .gp-dot.on { background:var(--gp-ok); border-color:var(--gp-ok); }
    .gp-dot.lock { background:#9aa3c7; border-color:#9aa3c7; }
    /* Catálogo */
    .gp-cat th { font-size:.72rem; text-transform:uppercase; color:#6c7393; border-top:none; white-space:nowrap; }
    .gp-cat td { vertical-align:middle; font-size:.85rem; }
    .gp-empty { text-align:center; color:#9aa0bb; padding:40px 10px; }
    .gp-empty i { font-size:2.6rem; opacity:.35; display:block; margin-bottom:10px; }
    /* Toast */
    #gpToasts { position:fixed; right:20px; bottom:20px; z-index:2000; display:flex; flex-direction:column; gap:8px; }
    .gp-toast { background:#1e2a55; color:#fff; border-radius:12px; padding:11px 14px; min-width:280px; max-width:420px; box-shadow:0 10px 30px rgba(0,0,0,.25); display:flex; gap:10px; align-items:center; animation: gpIn .25s ease-out; font-size:.88rem; }
    .gp-toast.err { background:#b02a37; }
    .gp-toast button { margin-left:auto; background:rgba(255,255,255,.15); border:none; color:#fff; border-radius:8px; padding:4px 10px; font-weight:700; font-size:.78rem; }
    @keyframes gpIn { from { transform: translateY(12px); opacity:0; } to { transform:none; opacity:1; } }
    .gp-spin { animation: gpSpin 1s linear infinite; }
    @keyframes gpSpin { to { transform: rotate(360deg); } }
    .modal-header.gp { background:linear-gradient(135deg,#2e3a75,#1e2a55); color:#fff; border-bottom:none; }
    .modal-header.gp .close { color:#fff; opacity:.9; text-shadow:none; }
    .gp-label { font-weight:800; color:var(--gp-primary); font-size:.78rem; text-transform:uppercase; }
    .pagination .page-link { color:var(--gp-primary); }
    .pagination .page-item.active .page-link { background:var(--gp-primary); border-color:var(--gp-primary); color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4" id="gpApp">

    {{-- Encabezado --}}
    <div class="gp-hero shadow mb-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap:16px; position:relative; z-index:1;">
            <div>
                <h2><i class="fas fa-key mr-2"></i>Gestión de Permisos</h2>
                <div class="small mt-1" style="opacity:.8;">Define qué funciones del aplicativo puede usar cada rol y qué roles puede asignar al crear usuarios.</div>
                <div class="d-flex flex-wrap mt-3" style="gap:10px;">
                    <div class="gp-chip-stat"><b id="stFunciones">0</b><span>Funciones</span></div>
                    <div class="gp-chip-stat"><b id="stModulos">0</b><span>Módulos</span></div>
                    <div class="gp-chip-stat"><b id="stRoles">0</b><span>Roles</span></div>
                    <div class="gp-chip-stat"><b id="stNuevos">0</b><span>Nuevas</span></div>
                </div>
            </div>
            <div class="d-flex flex-wrap" style="gap:8px;">
                <button class="btn btn-light gp-btn-hero" id="btnSincronizar" title="Detectar opciones y vistas nuevas del aplicativo">
                    <i class="fas fa-sync-alt mr-1"></i> Sincronizar funciones
                </button>
                <button class="btn btn-outline-light gp-btn-hero" id="btnNuevoPermiso">
                    <i class="fas fa-plus mr-1"></i> Permiso personalizado
                </button>
            </div>
        </div>
    </div>

    {{-- Aviso de funciones nuevas --}}
    <div class="gp-banner mb-3 d-none" id="bannerNuevos">
        <div class="d-flex align-items-center flex-wrap" style="gap:10px;">
            <i class="fas fa-bell text-warning"></i>
            <div class="flex-grow-1 small"><b id="bannerNuevosTxt"></b> Quedaron disponibles para todos los roles; revíselas y restrinja las que correspondan.</div>
            <button class="btn btn-sm btn-warning font-weight-bold" id="btnVerNuevos"><i class="fas fa-eye mr-1"></i>Ver nuevas</button>
            <button class="btn btn-sm btn-outline-secondary" id="btnRevisados"><i class="fas fa-check mr-1"></i>Marcar revisadas</button>
        </div>
    </div>

    <div class="row">
        {{-- Panel de roles --}}
        <div class="col-xl-3 col-lg-4 mb-3">
            <div class="gp-card h-100">
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <b style="color:#1e2a55;"><i class="fas fa-user-shield mr-1"></i> Roles</b>
                        <a href="{{ route('config.roles.index') }}" class="small" title="Ir a Gestión de Roles"><i class="fas fa-external-link-alt"></i> Gestionar</a>
                    </div>
                    <div class="gp-search"><i class="fas fa-search"></i><input type="text" class="form-control form-control-sm" id="buscarRol" placeholder="Buscar rol..."></div>
                </div>
                <div class="gp-roles" id="listaRoles"></div>
            </div>
        </div>

        {{-- Área de trabajo --}}
        <div class="col-xl-9 col-lg-8 mb-3">
            <div class="gp-card p-3">
                <ul class="nav gp-tabs mb-3" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tabPermisos"><i class="fas fa-th-large mr-1"></i> Permisos del rol</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tabAsignables"><i class="fas fa-user-plus mr-1"></i> Roles asignables</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tabMatriz"><i class="fas fa-table mr-1"></i> Matriz general</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tabCatalogo"><i class="fas fa-list-ul mr-1"></i> Catálogo de funciones <span class="badge badge-warning ml-1 d-none" id="tabNuevosBadge"></span></a></li>
                </ul>

                <div class="tab-content">
                    {{-- ============ PERMISOS DEL ROL ============ --}}
                    <div class="tab-pane fade show active" id="tabPermisos">
                        <div class="gp-role-head mb-3">
                            <div class="gp-ring" id="rolRing"><span id="rolPct">0%</span></div>
                            <div class="flex-grow-1">
                                <div style="font-size:1.25rem; font-weight:800; color:#1e2a55;" id="rolNombre">—</div>
                                <div class="small text-muted" id="rolMeta"></div>
                            </div>
                            <div class="d-flex flex-wrap" style="gap:6px;" id="accionesRol">
                                <button class="btn btn-sm btn-success font-weight-bold" id="btnAsignarVisibles"><i class="fas fa-check-double mr-1"></i>Asignar visibles</button>
                                <button class="btn btn-sm btn-outline-danger font-weight-bold" id="btnQuitarVisibles"><i class="fas fa-times mr-1"></i>Quitar visibles</button>
                                <div class="input-group input-group-sm" style="width:auto;">
                                    <select class="custom-select custom-select-sm" id="copiarDesde" style="max-width:170px;"><option value="">Copiar de…</option></select>
                                    <div class="input-group-append"><button class="btn btn-outline-primary" id="btnCopiar" title="Reemplazar los permisos de este rol por los del rol elegido"><i class="fas fa-clone"></i></button></div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info small d-none" id="avisoRol"></div>

                        <div class="d-flex flex-wrap align-items-center mb-3" style="gap:10px;">
                            <div class="gp-search flex-grow-1" style="min-width:220px; max-width:360px;"><i class="fas fa-search"></i><input type="text" class="form-control form-control-sm" id="buscarPermiso" placeholder="Buscar función, módulo o clave..."></div>
                            <div class="gp-chips" id="chipsEstado">
                                <span class="gp-chip on" data-v="todos">Todos</span>
                                <span class="gp-chip" data-v="asignados">Asignados</span>
                                <span class="gp-chip" data-v="sin">Sin asignar</span>
                                <span class="gp-chip" data-v="nuevos"><i class="fas fa-star"></i> Nuevos</span>
                            </div>
                            <div class="gp-chips" id="chipsTipo">
                                <span class="gp-chip on" data-v="">Todo</span>
                                <span class="gp-chip" data-v="vista"><i class="far fa-eye"></i> Vistas</span>
                                <span class="gp-chip" data-v="accion"><i class="fas fa-bolt"></i> Acciones</span>
                                <span class="gp-chip" data-v="personalizado"><i class="fas fa-magic"></i> Personalizados</span>
                            </div>
                            <div class="ml-auto">
                                <button class="btn btn-sm btn-light" id="btnExpandir" title="Expandir / contraer módulos"><i class="fas fa-expand-alt"></i></button>
                            </div>
                        </div>

                        <div id="modulos"></div>
                    </div>

                    {{-- ============ ROLES ASIGNABLES ============ --}}
                    <div class="tab-pane fade" id="tabAsignables">
                        <div class="d-flex align-items-center flex-wrap mb-3" style="gap:10px;">
                            <div class="flex-grow-1">
                                <div style="font-size:1.1rem; font-weight:800; color:#1e2a55;">Roles que <span id="asigRolNombre" class="text-primary">—</span> puede asignar</div>
                                <div class="small text-muted">Estos son los roles que aparecerán en el campo <b>Perfil de Acceso (Rol)</b> cuando este rol cree o edite usuarios.</div>
                            </div>
                            <button class="btn btn-sm btn-light" id="btnAsigTodos"><i class="fas fa-check-double mr-1"></i>Todos</button>
                            <button class="btn btn-sm btn-light" id="btnAsigNinguno"><i class="fas fa-eraser mr-1"></i>Ninguno</button>
                            <button class="btn btn-sm btn-primary font-weight-bold" id="btnGuardarAsig" style="background:var(--gp-primary);border:none;"><i class="fas fa-save mr-1"></i>Guardar</button>
                        </div>
                        <div class="alert alert-info small d-none" id="avisoAsig"></div>
                        <div class="gp-asig-grid" id="gridAsignables"></div>
                        <div class="small text-muted mt-3"><i class="fas fa-lock mr-1"></i> Por seguridad, el rol <b>Super Admin</b> solo lo puede asignar otro Super Admin.</div>
                    </div>

                    {{-- ============ MATRIZ ============ --}}
                    <div class="tab-pane fade" id="tabMatriz">
                        <div class="d-flex flex-wrap align-items-center mb-3" style="gap:10px;">
                            <div class="gp-search flex-grow-1" style="min-width:220px; max-width:360px;"><i class="fas fa-search"></i><input type="text" class="form-control form-control-sm" id="buscarMatriz" placeholder="Filtrar funciones..."></div>
                            <select class="custom-select custom-select-sm" id="moduloMatriz" style="max-width:260px;"><option value="">Todos los módulos</option></select>
                            <div class="small text-muted ml-auto"><span class="gp-dot on"><i class="fas fa-check"></i></span> asignado · clic en una celda para cambiarlo</div>
                        </div>
                        <div class="gp-matriz-wrap"><table class="gp-matriz" id="tablaMatriz"></table></div>
                    </div>

                    {{-- ============ CATÁLOGO ============ --}}
                    <div class="tab-pane fade" id="tabCatalogo">
                        <div class="d-flex flex-wrap align-items-center mb-3" style="gap:10px;">
                            <div class="gp-search flex-grow-1" style="min-width:220px; max-width:320px;"><i class="fas fa-search"></i><input type="text" class="form-control form-control-sm" id="buscarCat" placeholder="Nombre, clave, ruta..."></div>
                            <select class="custom-select custom-select-sm" id="catModulo" style="max-width:220px;"><option value="">Todos los módulos</option></select>
                            <select class="custom-select custom-select-sm" id="catTipo" style="max-width:160px;">
                                <option value="">Todos los tipos</option><option value="vista">Vistas</option><option value="accion">Acciones</option><option value="personalizado">Personalizados</option>
                            </select>
                            <select class="custom-select custom-select-sm" id="catEstado" style="max-width:170px;">
                                <option value="">Todos los estados</option><option value="activo">Controlados</option><option value="inactivo">No controlados</option><option value="nuevo">Nuevos</option><option value="huerfano">Ya no existen</option>
                            </select>
                            <select class="custom-select custom-select-sm" id="catPorPagina" style="max-width:110px;">
                                <option value="15">15</option><option value="30">30</option><option value="60">60</option><option value="1000">Todos</option>
                            </select>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover gp-cat mb-0">
                                <thead><tr><th>Función</th><th>Módulo</th><th>Tipo</th><th>Ruta</th><th class="text-center">Roles</th><th class="text-center" title="Si está apagado, la función no se controla y todos los roles pueden usarla">Controlado</th><th class="text-center">Acciones</th></tr></thead>
                                <tbody id="tablaCatalogo"></tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center flex-wrap mt-3" style="gap:10px;">
                            <div class="small text-muted" id="catResumen"></div>
                            <ul class="pagination pagination-sm mb-0" id="catPaginacion"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: editar / crear permiso --}}
<div class="modal fade" id="modalPermiso" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:14px; overflow:hidden; border:none;">
            <div class="modal-header gp">
                <h5 class="modal-title" id="modalPermisoTitulo"><i class="fas fa-key mr-2"></i>Permiso</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formPermiso">
                <div class="modal-body px-4 py-3">
                    <input type="hidden" id="permId">
                    <div class="form-group" id="grupoClave">
                        <label class="gp-label">Clave <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="permClave" maxlength="191" placeholder="ej: usuarios.exportar">
                        <small class="text-muted">Úsela en las vistas para mostrar u ocultar elementos: <code>@@puede('<span id="permClaveEj">usuarios.exportar</span>') … @@endpuede</code></small>
                    </div>
                    <div class="form-group" id="grupoClaveFija">
                        <label class="gp-label">Clave</label>
                        <div><code id="permClaveFija"></code> <span id="permRutaFija" class="small text-muted"></span></div>
                    </div>
                    <div class="form-group">
                        <label class="gp-label">Nombre visible <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="permNombre" maxlength="150" required>
                    </div>
                    <div class="form-group">
                        <label class="gp-label">Módulo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="permModulo" maxlength="120" list="listaModulos" required>
                        <datalist id="listaModulos"></datalist>
                    </div>
                    <div class="form-group">
                        <label class="gp-label">Descripción</label>
                        <textarea class="form-control" id="permDescripcion" rows="2" maxlength="255"></textarea>
                    </div>
                    <div class="custom-control custom-switch" id="grupoActivo">
                        <input type="checkbox" class="custom-control-input" id="permActivo">
                        <label class="custom-control-label" for="permActivo">Controlado (si se apaga, todos los roles pueden usar esta función)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary font-weight-bold" style="background:var(--gp-primary);border:none;" id="btnGuardarPermiso"><i class="fas fa-save mr-1"></i>Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="gpToasts"></div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ------------------------------------------------------------------ Estado
    var S = {
        roles: @json($roles),
        permisos: @json($permisos),
        soySuper: @json($soySuper),
        rolSel: null,
        estado: 'todos', tipo: '', buscar: '',
        colapsados: {}, todoColapsado: false,
        asigTemp: [],
        catPagina: 1
    };
    var URLS = {
        asignar: @json(route('config.permisos.asignar')),
        copiar: @json(route('config.permisos.copiar')),
        asignables: @json(route('config.permisos.asignables')),
        sincronizar: @json(route('config.permisos.sincronizar')),
        revisados: @json(route('config.permisos.revisados')),
        datos: @json(route('config.permisos.datos')),
        base: @json(url('/configuracion/permisos'))
    };
    var CSRF = $('meta[name="csrf-token"]').attr('content');
    var COLORES = ['#2e3a75','#1b9e55','#e67e22','#8e44ad','#16a085','#c0392b','#2980b9','#d35400','#7f8c8d','#27ae60'];
    var ICONOS = { 'Configuración':'fa-cog', 'Salud Ocupacional':'fa-briefcase-medical', 'Bienestar':'fa-heartbeat', 'Capacitaciones':'fa-chalkboard-teacher',
        'Recaudo':'fa-hand-holding-usd', 'API':'fa-plug', 'Otros':'fa-ellipsis-h', 'Eventos':'fa-calendar-alt', 'General':'fa-home', 'Diagnóstico de Correo':'fa-envelope' };

    // ------------------------------------------------------------------ Utilidades
    function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }
    function norm(s) { return String(s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, ''); }
    function rol(id) { return S.roles.find(function (r) { return r.id === id; }); }
    function perm(id) { return S.permisos.find(function (p) { return p.id === id; }); }
    function colorRol(r) { return COLORES[(r.id - 1) % COLORES.length]; }
    function iniciales(n) { return n.split(/\s+/).map(function (w) { return w[0]; }).join('').substr(0, 2).toUpperCase(); }
    function iconoModulo(m) { return ICONOS[m.split(' › ')[0]] || 'fa-cube'; }
    function tienePermiso(r, pid) { return r.total || r.permisos.indexOf(pid) !== -1; }
    function controlables() { return S.permisos.filter(function (p) { return p.activo && !p.huerfano; }); }
    function pctRol(r) {
        var c = controlables(); if (!c.length) return 100;
        if (r.total) return 100;
        var n = c.filter(function (p) { return r.permisos.indexOf(p.id) !== -1; }).length;
        return Math.round(n * 100 / c.length);
    }
    function editable(r) { return r && !r.total && (r.nombre !== 'Administrador' || S.soySuper); }
    function badgeMetodo(p) {
        if (p.tipo === 'personalizado') return '<span class="gp-badge gp-b-custom">Personalizado</span>';
        var m = (p.metodo || '').split('|')[0];
        var cls = { GET:'gp-b-get', POST:'gp-b-post', PUT:'gp-b-put', PATCH:'gp-b-put', DELETE:'gp-b-delete' }[m] || 'gp-b-get';
        return '<span class="gp-badge ' + cls + '">' + esc(p.tipo === 'vista' ? 'Vista' : m) + '</span>';
    }
    function badgesEstado(p) {
        return (p.nuevo ? ' <span class="gp-badge gp-b-new"><i class="fas fa-star"></i> Nuevo</span>' : '')
            + (!p.activo ? ' <span class="gp-badge gp-b-off" title="No se controla: todos los roles pueden usarla">Libre</span>' : '')
            + (p.huerfano ? ' <span class="gp-badge gp-b-orf" title="La ruta ya no existe en el aplicativo">No existe</span>' : '');
    }

    function toast(msg, opts) {
        opts = opts || {};
        var $t = $('<div class="gp-toast' + (opts.error ? ' err' : '') + '"><i class="fas ' + (opts.error ? 'fa-exclamation-circle' : 'fa-check-circle') + '"></i><div>' + esc(msg) + '</div></div>');
        if (opts.deshacer) {
            $('<button type="button">Deshacer</button>').on('click', function () { $t.remove(); opts.deshacer(); }).appendTo($t);
        }
        $('#gpToasts').append($t);
        setTimeout(function () { $t.fadeOut(300, function () { $t.remove(); }); }, opts.deshacer ? 6000 : 3500);
    }
    function post(url, data, method) {
        return $.ajax({ url: url, method: 'POST', data: $.extend({ _token: CSRF, _method: method || 'POST' }, data), headers: { Accept: 'application/json' } })
            .fail(function (xhr) {
                var r = xhr.responseJSON || {}, msg = r.message || 'Error al procesar la solicitud.';
                if (r.errors) { msg = Object.keys(r.errors).map(function (k) { return r.errors[k].join(' '); }).join(' '); }
                toast(msg, { error: true });
            });
    }

    // ------------------------------------------------------------------ Estadísticas
    function renderStats() {
        var mods = {}; S.permisos.forEach(function (p) { mods[p.modulo] = 1; });
        var nuevos = S.permisos.filter(function (p) { return p.nuevo; }).length;
        $('#stFunciones').text(controlables().length);
        $('#stModulos').text(Object.keys(mods).length);
        $('#stRoles').text(S.roles.length);
        $('#stNuevos').text(nuevos);
        $('#bannerNuevos').toggleClass('d-none', nuevos === 0);
        $('#bannerNuevosTxt').text(nuevos === 1 ? 'Se detectó 1 función nueva.' : 'Se detectaron ' + nuevos + ' funciones nuevas.');
        $('#tabNuevosBadge').text(nuevos).toggleClass('d-none', nuevos === 0);
    }

    // ------------------------------------------------------------------ Roles
    function renderRoles() {
        var q = norm($('#buscarRol').val());
        var html = S.roles.filter(function (r) { return !q || norm(r.nombre).indexOf(q) !== -1; }).map(function (r) {
            var pct = pctRol(r);
            return '<div class="gp-role' + (S.rolSel === r.id ? ' active' : '') + '" data-id="' + r.id + '">'
                + '<div class="gp-avatar" style="background:' + colorRol(r) + ';">' + esc(iniciales(r.nombre)) + '</div>'
                + '<div class="flex-grow-1" style="min-width:0;">'
                + '<div class="nombre text-truncate">' + esc(r.nombre) + (r.total ? ' <i class="fas fa-crown text-warning" title="Acceso total"></i>' : '') + (!r.activo ? ' <span class="gp-badge gp-b-off">Inactivo</span>' : '') + '</div>'
                + '<div class="meta"><i class="fas fa-users"></i> ' + r.usuarios + ' usuario(s) · ' + (r.total ? 'acceso total' : pct + '% funciones') + '</div>'
                + '<div class="gp-mini-bar"><i style="width:' + pct + '%;"></i></div>'
                + '</div></div>';
        }).join('');
        $('#listaRoles').html(html || '<div class="gp-empty"><i class="fas fa-search"></i>Sin coincidencias</div>');
    }

    function seleccionarRol(id) {
        S.rolSel = id;
        var r = rol(id);
        S.asigTemp = r.asignables.slice();
        renderRoles(); renderEncabezadoRol(); renderModulos(); renderAsignables(); renderCopiar();
    }

    function renderEncabezadoRol() {
        var r = rol(S.rolSel); if (!r) return;
        var pct = pctRol(r), c = controlables();
        var n = r.total ? c.length : c.filter(function (p) { return r.permisos.indexOf(p.id) !== -1; }).length;
        $('#rolNombre').text(r.nombre);
        $('#rolMeta').html('<i class="fas fa-users"></i> ' + r.usuarios + ' usuario(s) · <b>' + n + '</b> de ' + c.length + ' funciones' + (r.es_sistema ? ' · <span class="gp-badge gp-b-custom">Sistema</span>' : ''));
        $('#rolRing').css('--p', pct); $('#rolPct').text(pct + '%');
        var aviso = '';
        if (r.total) aviso = '<i class="fas fa-crown mr-1"></i> El rol <b>Super Admin</b> tiene acceso total a todas las funciones y no se puede restringir.';
        else if (!editable(r)) aviso = '<i class="fas fa-lock mr-1"></i> Solo el Super Admin puede cambiar los permisos del rol <b>Administrador</b>.';
        $('#avisoRol').html(aviso).toggleClass('d-none', !aviso);
        $('#accionesRol').toggle(editable(r));
    }

    function renderCopiar() {
        var opts = '<option value="">Copiar de…</option>' + S.roles.filter(function (r) { return r.id !== S.rolSel && !r.total; })
            .map(function (r) { return '<option value="' + r.id + '">' + esc(r.nombre) + '</option>'; }).join('');
        $('#copiarDesde').html(opts);
    }

    // ------------------------------------------------------------------ Permisos por módulo
    function permisosFiltrados() {
        var r = rol(S.rolSel), q = norm(S.buscar);
        return S.permisos.filter(function (p) {
            if (p.huerfano) return false;
            if (S.tipo && p.tipo !== S.tipo) return false;
            if (S.estado === 'asignados' && !tienePermiso(r, p.id)) return false;
            if (S.estado === 'sin' && tienePermiso(r, p.id)) return false;
            if (S.estado === 'nuevos' && !p.nuevo) return false;
            if (q && norm(p.nombre + ' ' + p.modulo + ' ' + p.clave + ' ' + (p.uri || '') + ' ' + (p.descripcion || '')).indexOf(q) === -1) return false;
            return true;
        });
    }

    function agrupar(lista) {
        var g = {}, orden = [];
        lista.forEach(function (p) { if (!g[p.modulo]) { g[p.modulo] = []; orden.push(p.modulo); } g[p.modulo].push(p); });
        orden.sort(function (a, b) { return a.localeCompare(b, 'es'); });
        return orden.map(function (m) { return { modulo: m, items: g[m] }; });
    }

    function renderModulos() {
        var r = rol(S.rolSel); if (!r) return;
        var puede = editable(r);
        var grupos = agrupar(permisosFiltrados());
        if (!grupos.length) { $('#modulos').html('<div class="gp-empty"><i class="fas fa-filter"></i>No hay funciones con los filtros aplicados.</div>'); return; }

        $('#modulos').html(grupos.map(function (g) {
            var asign = g.items.filter(function (p) { return tienePermiso(r, p.id); }).length;
            var pct = Math.round(asign * 100 / g.items.length);
            var colapsado = S.colapsados[g.modulo] !== undefined ? S.colapsados[g.modulo] : false;
            return '<div class="gp-modulo' + (colapsado ? ' collapsed' : '') + '" data-modulo="' + esc(g.modulo) + '">'
                + '<div class="gp-mod-head">'
                + '<i class="fas fa-chevron-down gp-caret"></i>'
                + '<div class="gp-mod-icon"><i class="fas ' + iconoModulo(g.modulo) + '"></i></div>'
                + '<div class="flex-grow-1"><div class="gp-mod-title">' + esc(g.modulo) + '</div>'
                + '<div class="gp-mod-count">' + asign + ' de ' + g.items.length + ' asignadas</div>'
                + '<div class="gp-mini-bar" style="max-width:220px;"><i style="width:' + pct + '%;"></i></div></div>'
                + '<label class="gp-switch mod-switch" title="Asignar / quitar todo el módulo" onclick="event.stopPropagation();"><input type="checkbox" class="chk-modulo"' + (asign === g.items.length ? ' checked' : '') + (!puede ? ' disabled' : '') + ' data-indet="' + (asign > 0 && asign < g.items.length ? 1 : 0) + '"><span></span></label>'
                + '</div>'
                + '<div class="gp-mod-body">' + g.items.map(function (p) {
                    var on = tienePermiso(r, p.id);
                    return '<div class="gp-perm' + (on ? ' on' : '') + '" data-id="' + p.id + '">'
                        + '<label class="gp-switch mt-1"><input type="checkbox" class="chk-perm" data-id="' + p.id + '"' + (on ? ' checked' : '') + (!puede ? ' disabled' : '') + '><span></span></label>'
                        + '<div style="min-width:0;"><div class="p-nombre">' + esc(p.nombre) + '</div>'
                        + '<div class="mt-1">' + badgeMetodo(p) + badgesEstado(p) + '</div>'
                        + '<div class="mt-1"><code title="' + esc(p.uri || '') + '">' + esc(p.clave) + '</code></div>'
                        + (p.descripcion ? '<div class="small text-muted mt-1">' + esc(p.descripcion) + '</div>' : '')
                        + '</div></div>';
                }).join('') + '</div></div>';
        }).join(''));

        $('#modulos .chk-modulo').each(function () { this.indeterminate = $(this).data('indet') == 1; });
    }

    function aplicarCambio(roleId, ids, valor, conDeshacer) {
        var r = rol(roleId);
        var previos = r.permisos.slice();
        // Actualización optimista
        if (valor) ids.forEach(function (id) { if (r.permisos.indexOf(id) === -1) r.permisos.push(id); });
        else r.permisos = r.permisos.filter(function (id) { return ids.indexOf(id) === -1; });
        refrescarVistasRol();

        return post(URLS.asignar, { role_id: roleId, permiso_ids: ids, valor: valor ? 1 : 0 })
            .done(function (resp) {
                r.permisos = resp.permisos.map(Number);
                refrescarVistasRol();
                toast(resp.message, conDeshacer === false ? {} : { deshacer: function () {
                    var aRestaurar = valor ? ids.filter(function (id) { return previos.indexOf(id) === -1; }) : ids.filter(function (id) { return previos.indexOf(id) !== -1; });
                    if (aRestaurar.length) aplicarCambio(roleId, aRestaurar, !valor, false);
                } });
            })
            .fail(function () { r.permisos = previos; refrescarVistasRol(); });
    }

    function refrescarVistasRol() {
        renderRoles(); renderEncabezadoRol(); renderModulos();
        if ($('#tabMatriz').hasClass('active')) renderMatriz();
        if ($('#tabCatalogo').hasClass('active')) renderCatalogo();
    }

    // ------------------------------------------------------------------ Roles asignables
    function renderAsignables() {
        var r = rol(S.rolSel); if (!r) return;
        $('#asigRolNombre').text(r.nombre);
        var aviso = '';
        if (r.total) aviso = '<i class="fas fa-crown mr-1"></i> El Super Admin puede asignar todos los roles.';
        $('#avisoAsig').html(aviso).toggleClass('d-none', !aviso);
        $('#btnGuardarAsig, #btnAsigTodos, #btnAsigNinguno').prop('disabled', !!r.total);

        $('#gridAsignables').html(S.roles.map(function (o) {
            var esSuper = o.nombre === 'Super Admin';
            var on = r.total || S.asigTemp.indexOf(o.id) !== -1;
            var locked = r.total || esSuper;
            return '<div class="gp-asig' + (on && !(!r.total && esSuper) ? ' on' : '') + (locked ? ' locked' : '') + '" data-id="' + o.id + '">'
                + '<div class="gp-avatar" style="background:' + colorRol(o) + '; width:36px; height:36px; font-size:.85rem;">' + esc(iniciales(o.nombre)) + '</div>'
                + '<div style="min-width:0;"><div style="font-weight:700; color:#1e2a55;" class="text-truncate">' + esc(o.nombre) + '</div>'
                + '<div class="small text-muted">' + (esSuper ? '<i class="fas fa-lock"></i> Solo Super Admin' : (o.activo ? o.usuarios + ' usuario(s)' : 'Inactivo')) + '</div></div>'
                + '<div class="check"><i class="fas fa-check"></i></div></div>';
        }).join(''));
    }

    // ------------------------------------------------------------------ Matriz
    function renderMatriz() {
        var q = norm($('#buscarMatriz').val()), mod = $('#moduloMatriz').val();
        var lista = S.permisos.filter(function (p) {
            return !p.huerfano && (!mod || p.modulo === mod) && (!q || norm(p.nombre + ' ' + p.clave + ' ' + p.modulo).indexOf(q) !== -1);
        });
        var head = '<thead><tr><th>Función</th>' + S.roles.map(function (r) {
            var n = r.total ? lista.length : lista.filter(function (p) { return r.permisos.indexOf(p.id) !== -1; }).length;
            return '<th title="' + esc(r.nombre) + '"><div class="gp-avatar mx-auto mb-1" style="background:' + colorRol(r) + '; width:28px; height:28px; font-size:.7rem; border-radius:8px;">' + esc(iniciales(r.nombre)) + '</div>' + esc(r.nombre) + '<div style="font-weight:600; opacity:.7;">' + n + '/' + lista.length + '</div></th>';
        }).join('') + '</tr></thead>';
        var body = agrupar(lista).map(function (g) {
            return '<tr class="grupo"><td colspan="' + (S.roles.length + 1) + '"><i class="fas ' + iconoModulo(g.modulo) + ' mr-2"></i>' + esc(g.modulo) + '</td></tr>'
                + g.items.map(function (p) {
                    return '<tr><td><div style="font-weight:600;">' + esc(p.nombre) + badgesEstado(p) + '</div><code style="font-size:.68rem;">' + esc(p.clave) + '</code></td>'
                        + S.roles.map(function (r) {
                            var on = tienePermiso(r, p.id), puede = editable(r);
                            return '<td class="c' + (puede ? ' celda' : '') + '" data-r="' + r.id + '" data-p="' + p.id + '" title="' + esc(r.nombre + ' · ' + p.nombre) + '">'
                                + '<span class="gp-dot' + (on ? (puede ? ' on' : ' on lock') : '') + '">' + (on ? '<i class="fas fa-check"></i>' : '') + '</span></td>';
                        }).join('') + '</tr>';
                }).join('');
        }).join('');
        $('#tablaMatriz').html(head + '<tbody>' + (body || '<tr><td colspan="' + (S.roles.length + 1) + '" class="gp-empty">Sin resultados</td></tr>') + '</tbody>');
    }

    // ------------------------------------------------------------------ Catálogo
    function renderSelectModulos() {
        var mods = []; S.permisos.forEach(function (p) { if (mods.indexOf(p.modulo) === -1) mods.push(p.modulo); });
        mods.sort(function (a, b) { return a.localeCompare(b, 'es'); });
        var opts = mods.map(function (m) { return '<option value="' + esc(m) + '">' + esc(m) + '</option>'; }).join('');
        ['#moduloMatriz', '#catModulo'].forEach(function (sel) { var v = $(sel).val(); $(sel).html('<option value="">Todos los módulos</option>' + opts).val(v); });
        $('#listaModulos').html(mods.map(function (m) { return '<option value="' + esc(m) + '">'; }).join(''));
    }

    function renderCatalogo() {
        var q = norm($('#buscarCat').val()), mod = $('#catModulo').val(), tipo = $('#catTipo').val(), est = $('#catEstado').val();
        var porPag = parseInt($('#catPorPagina').val(), 10);
        var lista = S.permisos.filter(function (p) {
            if (mod && p.modulo !== mod) return false;
            if (tipo && p.tipo !== tipo) return false;
            if (est === 'activo' && (!p.activo || p.huerfano)) return false;
            if (est === 'inactivo' && p.activo) return false;
            if (est === 'nuevo' && !p.nuevo) return false;
            if (est === 'huerfano' && !p.huerfano) return false;
            if (q && norm(p.nombre + ' ' + p.clave + ' ' + (p.uri || '') + ' ' + p.modulo + ' ' + (p.descripcion || '')).indexOf(q) === -1) return false;
            return true;
        });
        var paginas = Math.max(1, Math.ceil(lista.length / porPag));
        S.catPagina = Math.min(S.catPagina, paginas);
        var desde = (S.catPagina - 1) * porPag, pagina = lista.slice(desde, desde + porPag);

        $('#tablaCatalogo').html(pagina.map(function (p) {
            var nRoles = S.roles.filter(function (r) { return tienePermiso(r, p.id); }).length;
            var borrable = p.origen === 'manual' || p.huerfano;
            return '<tr data-id="' + p.id + '">'
                + '<td><div style="font-weight:700; color:#273058;">' + esc(p.nombre) + badgesEstado(p) + '</div><code style="font-size:.7rem;">' + esc(p.clave) + '</code></td>'
                + '<td class="small">' + esc(p.modulo) + '</td>'
                + '<td>' + badgeMetodo(p) + '</td>'
                + '<td class="small text-muted">' + esc(p.uri || '—') + '</td>'
                + '<td class="text-center"><span class="badge badge-light" style="font-size:.85rem;">' + nRoles + '/' + S.roles.length + '</span></td>'
                + '<td class="text-center"><label class="gp-switch"><input type="checkbox" class="chk-activo" data-id="' + p.id + '"' + (p.activo ? ' checked' : '') + (p.huerfano ? ' disabled' : '') + '><span></span></label></td>'
                + '<td class="text-center text-nowrap"><button class="btn btn-sm btn-outline-primary btn-editar" data-id="' + p.id + '" title="Editar"><i class="fas fa-edit"></i></button> '
                + (borrable ? '<button class="btn btn-sm btn-outline-danger btn-borrar" data-id="' + p.id + '" title="Eliminar"><i class="fas fa-trash-alt"></i></button>' : '')
                + '</td></tr>';
        }).join('') || '<tr><td colspan="7" class="gp-empty"><i class="fas fa-inbox"></i>No hay funciones con estos filtros.</td></tr>');

        $('#catResumen').text('Mostrando ' + (lista.length ? desde + 1 : 0) + ' a ' + Math.min(desde + porPag, lista.length) + ' de ' + lista.length + ' funciones');
        var pag = '';
        if (paginas > 1) {
            pag += '<li class="page-item' + (S.catPagina === 1 ? ' disabled' : '') + '"><a class="page-link" href="#" data-p="' + (S.catPagina - 1) + '">&laquo;</a></li>';
            for (var i = 1; i <= paginas; i++) {
                if (paginas > 9 && Math.abs(i - S.catPagina) > 2 && i !== 1 && i !== paginas) { if (Math.abs(i - S.catPagina) === 3) pag += '<li class="page-item disabled"><span class="page-link">…</span></li>'; continue; }
                pag += '<li class="page-item' + (i === S.catPagina ? ' active' : '') + '"><a class="page-link" href="#" data-p="' + i + '">' + i + '</a></li>';
            }
            pag += '<li class="page-item' + (S.catPagina === paginas ? ' disabled' : '') + '"><a class="page-link" href="#" data-p="' + (S.catPagina + 1) + '">&raquo;</a></li>';
        }
        $('#catPaginacion').html(pag);
    }

    function cargarDatos(d) {
        S.roles = d.roles; S.permisos = d.permisos;
        S.roles.forEach(function (r) { r.permisos = r.permisos.map(Number); r.asignables = r.asignables.map(Number); });
        if (!rol(S.rolSel)) S.rolSel = (S.roles.find(function (r) { return !r.total; }) || S.roles[0] || {}).id;
        renderStats(); renderSelectModulos();
        seleccionarRol(S.rolSel);
        renderMatriz(); renderCatalogo();
    }

    // ------------------------------------------------------------------ Eventos
    $('#listaRoles').on('click', '.gp-role', function () { seleccionarRol($(this).data('id')); });
    $('#buscarRol').on('input', renderRoles);

    var tBuscar;
    $('#buscarPermiso').on('input', function () { var v = this.value; clearTimeout(tBuscar); tBuscar = setTimeout(function () { S.buscar = v; renderModulos(); }, 200); });
    $('#chipsEstado').on('click', '.gp-chip', function () { $(this).addClass('on').siblings().removeClass('on'); S.estado = $(this).data('v'); renderModulos(); });
    $('#chipsTipo').on('click', '.gp-chip', function () { $(this).addClass('on').siblings().removeClass('on'); S.tipo = $(this).data('v'); renderModulos(); });
    $('#btnExpandir').on('click', function () {
        S.todoColapsado = !S.todoColapsado;
        $('#modulos .gp-modulo').each(function () { S.colapsados[$(this).data('modulo')] = S.todoColapsado; });
        $('#modulos .gp-modulo').toggleClass('collapsed', S.todoColapsado);
        $(this).find('i').attr('class', S.todoColapsado ? 'fas fa-compress-alt' : 'fas fa-expand-alt');
    });
    $('#modulos').on('click', '.gp-mod-head', function () {
        var $m = $(this).closest('.gp-modulo'); $m.toggleClass('collapsed'); S.colapsados[$m.data('modulo')] = $m.hasClass('collapsed');
    });
    $('#modulos').on('change', '.chk-perm', function () { aplicarCambio(S.rolSel, [Number($(this).data('id'))], this.checked); });
    $('#modulos').on('change', '.chk-modulo', function () {
        var ids = $(this).closest('.gp-modulo').find('.chk-perm').map(function () { return Number($(this).data('id')); }).get();
        aplicarCambio(S.rolSel, ids, this.checked);
    });
    $('#btnAsignarVisibles, #btnQuitarVisibles').on('click', function () {
        var valor = this.id === 'btnAsignarVisibles';
        var ids = permisosFiltrados().map(function (p) { return p.id; });
        if (!ids.length) return toast('No hay funciones visibles con los filtros actuales.', { error: true });
        if (ids.length > 10 && !confirm((valor ? 'Asignar ' : 'Quitar ') + ids.length + ' funciones al rol ' + rol(S.rolSel).nombre + '?')) return;
        aplicarCambio(S.rolSel, ids, valor);
    });
    $('#btnCopiar').on('click', function () {
        var desde = Number($('#copiarDesde').val()); if (!desde) return toast('Elija el rol del que desea copiar los permisos.', { error: true });
        var r = rol(S.rolSel);
        if (!confirm('Se reemplazarán los permisos de "' + r.nombre + '" por los de "' + rol(desde).nombre + '". ¿Continuar?')) return;
        post(URLS.copiar, { desde: desde, hacia: r.id }).done(function (resp) { r.permisos = resp.permisos.map(Number); refrescarVistasRol(); toast(resp.message); });
    });

    // Asignables
    $('#gridAsignables').on('click', '.gp-asig:not(.locked)', function () {
        var id = Number($(this).data('id')), i = S.asigTemp.indexOf(id);
        if (i === -1) S.asigTemp.push(id); else S.asigTemp.splice(i, 1);
        $(this).toggleClass('on', i === -1);
    });
    $('#btnAsigTodos').on('click', function () { S.asigTemp = S.roles.filter(function (r) { return r.nombre !== 'Super Admin'; }).map(function (r) { return r.id; }); renderAsignables(); });
    $('#btnAsigNinguno').on('click', function () { S.asigTemp = []; renderAsignables(); });
    $('#btnGuardarAsig').on('click', function () {
        var r = rol(S.rolSel), $b = $(this).prop('disabled', true);
        post(URLS.asignables, { role_id: r.id, asignables: S.asigTemp })
            .done(function (resp) { r.asignables = resp.asignables.map(Number); S.asigTemp = r.asignables.slice(); renderAsignables(); toast(resp.message); })
            .always(function () { $b.prop('disabled', false); });
    });

    // Matriz
    $('#buscarMatriz').on('input', function () { clearTimeout(tBuscar); tBuscar = setTimeout(renderMatriz, 200); });
    $('#moduloMatriz').on('change', renderMatriz);
    $('#tablaMatriz').on('click', 'td.celda', function () {
        var r = rol(Number($(this).data('r'))), pid = Number($(this).data('p'));
        aplicarCambio(r.id, [pid], r.permisos.indexOf(pid) === -1);
    });

    // Catálogo
    $('#buscarCat').on('input', function () { clearTimeout(tBuscar); tBuscar = setTimeout(function () { S.catPagina = 1; renderCatalogo(); }, 200); });
    $('#catModulo, #catTipo, #catEstado, #catPorPagina').on('change', function () { S.catPagina = 1; renderCatalogo(); });
    $('#catPaginacion').on('click', 'a.page-link', function (e) { e.preventDefault(); var p = Number($(this).data('p')); if (p) { S.catPagina = p; renderCatalogo(); } });
    $('#tablaCatalogo').on('change', '.chk-activo', function () {
        var p = perm(Number($(this).data('id'))), chk = this;
        post(URLS.base + '/' + p.id, { nombre: p.nombre, modulo: p.modulo, descripcion: p.descripcion || '', activo: chk.checked ? 1 : 0 }, 'PUT')
            .done(function (resp) { $.extend(p, resp.permiso); renderStats(); renderCatalogo(); renderModulos(); toast(p.activo ? 'La función vuelve a controlarse por rol.' : 'Función liberada: todos los roles pueden usarla.'); })
            .fail(function () { chk.checked = !chk.checked; });
    });
    $('#tablaCatalogo').on('click', '.btn-editar', function () { abrirModal(perm(Number($(this).data('id')))); });
    $('#tablaCatalogo').on('click', '.btn-borrar', function () {
        var p = perm(Number($(this).data('id')));
        if (!confirm('¿Eliminar el permiso "' + p.nombre + '"? Esta acción no se puede deshacer.')) return;
        post(URLS.base + '/' + p.id, {}, 'DELETE').done(function (resp) {
            S.permisos = S.permisos.filter(function (x) { return x.id !== p.id; });
            S.roles.forEach(function (r) { r.permisos = r.permisos.filter(function (id) { return id !== p.id; }); });
            renderStats(); renderSelectModulos(); refrescarVistasRol(); renderCatalogo(); toast(resp.message);
        });
    });

    // Modal permiso
    function abrirModal(p) {
        var nuevo = !p;
        $('#modalPermisoTitulo').html(nuevo ? '<i class="fas fa-magic mr-2"></i>Nuevo permiso personalizado' : '<i class="fas fa-edit mr-2"></i>Editar permiso');
        $('#permId').val(nuevo ? '' : p.id);
        $('#grupoClave').toggle(nuevo); $('#grupoClaveFija').toggle(!nuevo); $('#grupoActivo').toggle(!nuevo);
        $('#permClave').val(''); $('#permClaveEj').text('usuarios.exportar');
        if (!nuevo) { $('#permClaveFija').text(p.clave); $('#permRutaFija').text(p.uri ? (p.metodo + ' ' + p.uri) : ''); }
        $('#permNombre').val(nuevo ? '' : p.nombre);
        $('#permModulo').val(nuevo ? '' : p.modulo);
        $('#permDescripcion').val(nuevo ? '' : (p.descripcion || ''));
        $('#permActivo').prop('checked', nuevo ? true : p.activo).prop('disabled', !nuevo && p.huerfano);
        $('#modalPermiso').modal('show');
    }
    $('#permClave').on('input', function () { $('#permClaveEj').text(this.value || 'usuarios.exportar'); });
    $('#btnNuevoPermiso').on('click', function () { abrirModal(null); });
    $('#formPermiso').on('submit', function (e) {
        e.preventDefault();
        var id = $('#permId').val(), $b = $('#btnGuardarPermiso').prop('disabled', true);
        var data = { nombre: $('#permNombre').val(), modulo: $('#permModulo').val(), descripcion: $('#permDescripcion').val() };
        var req = id
            ? post(URLS.base + '/' + id, $.extend(data, { activo: $('#permActivo').prop('checked') ? 1 : 0 }), 'PUT').done(function (resp) { $.extend(perm(Number(id)), resp.permiso); renderStats(); renderSelectModulos(); refrescarVistasRol(); renderCatalogo(); })
            : post(URLS.base, $.extend(data, { clave: $('#permClave').val() })).done(function (resp) { cargarDatos(resp); });
        req.done(function (resp) { $('#modalPermiso').modal('hide'); toast(resp.message); }).always(function () { $b.prop('disabled', false); });
    });

    // Sincronizar / nuevos
    $('#btnSincronizar').on('click', function () {
        var $b = $(this).prop('disabled', true), $i = $b.find('i').addClass('gp-spin');
        post(URLS.sincronizar, {}).done(function (resp) { cargarDatos(resp); toast(resp.message); })
            .always(function () { $b.prop('disabled', false); $i.removeClass('gp-spin'); });
    });
    $('#btnVerNuevos').on('click', function () {
        $('a[href="#tabPermisos"]').tab('show');
        $('#chipsEstado .gp-chip[data-v="nuevos"]').trigger('click');
    });
    $('#btnRevisados').on('click', function () {
        post(URLS.revisados, {}).done(function (resp) { S.permisos.forEach(function (p) { p.nuevo = false; }); renderStats(); refrescarVistasRol(); renderCatalogo(); toast(resp.message); });
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var t = $(e.target).attr('href');
        if (t === '#tabMatriz') renderMatriz();
        if (t === '#tabCatalogo') renderCatalogo();
        if (t === '#tabAsignables') renderAsignables();
    });

    // ------------------------------------------------------------------ Inicio
    cargarDatos({ roles: S.roles, permisos: S.permisos });
    @if(($sync['nuevos'] ?? 0) > 0)
        toast(@json('Se agregaron ' . $sync['nuevos'] . ' función(es) nueva(s) al catálogo.'));
    @endif
})();
</script>
@endpush
