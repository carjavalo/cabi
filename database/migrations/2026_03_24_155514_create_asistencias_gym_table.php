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
        Schema::create('asistencias_gym', function (Blueprint $table) {
            $table->id();
            $table->string('identificacion');
            $table->date('fecha');
            $table->string('franja'); // e.g. "17:00 a 18:00"
            $table->boolean('asistio')->default(false);
            $table->timestamps();
            
            // Un usuario solo puede tener un registro de asistencia por fecha y franja especifica
            $table->unique(['identificacion', 'fecha', 'franja']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias_gym');
    }
};
