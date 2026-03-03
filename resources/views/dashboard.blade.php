@extends('layouts.app')

@section('title','Inicio')
@section('header','')

@push('head')
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        body { font-family: 'Lexend', sans-serif; }
    </style>
@endpush

@section('content')
@php
    // $publicidad es una colección (puede estar vacía)
    $pub = null;
    if(isset($publicidad) && is_iterable($publicidad)){
        $pub = $publicidad->first();
    }
@endphp

<div class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display min-h-screen">

    <main class="max-w-[1200px] w-full mx-auto p-4 sm:p-6 lg:p-12 space-y-8 sm:space-y-10">
        <section class="relative overflow-hidden rounded-[1.25rem] h-[200px] sm:h-[280px] lg:h-[340px] shadow-2xl">
            <div id="bannerCarousel" class="relative h-full">
                @if(isset($publicidad) && $publicidad->count())
                    @foreach($publicidad as $idx => $b)
                        <div class="carousel-slide absolute inset-0 bg-cover bg-center transition-opacity duration-700" data-index="{{ $idx }}" style="background-image: url('{{ $b->banner ?? asset('img/nuevologo.jpg') }}'); opacity: {{ $idx===0 ? '1' : '0' }};">
                            <div class="absolute inset-0 bg-gradient-to-r from-primary/90 via-primary/40 to-transparent"></div>
                            <div class="relative h-full flex flex-col justify-center px-6 lg:px-16 max-w-3xl text-white">
                                <span class="bg-white/20 backdrop-blur-md text-white text-[11px] font-bold uppercase tracking-[0.25em] px-4 py-1.5 rounded-full w-fit mb-4">{{ $b->tag ?? '' }}</span>
                                <h2 class="text-2xl lg:text-3xl font-extrabold mb-3 leading-[1.1]">{{ $b->titulo ?? '' }}</h2>
                                <p class="text-slate-100/90 text-base lg:text-lg leading-relaxed max-w-xl">{{ $b->descripcion ?? '' }}</p>
                                @if(!empty($b->link))
                                    <div class="mt-6">
                                        <a href="{{ $b->link }}" class="bg-white text-primary px-5 py-2 rounded-lg font-semibold shadow-sm hover:bg-slate-50">Ver más</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('img/nuevologo.jpg') }}')"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-primary/90 via-primary/40 to-transparent"></div>
                    <div class="relative h-full flex flex-col justify-center px-6 lg:px-16 max-w-3xl text-white">
                        <h2 class="text-2xl font-extrabold">Bienvenido</h2>
                        <p class="text-slate-100/90 text-base leading-relaxed max-w-xl">No hay publicidades configuradas.</p>
                    </div>
                @endif

                <!-- Controls -->
                @if(isset($publicidad) && $publicidad->count() > 1)
                <button id="carouselPrev" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/30 text-white rounded-full w-10 h-10 flex items-center justify-center" aria-label="Anterior">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M15 18L9 12L15 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button id="carouselNext" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/30 text-white rounded-full w-10 h-10 flex items-center justify-center" aria-label="Siguiente">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M9 6L15 12L9 18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <!-- Indicators -->
                <div id="carouselIndicators" class="absolute left-1/2 -translate-x-1/2 bottom-4 flex gap-2"></div>
                @endif
            </div>
        </section>

        <div class="space-y-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight">{{ $pub->seccion_titulo ?? 'Contenido Principal' }}</h3>
                    <p class="text-slate-500 mt-2 font-medium text-lg">{{ $pub->seccion_subtitulo ?? 'Subtítulo opcional.' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                {{-- Aquí puede iterarse contenido adicional desde la tabla publicidad si existe --}}
                    @if(isset($publicidad) && $publicidad->count())
                    @foreach($publicidad as $p)
                        <div class="bg-white dark:bg-slate-900 rounded-[1rem] overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
                            <div class="h-40 bg-cover bg-center" style="background-image: url('{{ $p->banner ?? asset('img/nuevologo.jpg') }}')"></div>
                            <div class="p-4">
                                <h4 class="font-bold text-slate-900 dark:text-slate-100 text-lg">{{ $p->titulo ?? 'Sin título' }}</h4>
                                <p class="text-sm text-slate-500 mt-2">{{ Str::limit($p->descripcion, 120) }}</p>
                                @if(!empty($p->link))
                                    <a href="{{ $p->link }}" class="inline-block mt-3 text-primary font-bold">Ver</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="bg-white dark:bg-slate-900 rounded-[1rem] overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                        <p class="text-slate-600">No hay elementos de publicidad configurados.</p>
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>



@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const slides = Array.from(document.querySelectorAll('#bannerCarousel .carousel-slide'));
    if(!slides.length) return;
    const indicators = document.getElementById('carouselIndicators');
    const prevBtn = document.getElementById('carouselPrev');
    const nextBtn = document.getElementById('carouselNext');
    let current = 0;
    let timer = null;

    function show(index){
        slides.forEach((s,i)=> s.style.opacity = (i===index? '1':'0'));
        // indicators
        if(indicators){
            indicators.querySelectorAll('button').forEach((b,i)=> b.classList.toggle('bg-white', i===index));
        }
        current = index;
    }

    function next(){ show((current+1) % slides.length); }
    function prev(){ show((current-1+slides.length) % slides.length); }

    // build indicators
    if(indicators){
        slides.forEach((s,i)=>{
            const btn = document.createElement('button');
            btn.className = 'w-3 h-3 rounded-full bg-white/40';
            btn.addEventListener('click', ()=>{ show(i); resetTimer(); });
            indicators.appendChild(btn);
        });
    }

    if(nextBtn) nextBtn.addEventListener('click', ()=>{ next(); resetTimer(); });
    if(prevBtn) prevBtn.addEventListener('click', ()=>{ prev(); resetTimer(); });

    function resetTimer(){
        if(timer) clearInterval(timer);
        timer = setInterval(next, 6000);
    }

    // start
    show(0);
    resetTimer();
});
</script>
@endpush
