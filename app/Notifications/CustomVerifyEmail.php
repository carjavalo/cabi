<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class CustomVerifyEmail extends VerifyEmail
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        Log::info('CustomVerifyEmail: Enviando correo de verificación a ' . $notifiable->email);
        Log::info('CustomVerifyEmail: URL de verificación generada: ' . $verificationUrl);

        return (new MailMessage)
            ->subject('Verificación de Correo Electrónico - CABI HUV')
            ->view('emails.verify-email', [
                'user' => $notifiable,
                'verificationUrl' => $verificationUrl,
            ]);
    }
}
