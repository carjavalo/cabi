<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioGim extends Model
{
    protected $table = 'horariosgim';

    protected $fillable = [
        'nombre', 'email', 'telefono', 'fecha', 'hora',
        'primer_apellido', 'segundo_apellido', 'identificacion', 'servicio',
        'dia_entrenamiento', 'horario'
    ];
}
