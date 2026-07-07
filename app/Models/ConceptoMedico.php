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

    protected function casts(): array
    {
        return [
            'fecha_atencion'              => 'date',
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
        'seguimiento' => 'Seguimiento',
        'egreso'      => 'Egreso',
        'brigada'     => 'Brigada',
    ];

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
