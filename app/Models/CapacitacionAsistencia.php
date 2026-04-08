<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapacitacionAsistencia extends Model
{
    protected $table = 'capacitacion_asistencias';

    protected $fillable = [
        'capacitacion_id', 'user_id', 'asistio', 'observacion',
    ];

    protected $casts = [
        'asistio' => 'boolean',
    ];

    public function capacitacion()
    {
        return $this->belongsTo(Capacitacion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
