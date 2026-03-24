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
                    <div class="col-md-3 mb-3">
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
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-sm text-white font-weight-bold px-3 mr-2" style="background-color: var(--brand);">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                        <button type="button" class="btn btn-sm btn-success font-weight-bold px-3" onclick="abrirModalAsistencia()">
                            <i class="fas fa-user-check mr-2"></i>Asistencia
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

<!-- Modal Asistencia -->
<div class="modal fade" id="modalAsistencia" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title font-weight-bold"><i class="fas fa-clipboard-list mr-2"></i>Asistencia GYM</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Pestañas -->
      <ul class="nav nav-tabs px-3 pt-3 border-0" id="tabAsistencia" role="tablist">
        <li class="nav-item">
          <a class="nav-link active font-weight-bold" id="tab-tomar" data-toggle="tab" href="#pane-tomar" role="tab">
            <i class="fas fa-user-check mr-1"></i>Tomar Asistencia
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link font-weight-bold" id="tab-consultar" data-toggle="tab" href="#pane-consultar" role="tab">
            <i class="fas fa-search mr-1"></i>Consultar Historial
          </a>
        </li>
      </ul>

      <div class="tab-content">
        <!-- ===== PESTAÑA: TOMAR ASISTENCIA ===== -->
        <div class="tab-pane fade show active p-4" id="pane-tomar" role="tabpanel">
          <div class="row mb-3">
              <div class="col-md-5">
                  <label class="font-weight-bold small text-muted text-uppercase">Fecha (Día calendario):</label>
                  <input type="date" id="asistencia_fecha" class="form-control" value="{{ date('Y-m-d') }}">
              </div>
              <div class="col-md-5">
                  <label class="font-weight-bold small text-muted text-uppercase">Franja Horaria:</label>
                  <select id="asistencia_franja" class="form-control">
                      <option value="">Seleccione una franja</option>
                      @foreach($franjasUnicas as $franja)
                          @if(!empty($franja))
                          <option value="{{ $franja }}">{{ $franja }}</option>
                          @endif
                      @endforeach
                  </select>
              </div>
              <div class="col-md-2 d-flex align-items-end">
                  <button type="button" class="btn btn-primary w-100 font-weight-bold" onclick="cargarAsistencia()"><i class="fas fa-sync mr-1"></i>Cargar</button>
              </div>
          </div>

          <hr>

          <div id="loadingAsistencia" class="text-center py-4" style="display: none;">
              <i class="fas fa-spinner fa-spin fa-2x text-success"></i>
              <p class="mt-2 text-muted">Cargando usuarios inscritos...</p>
          </div>

          <div id="listaAsistenciaContainer" style="display: none; max-height: 400px; overflow-y: auto;">
              <table class="table table-hover table-sm">
                  <thead class="bg-light sticky-top">
                      <tr>
                          <th>Identificación</th>
                          <th>Nombre Completo</th>
                          <th class="text-center">Asistió</th>
                      </tr>
                  </thead>
                  <tbody id="tablaAsistenciaCuerpo">
                      <!-- Filas dinámicas -->
                  </tbody>
              </table>
          </div>

          <div class="text-right mt-3">
            <button type="button" class="btn btn-success font-weight-bold" onclick="guardarAsistencia()" id="btnGuardarAsistencia" style="display: none;">
              <i class="fas fa-save mr-1"></i>Guardar Asistencia
            </button>
          </div>
        </div>

        <!-- ===== PESTAÑA: CONSULTAR HISTORIAL ===== -->
        <div class="tab-pane fade p-4" id="pane-consultar" role="tabpanel">
          <div class="row mb-3">
              <div class="col-md-3">
                  <label class="font-weight-bold small text-muted text-uppercase">Desde:</label>
                  <input type="date" id="consulta_desde" class="form-control" value="{{ date('Y-m-01') }}">
              </div>
              <div class="col-md-3">
                  <label class="font-weight-bold small text-muted text-uppercase">Hasta:</label>
                  <input type="date" id="consulta_hasta" class="form-control" value="{{ date('Y-m-d') }}">
              </div>
              <div class="col-md-3">
                  <label class="font-weight-bold small text-muted text-uppercase">Franja (opcional):</label>
                  <select id="consulta_franja" class="form-control">
                      <option value="">Todas</option>
                      @foreach($franjasUnicas as $franja)
                          @if(!empty($franja))
                          <option value="{{ $franja }}">{{ $franja }}</option>
                          @endif
                      @endforeach
                  </select>
              </div>
              <div class="col-md-3">
                  <label class="font-weight-bold small text-muted text-uppercase">Identificación:</label>
                  <input type="text" id="consulta_identificacion" class="form-control" placeholder="Opcional...">
              </div>
          </div>
          <div class="d-flex justify-content-end mb-3">
              <button type="button" class="btn btn-primary font-weight-bold mr-2" onclick="consultarAsistencia()">
                  <i class="fas fa-search mr-1"></i>Consultar
              </button>
              <button type="button" class="btn font-weight-bold text-white" id="btnExportarExcel" style="display: none; background-color: #217346;" onclick="exportarAsistenciaExcel()">
                  <i class="fas fa-file-excel mr-1"></i>Exportar Excel
              </button>
          </div>

          <!-- Resumen estadístico -->
          <div id="resumenAsistencia" class="row mb-3" style="display: none;">
              <div class="col-md-3">
                  <div class="card border-0 shadow-sm text-center py-2" style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9);">
                      <span class="small text-muted font-weight-bold">TOTAL REGISTROS</span>
                      <span class="h4 font-weight-bold mb-0" id="res_total" style="color: var(--brand);">0</span>
                  </div>
              </div>
              <div class="col-md-3">
                  <div class="card border-0 shadow-sm text-center py-2" style="background: linear-gradient(135deg, #e3f2fd, #bbdefb);">
                      <span class="small text-muted font-weight-bold">ASISTIERON</span>
                      <span class="h4 font-weight-bold mb-0 text-success" id="res_asistieron">0</span>
                  </div>
              </div>
              <div class="col-md-3">
                  <div class="card border-0 shadow-sm text-center py-2" style="background: linear-gradient(135deg, #fce4ec, #f8bbd0);">
                      <span class="small text-muted font-weight-bold">NO ASISTIERON</span>
                      <span class="h4 font-weight-bold mb-0 text-danger" id="res_no_asistieron">0</span>
                  </div>
              </div>
              <div class="col-md-3">
                  <div class="card border-0 shadow-sm text-center py-2" style="background: linear-gradient(135deg, #fff3e0, #ffe0b2);">
                      <span class="small text-muted font-weight-bold">% ASISTENCIA</span>
                      <span class="h4 font-weight-bold mb-0" style="color: #e65100;" id="res_porcentaje">0%</span>
                  </div>
              </div>
          </div>

          <div id="loadingConsulta" class="text-center py-4" style="display: none;">
              <i class="fas fa-spinner fa-spin fa-2x" style="color: var(--brand);"></i>
              <p class="mt-2 text-muted">Consultando registros...</p>
          </div>

          <div id="consultaResultContainer" style="display: none; max-height: 350px; overflow-y: auto;">
              <table class="table table-hover table-sm table-striped">
                  <thead class="bg-light sticky-top">
                      <tr>
                          <th>Fecha</th>
                          <th>Identificación</th>
                          <th>Nombre Completo</th>
                          <th>Franja</th>
                          <th class="text-center">Estado</th>
                      </tr>
                  </thead>
                  <tbody id="tablaConsultaCuerpo">
                  </tbody>
              </table>
          </div>
        </div>
      </div>

      <div class="modal-footer bg-light border-0">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
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
            Swal.fire({icon: 'warning', title: 'Atención', text: 'Por favor ingrese una observación válida.'});
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
                Swal.fire({icon: 'success', title: '¡Éxito!', text: 'La observación fue guardada exitosamente.', confirmButtonColor: '#2e3a75'});
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = 'Guardar Observación';
            console.error(err);
            Swal.fire({icon: 'error', title: 'Oops...', text: 'Ocurrió un error al guardar la observación.', confirmButtonColor: '#2e3a75'});
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

    function abrirModalAsistencia() {
        $('#modalAsistencia').modal('show');
    }

    // Variable global para almacenar los datos consultados para exportar
    let datosConsultaAsistencia = [];

    function consultarAsistencia() {
        const desde = document.getElementById('consulta_desde').value;
        const hasta = document.getElementById('consulta_hasta').value;
        const franja = document.getElementById('consulta_franja').value;
        const identificacion = document.getElementById('consulta_identificacion').value;

        if(!desde || !hasta) {
            Swal.fire({icon: 'warning', title: 'Campos requeridos', text: 'Por favor seleccione el rango de fechas.', confirmButtonColor: '#2e3a75'});
            return;
        }

        document.getElementById('loadingConsulta').style.display = 'block';
        document.getElementById('consultaResultContainer').style.display = 'none';
        document.getElementById('resumenAsistencia').style.display = 'none';

        let url = `{{ route('bienestar.asistencia.consultar') }}?fecha_desde=${desde}&fecha_hasta=${hasta}`;
        if(franja) url += `&franja=${encodeURIComponent(franja)}`;
        if(identificacion) url += `&identificacion=${encodeURIComponent(identificacion)}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                document.getElementById('loadingConsulta').style.display = 'none';

                if(!data.success) {
                    Swal.fire({icon: 'error', title: 'Error', text: data.error || 'No se pudieron obtener los datos.', confirmButtonColor: '#2e3a75'});
                    return;
                }

                // Resumen
                document.getElementById('res_total').textContent = data.resumen.total;
                document.getElementById('res_asistieron').textContent = data.resumen.asistieron;
                document.getElementById('res_no_asistieron').textContent = data.resumen.no_asistieron;
                document.getElementById('res_porcentaje').textContent = data.resumen.porcentaje + '%';
                document.getElementById('resumenAsistencia').style.display = 'flex';

                // Tabla de detalle
                const tbody = document.getElementById('tablaConsultaCuerpo');
                tbody.innerHTML = '';

                if(data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3"><i class="fas fa-info-circle mr-1"></i>No se encontraron registros de asistencia en este rango.</td></tr>';
                } else {
                    data.data.forEach(r => {
                        const badge = r.asistio
                            ? '<span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i>Asistió</span>'
                            : '<span class="badge badge-danger px-2 py-1"><i class="fas fa-times mr-1"></i>No asistió</span>';
                        tbody.innerHTML += `
                            <tr>
                                <td class="small">${r.fecha}</td>
                                <td class="font-weight-bold">${r.identificacion}</td>
                                <td>${r.nombre_completo}</td>
                                <td class="small">${r.franja}</td>
                                <td class="text-center">${badge}</td>
                            </tr>
                        `;
                    });
                }

                document.getElementById('consultaResultContainer').style.display = 'block';

                // Guardar datos y mostrar botón de exportar
                datosConsultaAsistencia = data.data;
                document.getElementById('btnExportarExcel').style.display = data.data.length > 0 ? 'inline-block' : 'none';
            })
            .catch(err => {
                console.error(err);
                document.getElementById('loadingConsulta').style.display = 'none';
                Swal.fire({icon: 'error', title: 'Error de red', text: 'No se pudo consultar la asistencia.', confirmButtonColor: '#2e3a75'});
            });
    }

    function exportarAsistenciaExcel() {
        if (!datosConsultaAsistencia || datosConsultaAsistencia.length === 0) {
            Swal.fire({icon: 'info', title: 'Sin datos', text: 'No hay registros para exportar. Realice una consulta primero.', confirmButtonColor: '#2e3a75'});
            return;
        }

        function generarExcelAsistencia() {
            try {
                const datosExcel = datosConsultaAsistencia.map((r, i) => ({
                    '#': i + 1,
                    'Fecha': r.fecha,
                    'Identificación': r.identificacion,
                    'Nombre Completo': r.nombre_completo,
                    'Franja': r.franja,
                    'Estado': r.asistio ? 'Asistió' : 'No asistió'
                }));

                const ws = XLSX.utils.json_to_sheet(datosExcel);
                ws['!cols'] = [
                    { wch: 5 },
                    { wch: 14 },
                    { wch: 16 },
                    { wch: 32 },
                    { wch: 16 },
                    { wch: 14 }
                ];

                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Asistencia GYM');

                const desde = document.getElementById('consulta_desde').value;
                const hasta = document.getElementById('consulta_hasta').value;
                const fileName = `Asistencia_GYM_${desde}_a_${hasta}.xlsx`;

                XLSX.writeFile(wb, fileName);
                Swal.fire({icon: 'success', title: '¡Descargado!', text: 'El archivo Excel se ha generado correctamente.', confirmButtonColor: '#28a745', timer: 2500, showConfirmButton: false});
            } catch(e) {
                console.error('Error generando Excel:', e);
                Swal.fire({icon: 'error', title: 'Error', text: 'Error al generar el archivo Excel.', confirmButtonColor: '#2e3a75'});
            }
        }

        if (typeof XLSX === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js';
            script.onload = function() { generarExcelAsistencia(); };
            script.onerror = function() {
                Swal.fire({icon: 'error', title: 'Error', text: 'No se pudo cargar la librería de Excel.', confirmButtonColor: '#2e3a75'});
            };
            document.head.appendChild(script);
        } else {
            generarExcelAsistencia();
        }
    }

    function cargarAsistencia() {
        const fecha = document.getElementById('asistencia_fecha').value;
        const franja = document.getElementById('asistencia_franja').value;

        if(!fecha || !franja) {
            Swal.fire({icon: 'warning', title: 'Campos requeridos', text: 'Por favor seleccione una fecha y una franja horaria.', confirmButtonColor: '#2e3a75'});
            return;
        }

        document.getElementById('loadingAsistencia').style.display = 'block';
        document.getElementById('listaAsistenciaContainer').style.display = 'none';
        document.getElementById('btnGuardarAsistencia').style.display = 'none';

        fetch(`{{ route('bienestar.asistencia.cargar') }}?fecha=${fecha}&franja=${encodeURIComponent(franja)}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('loadingAsistencia').style.display = 'none';
                
                if(!data.success) {
                    Swal.fire({icon: 'error', title: 'Error', text: data.error, confirmButtonColor: '#2e3a75'});
                    return;
                }

                const tbody = document.getElementById('tablaAsistenciaCuerpo');
                tbody.innerHTML = '';

                if(data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">No hay usuarios inscritos en esta franja y día de la semana.</td></tr>';
                } else {
                    data.data.forEach(user => {
                        const isChecked = user.asistio ? 'checked' : '';
                        tbody.innerHTML += `
                            <tr>
                                <td class="font-weight-bold">${user.identificacion}</td>
                                <td>${user.nombre_completo || 'N/A'}</td>
                                <td class="text-center">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input asistencia-checkbox" id="check_${user.identificacion}" data-id="${user.identificacion}" ${isChecked}>
                                        <label class="custom-control-label" for="check_${user.identificacion}"></label>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    document.getElementById('btnGuardarAsistencia').style.display = 'inline-block';
                }

                document.getElementById('listaAsistenciaContainer').style.display = 'block';
            })
            .catch(err => {
                console.error(err);
                document.getElementById('loadingAsistencia').style.display = 'none';
                Swal.fire({icon: 'error', title: 'Error de red', text: 'No se pudo cargar la asistencia.', confirmButtonColor: '#2e3a75'});
            });
    }

    function guardarAsistencia() {
        const fecha = document.getElementById('asistencia_fecha').value;
        const franja = document.getElementById('asistencia_franja').value;
        const btn = document.getElementById('btnGuardarAsistencia');
        
        let asistencias = {};
        document.querySelectorAll('.asistencia-checkbox').forEach(cb => {
            asistencias[cb.getAttribute('data-id')] = cb.checked;
        });

        if(Object.keys(asistencias).length === 0) {
            Swal.fire({icon: 'info', title: 'Sin datos', text: 'No hay usuarios para guardar su asistencia.', confirmButtonColor: '#2e3a75'});
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';

        fetch('{{ route("bienestar.asistencia.guardar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                fecha: fecha,
                franja: franja,
                asistencias: asistencias
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = 'Guardar Asistencia';
            if(data.success) {
                Swal.fire({icon: 'success', title: '¡Guardado!', text: 'La asistencia se ha guardado correctamente.', confirmButtonColor: '#28a745'});
                $('#modalAsistencia').modal('hide');
            } else {
                Swal.fire({icon: 'error', title: 'Oops...', text: 'Error al guardar asistencia.', confirmButtonColor: '#2e3a75'});
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = 'Guardar Asistencia';
            console.error(err);
            Swal.fire({icon: 'error', title: 'Error de servidor', text: 'Ocurrió un error al contactar al servidor.', confirmButtonColor: '#2e3a75'});
        });
    }
</script>
@endpush
@endsection
