<?php

namespace App\Models;

use App\Support\Archivos;
use Illuminate\Database\Eloquent\Model;

/**
 * Documento adjunto a un concepto médico (exámenes, órdenes, incapacidades…).
 * "ruta" guarda la referencia del archivo (ver App\Support\Archivos): en S3 queda en
 * talentoHumanoHUV/salud-ocupacional/{cédula}_{nombre del paciente}/…
 */
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

    /** Enlace para abrir el documento: pasa por el aplicativo, que valida el permiso. */
    public function getUrlAttribute(): string
    {
        return route('salud.concepto.documento', $this->id);
    }

    /** Enlace directo al archivo (en S3, temporal y firmado). */
    public function urlArchivo(): ?string
    {
        return Archivos::url($this->ruta, $this->nombre_original);
    }

    public function getEsImagenAttribute(): bool
    {
        return str_starts_with((string) $this->mime, 'image/');
    }

    public function getEsPdfAttribute(): bool
    {
        return $this->mime === 'application/pdf';
    }

    /**
     * Carpeta y nombre base del archivo en el almacenamiento. El paciente puede subir documentos
     * muchas veces: el nombre lleva cédula, nombre y la FECHA Y HORA DE CARGA, de modo que cada
     * documento se conserva aparte y nunca se sobrescribe. Ejemplo:
     *   salud-ocupacional/1144555666_Maria_Jose_Pena_Gomez/
     *     1144555666_Maria_Jose_Pena_Gomez_2026-10-14_15-42-07_concepto-17_Orden_medica.pdf
     *
     * @param \DateTimeInterface|null $fechaCarga Momento en que se subió (por defecto, ahora).
     */
    public static function ubicacion(ConceptoMedico $concepto, string $nombreOriginal, ?\DateTimeInterface $fechaCarga = null): array
    {
        $persona = Archivos::nombrePersona($concepto->identificacion, $concepto->paciente_nombre);
        $fecha = \Illuminate\Support\Carbon::instance($fechaCarga ?? now())->format('Y-m-d_H-i-s');
        $original = Archivos::limpiar(pathinfo($nombreOriginal, PATHINFO_FILENAME), 40);

        return [
            'salud-ocupacional/' . $persona,
            $persona . '_' . $fecha . '_concepto-' . $concepto->id . ($original !== '' ? '_' . $original : ''),
        ];
    }
}
