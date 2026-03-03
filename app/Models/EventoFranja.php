<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoFranja extends Model
{
    protected $fillable = ['evento_id', 'dia_semana', 'hora_inicio', 'hora_fin', 'capacidad_maxima'];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function inscripciones()
    {
        return $this->hasMany(EventoInscripcion::class, 'evento_franja_id');
    }
}
