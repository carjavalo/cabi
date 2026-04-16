<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    protected $fillable = [
        'nombre',
    ];

    protected function nombre(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? mb_convert_case(mb_strtolower(trim($value)), MB_CASE_TITLE, 'UTF-8') : $value,
        );
    }
}
