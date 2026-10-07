<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agenda Médica de Salud Ocupacional: citas de los trabajadores de
     * vinculación Planta. Los registros no se eliminan (las citas se cancelan)
     * para conservar la trazabilidad y los reportes.
     */
    public function up(): void
    {
        if (Schema::hasTable('agenda_citas')) {
            return;
        }

        Schema::create('agenda_citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->date('fecha');
            $table->time('hora');
            $table->string('motivo', 30);                 // tipo de atención (ingreso, periódico, …)
            $table->json('enfasis')->nullable();          // énfasis del examen
            // Datos de contacto al momento de agendar (para reportes)
            $table->string('telefono', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('cargo', 100)->nullable();
            $table->string('asistencia', 20)->default('pendiente');
            $table->string('estado', 20)->default('programada');
            $table->string('ingreso_matriz', 20)->default('no');
            $table->text('observaciones')->nullable();
            $table->foreignId('concepto_medico_id')->nullable()->constrained('conceptos_medicos')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['fecha', 'hora']);
            $table->index(['user_id', 'fecha']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_citas');
    }
};
