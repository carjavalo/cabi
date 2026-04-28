<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <title>Modal de Capacitaciones H.U.V.</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#2c4370",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                    },
                },
            },
        };
    </script>
    <style type="text/tailwindcss">
        :root {
            --primary-color: #2c4370;
        }
        body {
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
            background-color: var(--primary-color);
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .modal-shadow {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        }
        .overlay {
            background: #2c4370;
        }
    </style>
    <style>
        body {
            min-height: max(884px, 100dvh);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 overflow-hidden">
    <div class="fixed inset-0 overlay z-0"></div>
    <div
        class="relative z-10 w-full max-w-[390px] h-[820px] max-h-[95vh] bg-white dark:bg-slate-900 rounded-[2.5rem] flex flex-col overflow-hidden modal-shadow border border-white/10">

        <button
            class="absolute top-6 right-6 z-20 text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
            <span class="material-symbols-outlined text-2xl">close</span>
        </button>

        <header class="px-6 pt-8 pb-4 flex flex-col items-center shrink-0">


            <div class="w-40 mb-3 bg-white dark:bg-transparent overflow-hidden rounded-lg">
                <img src="http://intranet.huv.gov.co/intranet/wp-content/uploads/2026/02/logoinicio.gif"
                    alt="Hospital Universitario del Valle" class="w-full h-auto object-contain">
            </div>





            <div class="text-center">
                <h1 class="text-xl font-extrabold text-[#2c4370] dark:text-white tracking-tight leading-tight">
                    Capacitaciones H.U.V.</h1>
                <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                    Hospital Universitario del Valle</p>
            </div>
        </header>
        <main class="flex-1 px-5 space-y-3 flex flex-col justify-center">







            <a
                href=" https://n9.cl/3d81d">
                <button
                    class="w-full group flex items-center p-3.5 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 transition-all active:scale-[0.97]">
                    <div
                        class="w-10 h-10 bg-slate-50 dark:bg-slate-700 rounded-xl flex items-center justify-center text-[#2c4370] dark:text-blue-400 shrink-0">
                        <span class="material-symbols-outlined text-2xl">calendar_month</span>
                    </div>
                    <div class="ml-4 text-left flex-1">
                        <h2 class="text-sm font-bold text-slate-800 dark:text-white leading-tight">Ver Cronograma</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Próximas sesiones</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-300 text-sm">arrow_forward_ios</span>
                </button>
            </a>

            <a href=" https://docs.google.com/forms/d/1WTSkbo0xH5mI_EMi-sOZlkpyHdsvwWtSmLfRI3Vhu8Y/edit"  target="_blank">
                <button
                    class="w-full group flex items-center p-3.5 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 transition-all active:scale-[0.97]">
                    <div
                        class="w-10 h-10 bg-slate-50 dark:bg-slate-700 rounded-xl flex items-center justify-center text-[#2c4370] dark:text-blue-400 shrink-0">
                        <span class="material-symbols-outlined text-2xl">how_to_reg</span>
                    </div>
                    <div class="ml-4 text-left flex-1">
                        <h2 class="text-sm font-bold text-slate-800 dark:text-white leading-tight">Inscripción y
                            Participación</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Nuevos cursos</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-300 text-sm">arrow_forward_ios</span>
                </button>
            </a>


            <a href="https://meet.google.com/zwp-yahy-tpb" target="_blank">
                <button
                    class="w-full group flex items-center p-3.5 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 transition-all active:scale-[0.97]">
                    <div
                        class="w-10 h-10 bg-slate-50 dark:bg-slate-700 rounded-xl flex items-center justify-center text-[#2c4370] dark:text-blue-400 shrink-0">
                        <span class="material-symbols-outlined text-2xl">menu_book</span>
                    </div>
                    <div class="ml-4 text-left flex-1">
                        <h2 class="text-sm font-bold text-slate-800 dark:text-white leading-tight">Ingresar a la
                            Capacitación</h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Materiales y clases</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-300 text-sm">arrow_forward_ios</span>
                </button>
            </a>



            <div class="p-4 bg-[#2c4370] rounded-2xl text-white shadow-lg relative overflow-hidden mt-2">
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-base">info</span>
                        <p class="text-[10px] uppercase tracking-widest font-bold">Información Importante</p>
                    </div>
                    <p class="text-[11px] leading-snug opacity-90">Asegúrate de completar el registro al finalizar cada
                        sesión para certificar tu asistencia.</p>
                </div>
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full"></div>
            </div>
        </main>
        <footer
            class="mt-auto px-6 py-4 bg-white/95 dark:bg-slate-900/95 backdrop-blur-lg border-t border-slate-100 dark:border-slate-800 shrink-0">
            <nav class="flex justify-around items-center mb-1">
                <a class="flex flex-col items-center text-[#2c4370] dark:text-white" href="#">
                    <span class="material-symbols-outlined text-xl">home</span>
                    <span class="text-[9px] mt-0.5 font-bold">Inicio</span>
                </a>
                <a class="flex flex-col items-center text-slate-400 dark:text-slate-500" href="#">
                    <span class="material-symbols-outlined text-xl">history_edu</span>
                    <span class="text-[9px] mt-0.5 font-medium">Cursos</span>
                </a>
                <a class="flex flex-col items-center text-slate-400 dark:text-slate-500" href="#">
                    <span class="material-symbols-outlined text-xl">notifications</span>
                    <span class="text-[9px] mt-0.5 font-medium">Avisos</span>
                </a>
                <a class="flex flex-col items-center text-slate-400 dark:text-slate-500" href="#">
                    <span class="material-symbols-outlined text-xl">account_circle</span>
                    <span class="text-[9px] mt-0.5 font-medium">Perfil</span>
                </a>
            </nav>
            <div class="w-32 h-1.5 bg-slate-200 dark:bg-slate-800 mx-auto mt-4 rounded-full"></div>
        </footer>
    </div>
    <button
        class="fixed bottom-6 right-6 p-3 rounded-full bg-white/10 backdrop-blur text-white border border-white/20 z-50"
        onclick="document.documentElement.classList.toggle('dark')">
        <span class="material-symbols-outlined block dark:hidden text-lg">dark_mode</span>
        <span class="material-symbols-outlined hidden dark:block text-lg">light_mode</span>
    </button>

</body>

</html>
