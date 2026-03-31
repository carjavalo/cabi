<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DIAGNÓSTICO SMTP CABI ===\n\n";

echo "1. Configuración:\n";
echo "   MAIL_MAILER: " . config('mail.default') . "\n";
echo "   MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "   MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "   MAIL_SCHEME: " . config('mail.mailers.smtp.scheme') . "\n";
echo "   MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "   MAIL_PASSWORD: " . (config('mail.mailers.smtp.password') ? '***SET***' : '***EMPTY***') . "\n";
echo "   MAIL_FROM: " . config('mail.from.address') . "\n";
echo "   APP_URL: " . config('app.url') . "\n\n";

echo "2. Logo: " . (file_exists(public_path('img/logocorreo.jpeg')) ? 'EXISTE' : 'NO EXISTE') . "\n\n";

echo "3. Enviando correo de prueba...\n";
try {
    Illuminate\Support\Facades\Mail::raw(
        'Correo de prueba del sistema CABI. Si recibes esto, la configuración SMTP funciona correctamente.',
        function ($message) {
            $message->to('carjavalosistem@gmail.com')
                    ->subject('CABI - Test SMTP ' . date('Y-m-d H:i:s'));
        }
    );
    echo "   RESULTADO: ÉXITO - Correo enviado\n";
} catch (Exception $e) {
    echo "   RESULTADO: ERROR - " . $e->getMessage() . "\n";
}
