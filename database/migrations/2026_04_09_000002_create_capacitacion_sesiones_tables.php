<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('capacitacion_sesiones')) {
            Schema::create('capacitacion_sesiones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('capacitacion_id');
            $table->string('token', 64)->unique();
            $table->date('fecha');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->json('citados_ids')->nullable();
            $table->timestamps();

            $table->foreign('capacitacion_id')->references('id')->on('capacitaciones')->cascadeOnDelete();
        });
        }

        if (!Schema::hasTable('capacitacion_asistencia_registros')) {
        Schema::create('capacitacion_asistencia_registros', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sesion_id');
            $table->unsignedBigInteger('capacitacion_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nombre', 200);
            $table->string('identificacion', 50);
            $table->string('tipo_contrato', 100)->nullable();
            $table->string('correo', 200)->nullable();
            $table->boolean('autoriza_firma')->default(false);
            $table->boolean('es_citado')->default(false);
            $table->timestamps();

            $table->foreign('sesion_id')->references('id')->on('capacitacion_sesiones')->cascadeOnDelete();
            $table->foreign('capacitacion_id')->references('id')->on('capacitaciones')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->unique(['sesion_id', 'identificacion']);
        });
        }

        // Crear sesión inicial para capacitaciones existentes (solo si no tienen)
        $caps = \App\Models\Capacitacion::all();
        foreach ($caps as $cap) {
            $citadosIds = \App\Models\CapacitacionAsistencia::where('capacitacion_id', $cap->id)
                ->pluck('user_id')->toArray();

            \Illuminate\Support\Facades\DB::table('capacitacion_sesiones')->insert([
                'capacitacion_id' => $cap->id,
                'token' => Str::random(32),
                'fecha' => $cap->fecha,
                'hora_inicio' => $cap->hora_inicio,
                'hora_fin' => $cap->hora_fin,
                'citados_ids' => json_encode($citadosIds),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('capacitacion_asistencia_registros');
        Schema::dropIfExists('capacitacion_sesiones');
    }
};
