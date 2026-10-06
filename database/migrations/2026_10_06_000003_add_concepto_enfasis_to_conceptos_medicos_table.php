<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Concepto por énfasis (manipulación de alimentos, alturas y espacios
     * confinados) y observaciones del concepto (egreso, brigada, etc.).
     */
    public function up(): void
    {
        if (!Schema::hasTable('conceptos_medicos')) {
            return;
        }
        Schema::table('conceptos_medicos', function (Blueprint $table) {
            if (!Schema::hasColumn('conceptos_medicos', 'concepto_enfasis')) {
                $table->json('concepto_enfasis')->nullable()->after('concepto_resultado');
            }
            if (!Schema::hasColumn('conceptos_medicos', 'observaciones_concepto')) {
                $table->text('observaciones_concepto')->nullable()->after('concepto_enfasis');
            }
        });
    }

    public function down(): void
    {
        Schema::table('conceptos_medicos', function (Blueprint $table) {
            foreach (['concepto_enfasis', 'observaciones_concepto'] as $col) {
                if (Schema::hasColumn('conceptos_medicos', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
