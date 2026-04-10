<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapacitacionAsistenciaRegistro extends Model
{
    protected $table = 'capacitacion_asistencia_registros';

    protected $fillable = [
        'sesion_id', 'capacitacion_id', 'user_id',
        'nombre', 'identificacion', 'tipo_contrato', 'correo',
        'autoriza_firma', 'es_citado',
    ];

    protected $casts = [
        'autoriza_firma' => 'boolean',
        'es_citado' => 'boolean',
    ];

    public function sesion()
    {
        return $this->belongsTo(CapacitacionSesion::class, 'sesion_id');
    }

    public function capacitacion()
    {
        return $this->belongsTo(Capacitacion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
