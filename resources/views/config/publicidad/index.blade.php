@extends('layouts.app')

@section('title','Gestión de Publicidad')
@section('header','Gestión de Publicidad')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Publicidad</h5>
            <div>
                <button id="btnCreatePublicidad" class="btn" style="background:var(--brand);color:#fff;" data-toggle="modal" data-target="#createPublicidadModal">Crear Publicidad</button>
            </div>
        </div>

        <div class="mb-3">
            <input id="searchPublicidad" type="text" class="form-control" placeholder="Buscar por título, tag o descripción...">
        </div>

        <div class="table-responsive">
            <table class="table table-striped" id="tablaPublicidad">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Título</th>
                        <th>Tag</th>
                        <th>Link</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Crear / Editar Publicidad - Diseño Moderno -->
<div class="modal fade" id="createPublicidadModal" tabindex="-1" role="dialog" aria-labelledby="createPublicidadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: min(1200px, 95vw); margin: 0.5rem auto;">
    <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(46, 58, 117, 0.3); overflow: hidden;">
      <!-- Header Moderno -->
      <div class="modal-header" style="background: linear-gradient(135deg, #2e3a75 0%, #1e2a55 100%); color:#fff; border: none; padding: 1rem 1.25rem;">
        <div class="d-flex align-items-center">
            <div class="d-flex align-items-center justify-content-center d-none d-sm-flex" style="width: 48px; height: 48px; background: rgba(255,255,255,0.15); border-radius: 12px; margin-right: 1rem;">
                <i class="fas fa-bullhorn" style="font-size: 24px;"></i>
            </div>
            <div>
                <h5 class="modal-title mb-0" id="createPublicidadModalLabel" style="font-weight: 700; font-size: 1.5rem;">
                    <span id="modalPublicidadTitleText">Crear Publicidad</span>
                </h5>
                <p class="mb-0" style="font-size: 0.75rem; opacity: 0.8;">Complete la información para publicar contenido</p>
            </div>
        </div>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 1; font-size: 2rem; font-weight: 300;">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <form id="formPublicidad" enctype="multipart/form-data">
      <div class="modal-body" style="padding: 2.5rem; background: #f8f9fc;">
        <input type="hidden" name="id" id="pub_id">
        
        <!-- Sección: Información Principal -->
        <div class="mb-4">
            <div class="d-flex align-items-center mb-3">
                <div style="width: 4px; height: 24px; background: #2e3a75; border-radius: 2px; margin-right: 12px;"></div>
                <h6 class="mb-0" style="color: #2e3a75; font-weight: 700; font-size: 1.1rem;">Información Principal</h6>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label style="color: #2e3a75; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-heading mr-2" style="font-size: 0.75rem;"></i>Título
                    </label>
                    <input name="titulo" id="pub_titulo" class="form-control" placeholder="Ej: Nueva Campaña de Salud" style="border-radius: 10px; border: 2px solid #e8eaf0; padding: 0.75rem 1rem; font-size: 0.95rem; transition: all 0.3s;">
                </div>
                <div class="col-md-6 mb-3">
                    <label style="color: #2e3a75; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-tag mr-2" style="font-size: 0.75rem;"></i>Tag / Categoría
                    </label>
                    <input name="tag" id="pub_tag" class="form-control" placeholder="Ej: Salud, Bienestar" style="border-radius: 10px; border: 2px solid #e8eaf0; padding: 0.75rem 1rem; font-size: 0.95rem; transition: all 0.3s;">
                </div>
            </div>
            
            <div class="mb-3">
                <label style="color: #2e3a75; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">
                    <i class="fas fa-align-left mr-2" style="font-size: 0.75rem;"></i>Descripción
                </label>
                <textarea name="descripcion" id="pub_descripcion" class="form-control" rows="4" placeholder="Describa el contenido de la publicidad..." style="border-radius: 10px; border: 2px solid #e8eaf0; padding: 0.75rem 1rem; font-size: 0.95rem; transition: all 0.3s;"></textarea>
            </div>
            
            <div class="mb-3">
                <label style="color: #2e3a75; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">
                    <i class="fas fa-link mr-2" style="font-size: 0.75rem;"></i>Link (URL)
                </label>
                <input name="link" id="pub_link" class="form-control" placeholder="https://ejemplo.com" style="border-radius: 10px; border: 2px solid #e8eaf0; padding: 0.75rem 1rem; font-size: 0.95rem; transition: all 0.3s;">
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label style="color: #2e3a75; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-calendar-plus mr-2" style="font-size: 0.75rem;"></i>Fecha de Inicio
                    </label>
                    <input type="datetime-local" name="fecha_inicio" id="pub_fecha_inicio" class="form-control" style="border-radius: 10px; border: 2px solid #e8eaf0; padding: 0.75rem 1rem; font-size: 0.95rem; transition: all 0.3s;">
                    <small class="text-muted" style="font-size: 0.75rem;">Fecha y hora en que la publicidad se activará</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label style="color: #2e3a75; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-calendar-times mr-2" style="font-size: 0.75rem;"></i>Fecha de Terminación
                    </label>
                    <input type="datetime-local" name="fecha_fin" id="pub_fecha_fin" class="form-control" style="border-radius: 10px; border: 2px solid #e8eaf0; padding: 0.75rem 1rem; font-size: 0.95rem; transition: all 0.3s;">
                    <small class="text-muted" style="font-size: 0.75rem;">Fecha y hora en que la publicidad se desactivará automáticamente</small>
                </div>
            </div>
        </div>

        <!-- Sección: Contenido Visual -->
        <div class="mb-4">
            <div class="d-flex align-items-center mb-3">
                <div style="width: 4px; height: 24px; background: #2e3a75; border-radius: 2px; margin-right: 12px;"></div>
                <h6 class="mb-0" style="color: #2e3a75; font-weight: 700; font-size: 1.1rem;">Contenido Visual</h6>
            </div>
            
            <div class="mb-3">
                <label style="color: #2e3a75; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.75rem;">
                    <i class="fas fa-image mr-2" style="font-size: 0.75rem;"></i>Imagen de Publicidad
                </label>
                <div class="border-2 border-dashed rounded-3 p-4 text-center" style="border-color: #2e3a75 !important; background: rgba(46, 58, 117, 0.03); transition: all 0.3s;" onmouseover="this.style.background='rgba(46, 58, 117, 0.08)'" onmouseout="this.style.background='rgba(46, 58, 117, 0.03)'">
                    <div class="d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(46, 58, 117, 0.1); border-radius: 50%; margin: 0 auto;">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 36px; color: #2e3a75;"></i>
                    </div>
                    <p class="mb-2" style="color: #2e3a75; font-weight: 600; font-size: 1rem;">Arrastra y suelta tu imagen aquí</p>
                    <p class="mb-3" style="color: #6c757d; font-size: 0.875rem;">Formatos soportados: JPG, PNG, GIF (Max 2MB)</p>
                    <label for="pub_imagen" class="btn btn-primary" style="background: #2e3a75; border: none; border-radius: 10px; padding: 0.75rem 2rem; font-weight: 600; cursor: pointer;">
                        <i class="fas fa-folder-open mr-2"></i>Seleccionar Archivo
                    </label>
                    <input type="file" name="imagen" id="pub_imagen" class="d-none" accept="image/*">
                    <div class="mt-3">
                        <small class="text-muted" id="imagenActualText" style="font-weight: 500;"></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Información de Sección -->
        <div class="mb-4">
            <div class="d-flex align-items-center mb-3">
                <div style="width: 4px; height: 24px; background: #2e3a75; border-radius: 2px; margin-right: 12px;"></div>
                <h6 class="mb-0" style="color: #2e3a75; font-weight: 700; font-size: 1.1rem;">Información de Sección</h6>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label style="color: #2e3a75; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-text-height mr-2" style="font-size: 0.75rem;"></i>Sección Título
                    </label>
                    <input name="seccion_titulo" id="pub_seccion_titulo" class="form-control" placeholder="Título de la sección" style="border-radius: 10px; border: 2px solid #e8eaf0; padding: 0.75rem 1rem; font-size: 0.95rem; transition: all 0.3s;">
                </div>
                <div class="col-md-6 mb-3">
                    <label style="color: #2e3a75; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-text-width mr-2" style="font-size: 0.75rem;"></i>Sección Subtítulo
                    </label>
                    <input name="seccion_subtitulo" id="pub_seccion_subtitulo" class="form-control" placeholder="Subtítulo de la sección" style="border-radius: 10px; border: 2px solid #e8eaf0; padding: 0.75rem 1rem; font-size: 0.95rem; transition: all 0.3s;">
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="alert" style="background: rgba(46, 58, 117, 0.05); border: 2px solid rgba(46, 58, 117, 0.1); border-radius: 12px; padding: 1rem;">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle mr-3" style="color: #2e3a75; font-size: 1.25rem; margin-top: 2px;"></i>
                <div>
                    <p class="mb-1" style="color: #2e3a75; font-weight: 600; font-size: 0.875rem;">Vista Previa en Tiempo Real</p>
                    <p class="mb-0" style="color: #6c757d; font-size: 0.8rem;">La publicidad se mostrará en el sistema según la configuración establecida.</p>
                </div>
            </div>
        </div>
      </div>
      
      <div class="modal-footer" style="background: #fff; border-top: 2px solid #e8eaf0; padding: 1.5rem 2rem;">
        <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius: 10px; padding: 0.75rem 1.5rem; font-weight: 600; border: 2px solid #e8eaf0;">
            <i class="fas fa-times mr-2"></i>Cancelar
        </button>
        <button type="submit" class="btn" style="background: linear-gradient(135deg, #2e3a75 0%, #3d4d8f 100%); color:#fff; border: none; border-radius: 10px; padding: 0.75rem 2rem; font-weight: 600; box-shadow: 0 4px 12px rgba(46, 58, 117, 0.3);">
            <i class="fas fa-save mr-2"></i>Guardar Publicidad
        </button>
      </div>
      </form>
    </div>
  </div>
</div>

<style>
    #createPublicidadModal .form-control:focus {
        border-color: #2e3a75 !important;
        box-shadow: 0 0 0 0.2rem rgba(46, 58, 117, 0.15) !important;
    }
    
    #createPublicidadModal .btn:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const apiDataUrl = "{{ route('config.publicidad.data') }}";
    const storeUrl = "{{ route('config.publicidad.store') }}";
    const baseUrl = "{{ url('config/publicidad') }}";

    const tbody = document.querySelector('#tablaPublicidad tbody');
    const search = document.getElementById('searchPublicidad');
    const token = document.querySelector('meta[name=csrf-token]').getAttribute('content');

    async function loadData(){
        try {
            const res = await fetch(apiDataUrl);
            const items = await res.json();
            renderTable(items);
        } catch(e) {
            console.error('Error loading data:', e);
        }
    }

    function renderTable(items){
        tbody.innerHTML = '';
        items.forEach(i => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${i.id}</td>
                <td>${i.banner ? `<img src="${i.banner}" style="height:48px;object-fit:cover;border-radius:6px">` : ''}</td>
                <td>${i.titulo || ''}</td>
                <td>${i.tag || ''}</td>
                <td>${i.link ? `<a href="${i.link}" target="_blank">Abrir</a>` : ''}</td>
                <td>
                    <button class="btn btn-sm btn-primary btn-edit" data-id="${i.id}">
                        <i class="fas fa-edit mr-1"></i>Editar
                    </button>
                    <button class="btn btn-sm btn-danger btn-delete ml-1" data-id="${i.id}">
                        <i class="fas fa-trash mr-1"></i>Eliminar
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
        // aplicar paginación (12 por página)
        if(window.applyPagination) window.applyPagination('#tablaPublicidad', 12);
    }

    // Limpiar modal al abrir para crear
    document.getElementById('btnCreatePublicidad').addEventListener('click', function(){
        document.getElementById('formPublicidad').reset();
        document.getElementById('pub_id').value = '';
        document.getElementById('modalPublicidadTitleText').textContent = 'Crear Publicidad';
        document.getElementById('imagenActualText').textContent = '';
    });

    // search filter
    search.addEventListener('input', async function(){
        const q = this.value.toLowerCase();
        const res = await fetch(apiDataUrl);
        const items = await res.json();
        const filtered = items.filter(i => (i.titulo||'').toLowerCase().includes(q) || (i.tag||'').toLowerCase().includes(q) || (i.descripcion||'').toLowerCase().includes(q));
        renderTable(filtered);
    });

    // submit form (create/update)
    const form = document.getElementById('formPublicidad');
    form.addEventListener('submit', async function(e){
        e.preventDefault();
        const formData = new FormData(form);
        const id = formData.get('id');
        let url = storeUrl;
        let method = 'POST';
        
        if(id){
            url = `${baseUrl}/${id}`;
            method = 'POST';
            formData.append('_method','PUT');
        }
        
        try {
            const res = await fetch(url, {
                method: method,
                headers: { 'X-CSRF-TOKEN': token },
                body: formData
            });
            const data = await res.json();
            
            if(data.success){
                $('#createPublicidadModal').modal('hide');
                form.reset();
                loadData();
                alert('Guardado correctamente');
            } else {
                alert('Error al guardar: ' + (data.message || 'Error desconocido'));
            }
        } catch(e) {
            console.error('Error:', e);
            alert('Error al guardar. Por favor, intente nuevamente.');
        }
    });

    // edit / delete handlers
    tbody.addEventListener('click', async function(e){
        const editBtn = e.target.closest('.btn-edit');
        const deleteBtn = e.target.closest('.btn-delete');
        
        if(editBtn){
            const id = editBtn.dataset.id;
            console.log('Editando publicidad ID:', id);
            
            try {
                const res = await fetch(`${baseUrl}/${id}`);
                console.log('Response status:', res.status);
                
                if(!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                
                const item = await res.json();
                console.log('Item cargado:', item);
                
                document.getElementById('pub_id').value = item.id;
                document.getElementById('pub_titulo').value = item.titulo || '';
                document.getElementById('pub_tag').value = item.tag || '';
                document.getElementById('pub_descripcion').value = item.descripcion || '';
                document.getElementById('pub_link').value = item.link || '';
                document.getElementById('pub_seccion_titulo').value = item.seccion_titulo || '';
                document.getElementById('pub_seccion_subtitulo').value = item.seccion_subtitulo || '';
                
                // Formatear fechas para datetime-local
                if(item.fecha_inicio) {
                    const fechaInicio = new Date(item.fecha_inicio);
                    document.getElementById('pub_fecha_inicio').value = fechaInicio.toISOString().slice(0, 16);
                }
                if(item.fecha_fin) {
                    const fechaFin = new Date(item.fecha_fin);
                    document.getElementById('pub_fecha_fin').value = fechaFin.toISOString().slice(0, 16);
                }
                
                document.getElementById('modalPublicidadTitleText').textContent = 'Editar Publicidad';
                
                if(item.banner) {
                    document.getElementById('imagenActualText').textContent = 'Imagen actual: ' + item.banner.split('/').pop();
                } else {
                    document.getElementById('imagenActualText').textContent = '';
                }
                
                $('#createPublicidadModal').modal('show');
            } catch(e) {
                console.error('Error completo:', e);
                alert('Error al cargar la publicidad: ' + e.message);
            }
        }
        
        if(deleteBtn){
            if(!confirm('¿Está seguro de eliminar este elemento?')) return;
            const id = deleteBtn.dataset.id;
            
            try {
                const res = await fetch(`${baseUrl}/${id}`, { 
                    method: 'POST', 
                    headers: {
                        'X-CSRF-TOKEN': token, 
                        'Content-Type':'application/json'
                    }, 
                    body: JSON.stringify({'_method':'DELETE'}) 
                });
                const data = await res.json();
                
                if(data.success) {
                    loadData();
                    alert('Eliminado correctamente');
                } else {
                    alert('Error al eliminar');
                }
            } catch(e) {
                console.error('Error:', e);
                alert('Error al eliminar. Por favor, intente nuevamente.');
            }
        }
    });

    // inicializar
    loadData();
});
</script>
@endpush
