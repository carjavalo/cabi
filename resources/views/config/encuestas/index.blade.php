@extends('layouts.app')

@section('title','Gestión Encuestas')
@section('header','Gestión Encuestas')

@section('content')
<style>
    .encuestas-header {
        background: linear-gradient(135deg, #2e3a75 0%, #3d4d8f 100%);
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(46, 58, 117, 0.15);
    }
    .encuestas-header h5 {
        color: white;
        font-weight: 600;
        font-size: 1.5rem;
        margin: 0;
    }
    .encuestas-header p {
        color: rgba(255, 255, 255, 0.85);
        margin: 0.5rem 0 0 0;
        font-size: 0.95rem;
    }
    .btn-nueva-encuesta {
        background: white;
        color: #2e3a75;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .btn-nueva-encuesta:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        color: #2e3a75;
    }
    .search-container {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    .search-input {
        border: 2px solid #e8eaf0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    .search-input:focus {
        border-color: #2e3a75;
        box-shadow: 0 0 0 3px rgba(46, 58, 117, 0.1);
    }
    .table-container {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    #tablaEncuestas thead {
        background: #f8f9fc;
        border-bottom: 2px solid #2e3a75;
    }
    #tablaEncuestas thead th {
        color: #2e3a75;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        border: none;
    }
    #tablaEncuestas tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f0f0f5;
    }
    #tablaEncuestas tbody tr:hover {
        background: #f8f9fc;
        transform: scale(1.01);
    }
    #tablaEncuestas tbody td {
        padding: 1rem;
        vertical-align: middle;
    }
    .btn-action {
        padding: 0.4rem 1rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
    }
    .btn-edit {
        background: #2e3a75;
        color: white;
    }
    .btn-edit:hover {
        background: #1f2850;
        transform: translateY(-1px);
    }
    .btn-delete {
        background: #dc3545;
        color: white;
    }
    .btn-delete:hover {
        background: #c82333;
        transform: translateY(-1px);
    }

    /* Estilos elegantes para checkboxes de respuestas correctas */
    .correct-answer-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        padding: 0.5rem;
    }
    
    .correct-answer-checkbox {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
        z-index: 2;
    }
    
    .correct-answer-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 2px solid #cbd5e0;
        background: white;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 1;
    }
    
    .correct-answer-icon i {
        font-size: 16px;
        color: #cbd5e0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .correct-answer-checkbox:checked ~ .correct-answer-icon {
        background: linear-gradient(135deg, #2e3a75 0%, #3d4d8f 100%);
        border-color: #2e3a75;
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(46, 58, 117, 0.3);
    }
    
    .correct-answer-checkbox:checked ~ .correct-answer-icon i {
        color: white;
        transform: scale(1.2);
    }
    
    .correct-answer-checkbox:hover ~ .correct-answer-icon {
        border-color: #2e3a75;
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(46, 58, 117, 0.15);
    }
    
    .correct-answer-checkbox:not(:checked):hover ~ .correct-answer-icon i {
        color: #2e3a75;
    }

    /* Animación de pulso para checkboxes marcados */
    @keyframes pulse-correct {
        0%, 100% {
            box-shadow: 0 4px 12px rgba(46, 58, 117, 0.3);
        }
        50% {
            box-shadow: 0 4px 20px rgba(46, 58, 117, 0.5);
        }
    }
    
    .correct-answer-checkbox:checked ~ .correct-answer-icon {
        animation: pulse-correct 2s infinite;
    }

    /* Tooltip para checkboxes */
    .correct-answer-wrapper::before {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(-8px);
        background: #2e3a75;
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.75rem;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s ease;
        z-index: 10;
    }
    
    .correct-answer-wrapper::after {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(-2px);
        border: 5px solid transparent;
        border-top-color: #2e3a75;
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s ease;
    }
    
    .correct-answer-wrapper:hover::before,
    .correct-answer-wrapper:hover::after {
        opacity: 1;
        transform: translateX(-50%) translateY(-4px);
    }

    /* Estilo para el alert de información */
    .info-alert-elegant {
        background: linear-gradient(135deg, #e8f4f8 0%, #d4e9f2 100%);
        border: 2px solid #bee5eb;
        border-radius: 10px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 2px 8px rgba(12, 84, 96, 0.1);
        transition: all 0.3s ease;
    }

    .info-alert-elegant:hover {
        box-shadow: 0 4px 12px rgba(12, 84, 96, 0.15);
        transform: translateY(-2px);
    }

    .info-alert-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: white;
        border-radius: 50%;
        color: #0c5460;
        font-size: 18px;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(12, 84, 96, 0.15);
    }

    .info-alert-content {
        flex: 1;
        color: #0c5460;
    }

    .info-alert-content strong {
        display: block;
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    .info-alert-content small {
        font-size: 0.85rem;
        line-height: 1.4;
    }

    /* Modales de confirmación personalizados */
    .custom-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .custom-modal-overlay.show {
        opacity: 1;
        pointer-events: all;
    }

    .custom-modal-box {
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(46, 58, 117, 0.3);
        max-width: 480px;
        width: 90%;
        transform: scale(0.9) translateY(20px);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .custom-modal-overlay.show .custom-modal-box {
        transform: scale(1) translateY(0);
    }

    .custom-modal-header {
        background: linear-gradient(135deg, #2e3a75 0%, #3d4d8f 100%);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .custom-modal-icon {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        flex-shrink: 0;
    }

    .custom-modal-title {
        color: white;
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }

    .custom-modal-body {
        padding: 2rem 1.5rem;
    }

    .custom-modal-message {
        color: #4a5568;
        font-size: 1rem;
        line-height: 1.6;
        margin: 0;
    }

    .custom-modal-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e8eaf0;
        border-radius: 8px;
        font-size: 1rem;
        margin-top: 1rem;
        transition: all 0.3s ease;
    }

    .custom-modal-input:focus {
        outline: none;
        border-color: #2e3a75;
        box-shadow: 0 0 0 3px rgba(46, 58, 117, 0.1);
    }

    .custom-modal-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fc;
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    .custom-modal-btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .custom-modal-btn-cancel {
        background: #e2e8f0;
        color: #4a5568;
    }

    .custom-modal-btn-cancel:hover {
        background: #cbd5e0;
        transform: translateY(-1px);
    }

    .custom-modal-btn-confirm {
        background: linear-gradient(135deg, #2e3a75 0%, #3d4d8f 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(46, 58, 117, 0.3);
    }

    .custom-modal-btn-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(46, 58, 117, 0.4);
    }

    .custom-modal-btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    .custom-modal-btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(220, 53, 69, 0.4);
    }

    .custom-modal-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Estilos para el checkbox de permitir repetir */
    #modalPermitirRepetir {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        width: 22px;
        height: 22px;
        border: 2px solid white;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.2);
        cursor: pointer;
        position: relative;
        transition: all 0.3s ease;
    }

    #modalPermitirRepetir:checked {
        background: white;
        border-color: white;
    }

    #modalPermitirRepetir:checked::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #2e3a75;
        font-size: 16px;
        font-weight: bold;
    }

    #modalPermitirRepetir:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    /* Estilos para botón permitir repetir en tabla de respuestas */
    .btn-permitir-repetir:hover {
        background: #218838 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }

    .btn-permitir-repetir:active {
        transform: translateY(0);
    }
</style>

<div class="encuestas-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>Gestión de Encuestas</h5>
            <p>Crea y administra encuestas para el Hospital Universitario del Valle</p>
        </div>
        <button id="btnCreateEncuesta" class="btn btn-nueva-encuesta" data-toggle="modal" data-target="#createEncuestaModal">
            <i class="fas fa-plus-circle mr-2"></i>Nueva Encuesta
        </button>
    </div>
</div>

<div class="search-container">
    <div class="row">
        <div class="col-md-6">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white border-right-0" style="border: 2px solid #e8eaf0; border-right: none; border-radius: 8px 0 0 8px;">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                </div>
                <input id="searchEncuesta" type="text" class="form-control search-input border-left-0" placeholder="Buscar por título o código..." style="border-left: none;">
            </div>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover" id="tablaEncuestas">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Código</th>
                    <th>Fecha de Creación</th>
                    <th class="text-center">Link/QR</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para ver respuestas de encuesta -->
<div class="modal fade" id="respuestasModal" tabindex="-1" role="dialog" aria-labelledby="respuestasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width:95%;">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(46, 58, 117, 0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #2e3a75 0%, #3d4d8f 100%); border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" id="respuestasModalLabel" style="color: white; font-weight: 600;">
                    <i class="fas fa-chart-bar mr-2"></i>Respuestas de la Encuesta
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 id="respuestasEncuestaTitulo" style="color: #2e3a75; font-weight: 600; margin: 0;"></h6>
                        <small class="text-muted" id="respuestasEncuestaInfo"></small>
                    </div>
                    <button id="exportExcelBtn" class="btn btn-success">
                        <i class="fas fa-file-excel mr-2"></i>Exportar a Excel
                    </button>
                </div>
                <div class="alert alert-info" style="background: #e8f4f8; border: 1px solid #bee5eb; border-radius: 8px;">
                    <i class="fas fa-info-circle mr-2"></i>
                    <small>La tabla muestra cada pregunta en una columna separada. Use el scroll horizontal para ver todas las columnas.</small>
                </div>
                <div class="table-responsive" style="max-height: 500px; overflow: auto;">
                    <table class="table table-bordered table-hover" id="tablaRespuestas" style="white-space: nowrap;">
                        <thead style="background: #f8f9fc; position: sticky; top: 0; z-index: 10;">
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="background: #f8f9fc;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar QR Code -->
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(46, 58, 117, 0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #2e3a75 0%, #3d4d8f 100%); border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" id="qrModalLabel" style="color: white; font-weight: 600;">
                    <i class="fas fa-qrcode mr-2"></i>Código QR de la Encuesta
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" style="padding: 2rem;">
                <h6 id="qrEncuestaTitulo" style="color: #2e3a75; font-weight: 600; margin-bottom: 1.5rem;"></h6>
                <div id="qrCodeContainer" style="display: inline-block; padding: 1rem; background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"></div>
                <div class="mt-3">
                    <div class="input-group">
                        <input type="text" id="encuestaLinkInput" class="form-control" readonly style="border-radius: 8px 0 0 8px; border: 2px solid #e8eaf0;">
                        <div class="input-group-append">
                            <button class="btn" id="copyLinkBtn" style="background: #2e3a75; color: white; border-radius: 0 8px 8px 0;">
                                <i class="fas fa-copy mr-1"></i>Copiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background: #f8f9fc;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" id="downloadQRBtn" class="btn" style="background: #2e3a75; color: white;">
                    <i class="fas fa-download mr-2"></i>Descargar QR
                </button>
            </div>
        </div>
    </div>
</div>

    @push('head')
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
                    tailwind.config = {
                            darkMode: "class",
                            theme: {
                                    extend: {
                                            colors: {
                                                    "primary": "#2e3b76",
                                                    "background-light": "#f6f6f8",
                                                    "background-dark": "#15161d",
                                            },
                                            fontFamily: {
                                                    "display": ["Manrope"]
                                            },
                                            borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                                    },
                            },
                    }
            </script>
    @endpush

    <!-- Modal: Creador de Encuestas (tailwind content embedded) -->
    <div class="modal fade" id="createEncuestaModal" tabindex="-1" role="dialog" aria-labelledby="createEncuestaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:1200px;">
            <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(46, 58, 117, 0.2);">
                <div class="modal-body p-0" style="max-height:85vh; overflow:auto;">

    <!-- Begin embedded survey builder -->
    <div class="flex h-full flex-col overflow-hidden">
    <!-- Survey meta (title / code) -->
    <div class="p-4 border-b" style="background: linear-gradient(135deg, #2e3a75 0%, #3d4d8f 100%);">
        <h5 class="mb-3" id="modalEncuestaTitle" style="color: white; font-weight: 600; font-size: 1.3rem;">
            <i class="fas fa-clipboard-list mr-2"></i><span id="modalTitleText">Nueva Encuesta</span>
        </h5>
        <div class="form-row">
            <div class="col-12 mb-3">
                <input id="modalEncuestaTitulo" placeholder="Título de la encuesta" class="form-control" style="border-radius: 8px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95);" />
            </div>
            <div class="col-12 mb-3">
                <input id="modalEncuestaCodigo" placeholder="Código (opcional)" class="form-control" style="border-radius: 8px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95);" />
            </div>
            <div class="col-12">
                <div style="background: rgba(255,255,255,0.15); padding: 1rem; border-radius: 8px; border: 2px solid rgba(255,255,255,0.3);">
                    <label style="display: flex; align-items: center; cursor: pointer; margin: 0; color: white; font-weight: 500;">
                        <input type="checkbox" id="modalPermitirRepetir" style="width: 22px; height: 22px; cursor: pointer; margin-right: 12px; accent-color: white;">
                        <span style="font-size: 1rem;">
                            <i class="fas fa-redo-alt mr-2"></i>Permitir que los usuarios respondan múltiples veces
                        </span>
                    </label>
                    <small style="color: rgba(255,255,255,0.85); display: block; margin-top: 0.5rem; margin-left: 34px;">
                        Si está marcado, los usuarios podrán responder esta encuesta más de una vez.
                    </small>
                </div>
            </div>
            <div class="col-md-6 mt-3">
                <label style="color: white; font-weight: 500; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">
                    <i class="fas fa-calendar-plus mr-2"></i>Fecha de Inicio
                </label>
                <input type="datetime-local" id="modalFechaInicio" class="form-control" style="border-radius: 8px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95);" />
                <small style="color: rgba(255,255,255,0.85); display: block; margin-top: 0.3rem;">
                    Fecha y hora en que la encuesta estará disponible
                </small>
            </div>
            <div class="col-md-6 mt-3">
                <label style="color: white; font-weight: 500; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">
                    <i class="fas fa-calendar-times mr-2"></i>Fecha de Terminación
                </label>
                <input type="datetime-local" id="modalFechaFin" class="form-control" style="border-radius: 8px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95);" />
                <small style="color: rgba(255,255,255,0.85); display: block; margin-top: 0.3rem;">
                    Fecha y hora en que la encuesta dejará de estar disponible
                </small>
            </div>
        </div>
    </div>
    <!-- Top Navigation Bar -->
    <header class="flex h-16 w-full items-center justify-between border-b border-slate-200 bg-white px-5 dark:border-slate-800 dark:bg-background-dark z-10">
    <div class="flex items-center gap-3">
    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary text-white">
    <span class="material-symbols-outlined">local_hospital</span>
    </div>
    <div class="flex flex-col">
    <h2 class="text-sm font-bold leading-tight tracking-tight text-primary">HUV Digital</h2>
    <p class="text-[10px] font-medium uppercase tracking-widest text-slate-500">Hospital Universitario del Valle</p>
    </div>
    </div>
    <nav class="hidden flex-1 justify-center md:flex">
    <div class="flex items-center gap-5">
    <a class="text-xs font-semibold text-primary border-b-2 border-primary pb-5 mt-5" href="#">Creador</a>
    <a class="text-xs font-medium text-slate-600 hover:text-primary transition-colors" href="#">Mis Encuestas</a>
    <a class="text-xs font-medium text-slate-600 hover:text-primary transition-colors" href="#">Analíticas</a>
    <a class="text-xs font-medium text-slate-600 hover:text-primary transition-colors" href="#">Plantillas</a>
    </div>
    </nav>
    <div class="flex items-center gap-2">
    <button class="flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
    <span>Guardar Borrador</span>
    </button>
    <button class="flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-primary/90 transition-all">
    <span class="material-symbols-outlined text-[16px]">publish</span>
    <span>Publicar</span>
    </button>
    <div class="ml-2 h-10 w-10 rounded-full bg-slate-200 border-2 border-white shadow-sm" data-alt="User profile avatar placeholder">
    <img alt="Avatar" class="h-full w-full rounded-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA-IUz2nhZgsp_GxW4RCQlEhQPxeea28RlDi-qW1MX1TIFnanPq-xBvyhe20Mdyg0eX1-rkyc3gbP4jtVQ8jJV42hb18ehqG5rbwdRYaHx4RKYuTHWmSpDR4G9YnrnoLh3offWtkrZmQvftc6nwWLZbpnFiXsRTnyYgAq7XflkDU0fgqHgaDS1RS1ulqCTbWqZ2gg3fIhlWWxEC_cmgBWBzaxLPiP6Swdq95r5aVV7nuiXjzvA2NgRqbmZNDSreZ-au3RaW-x63g6Be"/>
    </div>
    </div>
    </header>
    <div class="flex flex-1 overflow-hidden">
    <!-- Sidebar Navigation - Compacto -->
    <aside class="w-56 flex-col border-r border-slate-200 bg-white dark:border-slate-800 dark:bg-background-dark hidden lg:flex">
    <div class="flex flex-col gap-1 p-3">
    <h3 class="px-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Estructura</h3>
    <div class="flex items-center gap-2 rounded-lg bg-primary/10 px-2 py-2 text-primary">
    <span class="material-symbols-outlined text-[18px]">account_tree</span>
    <span class="text-xs font-semibold">Esquema</span>
    </div>
    <div class="flex items-center gap-2 px-2 py-2 text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer transition-colors">
    <span class="material-symbols-outlined text-[18px]">visibility</span>
    <span class="text-xs font-medium">Vista Previa</span>
    </div>
    <div class="flex items-center gap-2 px-2 py-2 text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer transition-colors">
    <span class="material-symbols-outlined text-[18px]">settings</span>
    <span class="text-xs font-medium">Configuración</span>
    </div>
    <div class="flex items-center gap-2 px-2 py-2 text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer transition-colors">
    <span class="material-symbols-outlined text-[18px]">history</span>
    <span class="text-xs font-medium">Historial</span>
    </div>
    </div>
    <div class="mt-auto p-3">
    <div class="rounded-xl bg-slate-50 p-3 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800">
    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter mb-2">Progreso</p>
    <div class="mb-1 flex items-center justify-between text-[10px] font-bold">
    <span>4 Preguntas</span>
    <span class="text-primary">60%</span>
    </div>
    <div class="h-1.5 w-full rounded-full bg-slate-200 dark:bg-slate-700">
    <div class="h-full w-[60%] rounded-full bg-primary"></div>
    </div>
    </div>
    </div>
    </aside>
    <!-- Main Workspace -->
    <main class="flex-1 overflow-y-auto bg-background-light p-5 dark:bg-background-dark">
    <div class="mx-auto max-w-full">
    <div class="mb-4 flex items-center justify-between">
    <div>
    <h1 class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">Creador de Encuestas</h1>
    <p class="text-xs text-slate-500 dark:text-slate-400">Hospital Universitario del Valle</p>
    </div>
    <button class="flex items-center gap-2 rounded-lg bg-primary px-5 py-2 text-sm font-bold text-white shadow-lg hover:bg-primary/90 transition-all">
    <span class="material-symbols-outlined text-[18px]">add_circle</span>
    <span>Añadir Pregunta</span>
    </button>
    </div>
    <!-- Survey Builder Container (dynamic) -->
    <div class="p-5">
        <div class="mb-4 d-flex align-items-center justify-content-between" style="background: #f8f9fc; padding: 1.25rem; border-radius: 10px; border-left: 4px solid #2e3a75;">
            <div>
                <h3 class="mb-0 font-weight-bold" style="color: #2e3a75; font-size: 1.1rem;">
                    <i class="fas fa-tools mr-2"></i>Constructor de Encuesta
                </h3>
                <small class="text-muted" style="font-size: 0.85rem;">Añade preguntas, selecciona tipo y opciones</small>
            </div>
            <div>
                <button id="addQuestionBtn" class="btn btn-sm" style="background: #2e3a75; color: white; padding: 0.55rem 1.1rem; border-radius: 8px; font-weight: 600; border: none; font-size: 0.9rem;">
                    <i class="fas fa-plus mr-1"></i>Añadir Pregunta
                </button>
            </div>
        </div>
        <div id="questionsContainer" class="space-y-4"></div>
    </div>
    </main>
    <!-- Quick Inspector / Properties -->
    <aside class="w-64 border-l border-slate-200 bg-white p-4 hidden xl:block dark:border-slate-800 dark:bg-background-dark">
    <h3 class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Propiedades</h3>
    <div class="space-y-4">
    <div>
    <label class="mb-1.5 block text-[11px] font-medium text-slate-600">Tipo de Pregunta</label>
    <select class="w-full rounded-lg border-slate-200 bg-slate-50 py-2 text-xs focus:ring-primary dark:border-slate-700 dark:bg-slate-800">
    <option>Selección Única</option>
    <option selected="">Selección Múltiple</option>
    <option>Texto Corto</option>
    <option>Texto Largo</option>
    <option>Escala Lineal</option>
    </select>
    </div>
    <div class="space-y-2">
    <label class="flex items-center gap-2 cursor-pointer">
    <input checked="" class="rounded text-primary focus:ring-primary" type="checkbox"/>
    <span class="text-xs text-slate-700 font-medium">Obligatoria</span>
    </label>
    <label class="flex items-center gap-2 cursor-pointer">
    <input class="rounded text-primary focus:ring-primary" type="checkbox"/>
    <span class="text-xs text-slate-700 font-medium">Aleatorizar</span>
    </label>
    <label class="flex items-center gap-2 cursor-pointer">
    <input class="rounded text-primary focus:ring-primary" type="checkbox"/>
    <span class="text-xs text-slate-700 font-medium">Permitir "Otro"</span>
    </label>
    </div>
    <hr class="border-slate-100 dark:border-slate-800"/>
    <div>
    <h4 class="mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Estilo Visual</h4>
    <div class="grid grid-cols-4 gap-2">
    <div class="h-7 rounded-md bg-primary ring-2 ring-primary ring-offset-1"></div>
    <div class="h-7 rounded-md bg-emerald-500"></div>
    <div class="h-7 rounded-md bg-sky-500"></div>
    <div class="h-7 rounded-md bg-slate-800"></div>
    </div>
    </div>
    <div class="rounded-lg bg-primary/5 p-3 border border-primary/10">
    <div class="flex items-center gap-1.5 text-primary mb-1.5">
    <span class="material-symbols-outlined text-[16px]">info</span>
    <span class="text-[10px] font-bold">Consejo HUV</span>
    </div>
    <p class="text-[10px] leading-relaxed text-slate-600">
                                                            Use preguntas de selección múltiple para métricas rápidas y texto libre para comentarios detallados.
                                                    </p>
    </div>
    </div>
    </aside>
    </div>
    </div>
    <!-- End embedded survey builder -->

                </div>
                <div class="modal-footer" style="background: #f8f9fc; border-top: 2px solid #e8eaf0; padding: 1.25rem;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="padding: 0.6rem 1.5rem; border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-times mr-2"></i>Cerrar
                    </button>
                    <button type="button" id="saveEncuestaModalBtn" class="btn" style="background: #2e3a75; color: white; padding: 0.6rem 1.5rem; border-radius: 8px; font-weight: 600; border: none;">
                        <i class="fas fa-save mr-2"></i>Guardar Encuesta
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
// Sistema de modales personalizados
const CustomModal = {
    alert: function(message, title = 'Información') {
        return new Promise((resolve) => {
            const overlay = this.createOverlay();
            const modal = this.createModal({
                title: title,
                message: message,
                icon: 'fas fa-info-circle',
                buttons: [
                    { text: 'Aceptar', class: 'custom-modal-btn-confirm', icon: 'fas fa-check', callback: () => { this.close(overlay); resolve(true); } }
                ]
            });
            overlay.appendChild(modal);
            document.body.appendChild(overlay);
            setTimeout(() => overlay.classList.add('show'), 10);
        });
    },

    confirm: function(message, title = 'Confirmar') {
        return new Promise((resolve) => {
            const overlay = this.createOverlay();
            const modal = this.createModal({
                title: title,
                message: message,
                icon: 'fas fa-question-circle',
                buttons: [
                    { text: 'Cancelar', class: 'custom-modal-btn-cancel', icon: 'fas fa-times', callback: () => { this.close(overlay); resolve(false); } },
                    { text: 'Confirmar', class: 'custom-modal-btn-danger', icon: 'fas fa-check', callback: () => { this.close(overlay); resolve(true); } }
                ]
            });
            overlay.appendChild(modal);
            document.body.appendChild(overlay);
            setTimeout(() => overlay.classList.add('show'), 10);
        });
    },

    prompt: function(message, title = 'Ingrese información', defaultValue = '') {
        return new Promise((resolve) => {
            const overlay = this.createOverlay();
            const modal = this.createModal({
                title: title,
                message: message,
                icon: 'fas fa-edit',
                input: true,
                defaultValue: defaultValue,
                buttons: [
                    { text: 'Cancelar', class: 'custom-modal-btn-cancel', icon: 'fas fa-times', callback: () => { this.close(overlay); resolve(null); } },
                    { text: 'Aceptar', class: 'custom-modal-btn-confirm', icon: 'fas fa-check', callback: () => { 
                        const input = modal.querySelector('.custom-modal-input');
                        const value = input ? input.value : null;
                        this.close(overlay); 
                        resolve(value); 
                    } }
                ]
            });
            overlay.appendChild(modal);
            document.body.appendChild(overlay);
            setTimeout(() => {
                overlay.classList.add('show');
                const input = modal.querySelector('.custom-modal-input');
                if(input) input.focus();
            }, 10);
        });
    },

    createOverlay: function() {
        const overlay = document.createElement('div');
        overlay.className = 'custom-modal-overlay';
        overlay.addEventListener('click', (e) => {
            if(e.target === overlay) {
                // No cerrar al hacer clic fuera
            }
        });
        return overlay;
    },

    createModal: function(config) {
        const modal = document.createElement('div');
        modal.className = 'custom-modal-box';
        
        let html = `
            <div class="custom-modal-header">
                <div class="custom-modal-icon">
                    <i class="${config.icon}"></i>
                </div>
                <h3 class="custom-modal-title">${config.title}</h3>
            </div>
            <div class="custom-modal-body">
                <p class="custom-modal-message">${config.message}</p>
                ${config.input ? `<input type="text" class="custom-modal-input" value="${config.defaultValue || ''}" placeholder="Ingrese aquí...">` : ''}
            </div>
            <div class="custom-modal-footer">
        `;
        
        config.buttons.forEach(btn => {
            html += `<button class="custom-modal-btn ${btn.class}"><i class="${btn.icon}"></i> ${btn.text}</button>`;
        });
        
        html += `</div>`;
        modal.innerHTML = html;
        
        // Agregar eventos a los botones
        const buttons = modal.querySelectorAll('.custom-modal-btn');
        buttons.forEach((btn, index) => {
            btn.addEventListener('click', config.buttons[index].callback);
        });
        
        return modal;
    },

    close: function(overlay) {
        overlay.classList.remove('show');
        setTimeout(() => {
            if(overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
        }, 300);
    }
};

document.addEventListener('DOMContentLoaded', function(){
    const apiDataUrl = "{{ route('config.encuestas.data') }}";
    const storeUrl = "{{ route('config.encuestas.store') }}";
    const showUrl = "{{ url('config/encuestas') }}"; // Base URL para show
    const tablaBody = document.querySelector('#tablaEncuestas tbody');
    const search = document.getElementById('searchEncuesta');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    async function loadData(){
        try{
            const res = await fetch(apiDataUrl);
            const items = await res.json();
            renderTable(items);
        }catch(e){ console.error(e); }
    }

    function renderTable(items){
        tablaBody.innerHTML = '';
        items.forEach(it=>{
            const encuestaUrl = `${window.location.origin}/encuestas/responder/${it.id}`;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><span style="color: #2e3a75; font-weight: 600;">#${it.id ?? ''}</span></td>
                <td><strong>${it.titulo ?? ''}</strong></td>
                <td><span class="badge badge-secondary" style="background: #e8eaf0; color: #2e3a75; padding: 0.4rem 0.8rem; border-radius: 6px;">${it.codigo ?? 'N/A'}</span></td>
                <td><small class="text-muted"><i class="far fa-calendar-alt mr-1"></i>${it.created_at ?? ''}</small></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-qr" data-id="${it.id}" data-titulo="${escapeHtml(it.titulo)}" data-url="${encuestaUrl}" style="background: #2e3a75; color: white; border-radius: 6px; padding: 0.4rem 0.8rem; margin-right: 0.25rem;" title="Ver QR y Link">
                        <i class="fas fa-qrcode"></i>
                    </button>
                    <button class="btn btn-sm btn-copy-link" data-url="${encuestaUrl}" style="background: #17a2b8; color: white; border-radius: 6px; padding: 0.4rem 0.8rem;" title="Copiar Link">
                        <i class="fas fa-link"></i>
                    </button>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-action btn-edit" data-id="${it.id}">
                        <i class="fas fa-edit mr-1"></i>Editar
                    </button>
                    <button class="btn btn-sm btn-action btn-delete ml-1" data-id="${it.id}">
                        <i class="fas fa-trash mr-1"></i>Eliminar
                    </button>
                    <button class="btn btn-sm btn-respuestas ml-1" data-id="${it.id}" data-titulo="${escapeHtml(it.titulo)}" style="background: #28a745; color: white; border-radius: 6px; padding: 0.4rem 0.8rem;">
                        <i class="fas fa-chart-line mr-1"></i>Ver Respuestas
                    </button>
                </td>
            `;
            tablaBody.appendChild(tr);
        });
        if(window.applyPagination) window.applyPagination('#tablaEncuestas', 12);
    }

    search.addEventListener('input', function(){
        const q = this.value.toLowerCase();
        Array.from(tablaBody.querySelectorAll('tr')).forEach(r=>{
            const txt = r.textContent.toLowerCase();
            r.style.display = txt.includes(q) ? '' : 'none';
        });
    });

    // Builder state
    let questions = [];
    let editingId = null;

    const modalEl = document.getElementById('createEncuestaModal');
    const questionsContainer = document.getElementById('questionsContainer');

    function makeQuestion(type = 'multiple', title = '', options = [], correctAnswers = []){
        return { type, title, options, correctAnswers: correctAnswers || [] };
    }

    function escapeHtml(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

    function renderQuestions(){
        questionsContainer.innerHTML = '';
        questions.forEach((q, idx) => {
            const div = document.createElement('div');
            div.className = 'card p-3 mb-3';
            div.style.cssText = 'border: 2px solid #e8eaf0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: all 0.3s ease;';
            
            // Determinar si mostrar checkboxes de respuestas correctas
            const showCorrectAnswers = q.type === 'multiple' || q.type === 'single';
            
            div.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3" style="padding-bottom: 0.75rem; border-bottom: 2px solid #f0f0f5;">
                    <strong style="color: #2e3a75; font-size: 1.1rem;">
                        <i class="fas fa-question-circle mr-2"></i>Pregunta ${idx+1}
                    </strong>
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-control form-control-sm mr-2 q-type" data-idx="${idx}" style="border-radius: 6px; border: 2px solid #e8eaf0; min-width: 180px;">
                            <option value="multiple" ${q.type==='multiple' ? 'selected' : ''}>Selección Múltiple</option>
                            <option value="single" ${q.type==='single' ? 'selected' : ''}>Selección Única</option>
                            <option value="text" ${q.type==='text' ? 'selected' : ''}>Texto</option>
                        </select>
                        <button class="btn btn-sm btn-danger btn-remove-q" data-idx="${idx}" style="border-radius: 6px; padding: 0.4rem 0.8rem;">
                            <i class="fas fa-trash mr-1"></i>Eliminar
                        </button>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label style="color: #2e3a75; font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem;">Título de la pregunta</label>
                    <input type="text" class="form-control q-title" data-idx="${idx}" placeholder="Escribe aquí la pregunta..." value="${escapeHtml(q.title)}" style="border-radius: 8px; border: 2px solid #e8eaf0; padding: 0.75rem;" />
                </div>
                ${showCorrectAnswers ? `
                <div class="info-alert-elegant mb-3">
                    <div class="info-alert-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="info-alert-content">
                        <strong>Marque las opciones correctas/ideales</strong>
                        <small>Haga clic en el ícono de verificación para seleccionar una o más respuestas correctas.</small>
                    </div>
                </div>
                ` : ''}
                <div class="options-list">
                    ${q.options.map((opt,i)=> `
                        <div class="input-group mb-2 option-row">
                            ${showCorrectAnswers ? `
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="background: #f8f9fc; border: 2px solid #e8eaf0; border-right: none; border-radius: 8px 0 0 8px; padding: 0;">
                                    <div class="correct-answer-wrapper" data-tooltip="${(q.correctAnswers || []).includes(i) ? 'Respuesta correcta' : 'Marcar como correcta'}">
                                        <input type="checkbox" class="correct-answer-checkbox" data-q="${idx}" data-i="${i}" 
                                            ${(q.correctAnswers || []).includes(i) ? 'checked' : ''}>
                                        <div class="correct-answer-icon">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                </span>
                            </div>
                            ` : ''}
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="background: #f8f9fc; border: 2px solid #e8eaf0; ${showCorrectAnswers ? 'border-left: none;' : ''} border-right: none; ${showCorrectAnswers ? '' : 'border-radius: 8px 0 0 8px;'}">
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </span>
                            </div>
                            <input class="form-control option-input" data-q="${idx}" data-i="${i}" value="${escapeHtml(opt)}" placeholder="Opción ${i+1}" style="border: 2px solid #e8eaf0; border-left: none; border-right: none;"/>
                            <div class="input-group-append">
                                <button class="btn btn-outline-danger btn-sm btn-remove-opt" data-q="${idx}" data-i="${i}" style="border: 2px solid #e8eaf0; border-left: none; border-radius: 0 8px 8px 0;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
                <div class="mt-2">
                    <button class="btn btn-sm btn-add-opt" data-idx="${idx}" style="background: #f8f9fc; color: #2e3a75; border: 2px dashed #2e3a75; border-radius: 8px; padding: 0.5rem 1rem; font-weight: 600;">
                        <i class="fas fa-plus-circle mr-2"></i>Añadir Opción
                    </button>
                </div>
            `;
            questionsContainer.appendChild(div);
        });
    }

    // Add question button
    document.getElementById('addQuestionBtn').addEventListener('click', function(){
        questions.push(makeQuestion('multiple','', [''], []));
        renderQuestions();
    });

    // Delegate events inside questionsContainer
    questionsContainer.addEventListener('click', function(e){
        const addOpt = e.target.closest('.btn-add-opt');
        const removeQ = e.target.closest('.btn-remove-q');
        const removeOpt = e.target.closest('.btn-remove-opt');
        if(addOpt){
            const idx = parseInt(addOpt.getAttribute('data-idx'));
            questions[idx].options.push(''); renderQuestions();
        }
        if(removeQ){
            const idx = parseInt(removeQ.getAttribute('data-idx'));
            CustomModal.confirm('¿Está seguro de eliminar esta pregunta?', 'Confirmar eliminación').then(confirmed => {
                if(confirmed){ questions.splice(idx,1); renderQuestions(); }
            });
        }
        if(removeOpt){
            const q = parseInt(removeOpt.getAttribute('data-q'));
            const i = parseInt(removeOpt.getAttribute('data-i'));
            questions[q].options.splice(i,1);
            // Actualizar índices de respuestas correctas
            if(questions[q].correctAnswers){
                questions[q].correctAnswers = questions[q].correctAnswers
                    .filter(idx => idx !== i)
                    .map(idx => idx > i ? idx - 1 : idx);
            }
            renderQuestions();
        }
    });

    // Input/change handlers
    questionsContainer.addEventListener('input', function(e){
        const t = e.target;
        if(t.classList.contains('q-title')){
            const idx = parseInt(t.getAttribute('data-idx'));
            questions[idx].title = t.value;
        }
        if(t.classList.contains('option-input')){
            const q = parseInt(t.getAttribute('data-q'));
            const i = parseInt(t.getAttribute('data-i'));
            questions[q].options[i] = t.value;
        }
    });

    questionsContainer.addEventListener('change', function(e){
        const t = e.target;
        if(t.classList.contains('q-type')){
            const idx = parseInt(t.getAttribute('data-idx'));
            questions[idx].type = t.value;
            if(t.value === 'text') {
                questions[idx].options = [];
                questions[idx].correctAnswers = [];
            }
            if(questions[idx].options.length === 0 && t.value !== 'text') questions[idx].options = [''];
            renderQuestions();
        }
        if(t.classList.contains('correct-answer-checkbox')){
            const q = parseInt(t.getAttribute('data-q'));
            const i = parseInt(t.getAttribute('data-i'));
            if(!questions[q].correctAnswers) questions[q].correctAnswers = [];
            
            if(t.checked){
                if(!questions[q].correctAnswers.includes(i)){
                    questions[q].correctAnswers.push(i);
                }
            } else {
                questions[q].correctAnswers = questions[q].correctAnswers.filter(idx => idx !== i);
            }
            
            // Actualizar tooltip
            const wrapper = t.parentElement.querySelector('.correct-answer-wrapper');
            if(wrapper){
                wrapper.setAttribute('data-tooltip', t.checked ? 'Respuesta correcta' : 'Marcar como correcta');
            }
        }
    });

    // delegate edit/delete in table
    tablaBody.addEventListener('click', async function(e){
        const edit = e.target.closest('.btn-edit');
        const del = e.target.closest('.btn-delete');
        const qr = e.target.closest('.btn-qr');
        const copyLink = e.target.closest('.btn-copy-link');
        const respuestas = e.target.closest('.btn-respuestas');
        
        if(qr){
            const id = qr.getAttribute('data-id');
            const titulo = qr.getAttribute('data-titulo');
            const url = qr.getAttribute('data-url');
            showQRModal(id, titulo, url);
        }
        
        if(copyLink){
            const url = copyLink.getAttribute('data-url');
            try {
                await navigator.clipboard.writeText(url);
                copyLink.innerHTML = '<i class="fas fa-check"></i>';
                setTimeout(() => {
                    copyLink.innerHTML = '<i class="fas fa-link"></i>';
                }, 2000);
                CustomModal.alert('Link copiado al portapapeles', 'Éxito');
            } catch (err) {
                CustomModal.alert('No se pudo copiar el link', 'Error');
            }
        }
        
        if(respuestas){
            const id = respuestas.getAttribute('data-id');
            const titulo = respuestas.getAttribute('data-titulo');
            showRespuestasModal(id, titulo);
        }
        
        if(edit){
            const id = edit.getAttribute('data-id');
            console.log('Editando encuesta ID:', id);
            console.log('Show URL:', `${showUrl}/${id}/edit-data`);
            
            try{
                const res = await fetch(`${showUrl}/${id}/edit-data`);
                console.log('Response status:', res.status);
                
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                
                const item = await res.json();
                console.log('Item cargado:', item);
                
                editingId = item.id;
                document.getElementById('modalEncuestaTitulo').value = item.titulo || '';
                document.getElementById('modalEncuestaCodigo').value = item.codigo || '';
                document.getElementById('modalPermitirRepetir').checked = item.permitir_repetir == 1;
                
                // Formatear fechas para datetime-local
                if(item.fecha_inicio) {
                    const fechaInicio = new Date(item.fecha_inicio);
                    document.getElementById('modalFechaInicio').value = fechaInicio.toISOString().slice(0, 16);
                } else {
                    document.getElementById('modalFechaInicio').value = '';
                }
                if(item.fecha_fin) {
                    const fechaFin = new Date(item.fecha_fin);
                    document.getElementById('modalFechaFin').value = fechaFin.toISOString().slice(0, 16);
                } else {
                    document.getElementById('modalFechaFin').value = '';
                }
                
                document.getElementById('modalTitleText').textContent = 'Editar Encuesta';
                questions = [];
                
                try{
                    const str = item.estructura || '{}';
                    const obj = JSON.parse(str);
                    console.log('Estructura parseada:', obj);
                    
                    if(Array.isArray(obj.questions)){
                        obj.questions.forEach(q => {
                            questions.push(makeQuestion(
                                q.type || (q.options && q.options.length ? 'multiple':'text'), 
                                q.title || '', 
                                q.options || [],
                                q.correctAnswers || []
                            ));
                        });
                    }
                }catch(err){ 
                    console.error('Error parseando estructura:', err);
                    questions = []; 
                }
                
                console.log('Preguntas cargadas:', questions);
                renderQuestions();
                
                // Asegurar que el modal se muestre
                const modalElement = document.getElementById('createEncuestaModal');
                if (modalElement) {
                    $(modalElement).modal('show');
                    console.log('Modal abierto');
                } else {
                    console.error('Modal element not found');
                }
            }catch(e){ 
                console.error('Error completo:', e); 
                await CustomModal.alert('Error al cargar la encuesta: ' + e.message + '. Por favor, intente nuevamente.', 'Error'); 
            }
        }
        if(del){
            const id = del.getAttribute('data-id');
            const confirmed = await CustomModal.confirm('¿Está seguro de eliminar esta encuesta? Esta acción no se puede deshacer.', 'Confirmar eliminación');
            if(!confirmed) return;
            try{
                const res = await fetch(`${storeUrl}/${id}`, { method: 'POST', headers: {'X-CSRF-TOKEN': token, 'Content-Type':'application/json'}, body: JSON.stringify({'_method':'DELETE'}) });
                const json = await res.json();
                if(json.success) {
                    loadData();
                    CustomModal.alert('La encuesta ha sido eliminada correctamente.', 'Éxito');
                } else {
                    CustomModal.alert('No se pudo eliminar la encuesta. Por favor, intente nuevamente.', 'Error');
                }
            }catch(e){ console.error(e); CustomModal.alert('Error al eliminar la encuesta.', 'Error'); }
        }
    });

    // Función para mostrar modal de QR
    function showQRModal(id, titulo, url){
        document.getElementById('qrEncuestaTitulo').textContent = titulo;
        document.getElementById('encuestaLinkInput').value = url;
        
        const qrContainer = document.getElementById('qrCodeContainer');
        qrContainer.innerHTML = '';
        
        new QRCode(qrContainer, {
            text: url,
            width: 256,
            height: 256,
            colorDark: "#2e3a75",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        
        $('#qrModal').modal('show');
    }

    // Copiar link desde modal QR
    document.getElementById('copyLinkBtn').addEventListener('click', async function(){
        const input = document.getElementById('encuestaLinkInput');
        input.select();
        input.setSelectionRange(0, 99999); // Para móviles
        
        try {
            await navigator.clipboard.writeText(input.value);
            const originalHTML = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check mr-1"></i>Copiado';
            this.style.background = '#28a745';
            setTimeout(() => {
                this.innerHTML = originalHTML;
                this.style.background = '#2e3a75';
            }, 2000);
        } catch (err) {
            // Fallback para navegadores que no soportan clipboard API
            try {
                document.execCommand('copy');
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check mr-1"></i>Copiado';
                this.style.background = '#28a745';
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                    this.style.background = '#2e3a75';
                }, 2000);
            } catch (err2) {
                CustomModal.alert('No se pudo copiar el link. Por favor, cópielo manualmente.', 'Error');
            }
        }
    });

    // Descargar QR
    document.getElementById('downloadQRBtn').addEventListener('click', function(){
        const canvas = document.querySelector('#qrCodeContainer canvas');
        if(canvas){
            const link = document.createElement('a');
            link.download = 'encuesta-qr.png';
            link.href = canvas.toDataURL();
            link.click();
        }
    });

    // Función para mostrar modal de respuestas
    async function showRespuestasModal(encuestaId, titulo){
        document.getElementById('respuestasEncuestaTitulo').textContent = titulo;
        document.getElementById('respuestasEncuestaInfo').textContent = 'Cargando respuestas...';
        
        $('#respuestasModal').modal('show');
        
        try{
            // Cargar estructura de la encuesta
            const encuestaRes = await fetch(`${showUrl}/${encuestaId}/edit-data`);
            const encuestaData = await encuestaRes.json();
            const estructura = JSON.parse(encuestaData.estructura || '{}');
            const preguntas = estructura.questions || [];
            
            // Cargar respuestas
            const res = await fetch(`${showUrl}/${encuestaId}/respuestas`);
            const data = await res.json();
            
            renderRespuestas(data.respuestas || [], titulo, encuestaId, preguntas);
            document.getElementById('respuestasEncuestaInfo').textContent = `Total de respuestas: ${data.respuestas?.length || 0}`;
        }catch(e){
            console.error(e);
            document.getElementById('respuestasEncuestaInfo').textContent = 'Error al cargar respuestas';
            renderRespuestas([], titulo, encuestaId, []);
        }
    }

    // Renderizar tabla de respuestas con columnas dinámicas
    function renderRespuestas(respuestas, tituloEncuesta, encuestaId, preguntas){
        const table = document.getElementById('tablaRespuestas');
        const thead = table.querySelector('thead');
        const tbody = table.querySelector('tbody');
        
        // Limpiar tabla
        thead.innerHTML = '';
        tbody.innerHTML = '';
        
        if(respuestas.length === 0){
            tbody.innerHTML = '<tr><td colspan="100" class="text-center text-muted">No hay respuestas registradas para esta encuesta</td></tr>';
            return;
        }
        
        // Crear encabezados dinámicos
        const headerRow = document.createElement('tr');
        headerRow.innerHTML = `
            <th style="min-width: 150px;">Usuario</th>
            <th style="min-width: 200px;">Email</th>
            <th style="min-width: 150px;">Fecha</th>
        `;
        
        // Agregar columna por cada pregunta
        preguntas.forEach((pregunta, idx) => {
            const th = document.createElement('th');
            th.style.minWidth = '200px';
            th.innerHTML = `<div style="max-width: 250px; white-space: normal;">${pregunta.title || `Pregunta ${idx + 1}`}</div>`;
            headerRow.appendChild(th);
        });
        
        // Agregar columna de acciones
        const thAcciones = document.createElement('th');
        thAcciones.style.minWidth = '150px';
        thAcciones.className = 'text-center';
        thAcciones.innerHTML = 'Acciones';
        headerRow.appendChild(thAcciones);
        
        thead.appendChild(headerRow);
        
        // Crear filas de datos
        respuestas.forEach(resp => {
            const tr = document.createElement('tr');
            
            // Columnas fijas
            tr.innerHTML = `
                <td>${resp.usuario_nombre || 'Anónimo'}</td>
                <td>${resp.usuario_email || 'N/A'}</td>
                <td><small>${resp.fecha || ''}</small></td>
            `;
            
            // Agregar respuesta de cada pregunta
            preguntas.forEach(pregunta => {
                const td = document.createElement('td');
                const respuesta = resp.respuestas[pregunta.title];
                
                if (Array.isArray(respuesta)) {
                    // Respuesta múltiple
                    td.innerHTML = `<small>${respuesta.join(', ')}</small>`;
                } else {
                    // Respuesta simple
                    td.innerHTML = `<small>${respuesta || '-'}</small>`;
                }
                
                tr.appendChild(td);
            });
            
            // Agregar columna de acciones
            const tdAcciones = document.createElement('td');
            tdAcciones.className = 'text-center';
            tdAcciones.innerHTML = `
                <button class="btn btn-sm btn-permitir-repetir" 
                        data-respuesta-id="${resp.id}" 
                        data-usuario-nombre="${resp.usuario_nombre || 'Anónimo'}"
                        data-encuesta-id="${encuestaId}"
                        style="background: #28a745; color: white; border-radius: 6px; padding: 0.4rem 0.8rem; border: none; font-size: 0.85rem; transition: all 0.2s ease;"
                        title="Permitir que este usuario responda nuevamente">
                    <i class="fas fa-redo-alt mr-1"></i>Permitir Repetir
                </button>
            `;
            tr.appendChild(tdAcciones);
            
            tbody.appendChild(tr);
        });
        
        // Guardar datos para exportar
        window.currentRespuestas = { respuestas, tituloEncuesta, encuestaId, preguntas };
    }

    // Ver detalle de respuesta (ya no es necesario pero lo dejamos por si acaso)
    document.querySelector('#tablaRespuestas').addEventListener('click', async function(e){
        const btn = e.target.closest('.btn-ver-detalle');
        const btnPermitir = e.target.closest('.btn-permitir-repetir');
        
        if(btn){
            const respuesta = JSON.parse(btn.getAttribute('data-respuesta'));
            let detalleHtml = '<div style="max-height: 400px; overflow-y: auto;">';
            
            if(respuesta.respuestas && typeof respuesta.respuestas === 'object'){
                Object.keys(respuesta.respuestas).forEach(key => {
                    const valor = respuesta.respuestas[key];
                    const valorTexto = Array.isArray(valor) ? valor.join(', ') : valor;
                    detalleHtml += `<p><strong>${key}:</strong> ${valorTexto}</p>`;
                });
            }
            
            detalleHtml += '</div>';
            CustomModal.alert(detalleHtml, `Respuesta de ${respuesta.usuario_nombre || 'Anónimo'}`);
        }
        
        if(btnPermitir){
            const respuestaId = btnPermitir.getAttribute('data-respuesta-id');
            const usuarioNombre = btnPermitir.getAttribute('data-usuario-nombre');
            const encuestaId = btnPermitir.getAttribute('data-encuesta-id');
            
            const confirmed = await CustomModal.confirm(
                `¿Está seguro de permitir que ${usuarioNombre} pueda responder esta encuesta nuevamente? Esto eliminará su respuesta actual.`,
                'Confirmar acción'
            );
            
            if(!confirmed) return;
            
            try {
                const res = await fetch(`${showUrl}/${encuestaId}/respuestas/${respuestaId}/permitir-repetir`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });
                
                const result = await res.json();
                
                if(result.success) {
                    await CustomModal.alert('Se ha eliminado la respuesta. El usuario ahora puede responder nuevamente.', 'Éxito');
                    // Recargar respuestas
                    const titulo = document.getElementById('respuestasEncuestaTitulo').textContent;
                    showRespuestasModal(encuestaId, titulo);
                } else {
                    await CustomModal.alert(result.message || 'Error al procesar la solicitud', 'Error');
                }
            } catch (error) {
                console.error('Error:', error);
                await CustomModal.alert('Error al procesar la solicitud. Por favor, intente nuevamente.', 'Error');
            }
        }
    });

    // Exportar a Excel
    document.getElementById('exportExcelBtn').addEventListener('click', function(){
        if(!window.currentRespuestas || !window.currentRespuestas.respuestas.length){
            CustomModal.alert('No hay respuestas para exportar', 'Información');
            return;
        }
        
        const { respuestas, tituloEncuesta, preguntas } = window.currentRespuestas;
        
        // Preparar datos para Excel con columnas dinámicas
        const excelData = respuestas.map(resp => {
            const row = {
                'Usuario': resp.usuario_nombre || 'Anónimo',
                'Email': resp.usuario_email || 'N/A',
                'Fecha': resp.fecha || ''
            };
            
            // Agregar cada pregunta como columna
            preguntas.forEach((pregunta, idx) => {
                const respuesta = resp.respuestas[pregunta.title];
                
                if (Array.isArray(respuesta)) {
                    // Si es array (respuesta múltiple), unir con comas
                    row[pregunta.title] = respuesta.join(', ');
                } else {
                    // Respuesta simple
                    row[pregunta.title] = respuesta || '';
                }
            });
            
            return row;
        });
        
        // Crear libro de Excel
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.json_to_sheet(excelData);
        
        // Ajustar ancho de columnas
        const colWidths = [
            { wch: 20 },  // Usuario
            { wch: 30 },  // Email
            { wch: 20 }   // Fecha
        ];
        
        // Agregar ancho para cada pregunta
        preguntas.forEach(() => {
            colWidths.push({ wch: 40 });
        });
        
        ws['!cols'] = colWidths;
        
        XLSX.utils.book_append_sheet(wb, ws, 'Respuestas');
        
        // Descargar archivo
        const fileName = `${tituloEncuesta.replace(/[^a-z0-9]/gi, '_')}_respuestas.xlsx`;
        XLSX.writeFile(wb, fileName);
        
        CustomModal.alert('Archivo Excel descargado correctamente', 'Éxito');
    });

    // Save modal handler (create or update)
    document.getElementById('saveEncuestaModalBtn').addEventListener('click', async function(){
        const tituloEl = document.getElementById('modalEncuestaTitulo');
        const titulo = (tituloEl.value || '').trim();
        const codigo = document.getElementById('modalEncuestaCodigo').value || '';
        const permitirRepetir = document.getElementById('modalPermitirRepetir').checked;
        
        if(!titulo){ 
            await CustomModal.alert('Por favor, ingrese un título para la encuesta.', 'Campo requerido');
            tituloEl.focus(); 
            return; 
        }
        
        const qs = questions.map(q=>({ 
            type:q.type, 
            title:(q.title||'').trim(), 
            options: (q.type==='text'?[]: (q.options||[]).map(o=>o.trim()).filter(Boolean)),
            correctAnswers: q.correctAnswers || []
        })).filter(q=>q.title);
        
        const estructura = JSON.stringify({ questions: qs });
        const fechaInicio = document.getElementById('modalFechaInicio').value || null;
        const fechaFin = document.getElementById('modalFechaFin').value || null;
        const payload = { 
            titulo, 
            codigo, 
            estructura, 
            activo:1, 
            permitir_repetir: permitirRepetir ? 1 : 0,
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin
        };
        
        try{
            let res;
            if(editingId){
                payload._method = 'PUT';
                res = await fetch(`${storeUrl}/${editingId}`, { method: 'POST', headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token}, body: JSON.stringify(payload) });
            } else {
                res = await fetch(storeUrl, { method: 'POST', headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token}, body: JSON.stringify(payload) });
            }
            const json = await res.json();
            if(json.success){
                $('#createEncuestaModal').modal('hide');
                editingId = null; 
                questions = [];
                loadData();
                await CustomModal.alert('La encuesta ha sido guardada correctamente.', 'Éxito');
            } else {
                await CustomModal.alert(json.message || 'No se pudo guardar la encuesta. Por favor, intente nuevamente.', 'Error');
            }
        }catch(err){ 
            console.error(err); 
            await CustomModal.alert('Error al guardar la encuesta. Por favor, verifique su conexión e intente nuevamente.', 'Error de conexión');
        }
    });

    // init
    renderQuestions();
    loadData();
    
    // Botón Nueva Encuesta - limpiar estado
    document.getElementById('btnCreateEncuesta').addEventListener('click', function() {
        console.log('Abriendo modal para nueva encuesta');
        editingId = null;
        questions = [];
        document.getElementById('modalEncuestaTitulo').value = '';
        document.getElementById('modalEncuestaCodigo').value = '';
        document.getElementById('modalPermitirRepetir').checked = false;
        document.getElementById('modalFechaInicio').value = '';
        document.getElementById('modalFechaFin').value = '';
        document.getElementById('modalTitleText').textContent = 'Nueva Encuesta';
        renderQuestions();
    });
    
    // Limpiar modal al cerrar
    $('#createEncuestaModal').on('hidden.bs.modal', function () {
        console.log('Modal cerrado - limpiando estado');
        editingId = null;
        questions = [];
        document.getElementById('modalEncuestaTitulo').value = '';
        document.getElementById('modalEncuestaCodigo').value = '';
        document.getElementById('modalPermitirRepetir').checked = false;
        renderQuestions();
    });
    
    // Evento al abrir modal para asegurar que se renderiza
    $('#createEncuestaModal').on('shown.bs.modal', function () {
        console.log('Modal mostrado - renderizando preguntas');
        renderQuestions();
    });
});
</script>
@endpush
