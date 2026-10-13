{{--
    Firma del profesional: solo se muestra para los roles de médico (Role::requiereFirma).
    La firma registrada se coloca sola en el paso "Recomendaciones y firma" de los conceptos médicos.
    Parámetros: $rolesConFirma (array), $rolSeleccionado (string), $firmaActual (?string data URL).
    El navegador recorta la firma, le quita el fondo claro y convierte los PDF a imagen (firma_procesada).
--}}
@php($firmaPrevia = old('firma_procesada') ?: ($firmaActual ?? null))
<div id="firmaProfesional" class="{{ in_array($rolSeleccionado, $rolesConFirma, true) ? '' : 'd-none' }}">
    <h6 class="text-secondary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-signature mr-1"></i> Firma del Profesional</h6>

    <div class="form-row mb-3">
        <div class="form-group col-md-6 mb-2 mb-md-0">
            <label for="firma_archivo" class="text-secondary font-weight-bold small">Firma (imagen o PDF)</label>
            <div class="custom-file">
                <input type="file" id="firma_archivo" name="firma_archivo" class="custom-file-input @error('firma_archivo') is-invalid @enderror"
                       accept=".png,.jpg,.jpeg,.pdf,image/png,image/jpeg,application/pdf">
                <label class="custom-file-label text-truncate" for="firma_archivo" data-browse="Buscar" id="firmaArchivoNombre">Seleccione la imagen o el PDF de la firma</label>
            </div>
            @error('firma_archivo')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
            <small class="form-text text-muted">
                PNG, JPG o PDF (máx. 5 MB). Se recorta y se le quita el fondo blanco automáticamente.
                Aparecerá sola en los conceptos médicos que emita este profesional.
            </small>
            @if(!empty($firmaActual))
            <div class="custom-control custom-checkbox mt-2">
                <input type="checkbox" class="custom-control-input" id="quitar_firma" name="quitar_firma" value="1" {{ old('quitar_firma') ? 'checked' : '' }}>
                <label class="custom-control-label small" for="quitar_firma">Quitar la firma registrada</label>
            </div>
            @endif
            <input type="hidden" name="firma_procesada" id="firma_procesada" value="{{ old('firma_procesada') }}">
        </div>
        <div class="form-group col-md-6">
            <label class="text-secondary font-weight-bold small">Vista previa</label>
            <div id="firmaVistaPrevia" style="height:130px;border:1.5px dashed #c1c7dd;border-radius:10px;background:#fbfbfe;display:flex;align-items:center;justify-content:center;padding:8px;">
                <img id="firmaImagen" alt="Firma del profesional" src="{{ $firmaPrevia }}" style="max-height:110px;max-width:100%;object-fit:contain;{{ $firmaPrevia ? '' : 'display:none;' }}">
                <span id="firmaSinImagen" class="text-muted small" style="{{ $firmaPrevia ? 'display:none;' : '' }}"><i class="fas fa-file-signature mr-1"></i> Sin firma registrada</span>
            </div>
            <small id="firmaEstado" class="form-text text-muted"></small>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var ROLES_CON_FIRMA = @json(array_values($rolesConFirma));
    var PDFJS = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/';
    var MAX_BYTES = {{ \App\Models\FirmaUsuario::MAX_KB }} * 1024;

    var bloque = document.getElementById('firmaProfesional');
    var rol = document.getElementById('role');
    var archivo = document.getElementById('firma_archivo');
    var oculto = document.getElementById('firma_procesada');
    var img = document.getElementById('firmaImagen');
    var vacio = document.getElementById('firmaSinImagen');
    var estado = document.getElementById('firmaEstado');
    var nombreArchivo = document.getElementById('firmaArchivoNombre');
    var quitar = document.getElementById('quitar_firma');
    var original = img.getAttribute('src') || '';
    var procesando = false;

    // Mostrar el campo solo para los roles de médico
    function alternarBloque() { bloque.classList.toggle('d-none', ROLES_CON_FIRMA.indexOf(rol.value) === -1); }
    rol.addEventListener('change', alternarBloque);

    function verImagen(src) {
        img.style.display = src ? '' : 'none';
        vacio.style.display = src ? 'none' : '';
        if (src) img.src = src; else img.removeAttribute('src');
    }
    function aviso(texto, tipo) { estado.className = 'form-text ' + (tipo === 'error' ? 'text-danger' : tipo === 'ok' ? 'text-success' : 'text-muted'); estado.textContent = texto || ''; }

    if (quitar) quitar.addEventListener('change', function () {
        if (quitar.checked) { oculto.value = ''; archivo.value = ''; nombreArchivo.textContent = 'Seleccione la imagen o el PDF de la firma'; verImagen(''); aviso('La firma registrada se quitará al guardar.'); }
        else { verImagen(original); aviso(''); }
    });

    // ── Lectura del archivo en un lienzo ──
    function lienzoDesdeImagen(f) {
        return new Promise(function (ok, falla) {
            var url = URL.createObjectURL(f), im = new Image();
            im.onload = function () {
                var esc = Math.min(1, 2000 / Math.max(im.naturalWidth, im.naturalHeight));
                var c = document.createElement('canvas');
                c.width = Math.max(1, Math.round(im.naturalWidth * esc)); c.height = Math.max(1, Math.round(im.naturalHeight * esc));
                c.getContext('2d').drawImage(im, 0, 0, c.width, c.height);
                URL.revokeObjectURL(url); ok(c);
            };
            im.onerror = function () { URL.revokeObjectURL(url); falla(new Error('imagen')); };
            im.src = url;
        });
    }
    function cargarPdfJs() {
        return new Promise(function (ok, falla) {
            if (window.pdfjsLib) return ok(window.pdfjsLib);
            var s = document.createElement('script');
            s.src = PDFJS + 'pdf.min.js';
            s.onload = function () { window.pdfjsLib.GlobalWorkerOptions.workerSrc = PDFJS + 'pdf.worker.min.js'; ok(window.pdfjsLib); };
            s.onerror = function () { falla(new Error('pdfjs')); };
            document.head.appendChild(s);
        });
    }
    function lienzoDesdePdf(f) {
        return Promise.all([cargarPdfJs(), f.arrayBuffer()]).then(function (r) {
            return r[0].getDocument({ data: new Uint8Array(r[1]) }).promise;
        }).then(function (pdf) { return pdf.getPage(1); }).then(function (pagina) {
            var vista0 = pagina.getViewport({ scale: 1 });
            var vista = pagina.getViewport({ scale: Math.min(3, 2000 / Math.max(vista0.width, vista0.height)) });
            var c = document.createElement('canvas');
            c.width = Math.round(vista.width); c.height = Math.round(vista.height);
            var ctx = c.getContext('2d');
            ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, c.width, c.height);
            // intent 'print': no depende de requestAnimationFrame (se completa aunque la pestaña no esté visible)
            return pagina.render({ canvasContext: ctx, viewport: vista, intent: 'print' }).promise.then(function () { return c; });
        });
    }

    // ── Limpieza: fondo claro → transparente, recorte al trazo y tamaño máximo 900×300 ──
    function limpiarFirma(c) {
        var w = c.width, h = c.height, ctx = c.getContext('2d');
        var datos = ctx.getImageData(0, 0, w, h), px = datos.data;
        function lum(i) { return 0.299 * px[i] + 0.587 * px[i + 1] + 0.114 * px[i + 2]; }

        // Brillo del fondo: promedio del borde de la imagen (papel escaneado o fotografiado)
        var suma = 0, n = 0, paso = Math.max(1, Math.floor(Math.min(w, h) / 50));
        for (var x = 0; x < w; x += paso) { [0, h - 1].forEach(function (y) { var i = (y * w + x) * 4; if (px[i + 3] > 16) { suma += lum(i); n++; } }); }
        for (var y = 0; y < h; y += paso) { [0, w - 1].forEach(function (xx) { var i = (y * w + xx) * 4; if (px[i + 3] > 16) { suma += lum(i); n++; } }); }
        var fondo = n ? suma / n : 255;
        if (fondo < 140) fondo = 255;                  // fondo oscuro o sin borde claro: umbral fijo
        var claro = fondo - 25, tinta = fondo - 80;    // > claro: fondo · < tinta: trazo pleno · entre ambos: semitransparente

        var minX = w, minY = h, maxX = -1, maxY = -1;
        for (var yy = 0; yy < h; yy++) {
            for (var xx2 = 0; xx2 < w; xx2++) {
                var i = (yy * w + xx2) * 4, a = px[i + 3], l = lum(i);
                if (a < 16 || l >= claro) { px[i + 3] = 0; continue; }
                if (l > tinta) px[i + 3] = Math.round(a * (claro - l) / (claro - tinta));
                if (xx2 < minX) minX = xx2; if (xx2 > maxX) maxX = xx2;
                if (yy < minY) minY = yy; if (yy > maxY) maxY = yy;
            }
        }
        if (maxX < 0) return null;                     // no hay trazo
        ctx.putImageData(datos, 0, 0);

        var m = Math.round(Math.max(maxX - minX, maxY - minY) * 0.03) + 4;
        minX = Math.max(0, minX - m); minY = Math.max(0, minY - m); maxX = Math.min(w - 1, maxX + m); maxY = Math.min(h - 1, maxY + m);
        var cw = maxX - minX + 1, ch = maxY - minY + 1, esc = Math.min(1, 900 / cw, 300 / ch);
        var salida = document.createElement('canvas');
        salida.width = Math.max(1, Math.round(cw * esc)); salida.height = Math.max(1, Math.round(ch * esc));
        salida.getContext('2d').drawImage(c, minX, minY, cw, ch, 0, 0, salida.width, salida.height);
        return salida.toDataURL('image/png');
    }

    archivo.addEventListener('change', function () {
        var f = archivo.files && archivo.files[0];
        oculto.value = '';
        nombreArchivo.textContent = f ? f.name : 'Seleccione la imagen o el PDF de la firma';
        if (!f) { verImagen(quitar && quitar.checked ? '' : original); aviso(''); return; }
        if (f.size > MAX_BYTES) { archivo.value = ''; nombreArchivo.textContent = 'Seleccione la imagen o el PDF de la firma'; aviso('El archivo supera 5 MB. Seleccione uno más liviano.', 'error'); return; }

        var esPdf = f.type === 'application/pdf' || /\.pdf$/i.test(f.name);
        if (!esPdf && !/^image\/(png|jpe?g)$/i.test(f.type) && !/\.(png|jpe?g)$/i.test(f.name)) {
            archivo.value = ''; nombreArchivo.textContent = 'Seleccione la imagen o el PDF de la firma';
            aviso('Formato no permitido. Use una imagen PNG o JPG, o un PDF.', 'error'); return;
        }
        if (quitar) quitar.checked = false;
        procesando = true;
        aviso(esPdf ? 'Convirtiendo el PDF en imagen…' : 'Procesando la firma…');

        (esPdf ? lienzoDesdePdf(f) : lienzoDesdeImagen(f)).then(function (c) {
            var url = limpiarFirma(c);
            if (!url) throw new Error('vacia');
            oculto.value = url;
            verImagen(url);
            aviso('Firma lista. Se guardará al guardar el usuario.', 'ok');
        }).catch(function (e) {
            oculto.value = '';
            if (e && e.message === 'vacia') {
                archivo.value = ''; nombreArchivo.textContent = 'Seleccione la imagen o el PDF de la firma'; verImagen(original);
                aviso('No se encontró ninguna firma en el archivo (está en blanco).', 'error');
            } else if (esPdf) {
                archivo.value = ''; nombreArchivo.textContent = 'Seleccione la imagen o el PDF de la firma'; verImagen(original);
                aviso('No fue posible leer el PDF. Intente con otro archivo o suba la firma como imagen (PNG o JPG).', 'error');
            } else {
                aviso('No se pudo procesar la imagen; se guardará tal como está.', 'error');
            }
        }).then(function () { procesando = false; });
    });

    // No enviar el formulario mientras la firma se está procesando
    archivo.form.addEventListener('submit', function (e) {
        if (procesando) { e.preventDefault(); aviso('Espere a que termine de procesarse la firma y vuelva a guardar.', 'error'); }
    });
})();
</script>
@endpush
