<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Cargo extends Model
{
    protected $table = 'cargos';
    
    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    protected function nombre(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? mb_convert_case(mb_strtolower(trim($value)), MB_CASE_TITLE, 'UTF-8') : $value,
        );
    }
}
