<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CapacitacionCitado extends Notification
{
    use Queueable;

    protected $capacitacion;

    public function __construct($capacitacion)
    {
        $this->capacitacion = $capacitacion;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $this->ensureSmtpWorks();

        Log::info('CapacitacionCitado: Enviando notificación a ' . $notifiable->email . ' para capacitación: ' . $this->capacitacion->titulo);

        return (new MailMessage)
            ->subject('Citación a Capacitación: ' . $this->capacitacion->titulo . ' - CABI HUV')
            ->view('emails.capacitacion-citado', [
                'user' => $notifiable,
                'capacitacion' => $this->capacitacion,
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

        if (stripos($host, 'gmail') === false) {
            return;
        }

        $conn = @fsockopen($host, $port, $errno, $errstr, 5);
        if ($conn) {
            fclose($conn);
            return;
        }

        $conn465 = @fsockopen($host, 465, $errno2, $errstr2, 5);
        if ($conn465) {
            fclose($conn465);
            config([
                'mail.mailers.smtp.port' => 465,
                'mail.mailers.smtp.scheme' => 'smtps',
            ]);
            app()->forgetInstance('mail.manager');
        }
    }
}
