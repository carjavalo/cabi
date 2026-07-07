<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Documentos de la EPS adjuntos a un concepto médico (PDF o imágenes).
     */
    public function up(): void
    {
        Schema::create('concepto_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concepto_medico_id')->constrained('conceptos_medicos')->cascadeOnDelete();
            $table->string('nombre_original');
            $table->string('ruta');
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('tamano')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concepto_documentos');
    }
};
