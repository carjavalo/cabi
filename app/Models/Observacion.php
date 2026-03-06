<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Observacion extends Model
{
    protected $table = 'observacions';

    protected $fillable = [
        'identificacion',
        'observacion',
        'creado_por',
        'fecha'
    ];
}
