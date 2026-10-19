<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Campos de texto libre del concepto que eran VARCHAR(255). Con el modo estricto
     * de MySQL, un texto más largo (p. ej. un motivo de consulta o un detalle de EPP
     * extenso) hacía fallar el guardado con error 500. Se amplían a TEXT.
     */
    private const CAMPOS = [
        'paciente_nombre', 'lugar_atencion', 'cargo_inicio', 'servicio', 'empleador',
        'epp_detalle', 'restricciones_previas_detalle', 'motivo_consulta',
        'aspecto_general', 'medico', 'registro',
    ];

    public function up(): void
    {
        if (!Schema::hasTable('conceptos_medicos')) {
            return;
        }
        Schema::table('conceptos_medicos', function (Blueprint $table) {
            foreach (self::CAMPOS as $campo) {
                if (Schema::hasColumn('conceptos_medicos', $campo)) {
                    $table->text($campo)->nullable()->change();
                }
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('conceptos_medicos')) {
            return;
        }
        Schema::table('conceptos_medicos', function (Blueprint $table) {
            foreach (self::CAMPOS as $campo) {
                if (Schema::hasColumn('conceptos_medicos', $campo)) {
                    $table->string($campo)->nullable()->change();
                }
            }
        });
    }
};
