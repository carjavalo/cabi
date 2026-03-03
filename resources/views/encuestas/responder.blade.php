<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $encuesta->titulo ?? 'Encuesta' }} - HUV</title>
    
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
            padding: 2rem 0;
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
        
        .encuesta-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .encuesta-header {
            background: white;
            border-radius: 16px 16px 0 0;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
        }
        
        .encuesta-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
        }
        
        .encuesta-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .encuesta-body {
            background: white;
            padding: 2.5rem;
            border-radius: 0 0 16px 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .pregunta-card {
            background: #f8f9fc;
            border: 2px solid #e8eaf0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .pregunta-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(46, 58, 117, 0.1);
        }
        
        .pregunta-numero {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            font-weight: 600;
            margin-right: 0.75rem;
        }
        
        .pregunta-titulo {
            color: #2d3748;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .opcion-item {
            background: white;
            border: 2px solid #e8eaf0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }
        
        .opcion-item:hover {
            border-color: var(--primary-color);
            background: #f8f9fc;
            transform: translateX(4px);
        }
        
        .opcion-item input[type="radio"],
        .opcion-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 1rem;
            cursor: pointer;
            accent-color: var(--primary-color);
        }
        
        .opcion-item.selected {
            border-color: var(--primary-color);
            background: rgba(46, 58, 117, 0.05);
            border-width: 2px;
        }
        
        .opcion-label {
            flex: 1;
            cursor: pointer;
            margin: 0;
            font-size: 1rem;
        }
        
        .form-control-text {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e8eaf0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control-text:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(46, 58, 117, 0.1);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            padding: 1rem 3rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(46, 58, 117, 0.3);
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 58, 117, 0.4);
        }
        
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .success-message {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(72, 187, 120, 0.3);
        }
        
        .success-message i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .success-message h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .logo-huv {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-huv img {
            max-width: 150px;
            height: auto;
        }
        
        .required-indicator {
            color: #e53e3e;
            margin-left: 0.25rem;
        }
        
        @media (max-width: 768px) {
            .encuesta-container {
                padding: 0 1rem;
            }
            
            .encuesta-header {
                padding: 1.5rem;
            }
            
            .encuesta-header h1 {
                font-size: 1.5rem;
            }
            
            .encuesta-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="encuesta-container">
        <div class="logo-huv">
            <h3 style="color: white; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                <i class="fas fa-hospital"></i> Hospital Universitario del Valle
            </h3>
        </div>
        
        <div id="encuestaForm">
            <div class="encuesta-header">
                <h1><i class="fas fa-clipboard-list mr-2"></i>{{ $encuesta->titulo }}</h1>
                @if($encuesta->codigo)
                <p><i class="fas fa-tag mr-2"></i>Código: {{ $encuesta->codigo }}</p>
                @endif
            </div>
            
            <div class="encuesta-body">
                <form id="respuestaForm">
                    @csrf
                    <input type="hidden" name="encuesta_id" value="{{ $encuesta->id }}">
                    
                    <div id="preguntasContainer"></div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-submit">
                            <i class="fas fa-paper-plane mr-2"></i>Enviar Respuestas
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div id="successMessage" style="display: none;">
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <h2>¡Gracias por tu participación!</h2>
                <p style="font-size: 1.1rem; margin: 0;">Tus respuestas han sido registradas correctamente.</p>
            </div>
        </div>
        
        <div id="alreadyAnsweredMessage" style="display: none;">
            <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 2rem; border-radius: 12px; text-align: center; box-shadow: 0 4px 20px rgba(245, 158, 11, 0.3);">
                <i class="fas fa-info-circle" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">Ya has respondido esta encuesta</h2>
                <p style="font-size: 1.1rem; margin: 0;">Esta encuesta no permite respuestas múltiples. Gracias por tu participación.</p>
            </div>
        </div>
    </div>
    
    <!-- jQuery y Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const encuesta = @json($encuesta);
        const estructura = JSON.parse(encuesta.estructura || '{}');
        const preguntas = estructura.questions || [];
        
        // Renderizar preguntas
        function renderPreguntas() {
            const container = document.getElementById('preguntasContainer');
            container.innerHTML = '';
            
            preguntas.forEach((pregunta, index) => {
                const card = document.createElement('div');
                card.className = 'pregunta-card';
                
                let html = `
                    <div class="pregunta-titulo">
                        <span class="pregunta-numero">${index + 1}</span>
                        <span>${pregunta.title}</span>
                    </div>
                `;
                
                if (pregunta.type === 'text') {
                    html += `
                        <textarea 
                            name="respuesta_${index}" 
                            class="form-control-text" 
                            rows="4" 
                            placeholder="Escribe tu respuesta aquí..."
                            required
                        ></textarea>
                    `;
                } else if (pregunta.type === 'single') {
                    pregunta.options.forEach((opcion, optIndex) => {
                        html += `
                            <label class="opcion-item">
                                <input 
                                    type="radio" 
                                    name="respuesta_${index}" 
                                    value="${opcion}"
                                    required
                                >
                                <span class="opcion-label">${opcion}</span>
                            </label>
                        `;
                    });
                } else if (pregunta.type === 'multiple') {
                    pregunta.options.forEach((opcion, optIndex) => {
                        html += `
                            <label class="opcion-item">
                                <input 
                                    type="checkbox" 
                                    name="respuesta_${index}[]" 
                                    value="${opcion}"
                                >
                                <span class="opcion-label">${opcion}</span>
                            </label>
                        `;
                    });
                }
                
                card.innerHTML = html;
                container.appendChild(card);
            });
            
            // Agregar efecto de selección
            document.querySelectorAll('.opcion-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    if (e.target.tagName !== 'INPUT') {
                        const input = this.querySelector('input');
                        if (input.type === 'radio') {
                            document.querySelectorAll(`input[name="${input.name}"]`).forEach(radio => {
                                radio.closest('.opcion-item').classList.remove('selected');
                            });
                            input.checked = true;
                            this.classList.add('selected');
                        } else {
                            input.checked = !input.checked;
                            this.classList.toggle('selected', input.checked);
                        }
                    } else {
                        if (e.target.type === 'radio') {
                            document.querySelectorAll(`input[name="${e.target.name}"]`).forEach(radio => {
                                radio.closest('.opcion-item').classList.remove('selected');
                            });
                            this.classList.add('selected');
                        } else {
                            this.classList.toggle('selected', e.target.checked);
                        }
                    }
                });
            });
        }
        
        // Enviar formulario
        document.getElementById('respuestaForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const respuestas = {};
            
            preguntas.forEach((pregunta, index) => {
                if (pregunta.type === 'multiple') {
                    const valores = formData.getAll(`respuesta_${index}[]`);
                    respuestas[pregunta.title] = valores.length > 0 ? valores : [];
                } else {
                    respuestas[pregunta.title] = formData.get(`respuesta_${index}`) || '';
                }
            });
            
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enviando...';
            
            try {
                const response = await fetch('{{ route("encuestas.guardar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        encuesta_id: encuesta.id,
                        respuestas: respuestas
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('encuestaForm').style.display = 'none';
                    document.getElementById('successMessage').style.display = 'block';
                } else {
                    // Verificar si es error de respuesta duplicada
                    if (response.status === 400 && result.message.includes('Ya has respondido')) {
                        document.getElementById('encuestaForm').style.display = 'none';
                        document.getElementById('alreadyAnsweredMessage').style.display = 'block';
                    } else {
                        alert(result.message || 'Error al guardar las respuestas. Por favor, intenta nuevamente.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Enviar Respuestas';
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al enviar las respuestas. Por favor, verifica tu conexión e intenta nuevamente.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Enviar Respuestas';
            }
        });
        
        // Inicializar
        renderPreguntas();
    </script>
</body>
</html>
