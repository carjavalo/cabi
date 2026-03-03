<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('inscripgym')) {
            return;
        }

        Schema::create('inscripgym', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('primer_apellido');
            $table->string('segundo_apellido')->nullable();
            $table->string('identificacion')->nullable();
            $table->integer('edad')->nullable();
            $table->string('celular')->nullable();
            $table->string('tipo_vinculacion')->nullable();
            $table->string('servicio_unidad')->nullable();
            $table->string('contacto_emergencia')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inscripgym');
    }
};
