<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ConceptoDocumento extends Model
{
    protected $table = 'concepto_documentos';

    protected $fillable = [
        'concepto_medico_id',
        'nombre_original',
        'ruta',
        'mime',
        'tamano',
    ];

    public function conceptoMedico()
    {
        return $this->belongsTo(ConceptoMedico::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->ruta);
    }

    public function getEsImagenAttribute(): bool
    {
        return str_starts_with((string) $this->mime, 'image/');
    }

    public function getEsPdfAttribute(): bool
    {
        return $this->mime === 'application/pdf';
    }
}
