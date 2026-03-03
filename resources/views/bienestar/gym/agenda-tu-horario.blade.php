<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>HUV - Registro de Cita de Entreno</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        corporate: "#2e3a75",
                        "huv-blue": "#2e3a75",
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    },
                },
            },
        };
    </script>
    <style type="text/tailwindcss">
        :root {
            --primary-color: #2e3a75;
        }
        /* remove bg-hero image; global background applied via body below */
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        input:focus, select:focus {
            border-color: #2e3a75 !var(--tw-ring-color);
            --tw-ring-color: #2e3a75;
        }
    </style>
    <style>
        /* Aplicar fondo global desde public/img/nuevologo.jpg */
        body {
            background-image: url('{{ asset('img/nuevologo.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const identificacionInput = document.getElementById('identificacion');
            
            identificacionInput.addEventListener('blur', function() {
                const identificacion = this.value.trim();
                
                if (identificacion) {
                    // Fetch data from inscripgym
                    fetch(`/agenda/horario/fetch-inscrip/${identificacion}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.found) {
                                // Populate fields
                                document.getElementById('nombres').value = data.data.nombre || '';
                                document.getElementById('primer_apellido').value = data.data.primer_apellido || '';
                                document.getElementById('segundo_apellido').value = data.data.segundo_apellido || '';
                                document.getElementById('celular').value = data.data.celular || '';
                                document.getElementById('servicio').value = data.data.servicio || '';
                            } else {
                                // Clear fields if not found
                                document.getElementById('nombres').value = '';
                                document.getElementById('primer_apellido').value = '';
                                document.getElementById('segundo_apellido').value = '';
                                document.getElementById('telefono').value = '';
                                document.getElementById('servicio').value = '';
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching data:', error);
                        });
                }
            });
        });
    </script>
</head>
<body class="bg-slate-100 font-sans min-h-screen">
{{-- background applied globally via CSS; removed fixed bg-hero element --}}
<main class="relative z-10 min-h-screen flex flex-col items-center py-8 px-4">
    <form method="POST" action="{{ route('agenda.horario.store') }}">
        @csrf
        <header class="w-full max-w-5xl mb-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 bg-white rounded-full flex items-center justify-center p-2 shadow-lg">
                    <span class="material-symbols-outlined text-corporate text-3xl">local_hospital</span>
                </div>
                <div>
                    <h1 class="text-white text-lg font-bold leading-tight">HOSPITAL UNIVERSITARIO<br/>DEL VALLE</h1>
                    <p class="text-blue-200 text-[10px] tracking-widest uppercase">Evaristo García E.S.E</p>
                </div>
            </div>
            <div class="flex items-center gap-8">
                <div class="text-right hidden sm:block">
                    <p class="text-white text-3xl font-black italic tracking-tighter leading-none">LATIENDO JUNTOS</p>
                    <p class="text-blue-300 text-xs font-medium uppercase tracking-wider mt-1">Desde 1956 escribiendo historia</p>
                </div>
                <div class="border-l-2 border-white/30 pl-6 flex items-center">
                    <div class="text-white leading-none">
                        <span class="text-5xl font-black italic">70</span>
                        <span class="text-xs font-bold tracking-widest block text-center">AÑOS</span>
                    </div>
                </div>
            </div>
        </header>
        <div class="w-full max-w-5xl bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl overflow-hidden border border-white/40">
            <div class="bg-corporate/5 p-8 border-b border-slate-200">
                <h2 class="text-3xl font-extrabold text-corporate flex items-center gap-3">
                    <span class="material-symbols-outlined text-4xl">calendar_month</span>
                    Registro de Cita de Entreno HUV
                </h2>
                <p class="text-slate-600 mt-2 font-medium">Complete los siguientes campos para programar su sesión de entrenamiento.</p>
            </div>
            <div class="p-8 space-y-12">
                <section>
                    <div class="flex items-center gap-2 mb-6 border-b border-slate-100 pb-2">
                        <span class="material-symbols-outlined text-corporate">person</span>
                        <h3 class="text-lg font-bold text-slate-800 uppercase tracking-wide">Sección 1: Datos Personales</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase">Identificación</label>
                            <input id="identificacion" name="identificacion" class="w-full h-12 px-4 rounded-xl border-slate-200 bg-white focus:ring-2 focus:ring-corporate/20 transition-all" placeholder="C.C. / C.E." type="text" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase">Nombres</label>
                            <input id="nombres" name="nombres" class="w-full h-12 px-4 rounded-xl border-slate-200 bg-white focus:ring-2 focus:ring-corporate/20 transition-all" placeholder="Sus nombres" type="text" required readonly/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase">Primer Apellido</label>
                            <input id="primer_apellido" name="primer_apellido" class="w-full h-12 px-4 rounded-xl border-slate-200 bg-white focus:ring-2 focus:ring-corporate/20 transition-all" placeholder="Primer apellido" type="text" required readonly/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase">Segundo Apellido</label>
                            <input id="segundo_apellido" name="segundo_apellido" class="w-full h-12 px-4 rounded-xl border-slate-200 bg-white focus:ring-2 focus:ring-corporate/20 transition-all" placeholder="Segundo apellido" type="text" readonly/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase">Teléfono</label>
                            <input id="telefono" name="telefono" class="w-full h-12 px-4 rounded-xl border-slate-200 bg-white focus:ring-2 focus:ring-corporate/20 transition-all" placeholder="Teléfono" type="text" readonly/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase">Servicio</label>
                            <input id="servicio" name="servicio" class="w-full h-12 px-4 rounded-xl border-slate-200 bg-white focus:ring-2 focus:ring-corporate/20 transition-all" placeholder="Servicio o unidad" type="text" required readonly/>
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
                            <select name="dia_entrenamiento" class="w-full h-14 pl-12 pr-4 rounded-2xl border-2 border-corporate/20 bg-white font-bold text-corporate appearance-none focus:border-corporate focus:ring-0 transition-all cursor-pointer" required>
                                <option value="LUNES">LUNES</option>
                                <option value="MARTES">MARTES</option>
                                <option value="MIERCOLES">MIERCOLES</option>
                                <option value="JUEVES">JUEVES</option>
                                <option value="VIERNES">VIERNES</option>
                            </select>
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-corporate">event</span>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="flex items-center gap-2 mb-6 border-b border-slate-100 pb-2">
                        <span class="material-symbols-outlined text-corporate">schedule</span>
                        <h3 class="text-lg font-bold text-slate-800 uppercase tracking-wide">Sección 3: Selección de Horario</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label>
                                <input type="radio" name="horario" value="07:00 - 08:00" required>
                                07:00 - 08:00
                            </label>
                        </div>
                        <div>
                            <label>
                                <input type="radio" name="horario" value="09:00 - 10:00">
                                09:00 - 10:00
                            </label>
                        </div>
                        <div>
                            <label>
                                <input type="radio" name="horario" value="04:30 - 05:30">
                                04:30 - 05:30
                            </label>
                        </div>
                    </div>
                </section>
                <div class="pt-8 border-t border-slate-100">
                    <button type="submit" class="w-full bg-corporate hover:bg-corporate/90 text-white font-bold py-5 rounded-2xl shadow-xl shadow-corporate/20 flex items-center justify-center gap-3 transition-all transform hover:-translate-y-1 active:scale-95 group">
                        <span class="text-lg">CONFIRMAR MI CITA DE ENTRENO</span>
                        <span class="material-symbols-outlined transition-transform group-hover:translate-x-1">check_circle</span>
                    </button>
                    <p class="text-center text-xs text-slate-400 mt-6 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">info</span>
                        Al confirmar, se enviará un soporte de citación a su correo institucional.
                    </p>
                </div>
            </div>
        </div>
    </form>
</main>
<div class="fixed top-0 left-0 w-32 h-32 bg-corporate/20 blur-3xl rounded-full -translate-x-1/2 -translate-y-1/2 z-0"></div>
</body>
</html>