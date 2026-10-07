<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 60)->unique();
                $table->string('descripcion', 255)->nullable();
                $table->boolean('activo')->default(true);
                $table->boolean('es_sistema')->default(false);
                $table->timestamps();
            });
        }

        // Roles base + roles que ya tienen asignados los usuarios
        Role::sembrar();
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
