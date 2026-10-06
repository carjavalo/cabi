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
        'concepto_enfasis',
        'observaciones_concepto',
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

    /** Secciones del paso 4 que admiten varias filas (una por empresa/evento). */
    public const CAMPOS_FILAS = ['antecedentes_ocupacionales', 'accidentes_laborales', 'enfermedad_laboral'];

    /**
     * Normaliza una sección de filas a una lista de registros no vacíos.
     * Las consultas anteriores guardaban un solo registro (objeto); se
     * convierte a lista de una fila para mostrarlo igual que los nuevos.
     */
    public static function filas($valor): array
    {
        if (!is_array($valor) || $valor === []) {
            return [];
        }
        // Lista de filas (cualquier índice, p. ej. 0, 3, 7 tras eliminar filas) o un registro suelto
        $esLista = count(array_filter($valor, 'is_array')) === count($valor);
        $lista = $esLista ? array_values($valor) : [$valor];

        return array_values(array_filter(array_map(
            fn ($f) => is_array($f) ? array_map(fn ($v) => is_string($v) ? trim($v) : $v, $f) : null,
            $lista
        ), fn ($f) => is_array($f) && array_filter($f, fn ($v) => $v !== null && $v !== '')));
    }

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
            'concepto_enfasis'            => 'array',
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
     * Opciones del concepto emitido según el tipo de atención:
     * ingreso, periódico y especializado usan el grupo "general";
     * egreso y brigada tienen sus propias opciones.
     * Cada opción: [etiqueta, descripción, clase visual ok|warn|bad].
     */
    public const CONCEPTOS_GRUPOS = [
        'general' => [
            'apto'               => ['Apto', 'Sin restricciones para el desempeño del cargo.', 'ok'],
            'apto_restricciones' => ['Apto con restricciones', 'Puede desempeñar el cargo con recomendaciones.', 'warn'],
            'con_restricciones'  => ['Con restricciones que impiden el desempeño del cargo', 'No apto para el cargo evaluado.', 'bad'],
        ],
        'egreso' => [
            'egreso_sin_alteraciones' => ['Al momento del egreso sin alteraciones de salud', 'Registra las observaciones en Recomendaciones y firma.', 'ok'],
            'egreso_con_alteraciones' => ['Al momento del egreso con alteraciones de salud de origen común, continuar control por EPS', 'Registra las observaciones en Recomendaciones y firma.', 'warn'],
        ],
        'brigada' => [
            'apto_brigada'    => ['Apto para ingreso a la brigada', 'Registra las observaciones en Recomendaciones y firma.', 'ok'],
            'no_apto_brigada' => ['No apto para ingreso a la brigada', 'Registra las observaciones en Recomendaciones y firma.', 'bad'],
        ],
    ];

    /** Etiquetas legibles de todos los conceptos (cualquier grupo). */
    public const CONCEPTOS = [
        'apto'                    => 'Apto',
        'apto_restricciones'      => 'Apto con restricciones',
        'con_restricciones'       => 'Con restricciones que impiden el desempeño del cargo',
        'egreso_sin_alteraciones' => 'Al momento del egreso sin alteraciones de salud',
        'egreso_con_alteraciones' => 'Al momento del egreso con alteraciones de salud de origen común, continuar control por EPS',
        'apto_brigada'            => 'Apto para ingreso a la brigada',
        'no_apto_brigada'         => 'No apto para ingreso a la brigada',
    ];

    /**
     * Concepto adicional por énfasis (se anexa después del concepto general).
     * La clave coincide con la de ENFASIS; los énfasis sin entrada aquí no
     * tienen concepto adicional.
     */
    public const CONCEPTOS_ENFASIS = [
        'manipulacion_alimentos' => [
            'titulo'   => 'Énfasis en manipulación de alimentos',
            'opciones' => [
                'apto'                 => ['Apto para manipulación de alimentos', 'ok'],
                'no_apto'              => ['No apto para manipulación de alimentos', 'bad'],
                'no_apto_temporal'     => ['No apto temporalmente para manipulación de alimentos (hasta nueva valoración médica)', 'bad'],
                'apto_recomendaciones' => ['Apto con recomendaciones médicas temporales', 'warn'],
                'apto_seguimiento'     => ['Apto con seguimiento por patología dermatológica, gastrointestinal o infecciosa controlada', 'warn'],
            ],
        ],
        'alturas_confinados' => [
            'titulo'   => 'Énfasis alturas y espacios confinados',
            'opciones' => [
                'apto'    => ['Apto para trabajo en alturas', 'ok'],
                'no_apto' => ['No apto para trabajo en alturas', 'bad'],
            ],
        ],
    ];

    /** Campos de los pasos 5 y 6 que se precargan desde la consulta anterior. */
    public const CAMPOS_CONCEPTO = ['concepto_resultado', 'concepto_enfasis', 'observaciones_concepto', 'recomendaciones', 'restricciones', 'sst'];

    /** Grupo de opciones de concepto que corresponde a un tipo de atención. */
    public static function grupoConcepto(?string $tipo): string
    {
        return in_array($tipo, ['egreso', 'brigada'], true) ? $tipo : 'general';
    }

    /** Clase visual (ok|warn|bad) del concepto emitido. */
    public function getConceptoClaseAttribute(): string
    {
        foreach (self::CONCEPTOS_GRUPOS as $ops) {
            if (isset($ops[$this->concepto_resultado])) {
                return $ops[$this->concepto_resultado][2];
            }
        }
        return '';
    }

    /** Conceptos por énfasis emitidos: [[titulo, etiqueta, clase], ...]. */
    public function getConceptosEnfasisListaAttribute(): array
    {
        $out = [];
        foreach ((array) ($this->concepto_enfasis ?? []) as $enf => $op) {
            $cfg = self::CONCEPTOS_ENFASIS[$enf] ?? null;
            if ($cfg && isset($cfg['opciones'][$op])) {
                $out[] = [$cfg['titulo'], $cfg['opciones'][$op][0], $cfg['opciones'][$op][1]];
            }
        }
        return $out;
    }

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
