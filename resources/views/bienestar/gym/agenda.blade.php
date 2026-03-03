@extends('layouts.app')

@section('title','Agenda tu Horario - GYM')
@section('header','Agenda tu Horario')

@push('head')
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
  <style type="text/tailwindcss">
    :root { --primary-color: #2e3a75; }
    /* bg-hero removed to allow global site background (public/img/nuevologo.jpg) from layout */
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    input:focus, select:focus { border-color: #2e3a75 !var(--tw-ring-color); --tw-ring-color: #2e3a75; }
    .slot-input { position:absolute; inset:0; opacity:0; }
  </style>
@endpush

@section('content')
  {{-- removed bg-hero fixed background so layout's global background is visible --}}
  <div class="w-full bg-transparent">
    <div id="agendaWrapper" class="w-full max-w-5xl mx-auto bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl overflow-hidden border border-white/40 p-0" style="margin-top:10px;">
      <div class="bg-corporate/5 p-4 sm:p-6 md:p-8 border-b border-slate-200">
        <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-corporate flex items-center gap-3">
          <span class="material-symbols-outlined text-2xl sm:text-3xl md:text-4xl">calendar_month</span>
          Registro de Cita de Entreno HUV
        </h2>
        <p class="text-slate-600 mt-2 font-medium text-sm sm:text-base">Complete los siguientes campos para programar su sesión de entrenamiento.</p>
      </div>

      <div class="p-4 sm:p-6 md:p-8 space-y-6">
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- Not-found suggestion (hidden by default) -->
        <div id="inscripNotFound" class="hidden p-4 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800">
          <strong>No se encontró inscripción previa.</strong>
          <span class="ml-2">Si no está registrado, por favor complete la inscripción antes de agendar.</span>
          <a href="{{ url('/bienestar/gym/inscripcion') }}" class="ml-3 font-bold underline text-yellow-900">Ir a Inscripción</a>
        </div>

        <form method="POST" action="{{ route('agenda.horario.store') }}">
          @csrf
          <section>
            <div class="flex items-center gap-2 mb-6 border-b border-slate-100 pb-2">
              <span class="material-symbols-outlined text-corporate">person</span>
              <h3 class="text-lg font-bold text-slate-800 uppercase tracking-wide">Sección 1: Datos Personales</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Identificación</label>
                <input name="identificacion" value="{{ old('identificacion') }}" class="w-full h-12 px-4 rounded-xl border-slate-200 bg-white" placeholder="C.C. / C.E." type="text"/>
              </div>
              <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Nombres</label>
                <input name="nombre" value="{{ old('nombre') }}" readonly class="w-full h-12 px-4 rounded-xl border-slate-200 bg-white/50" placeholder="Sus nombres" type="text"/>
              </div>
              <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Primer Apellido</label>
                <input name="primer_apellido" value="{{ old('primer_apellido') }}" readonly class="w-full h-12 px-4 rounded-xl border-slate-200 bg-white/50" placeholder="Primer apellido" type="text"/>
              </div>
              <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Segundo Apellido</label>
                <input name="segundo_apellido" value="{{ old('segundo_apellido') }}" readonly class="w-full h-12 px-4 rounded-xl border-slate-200 bg-white/50" placeholder="Segundo apellido" type="text"/>
              </div>
              <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Celular</label>
                <input name="telefono" value="{{ old('telefono') }}" readonly class="w-full h-12 px-4 rounded-xl border-slate-200 bg-white/50" placeholder="Número de celular" type="text"/>
              </div>
              <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Servicio</label>
                <input name="servicio" value="{{ old('servicio') }}" readonly class="w-full h-12 px-4 rounded-xl border-slate-200 bg-white/50" placeholder="Servicio o unidad" type="text"/>
              </div>
              <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Correo (lectura)</label>
                <input name="correolec" value="{{ old('correolec') }}" readonly class="w-full h-12 px-4 rounded-xl border-slate-200 bg-white/50" placeholder="correo.lectura@ejemplo.com" type="email"/>
              </div>
            </div>
          </section>

          <section>
            <div class="flex items-center gap-2 mb-6 border-b border-slate-100 pb-2">
              <span class="material-symbols-outlined text-corporate">today</span>
              <h3 class="text-lg font-bold text-slate-800 uppercase tracking-wide">Sección 2: Selección de Día</h3>
            </div>
            <div class="max-w-md">
              <label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Día de entrenamiento</label>
              <div class="relative">
                <select name="dia_entrenamiento" class="w-full h-14 pl-4 pr-4 rounded-2xl border-2 border-corporate/20 bg-white font-bold text-corporate appearance-none">
                  <option value="LUNES" {{ old('dia_entrenamiento')=='LUNES'?'selected':'' }}>LUNES</option>
                  <option value="MARTES" {{ old('dia_entrenamiento')=='MARTES'?'selected':'' }}>MARTES</option>
                  <option value="MIERCOLES" {{ old('dia_entrenamiento')=='MIERCOLES'?'selected':'' }}>MIÉRCOLES</option>
                  <option value="JUEVES" {{ old('dia_entrenamiento')=='JUEVES'?'selected':'' }}>JUEVES</option>
                  <option value="VIERNES" {{ old('dia_entrenamiento')=='VIERNES'?'selected':'' }}>VIERNES</option>
                </select>
              </div>
            </div>
          </section>

          <section>
            <div class="flex items-center gap-2 mb-6 border-b border-slate-100 pb-2">
              <span class="material-symbols-outlined text-corporate">schedule</span>
              <h3 class="text-lg font-bold text-slate-800 uppercase tracking-wide">Sección 3: Selección de Horario</h3>
            </div>
            <div id="slotsContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              {{-- slots dinámicos se inyectan por JS --}}
            </div>
          </section>

          <div class="pt-8 border-t border-slate-100">
            <button type="submit" class="w-full bg-corporate hover:bg-corporate/90 text-white font-bold py-5 rounded-2xl shadow-xl flex items-center justify-center gap-3">
              <span class="text-lg">CONFIRMAR MI CITA DE ENTRENO</span>
              <span class="material-symbols-outlined">check_circle</span>
            </button>
            <p class="text-center text-xs text-slate-400 mt-6 flex items-center justify-center gap-2">
              <span class="material-symbols-outlined text-sm">info</span>
              Al confirmar, se enviará un soporte de citación a su correo institucional.
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    // Map days to available slots
    const daySlots = {
      'LUNES': [
        '07:00 - 08:00','08:00 - 09:00','10:00 - 11:00','11:00 - 12:00','12:00 - 13:00','13:00 - 14:00','16:00 - 17:00','17:00 - 18:00'
      ],
      'MARTES': [
        '07:00 - 08:00','08:00 - 09:00','10:00 - 11:00','11:00 - 12:00','12:00 - 13:00','13:00 - 14:00','16:00 - 17:00','17:00 - 18:00'
      ],
      'MIERCOLES': [
        '07:00 - 08:00','08:00 - 09:00','10:00 - 11:00','11:00 - 12:00','12:00 - 13:00','13:00 - 14:00','16:00 - 17:00','17:00 - 18:00'
      ],
      'JUEVES': [
        '07:00 - 08:00','08:00 - 09:00','10:00 - 11:00','11:00 - 12:00','12:00 - 13:00','13:00 - 14:00','16:00 - 17:00','17:00 - 18:00'
      ],
      'VIERNES': [
        '07:00 - 08:00','08:00 - 09:00','10:00 - 11:00','11:00 - 12:00','12:00 - 13:00','13:00 - 14:00','16:30 - 17:30'
      ]
    };

    function renderSlotsFor(day){
      const container = document.getElementById('slotsContainer');
      container.innerHTML = '';
      const slots = daySlots[day] || [];
      const old = '{{ addslashes(old('horario', '')) }}';
      slots.forEach(s => {
        const label = document.createElement('label');
        label.className = 'relative group p-4 border-2 rounded-2xl text-left transition-all hover:shadow-md cursor-pointer';
        if(old === s) label.classList.add('border-corporate','bg-corporate/5');

        const input = document.createElement('input');
        input.type = 'radio';
        input.name = 'horario';
        input.value = s;
        input.className = 'slot-input';
        if(old === s) input.checked = true;

        const top = document.createElement('div');
        top.className = 'flex justify-between items-start mb-2';
        const spanTime = document.createElement('span');
        spanTime.className = 'text-xl font-black ' + (old===s ? 'text-corporate' : 'text-slate-800');
        spanTime.textContent = s;
        top.appendChild(spanTime);

        const bottom = document.createElement('div');
        bottom.className = 'flex justify-between items-end';
        const descr = document.createElement('span');
        descr.className = 'text-xs font-semibold text-slate-500 uppercase tracking-tight';
        descr.textContent = 'Capacitación';
        const capacity = document.createElement('span');
        capacity.className = 'text-xs font-bold text-corporate';
        capacity.textContent = '20/20 cupos';
        bottom.appendChild(descr);
        bottom.appendChild(capacity);

        label.appendChild(input);
        label.appendChild(top);
        label.appendChild(bottom);
        container.appendChild(label);
      });
    }

    document.addEventListener('DOMContentLoaded', function(){
      const daySelect = document.querySelector('select[name="dia_entrenamiento"]');
      const initialDay = daySelect.value || 'LUNES';
      renderSlotsFor(initialDay);
      daySelect.addEventListener('change', function(){
        renderSlotsFor(this.value);
      });
      // Ajustar zoom para que el formulario quepa en la ventana
      const wrapper = document.getElementById('agendaWrapper');
      function adjustZoom(){
        if(!wrapper) return;
        const available = window.innerHeight - 80;
        const needed = wrapper.scrollHeight;
        let scale = 1;
        if(needed > available) scale = Math.max(0.75, available / needed);
        // Use transform (cross-browser compatible: Chrome, Firefox, Safari)
        wrapper.style.transform = scale < 1 ? `scale(${scale})` : '';
        wrapper.style.transformOrigin = 'top center';
        // Reserve space for scaled element so parent doesn't collapse
        if(scale < 1) wrapper.style.marginBottom = `-${needed * (1 - scale)}px`;
        else wrapper.style.marginBottom = '';
      }
      adjustZoom();
      window.addEventListener('resize', adjustZoom);

      // Autofill when identificacion changes (fetch from inscripgym)
      const idInput = document.querySelector('input[name="identificacion"]');
      const apiBase = '{{ url('/bienestar/gym/inscrip-by-id') }}';
      if (idInput) {
        const notFoundDiv = document.getElementById('inscripNotFound');
        idInput.addEventListener('blur', function(){
          const val = this.value && this.value.trim();
          if (!val) { if(notFoundDiv) notFoundDiv.classList.add('hidden'); return; }
          const url = apiBase + '/' + encodeURIComponent(val);
          fetch(url, { credentials: 'same-origin' })
            .then(r => r.json())
            .then(json => {
              if (!json || !json.found) {
                if(notFoundDiv) notFoundDiv.classList.remove('hidden');
                return;
              }
              if(notFoundDiv) notFoundDiv.classList.add('hidden');
              const d = json.data;
              console.log('Datos recibidos de inscripgym:', d);
              console.log('Teléfono (celular):', d.telefono);
              console.log('Servicio:', d.servicio);
              // fill fields (only read-only ones)
              const fields = ['nombre','primer_apellido','segundo_apellido','correolec','telefono','servicio'];
              fields.forEach(f => {
                const el = document.querySelector('[name="'+f+'"]');
                if (!el) {
                  console.log('Campo no encontrado:', f);
                  return;
                }
                el.value = d[f] ?? '';
                console.log(`Campo ${f} actualizado con:`, d[f]);
              });
            })
            .catch(()=>{
              if(notFoundDiv) notFoundDiv.classList.remove('hidden');
            });
        });
        // hide suggestion when user starts typing a different id
        idInput.addEventListener('input', function(){ if(notFoundDiv) notFoundDiv.classList.add('hidden'); });
      }
    });
  </script>
@endpush
