<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabla de dias seleccionados para el evento
        Schema::create('evento_dias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->integer('dia_semana')->comment('0=Domingo, 1=Lunes, etc.');
            $table->timestamps();
        });

        // Tabla de franjas horarias del evento
        Schema::create('evento_franjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->integer('capacidad_maxima');
            $table->timestamps();
        });

        // Tabla de inscritos al evento
        Schema::create('evento_inscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->foreignId('evento_franja_id')->nullable()->constrained('evento_franjas')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('nombre_completo')->nullable();
            $table->string('identificacion')->nullable();
            $table->date('fecha_reserva')->nullable();
            $table->boolean('asistencia')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento_inscripciones');
        Schema::dropIfExists('evento_franjas');
        Schema::dropIfExists('evento_dias');
    }
};
