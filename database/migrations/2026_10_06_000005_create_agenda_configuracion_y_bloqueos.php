<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agenda Médica: configuración de la jornada y del tiempo de atención por
     * cita, bloqueos de horario y duración propia de cada cita (para que un
     * cambio de configuración no altere las citas ya agendadas).
     */
    public function up(): void
    {
        if (!Schema::hasTable('agenda_configuracion')) {
            Schema::create('agenda_configuracion', function (Blueprint $table) {
                $table->id();
                $table->unsignedSmallInteger('duracion_minutos')->default(30);
                $table->time('manana_inicio')->default('07:00:00');
                $table->time('manana_fin')->default('12:30:00');
                $table->time('tarde_inicio')->nullable()->default('14:00:00');
                $table->time('tarde_fin')->nullable()->default('16:00:00');
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
            DB::table('agenda_configuracion')->insert(['created_at' => now(), 'updated_at' => now()]);
        }

        if (!Schema::hasTable('agenda_bloqueos')) {
            Schema::create('agenda_bloqueos', function (Blueprint $table) {
                $table->id();
                $table->date('fecha');
                $table->time('hora_inicio');
                $table->time('hora_fin');
                $table->string('motivo', 150)->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->index('fecha');
            });
        }

        if (Schema::hasTable('agenda_citas') && !Schema::hasColumn('agenda_citas', 'duracion_minutos')) {
            Schema::table('agenda_citas', function (Blueprint $table) {
                $table->unsignedSmallInteger('duracion_minutos')->default(30)->after('hora');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('agenda_citas', 'duracion_minutos')) {
            Schema::table('agenda_citas', function (Blueprint $table) {
                $table->dropColumn('duracion_minutos');
            });
        }
        Schema::dropIfExists('agenda_bloqueos');
        Schema::dropIfExists('agenda_configuracion');
    }
};
