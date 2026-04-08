<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capacitaciones', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();
            $table->string('ubicacion', 200)->nullable();
            $table->string('instructor', 200)->nullable();
            $table->date('fecha');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->integer('capacidad_maxima')->default(0);
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('capacitacion_asistencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('capacitacion_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('asistio')->default(false);
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->foreign('capacitacion_id')->references('id')->on('capacitaciones')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['capacitacion_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capacitacion_asistencias');
        Schema::dropIfExists('capacitaciones');
    }
};
