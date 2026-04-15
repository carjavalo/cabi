<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citación a Capacitación - CABI HUV</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f0f2f5; padding: 30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="620" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">

                    {{-- HEADER CON LOGO --}}
                    <tr>
                        <td style="background-color: #eef1f6; padding: 20px 30px; text-align: center; border-bottom: 3px solid #2e3a75;">
                            @php
                                $logoUrl = rtrim(config('app.url'), '/') . '/img/logocorreo.jpeg';
                            @endphp
                            <img src="{{ $logoUrl }}" alt="Hospital Universitario del Valle" style="max-width: 100%; height: auto; max-height: 90px;" onerror="this.style.display='none'">
                        </td>
                    </tr>

                    {{-- BARRA CORPORATIVA --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #2e3a75 0%, #1a234f 100%); padding: 18px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: 700; letter-spacing: 0.5px;">
                                Citación a Capacitación
                            </h1>
                            <p style="margin: 4px 0 0; color: #b8c1e0; font-size: 13px;">
                                Plan Institucional de Capacitación &mdash; Sistema CABI
                            </p>
                        </td>
                    </tr>

                    {{-- CONTENIDO PRINCIPAL --}}
                    <tr>
                        <td style="padding: 35px 40px 20px;">
                            <h2 style="color: #2e3a75; font-size: 22px; margin: 0 0 8px; font-weight: 700;">
                                ¡Hola, {{ $user->name }}!
                            </h2>
                            <p style="color: #555; font-size: 15px; line-height: 1.6; margin: 0 0 20px;">
                                Le informamos que ha sido <strong>citado(a)</strong> a la siguiente capacitación del Plan Institucional de Capacitación del Hospital Universitario del Valle:
                            </p>

                            {{-- CAJA CON DETALLES DE LA CAPACITACIÓN --}}
                            <div style="background-color: #f8f9fc; border: 1px solid #d6dce8; border-left: 4px solid #2e3a75; border-radius: 6px; padding: 22px 24px; margin: 20px 0 25px;">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="padding: 8px 0; vertical-align: top;">
                                            <span style="color: #2e3a75; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">📋 Capacitación</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0 0 16px;">
                                            <span style="color: #333; font-size: 18px; font-weight: 700;">{{ $capacitacion->titulo }}</span>
                                        </td>
                                    </tr>

                                    @if($capacitacion->descripcion)
                                    <tr>
                                        <td style="padding: 0 0 16px;">
                                            <span style="color: #888; font-size: 12px; font-weight: 600; text-transform: uppercase;">Descripción</span><br>
                                            <span style="color: #555; font-size: 14px;">{{ $capacitacion->descripcion }}</span>
                                        </td>
                                    </tr>
                                    @endif

                                    <tr>
                                        <td style="padding: 0 0 12px;">
                                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td width="50%" style="vertical-align: top;">
                                                        <span style="color: #888; font-size: 12px; font-weight: 600; text-transform: uppercase;">📅 Fecha</span><br>
                                                        <span style="color: #333; font-size: 15px; font-weight: 600;">{{ \Carbon\Carbon::parse($capacitacion->fecha)->translatedFormat('l, d \d\e F \d\e Y') }}</span>
                                                    </td>
                                                    <td width="50%" style="vertical-align: top;">
                                                        <span style="color: #888; font-size: 12px; font-weight: 600; text-transform: uppercase;">🕐 Horario</span><br>
                                                        <span style="color: #333; font-size: 15px; font-weight: 600;">
                                                            @if($capacitacion->hora_inicio && $capacitacion->hora_fin)
                                                                {{ \Carbon\Carbon::parse($capacitacion->hora_inicio)->format('h:i A') }} - {{ \Carbon\Carbon::parse($capacitacion->hora_fin)->format('h:i A') }}
                                                            @else
                                                                Por confirmar
                                                            @endif
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="padding: 12px 0 0;">
                                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td width="50%" style="vertical-align: top;">
                                                        <span style="color: #888; font-size: 12px; font-weight: 600; text-transform: uppercase;">📍 Ubicación</span><br>
                                                        <span style="color: #333; font-size: 14px;">{{ $capacitacion->ubicacion ?: 'Por confirmar' }}</span>
                                                    </td>
                                                    <td width="50%" style="vertical-align: top;">
                                                        <span style="color: #888; font-size: 12px; font-weight: 600; text-transform: uppercase;">👨‍🏫 Instructor</span><br>
                                                        <span style="color: #333; font-size: 14px;">{{ $capacitacion->instructor ?: 'Por confirmar' }}</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            {{-- MENSAJE IMPORTANTE --}}
                            <div style="background-color: #fff8e1; border: 1px solid #ffe082; border-radius: 6px; padding: 14px 18px; margin: 0 0 25px;">
                                <p style="color: #5d4037; font-size: 13px; line-height: 1.6; margin: 0;">
                                    <strong>⚠️ Importante:</strong> Su asistencia es requerida. En caso de no poder asistir, por favor comuníquese con su jefe inmediato o con el área de capacitación.
                                </p>
                            </div>

                            <p style="color: #555; font-size: 14px; line-height: 1.6; margin: 0 0 15px; text-align: center;">
                                Le agradecemos su participación en las actividades de formación del Hospital.
                            </p>
                        </td>
                    </tr>

                    {{-- PIE DE PÁGINA --}}
                    <tr>
                        <td style="background-color: #2e3a75; padding: 20px 30px; text-align: center;">
                            <p style="color: #b8c1e0; font-size: 12px; margin: 0 0 4px;">
                                &copy; {{ date('Y') }} &mdash; Área de Plan Institucional de Capacitación
                            </p>
                            <p style="color: #8a95c0; font-size: 11px; margin: 0 0 4px;">
                                Hospital Universitario del Valle &quot;Evaristo García&quot; E.S.E.
                            </p>
                            <p style="color: #8a95c0; font-size: 11px; margin: 0;">
                                Este es un correo automático, por favor no responda a este mensaje.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
