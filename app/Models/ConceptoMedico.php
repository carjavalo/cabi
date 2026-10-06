<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConceptoMedico extends Model
{
    protected $table = 'conceptos_medicos';

    protected $fillable = [
        'user_id',
        'identificacion',
        'fecha_atencion',
        'hora_atencion',
        'lugar_atencion',
        'tipo_atencion',
        'enfasis',
        'paciente_nombre',
        'edad',
        'genero',
        'cargo_inicio',
        'servicio',
        'empleador',
        'nit',
        'eps',
        'afp',
        'arl',
        'factores_riesgo',
        'epp_usa',
        'epp_detalle',
        'restricciones_previas',
        'restricciones_previas_detalle',
        'motivo_consulta',
        'estado_salud',
        'antecedentes_ocupacionales',
        'accidentes_laborales',
        'enfermedad_laboral',
        'antecedentes_familiares',
        'antecedentes_personales',
        'habitos',
        'revision_sistemas',
        'signos_vitales',
        'aspecto_general',
        'examen_sistemas',
        'diagnostico',
        'vigilancia_epidemiologica',
        'concepto_resultado',
        'recomendaciones',
        'restricciones',
        'sst',
        'firma',
        'medico',
        'registro',
        'created_by',
    ];

    /**
     * Campos de la historia clínica ocupacional (paso 4). Se precargan desde la
     * última consulta del paciente para que el médico los actualice en la nueva.
     */
    public const CAMPOS_HISTORIA = [
        'factores_riesgo', 'epp_usa', 'epp_detalle', 'restricciones_previas',
        'restricciones_previas_detalle', 'motivo_consulta', 'estado_salud',
        'antecedentes_ocupacionales', 'accidentes_laborales', 'enfermedad_laboral',
        'antecedentes_familiares', 'antecedentes_personales', 'habitos',
        'revision_sistemas', 'signos_vitales', 'aspecto_general', 'examen_sistemas',
        'diagnostico', 'vigilancia_epidemiologica',
    ];

    /**
     * Cada consulta es un registro clínico histórico: una vez guardada no se
     * modifica ni se elimina (se usa para estadísticas y revisiones futuras).
     * Los cambios del médico se guardan siempre como una consulta nueva.
     */
    protected static function booted(): void
    {
        static::updating(function () {
            throw new \LogicException('Un concepto médico guardado no se puede modificar; registre una nueva consulta.');
        });
        static::deleting(function () {
            throw new \LogicException('Un concepto médico guardado no se puede eliminar.');
        });
    }

    protected function casts(): array
    {
        return [
            'fecha_atencion'              => 'date',
            'enfasis'                     => 'array',
            'factores_riesgo'             => 'array',
            'antecedentes_ocupacionales'  => 'array',
            'accidentes_laborales'        => 'array',
            'enfermedad_laboral'          => 'array',
            'antecedentes_personales'     => 'array',
            'habitos'                     => 'array',
            'signos_vitales'              => 'array',
            'examen_sistemas'             => 'array',
        ];
    }

    /**
     * Etiquetas legibles para el concepto emitido.
     */
    public const CONCEPTOS = [
        'apto'                => 'Apto',
        'apto_restricciones'  => 'Apto con restricciones',
        'con_restricciones'   => 'Con restricciones que impiden el desempeño del cargo',
    ];

    public const TIPOS = [
        'ingreso'     => 'Ingreso',
        'periodico'   => 'Periódico',
        'seguimiento' => 'Especializado',
        'egreso'      => 'Egreso',
        'brigada'     => 'Brigada',
    ];

    public const ENFASIS = [
        'osteomuscular'        => 'Osteomuscular',
        'alturas_confinados'   => 'Alturas y Espacios Confinados',
        'manipulacion_alimentos' => 'Manipulación de Alimentos',
        'radiacion_citotoxicos'  => 'Expuestos a Radiación y Citotóxicos',
    ];

    public function getEnfasisLabelAttribute(): string
    {
        $labels = array_map(fn ($k) => self::ENFASIS[$k] ?? $k, (array) ($this->enfasis ?? []));
        return $labels ? implode(', ', $labels) : '';
    }

    public function getConceptoLabelAttribute(): string
    {
        return self::CONCEPTOS[$this->concepto_resultado] ?? '—';
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo_atencion] ?? ($this->tipo_atencion ?: '—');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documentos()
    {
        return $this->hasMany(ConceptoDocumento::class);
    }
}
