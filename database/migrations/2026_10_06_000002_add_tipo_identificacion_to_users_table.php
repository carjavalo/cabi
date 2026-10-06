<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Complementa la tabla users con el tipo de documento del paciente
     * (C.C., C.E., T.I., P.A., P.P.T.) que pide el paso 2 del Concepto Médico.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'tipo_identificacion')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('tipo_identificacion', 5)->nullable()->default('CC')->after('apellido2');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'tipo_identificacion')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('tipo_identificacion');
            });
        }
    }
};
