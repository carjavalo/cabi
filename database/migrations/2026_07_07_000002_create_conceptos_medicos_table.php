<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registro de cada atención/visita de Salud Ocupacional.
     * Un trabajador (users) puede tener múltiples conceptos a lo largo del tiempo.
     */
    public function up(): void
    {
        Schema::create('conceptos_medicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('identificacion')->nullable()->index();

            // ── Paso 1: Datos de la atención ──
            $table->date('fecha_atencion');
            $table->time('hora_atencion')->nullable();
            $table->string('lugar_atencion')->nullable();
            $table->string('tipo_atencion', 30)->nullable();

            // ── Snapshot del paciente al momento de la atención ──
            $table->string('paciente_nombre')->nullable();
            $table->integer('edad')->nullable();
            $table->string('genero', 5)->nullable();

            // ── Paso 3: Datos laborales y de afiliación (por visita) ──
            $table->string('cargo_inicio')->nullable();
            $table->string('servicio')->nullable();
            $table->string('empleador')->nullable();
            $table->string('nit', 40)->nullable();
            $table->string('eps', 120)->nullable();
            $table->string('afp', 120)->nullable();
            $table->string('arl', 120)->nullable();

            // ── Paso 4: Historia clínica ocupacional ──
            $table->json('factores_riesgo')->nullable();
            $table->string('epp_usa', 5)->nullable();
            $table->string('epp_detalle')->nullable();
            $table->string('restricciones_previas', 5)->nullable();
            $table->string('restricciones_previas_detalle')->nullable();
            $table->string('motivo_consulta')->nullable();
            $table->text('estado_salud')->nullable();
            $table->json('antecedentes_ocupacionales')->nullable();
            $table->json('accidentes_laborales')->nullable();
            $table->json('enfermedad_laboral')->nullable();
            $table->text('antecedentes_familiares')->nullable();
            $table->json('antecedentes_personales')->nullable();
            $table->json('habitos')->nullable();
            $table->text('revision_sistemas')->nullable();
            $table->json('signos_vitales')->nullable();
            $table->string('aspecto_general')->nullable();
            $table->json('examen_sistemas')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('vigilancia_epidemiologica')->nullable();

            // ── Paso 5: Concepto médico ocupacional ──
            $table->string('concepto_resultado', 40)->nullable();

            // ── Paso 6: Recomendaciones y firma ──
            $table->text('recomendaciones')->nullable();
            $table->text('restricciones')->nullable();
            $table->text('sst')->nullable();
            $table->longText('firma')->nullable();
            $table->string('medico')->nullable();
            $table->string('registro')->nullable();

            // Auditoría: quién diligenció el concepto
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conceptos_medicos');
    }
};
