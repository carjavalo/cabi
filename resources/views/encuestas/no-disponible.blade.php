<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta No Disponible - HUV</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2e3a75;
            --primary-dark: #1f2850;
            --primary-light: #3d4d8f;
        }
        
        body {
            background-image: url('{{ asset('img/nuevologo.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(46, 58, 117, 0.92) 0%, rgba(31, 40, 80, 0.95) 100%);
            z-index: -1;
        }
        
        .message-container {
            max-width: 600px;
            width: 100%;
        }
        
        .message-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .message-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .message-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }
        
        .icon-wrapper {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            position: relative;
            z-index: 1;
        }
        
        .icon-wrapper i {
            font-size: 4rem;
            color: white;
        }
        
        .message-header h1 {
            color: white;
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        .message-body {
            padding: 3rem 2rem;
            text-align: center;
        }
        
        .message-body h2 {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .message-body p {
            color: #4a5568;
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }
        
        .info-box {
            background: #f8f9fc;
            border-left: 4px solid var(--primary-color);
            padding: 1.5rem;
            border-radius: 8px;
            margin: 2rem 0;
            text-align: left;
        }
        
        .info-box h3 {
            color: var(--primary-color);
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
        }
        
        .info-box h3 i {
            margin-right: 0.5rem;
        }
        
        .info-box p {
            color: #4a5568;
            font-size: 0.95rem;
            margin: 0.5rem 0;
            line-height: 1.6;
        }
        
        .info-box strong {
            color: var(--primary-dark);
        }
        
        .btn-home {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(46, 58, 117, 0.3);
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 58, 117, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .logo-huv {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-huv h3 {
            color: white;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            font-size: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .message-header {
                padding: 2rem 1.5rem;
            }
            
            .icon-wrapper {
                width: 100px;
                height: 100px;
            }
            
            .icon-wrapper i {
                font-size: 3rem;
            }
            
            .message-header h1 {
                font-size: 1.5rem;
            }
            
            .message-body {
                padding: 2rem 1.5rem;
            }
            
            .message-body h2 {
                font-size: 1.25rem;
            }
            
            .message-body p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="message-container">
        <div class="logo-huv">
            <h3>
                <i class="fas fa-hospital"></i> Hospital Universitario del Valle
            </h3>
        </div>
        
        <div class="message-card">
            <div class="message-header">
                <div class="icon-wrapper">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h1>Encuesta No Disponible</h1>
            </div>
            
            <div class="message-body">
                <h2>{{ $encuestaInactiva->titulo }}</h2>
                
                @if($encuestaInactiva->activo == 0)
                    <p>
                        <i class="fas fa-info-circle" style="color: var(--primary-color); margin-right: 0.5rem;"></i>
                        Esta encuesta ha sido desactivada temporalmente.
                    </p>
                @else
                    @php
                        $now = now();
                        $fechaInicio = $encuestaInactiva->fecha_inicio ? \Carbon\Carbon::parse($encuestaInactiva->fecha_inicio) : null;
                        $fechaFin = $encuestaInactiva->fecha_fin ? \Carbon\Carbon::parse($encuestaInactiva->fecha_fin) : null;
                    @endphp
                    
                    @if($fechaInicio && $now < $fechaInicio)
                        <p>
                            <i class="fas fa-clock" style="color: var(--primary-color); margin-right: 0.5rem;"></i>
                            Esta encuesta aún no está disponible.
                        </p>
                        
                        <div class="info-box">
                            <h3><i class="fas fa-calendar-check"></i>Información de Disponibilidad</h3>
                            <p><strong>Fecha de inicio:</strong> {{ $fechaInicio->format('d/m/Y H:i') }}</p>
                            @if($fechaFin)
                            <p><strong>Fecha de finalización:</strong> {{ $fechaFin->format('d/m/Y H:i') }}</p>
                            @endif
                            <p style="margin-top: 1rem; color: #718096;">
                                La encuesta estará disponible a partir de la fecha indicada. Por favor, vuelve más tarde.
                            </p>
                        </div>
                    @elseif($fechaFin && $now > $fechaFin)
                        <p>
                            <i class="fas fa-hourglass-end" style="color: var(--primary-color); margin-right: 0.5rem;"></i>
                            El período para responder esta encuesta ha finalizado.
                        </p>
                        
                        <div class="info-box">
                            <h3><i class="fas fa-calendar-times"></i>Período de Vigencia</h3>
                            @if($fechaInicio)
                            <p><strong>Fecha de inicio:</strong> {{ $fechaInicio->format('d/m/Y H:i') }}</p>
                            @endif
                            <p><strong>Fecha de finalización:</strong> {{ $fechaFin->format('d/m/Y H:i') }}</p>
                            <p style="margin-top: 1rem; color: #718096;">
                                Esta encuesta ya no acepta nuevas respuestas. Gracias por tu interés.
                            </p>
                        </div>
                    @else
                        <p>
                            <i class="fas fa-exclamation-triangle" style="color: var(--primary-color); margin-right: 0.5rem;"></i>
                            Esta encuesta no está disponible en este momento.
                        </p>
                    @endif
                @endif
                
                <div style="margin-top: 2.5rem;">
                    <a href="{{ url('/') }}" class="btn-home">
                        <i class="fas fa-home mr-2"></i>Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
