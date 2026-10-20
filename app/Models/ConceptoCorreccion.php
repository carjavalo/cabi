<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Auditoría de las correcciones hechas a un concepto médico desde Consulta SST:
 * quién corrigió, cuándo, por qué y el valor anterior y nuevo de cada campo.
 */
class ConceptoCorreccion extends Model
{
    protected $table = 'concepto_correcciones';

    protected $fillable = ['concepto_medico_id', 'user_id', 'cambios', 'motivo'];

    protected function casts(): array
    {
        return ['cambios' => 'array'];
    }

    public function concepto()
    {
        return $this->belongsTo(ConceptoMedico::class, 'concepto_medico_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
