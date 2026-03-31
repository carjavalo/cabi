<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Correo - CABI HUV</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f0f2f5; padding: 30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="620" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">

                    {{-- HEADER CON LOGO --}}
                    <tr>
                        <td style="background-color: #eef1f6; padding: 20px 30px; text-align: center; border-bottom: 3px solid #2e3a75;">
                            <img src="{{ $message->embed(public_path('img/logocorreo.jpeg')) }}" alt="Hospital Universitario del Valle" style="max-width: 100%; height: auto; max-height: 90px;">
                        </td>
                    </tr>

                    {{-- BARRA CORPORATIVA --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #2e3a75 0%, #1a234f 100%); padding: 18px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: 700; letter-spacing: 0.5px;">
                                Plan Institucional de Capacitación
                            </h1>
                            <p style="margin: 4px 0 0; color: #b8c1e0; font-size: 13px;">
                                Sistema CABI &mdash; Centro de Aprendizaje y Bienestar Integral
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
                                Gracias por registrarte en el sistema <strong>CABI</strong> del Hospital Universitario del Valle. Para completar tu registro y activar tu cuenta, es necesario que verifiques tu dirección de correo electrónico.
                            </p>

                            {{-- CAJA DE TRATAMIENTO DE DATOS --}}
                            <div style="background-color: #f8f9fc; border: 1px solid #d6dce8; border-left: 4px solid #2e3a75; border-radius: 6px; padding: 18px 20px; margin: 20px 0 25px;">
                                <h3 style="color: #2e3a75; font-size: 14px; margin: 0 0 10px; font-weight: 700;">
                                    📋 Autorización de Tratamiento de Datos Personales
                                </h3>
                                <p style="color: #555; font-size: 13px; line-height: 1.6; margin: 0 0 10px;">
                                    De conformidad con la <strong>Ley 1581 de 2012</strong> y el <strong>Decreto 1377 de 2013</strong>, el <strong>Hospital Universitario del Valle &quot;Evaristo García&quot; E.S.E.</strong>, en calidad de responsable del tratamiento de datos personales, le informa que los datos personales suministrados a través de este sistema serán recopilados, almacenados, usados, circulados y tratados con la finalidad de:
                                </p>
                                <ul style="color: #555; font-size: 13px; line-height: 1.7; margin: 0 0 10px; padding-left: 20px;">
                                    <li>Gestionar el registro y acceso al sistema CABI.</li>
                                    <li>Administrar la inscripción a eventos de capacitación institucional.</li>
                                    <li>Generar reportes estadísticos y de asistencia.</li>
                                    <li>Enviar comunicaciones relacionadas con el Plan Institucional de Capacitación.</li>
                                </ul>
                                <p style="color: #555; font-size: 13px; line-height: 1.6; margin: 0 0 10px;">
                                    Sus datos serán tratados con las medidas de seguridad necesarias para garantizar su confidencialidad e integridad. Usted tiene derecho a conocer, actualizar, rectificar y solicitar la supresión de sus datos personales, contactándonos a través del correo <strong>oficinapic@correohuv.gov.co</strong>.
                                </p>
                                <p style="color: #2e3a75; font-size: 13px; line-height: 1.6; margin: 0; font-weight: 600;">
                                    Al hacer clic en el botón &quot;Verificar Correo Electrónico&quot;, usted declara que ha leído, comprendido y aceptado la política de tratamiento de datos personales del Hospital Universitario del Valle E.S.E.
                                </p>
                            </div>

                            {{-- BOTÓN DE VERIFICACIÓN --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="padding: 10px 0 30px;">
                                        <a href="{{ $verificationUrl }}" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #2e3a75 0%, #1a234f 100%); color: #ffffff; text-decoration: none; padding: 14px 45px; border-radius: 30px; font-size: 16px; font-weight: 700; letter-spacing: 0.5px; box-shadow: 0 4px 15px rgba(46, 58, 117, 0.4);">
                                            ✅ Verificar Correo Electrónico
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #888; font-size: 13px; line-height: 1.5; margin: 0 0 10px; text-align: center;">
                                Si no creó esta cuenta, puede ignorar este mensaje de forma segura.
                            </p>

                            {{-- ENLACE ALTERNATIVO --}}
                            <div style="background-color: #f8f9fc; border-radius: 6px; padding: 12px 16px; margin-top: 20px;">
                                <p style="color: #888; font-size: 12px; line-height: 1.5; margin: 0;">
                                    Si tiene problemas al hacer clic en el botón, copie y pegue la siguiente URL en su navegador:
                                </p>
                                <p style="color: #2e3a75; font-size: 11px; word-break: break-all; margin: 6px 0 0;">
                                    {{ $verificationUrl }}
                                </p>
                            </div>
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
