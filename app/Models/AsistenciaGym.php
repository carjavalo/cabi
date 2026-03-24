<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsistenciaGym extends Model
{
    protected $table = 'asistencias_gym';
    protected $fillable = ['identificacion', 'fecha', 'franja', 'asistio'];
}
