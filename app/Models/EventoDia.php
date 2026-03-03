<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoDia extends Model
{
    protected $fillable = ['evento_id', 'dia_semana'];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}
