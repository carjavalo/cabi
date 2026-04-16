<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        try {
            Log::info('User::sendEmailVerification - Iniciando envío para: ' . $this->email);
            Log::info('User::sendEmailVerification - SMTP Config: ' . json_encode([
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'scheme' => config('mail.mailers.smtp.scheme'),
                'from' => config('mail.from.address'),
            ]));
            $this->notify(new CustomVerifyEmail);
            Log::info('User::sendEmailVerification - Correo enviado exitosamente a: ' . $this->email);
        } catch (\Exception $e) {
            Log::error('User::sendEmailVerification - FALLÓ para ' . $this->email . ': ' . $e->getMessage());
            Log::error('User::sendEmailVerification - Trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'apellido1',
        'apellido2',
        'identificacion',
        'servicio',
        'servicio_id',
        'tipo_vinculacion_id',
        'tipo_vinculacion',
        'email',
        'password',
        'cargo',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => mb_convert_case(mb_strtolower(trim($value)), MB_CASE_TITLE, 'UTF-8'),
        );
    }

    protected function apellido1(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? mb_convert_case(mb_strtolower(trim($value)), MB_CASE_TITLE, 'UTF-8') : $value,
        );
    }

    protected function apellido2(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? mb_convert_case(mb_strtolower(trim($value)), MB_CASE_TITLE, 'UTF-8') : $value,
        );
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => mb_strtolower(trim($value)),
        );
    }
}
