<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomVerifyEmail extends VerifyEmail
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        Log::info('CustomVerifyEmail: Preparando correo para ' . $notifiable->email);

        // Intentar envío con configuración actual
        // Si falla puerto 587, auto-reconfigurar a 465
        $this->ensureSmtpWorks();

        return (new MailMessage)
            ->subject('Verificación de Correo Electrónico - CABI HUV')
            ->view('emails.verify-email', [
                'user' => $notifiable,
                'verificationUrl' => $verificationUrl,
            ]);
    }

    /**
     * Verifica que SMTP funcione. Si el puerto 587 está bloqueado,
     * reconfigura automáticamente a puerto 465 (SSL).
     */
    protected function ensureSmtpWorks(): void
    {
        $host = config('mail.mailers.smtp.host');
        $port = (int) config('mail.mailers.smtp.port');

        // Solo aplicar para Gmail
        if (stripos($host, 'gmail') === false) {
            return;
        }

        // Probar si el puerto actual es accesible
        $conn = @fsockopen($host, $port, $errno, $errstr, 5);
        if ($conn) {
            fclose($conn);
            Log::info("CustomVerifyEmail: Puerto $port accesible");
            return;
        }

        Log::warning("CustomVerifyEmail: Puerto $port BLOQUEADO ($errstr). Intentando puerto 465...");

        // Intentar con puerto 465 (SSL)
        $conn465 = @fsockopen($host, 465, $errno2, $errstr2, 5);
        if ($conn465) {
            fclose($conn465);
            Log::info('CustomVerifyEmail: Puerto 465 disponible. Reconfigurando...');

            config([
                'mail.mailers.smtp.port' => 465,
                'mail.mailers.smtp.scheme' => 'smtps',
            ]);

            // Forzar recreación del mailer con nueva config
            app()->forgetInstance('mail.manager');

            return;
        }

        Log::error("CustomVerifyEmail: Puertos 587 y 465 BLOQUEADOS. El correo no se enviará.");
    }
}
