@extends('layouts.app')

@section('title','Capacitaciones')
@section('header','Gestión de Capacitaciones')

@push('head')
<style>
    .cap-gradient { background: linear-gradient(135deg, #2f4185 0%, #48599e 100%); }
    .stat-card { border-radius: 1.5rem; padding: 1.5rem; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-3px); }
    .badge-activo { background: #d4edda; color: #155724; }
    .badge-finalizado { background: #f8d7da; color: #721c24; }
    .btn-qr { font-size: 0.78rem; padding: 0.2rem 0.5rem; }
    .informe-tab { cursor: pointer; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; border: none; background: #eee; }
    .informe-tab.active { background: #2f4185; color: #fff; }
    .sesion-pill { cursor: pointer; font-size: 0.78rem; padding: 0.35rem 0.8rem; border-radius: 2rem; border: 1px solid #ccc; background: #fff; margin: 0.2rem; }
    .sesion-pill.active { background: #2f4185; color: #fff; border-color: #2f4185; }
    .informe-table th { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em; }
    .informe-table td { font-size: 0.82rem; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="cap-gradient rounded-3 shadow-lg mb-4 p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2 class="text-white mb-1 fw-bold">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Gestión de Capacitaciones
                </h2>
                <p class="text-white-50 mb-0 small">Crea capacitaciones, cita usuarios y registra asistencia</p>
            </div>
            <a href="{{ route('config.capacitaciones.create') }}" class="btn btn-light btn-lg shadow-sm">
                <i class="fas fa-plus me-2"></i>Nueva Capacitación
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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
                            <th class="px-4 py-3 text-muted fw-bold small text-center">QR Asistencia</th>
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
                            <td class="px-4 py-3">{{ $cap->fecha->format('d/m/Y') }}</td>
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
                                @php
                                    $asistUrl = null;
                                    if (!empty($hasSesiones) && $cap->ultimaSesion) {
                                        $asistUrl = route('capacitaciones.asistencia.publica', $cap->ultimaSesion->token);
                                    } elseif ($cap->token) {
                                        $asistUrl = route('capacitaciones.asistencia.publica', $cap->token);
                                    }
                                @endphp
                                @if($asistUrl)
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-qr" data-toggle="modal" data-target="#qrModal{{ $cap->id }}" title="Ver QR">
                                            <i class="fas fa-qrcode"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-qr btn-copy-link" data-link="{{ $asistUrl }}" title="Copiar link">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    {{-- Modal QR --}}
                                    <div class="modal fade" id="qrModal{{ $cap->id }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                            <div class="modal-content" style="border-radius:1rem;">
                                                <div class="modal-header border-0 pb-0">
                                                    <h6 class="modal-title fw-bold">QR - {{ $cap->titulo }}</h6>
                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <div class="modal-body text-center pt-2">
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($asistUrl) }}" alt="QR Asistencia" class="img-fluid mb-2" style="max-width:220px;">
                                                    <div class="mt-2">
                                                        <small class="text-muted d-block" style="word-break:break-all;">{{ $asistUrl }}</small>
                                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 btn-copy-link" data-link="{{ $asistUrl }}">
                                                            <i class="fas fa-copy me-1"></i> Copiar enlace
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-info bg-opacity-10 text-info" style="font-size:0.85rem;">{{ $cap->asistencias_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-success bg-opacity-10 text-success" style="font-size:0.85rem;">{{ $cap->asistieron_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($cap->activo && $cap->fecha >= now()->toDateString())
                                    <span class="badge badge-activo"><i class="fas fa-circle me-1" style="font-size:0.5rem;"></i>Activa</span>
                                @else
                                    <span class="badge badge-finalizado"><i class="fas fa-circle me-1" style="font-size:0.5rem;"></i>Finalizada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('config.capacitaciones.show', $cap->id) }}" class="btn btn-sm btn-outline-primary" title="Ver / Asistencia">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('config.capacitaciones.edit', $cap->id) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!empty($hasSesiones))
                                    <button type="button" class="btn btn-sm btn-outline-info btn-informes" data-cap-id="{{ $cap->id }}" data-cap-titulo="{{ $cap->titulo }}" title="Informes">
                                        <i class="fas fa-chart-bar"></i>
                                    </button>
                                    @endif
                                    <form action="{{ route('config.capacitaciones.destroy', $cap->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('¿Eliminar esta capacitación?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="fas fa-chalkboard-teacher fa-2x mb-2 d-block"></i>
                                No hay capacitaciones registradas aún.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $capacitaciones->links() }}
        </div>
    </div>
</div>

{{-- Modal Informes --}}
@if(!empty($hasSesiones))
<div class="modal fade" id="modalInformes" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" style="border-radius:1rem;">
            <div class="modal-header" style="background: linear-gradient(135deg, #2f4185, #48599e); border-radius: 1rem 1rem 0 0;">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fas fa-chart-bar me-2"></i>Informes: <span id="informeTitulo"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="informeBody" style="min-height:300px;">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                    <p class="text-muted mt-2">Cargando informes...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ─── Copiar link ───
    function copiarAlPortapapeles(text, btn) {
        var originalHtml = btn.innerHTML;
        function showOk() {
            btn.innerHTML = '<i class="fas fa-check text-success"></i>';
            setTimeout(function() { btn.innerHTML = originalHtml; }, 2000);
        }
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(showOk).catch(function() { fallbackCopy(text, btn, showOk); });
        } else {
            fallbackCopy(text, btn, showOk);
        }
    }
    function fallbackCopy(text, btn, onSuccess) {
        var ta = document.createElement('textarea');
        ta.value = text; ta.style.position = 'fixed'; ta.style.left = '-9999px';
        document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); onSuccess(); } catch(e) { prompt('Copiar este enlace:', text); }
        document.body.removeChild(ta);
    }
    document.querySelectorAll('.btn-copy-link').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            copiarAlPortapapeles(this.getAttribute('data-link'), this);
        });
    });

    // ─── Informes ───
    @if(!empty($hasSesiones))
    document.querySelectorAll('.btn-informes').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var capId = this.dataset.capId;
            var capTitulo = this.dataset.capTitulo;
            document.getElementById('informeTitulo').textContent = capTitulo;
            document.getElementById('informeBody').innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><p class="text-muted mt-2">Cargando informes...</p></div>';
            $('#modalInformes').modal('show');

            fetch('/configuracion/capacitaciones/' + capId + '/informes', {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.ok || !data.sesiones.length) {
                    document.getElementById('informeBody').innerHTML = '<div class="text-center py-5 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>No hay sesiones registradas.</div>';
                    return;
                }
                renderInformes(data, capId);
            })
            .catch(function() {
                document.getElementById('informeBody').innerHTML = '<div class="alert alert-danger">Error al cargar informes.</div>';
            });
        });
    });

    function renderInformes(data, capId) {
        var sesiones = data.sesiones;
        var html = '';

        // Sesión pills
        html += '<div class="mb-3 d-flex flex-wrap align-items-center gap-1">';
        html += '<strong class="small text-muted mr-2">Sesiones:</strong>';
        sesiones.forEach(function(s, i) {
            html += '<button class="sesion-pill' + (i === 0 ? ' active' : '') + '" data-sesion-idx="' + i + '">';
            html += '<i class="fas fa-calendar-alt mr-1"></i>' + s.fecha + ' ' + s.hora_inicio;
            html += '</button>';
        });
        html += '</div>';

        // Sesión content
        sesiones.forEach(function(s, i) {
            html += '<div class="sesion-content" data-sesion-idx="' + i + '" style="' + (i > 0 ? 'display:none;' : '') + '">';

            // Stats
            var totalAsist = s.citados_asistieron.length + s.no_citados_asistieron.length;
            html += '<div class="row mb-3 g-2">';
            html += '<div class="col-md-3"><div class="p-3 rounded-3 bg-light text-center"><div class="h4 fw-bold mb-0" style="color:#2f4185;">' + s.total_citados + '</div><small class="text-muted fw-bold">Citados</small></div></div>';
            html += '<div class="col-md-3"><div class="p-3 rounded-3 bg-light text-center"><div class="h4 fw-bold mb-0 text-success">' + s.citados_asistieron.length + '</div><small class="text-muted fw-bold">Citados Asistieron</small></div></div>';
            html += '<div class="col-md-3"><div class="p-3 rounded-3 bg-light text-center"><div class="h4 fw-bold mb-0 text-info">' + s.no_citados_asistieron.length + '</div><small class="text-muted fw-bold">No Citados</small></div></div>';
            html += '<div class="col-md-3"><div class="p-3 rounded-3 bg-light text-center"><div class="h4 fw-bold mb-0 text-danger">' + s.citados_no_asistieron.length + '</div><small class="text-muted fw-bold">Ausentes</small></div></div>';
            html += '</div>';

            // Export button
            html += '<div class="mb-3"><a href="/configuracion/capacitaciones/' + capId + '/sesiones/' + s.id + '/excel" class="btn btn-sm btn-success"><i class="fas fa-file-excel me-1"></i> Exportar Excel</a>';
            html += '<small class="text-muted ml-2">Sesión creada: ' + s.created_at + '</small></div>';

            // Tabs
            html += '<ul class="nav nav-tabs mb-2" role="tablist">';
            html += '<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#citAsist' + s.id + '">Citados que Asistieron (' + s.citados_asistieron.length + ')</a></li>';
            html += '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#noCitAsist' + s.id + '">No Citados (' + s.no_citados_asistieron.length + ')</a></li>';
            html += '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#ausentes' + s.id + '">Ausentes (' + s.citados_no_asistieron.length + ')</a></li>';
            html += '</ul>';

            html += '<div class="tab-content">';

            // Tab 1: Citados que asistieron
            html += '<div class="tab-pane fade show active" id="citAsist' + s.id + '">';
            html += buildTable(s.citados_asistieron, true);
            html += '</div>';

            // Tab 2: No citados
            html += '<div class="tab-pane fade" id="noCitAsist' + s.id + '">';
            html += buildTable(s.no_citados_asistieron, true);
            html += '</div>';

            // Tab 3: Ausentes
            html += '<div class="tab-pane fade" id="ausentes' + s.id + '">';
            if (s.citados_no_asistieron.length) {
                html += '<table class="table table-sm informe-table"><thead><tr><th>Nombre</th><th>Identificación</th></tr></thead><tbody>';
                s.citados_no_asistieron.forEach(function(r) {
                    html += '<tr><td>' + r.nombre + '</td><td>' + r.identificacion + '</td></tr>';
                });
                html += '</tbody></table>';
            } else {
                html += '<p class="text-muted text-center py-3">Todos los citados asistieron.</p>';
            }
            html += '</div>';

            html += '</div>'; // tab-content
            html += '</div>'; // sesion-content
        });

        document.getElementById('informeBody').innerHTML = html;

        // Sesion pill navigation
        document.querySelectorAll('.sesion-pill').forEach(function(pill) {
            pill.addEventListener('click', function() {
                document.querySelectorAll('.sesion-pill').forEach(function(p) { p.classList.remove('active'); });
                this.classList.add('active');
                var idx = this.dataset.sesionIdx;
                document.querySelectorAll('.sesion-content').forEach(function(c) {
                    c.style.display = c.dataset.sesionIdx === idx ? '' : 'none';
                });
            });
        });
    }

    function buildTable(rows, full) {
        if (!rows.length) return '<p class="text-muted text-center py-3">Sin registros.</p>';
        var html = '<div class="table-responsive"><table class="table table-sm table-hover informe-table"><thead><tr>';
        html += '<th>Nombre</th><th>Identificación</th><th>Área/Servicio</th><th>Cargo</th><th>Vinculación</th><th>Fecha/Hora</th>';
        html += '</tr></thead><tbody>';
        rows.forEach(function(r) {
            html += '<tr>';
            html += '<td>' + r.nombre + '</td>';
            html += '<td>' + r.identificacion + '</td>';
            html += '<td>' + (r.area_servicio || '—') + '</td>';
            html += '<td>' + (r.cargo || '—') + '</td>';
            html += '<td>' + (r.tipo_contrato || '—') + '</td>';
            html += '<td>' + r.hora_registro + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        return html;
    }
    @endif
});
</script>
@endpush
