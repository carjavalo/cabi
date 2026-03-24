@extends('layouts.app')

@section('title', 'Listados - Bienestar')
@section('header', 'Listados de Usuarios Inscritos en el GYM')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h4 class="card-title font-weight-bold" style="color: var(--brand);">Filtros de Búsqueda</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('bienestar.listados') }}" class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase">Identificación</label>
                        <input type="text" name="identificacion" class="form-control" value="{{ request('identificacion') }}" placeholder="Buscar por número...">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase">Día</label>
                        <select name="dia" class="form-control">
                            <option value="">Todos los días</option>
                            @php
                                $nombresDias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
                            @endphp
                            @foreach($diasUnicos as $dia)
                                @if(!empty($dia))
                                <option value="{{ $dia }}" {{ request('dia') == $dia ? 'selected' : '' }}>{{ $nombresDias[$dia] ?? $dia }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase">Franja Horaria</label>
                        <select name="franja" class="form-control">
                            <option value="">Todas las franjas</option>
                            @foreach($franjasUnicas as $franja)
                                @if(!empty($franja))
                                <option value="{{ $franja }}" {{ request('franja') == $franja ? 'selected' : '' }}>{{ $franja }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end justify-content-start">
                        <button type="submit" class="btn btn-sm text-white font-weight-bold px-3" style="background-color: var(--brand);">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-top-0 text-muted font-weight-bold small text-uppercase">Identificación</th>
                                <th class="border-top-0 text-muted font-weight-bold small text-uppercase">Nombre Completo</th>
                                <th class="border-top-0 text-muted font-weight-bold small text-uppercase">Día</th>
                                <th class="border-top-0 text-muted font-weight-bold small text-uppercase">Franja Horaria</th>
                                <th class="border-top-0 text-muted font-weight-bold small text-uppercase">F. Inscripción</th>
                                <th class="border-top-0 text-muted font-weight-bold small text-uppercase">Contacto E.</th>
                                <th class="border-top-0 text-muted font-weight-bold small text-uppercase text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $diasSemana = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
                            @endphp
                            @forelse($listados as $item)
                            <tr>
                                <td class="font-weight-bold">{{ $item->identificacion }}</td>
                                <td>{{ $item->nombre_completo ?? 'N/A' }}</td>
                                <td><span class="badge" style="background-color: rgba(46,58,117,0.1); color: var(--brand);">{{ isset($diasSemana[$item->dia_semana]) ? $diasSemana[$item->dia_semana] : 'N/A' }}</span></td>
                                <td>{{ $item->hora_inicio && $item->hora_fin ? substr($item->hora_inicio, 0, 5) . ' a ' . substr($item->hora_fin, 0, 5) : 'N/A' }}</td>
                                <td class="text-muted small">{{ $item->fecha_inscripcion ? \Carbon\Carbon::parse($item->fecha_inscripcion)->format('Y-m-d') : 'N/A' }}</td>
                                <td>
                                    @if($item->contacto_emergencia)
                                        <a href="tel:{{ $item->contacto_emergencia }}" class="text-decoration-none">
                                            <i class="fas fa-phone-alt mr-1"></i>{{ $item->contacto_emergencia }}
                                        </a>
                                    @else
                                        <span class="text-muted italic">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary shadow-sm" onclick="abrirModalCrearObs('{{ $item->identificacion }}')">
                                            <i class="fas fa-plus-circle mr-1"></i>Observar
                                        </button>
                                        <button class="btn btn-sm btn-outline-info shadow-sm ml-1" onclick="abrirModalVerObs('{{ $item->identificacion }}', '{{ $item->nombre_completo }}')">
                                            <i class="fas fa-eye mr-1"></i>Ver
                                        </button>
                                        @if($item->contacto_emergencia)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->contacto_emergencia) }}" target="_blank" class="btn btn-sm btn-outline-success shadow-sm ml-1" data-toggle="tooltip" title="Contactar por WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                                    No se encontraron usuarios inscritos con estos filtros.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                {{ $listados->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Observación -->
<div class="modal fade" id="modalCrearObs" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header" style="background-color: var(--brand); color: white;">
        <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Nueva Observación</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <form id="formNuevaObs">
            <input type="hidden" id="obs_identificacion" required>
            <div class="form-group mb-0">
                <label class="font-weight-bold text-muted small text-uppercase">Detalle el evento o anotación:</label>
                <textarea id="obs_texto" class="form-control" rows="4" placeholder="Escriba aquí los detalles..." required></textarea>
            </div>
        </form>
      </div>
      <div class="modal-footer bg-light border-0">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn text-white font-weight-bold" style="background-color: var(--brand);" onclick="guardarObservacion()" id="btnGuardarObs">Guardar Observación</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Ver Observaciones -->
<div class="modal fade" id="modalVerObs" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-list-alt mr-2" style="color: var(--brand);"></i>Observaciones de <span id="ver_obs_nombre" style="color: var(--brand);"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-0" style="max-height: 400px; overflow-y: auto;">
        <div id="loadingObs" class="text-center py-5">
            <i class="fas fa-spinner fa-spin fa-2x" style="color: var(--brand);"></i>
            <p class="mt-2 text-muted">Cargando observaciones...</p>
        </div>
        <div id="listaObsContainer" class="p-3" style="display: none;">
            <!-- Renderizado dinámico -->
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
    // Variables globales para los token CSRF
    const csrfToken = '{{ csrf_token() }}';

    function abrirModalCrearObs(identificacion) {
        document.getElementById('obs_identificacion').value = identificacion;
        document.getElementById('obs_texto').value = '';
        $('#modalCrearObs').modal('show');
    }

    function guardarObservacion() {
        const ide = document.getElementById('obs_identificacion').value;
        const texto = document.getElementById('obs_texto').value;
        const btn = document.getElementById('btnGuardarObs');

        if(!texto.trim()) {
            alert('Por favor ingrese una observación Válida.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';

        fetch('{{ route("bienestar.observaciones.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                identificacion: ide,
                observacion: texto
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = 'Guardar Observación';
            if(data.success) {
                $('#modalCrearObs').modal('hide');
                // Alerta nativa simple
                alert('La observación fue guardada exitosamente.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = 'Guardar Observación';
            console.error(err);
            alert('Ocurrió un error al guardar la observación.');
        });
    }

    function abrirModalVerObs(identificacion, nombreCompleto) {
        document.getElementById('ver_obs_nombre').textContent = nombreCompleto;
        document.getElementById('loadingObs').style.display = 'block';
        document.getElementById('listaObsContainer').style.display = 'none';
        $('#modalVerObs').modal('show');

        // Fetch de observaciones
        fetch(`/bienestar/observaciones/${identificacion}`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('listaObsContainer');
                container.innerHTML = '';
                
                if(data.length === 0) {
                    container.innerHTML = '<div class="alert bg-light text-center my-3"><i class="fas fa-info-circle mr-2"></i> No hay observaciones registradas para este usuario.</div>';
                } else {
                    data.forEach(obs => {
                        // Formatear fecha
                        const f = new Date(obs.created_at);
                        const fechaFormat = f.toLocaleDateString() + ' ' + f.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        
                        container.innerHTML += `
                            <div class="card mb-3 border-0 shadow-sm" style="border-left: 4px solid var(--brand) !important;">
                                <div class="card-body">
                                    <p class="mb-2">${obs.observacion}</p>
                                    <div class="d-flex justify-content-between text-muted small font-weight-bold">
                                        <span><i class="fas fa-user-edit mr-1"></i>${obs.creado_por || 'Sistema'}</span>
                                        <span><i class="fas fa-clock mr-1"></i>${fechaFormat}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
                
                document.getElementById('loadingObs').style.display = 'none';
                container.style.display = 'block';
            })
            .catch(err => {
                console.error(err);
                document.getElementById('loadingObs').style.display = 'none';
                document.getElementById('listaObsContainer').innerHTML = '<div class="alert alert-danger">Error al cargar las observaciones.</div>';
                document.getElementById('listaObsContainer').style.display = 'block';
            });
    }
</script>
@endpush
@endsection
