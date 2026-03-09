<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoInscripcion extends Model
{
    protected $table = 'evento_inscripciones';

    protected $fillable = [
        'evento_id', 'evento_franja_id', 'user_id', 
        'nombre_completo', 'identificacion', 'fecha_reserva', 'asistencia'
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function franja()
    {
        return $this->belongsTo(EventoFranja::class, 'evento_franja_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inscribirGym()
    {
        return $this->belongsTo(Inscripgym::class, 'identificacion', 'identificacion');
    }
}
