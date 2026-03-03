<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripgym extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'inscripgym';

    protected $fillable = [
        'nombres',
        'primer_apellido',
        'segundo_apellido',
        'identificacion',
        'edad',
        'celular',
        'tipo_vinculacion',
        'servicio_unidad',
        'contacto_emergencia',
        'correolec'
    ];
}
