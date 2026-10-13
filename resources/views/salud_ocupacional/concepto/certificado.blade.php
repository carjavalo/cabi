{{--
    Documento independiente del certificado para imprimir o guardar en PDF.
    No usa el diseño del aplicativo: sin menú, sin barras de desplazamiento y sin el nombre "CABI".
    El título es el nombre sugerido del PDF. Con márgenes de página en 0 el navegador no agrega
    encabezados ni pies (fecha, hora, título o dirección web).
--}}
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificado de concepto médico ocupacional - {{ $c->paciente_nombre ?: optional($c->user)->name }} - {{ optional($c->fecha_atencion)->format('d-m-Y') }}</title>
    @include('salud_ocupacional.concepto._certificado_estilos')
    <style>
        @page{ size:letter;margin:0; }
        html,body{ margin:0;padding:0; }
        @media screen{
            body{ background:#e9ebf2;padding:64px 12px 28px; }
            .doc{ box-shadow:0 10px 30px rgba(20,30,70,.18); }
            .barra{ position:fixed;top:0;left:0;right:0;display:flex;justify-content:center;gap:10px;padding:12px;background:#2e3a75;z-index:10; }
            .barra button{ font:600 14px Arial,sans-serif;padding:9px 18px;border-radius:8px;border:none;cursor:pointer; }
            .barra .prim{ background:#fff;color:#2e3a75; }
            .barra .sec{ background:transparent;color:#fff;border:1px solid rgba(255,255,255,.6); }
        }
        @media print{
            html,body{ background:#fff; }
            .barra{ display:none !important; }
            .doc{ max-width:none;min-height:0;box-shadow:none; }
        }
    </style>
</head>
<body>
    <div class="barra">
        <button type="button" class="prim" onclick="window.print()">Imprimir / Guardar PDF</button>
        <button type="button" class="sec" onclick="window.close()">Cerrar</button>
    </div>

    @include('salud_ocupacional.concepto._certificado')

    @if(request()->boolean('imprimir'))
    <script>window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 200); });</script>
    @endif
</body>
</html>
