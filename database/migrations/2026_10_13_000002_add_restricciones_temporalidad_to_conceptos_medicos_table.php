<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Temporalidad de las restricciones médicas ocupacionales (paso 6):
     * "1" a "12" meses o "indefinido" (más de 12 meses).
     */
    public function up(): void
    {
        if (!Schema::hasTable('conceptos_medicos') || Schema::hasColumn('conceptos_medicos', 'restricciones_temporalidad')) {
            return;
        }
        Schema::table('conceptos_medicos', function (Blueprint $table) {
            $table->string('restricciones_temporalidad', 12)->nullable()->after('restricciones');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('conceptos_medicos', 'restricciones_temporalidad')) {
            Schema::table('conceptos_medicos', function (Blueprint $table) {
                $table->dropColumn('restricciones_temporalidad');
            });
        }
    }
};
