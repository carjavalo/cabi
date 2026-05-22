<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recaudo extends Model
{
    protected $fillable = [
        'numero_recibo',
        'fecha',
        'user_id',
        'valor',
        'cantidad',
        'valor_parcial',
        'concepto'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
