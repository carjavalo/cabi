<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Consulta SST: un concepto médico no se borra ni se reescribe.
     * - "Eliminar" lo ANULA (queda en la base de datos con quién, cuándo y por qué).
     * - "Editar" corrige los campos del concepto y deja la auditoría de cada cambio.
     */
    public function up(): void
    {
        if (Schema::hasTable('conceptos_medicos') && !Schema::hasColumn('conceptos_medicos', 'anulado')) {
            Schema::table('conceptos_medicos', function (Blueprint $table) {
                $table->boolean('anulado')->default(false)->index();
                $table->timestamp('anulado_at')->nullable();
                $table->unsignedBigInteger('anulado_por')->nullable();
                $table->string('motivo_anulacion', 500)->nullable();
            });
        }

        if (!Schema::hasTable('concepto_correcciones')) {
            Schema::create('concepto_correcciones', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('concepto_medico_id')->index();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->json('cambios');                     // { campo: { antes, despues } }
                $table->string('motivo', 500);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('concepto_correcciones');
        if (Schema::hasColumn('conceptos_medicos', 'anulado')) {
            Schema::table('conceptos_medicos', function (Blueprint $table) {
                $table->dropColumn(['anulado', 'anulado_at', 'anulado_por', 'motivo_anulacion']);
            });
        }
    }
};
