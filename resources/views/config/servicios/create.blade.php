
@extends('layouts.app')

@section('title','Crear Servicio')
@section('header','Crear Servicio')

@push('head')
<style>
    .autocomplete-wrapper { position: relative; }
    .autocomplete-list {
        position: absolute; z-index: 1050; width: 100%;
        max-height: 220px; overflow-y: auto;
        background: #fff; border: 1px solid #dee2e6;
        border-top: 0; border-radius: 0 0 .375rem .375rem;
        box-shadow: 0 4px 12px rgba(0,0,0,.1);
        display: none;
    }
    .autocomplete-list .autocomplete-item {
        padding: 8px 14px; cursor: pointer; font-size: .9rem;
    }
    .autocomplete-list .autocomplete-item:hover,
    .autocomplete-list .autocomplete-item.active {
        background-color: #e9ecef;
    }
    .autocomplete-list .autocomplete-item .match-highlight {
        font-weight: 700; color: #dc3545;
    }
    .duplicate-warning {
        display: none; color: #dc3545; font-size: .85rem; margin-top: 4px;
    }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('config.servicios.store') }}" method="POST" id="formCrearServicio">
            @csrf
            <div class="form-group mb-3">
                <label class="form-label fw-semibold">Nombre</label>
                <div class="autocomplete-wrapper">
                    <input type="text" id="nombreServicio" name="nombre" class="form-control"
                           value="{{ old('nombre') }}" maxlength="150" required
                           autocomplete="off" placeholder="Escriba el nombre del servicio...">
                    <div id="autocompleteList" class="autocomplete-list"></div>
                </div>
                <div id="duplicateWarning" class="duplicate-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Ya existe un servicio con este nombre.
                </div>
            </div>
            <button type="submit" class="btn btn-primary" id="btnCrear">Crear</button>
            <a href="{{ route('config.servicios.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('nombreServicio');
    const list  = document.getElementById('autocompleteList');
    const warn  = document.getElementById('duplicateWarning');
    const btn   = document.getElementById('btnCrear');
    const url   = @json(route('config.servicios.buscar'));
    let timer   = null;
    let activeIdx = -1;

    input.addEventListener('input', function () {
        clearTimeout(timer);
        const val = this.value.trim();
        if (val.length < 2) { hide(); return; }
        timer = setTimeout(() => fetchResults(val), 300);
    });

    async function fetchResults(term) {
        const res  = await fetch(url + '?q=' + encodeURIComponent(term));
        const data = await res.json();
        activeIdx = -1;
        if (!data.length) { hide(); checkExact(term, []); return; }
        checkExact(term, data);
        list.innerHTML = data.map((name, i) => {
            const hl = name.replace(new RegExp('(' + escapeRx(term) + ')', 'gi'), '<span class="match-highlight">$1</span>');
            return '<div class="autocomplete-item" data-index="' + i + '" data-value="' + escHtml(name) + '">' + hl + '</div>';
        }).join('');
        list.style.display = 'block';
    }

    function checkExact(term, results) {
        const exists = results.some(n => n.toLowerCase() === term.toLowerCase());
        warn.style.display = exists ? 'block' : 'none';
        btn.disabled = exists;
    }

    list.addEventListener('click', function (e) {
        const item = e.target.closest('.autocomplete-item');
        if (item) { input.value = item.dataset.value; hide(); input.focus(); }
    });

    input.addEventListener('keydown', function (e) {
        const items = list.querySelectorAll('.autocomplete-item');
        if (!items.length) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); activeIdx = Math.min(activeIdx + 1, items.length - 1); highlight(items); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); activeIdx = Math.max(activeIdx - 1, 0); highlight(items); }
        else if (e.key === 'Enter' && activeIdx >= 0) { e.preventDefault(); input.value = items[activeIdx].dataset.value; hide(); }
        else if (e.key === 'Escape') { hide(); }
    });

    function highlight(items) {
        items.forEach((el, i) => el.classList.toggle('active', i === activeIdx));
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.autocomplete-wrapper')) hide();
    });

    function hide() { list.style.display = 'none'; list.innerHTML = ''; activeIdx = -1; }
    function escapeRx(s) { return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }
    function escHtml(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
});
</script>
@endpush
