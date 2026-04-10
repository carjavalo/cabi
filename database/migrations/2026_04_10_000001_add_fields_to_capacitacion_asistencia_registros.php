<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('capacitacion_asistencia_registros')) {
            Schema::table('capacitacion_asistencia_registros', function (Blueprint $table) {
                if (!Schema::hasColumn('capacitacion_asistencia_registros', 'apellidos')) {
                    $table->string('apellidos', 200)->nullable()->after('nombre');
                }
                if (!Schema::hasColumn('capacitacion_asistencia_registros', 'area_servicio')) {
                    $table->string('area_servicio', 200)->nullable()->after('tipo_contrato');
                }
                if (!Schema::hasColumn('capacitacion_asistencia_registros', 'cargo')) {
                    $table->string('cargo', 200)->nullable()->after('area_servicio');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('capacitacion_asistencia_registros')) {
            Schema::table('capacitacion_asistencia_registros', function (Blueprint $table) {
                $table->dropColumn(['apellidos', 'area_servicio', 'cargo']);
            });
        }
    }
};
