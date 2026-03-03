<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('horariosgim', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->date('fecha');
            $table->time('hora');
            $table->string('primer_apellido');
            $table->string('segundo_apellido')->nullable();
            $table->string('identificacion');
            $table->string('servicio');
            $table->string('dia_entrenamiento');
            $table->string('horario');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('horariosgim');
    }
};
