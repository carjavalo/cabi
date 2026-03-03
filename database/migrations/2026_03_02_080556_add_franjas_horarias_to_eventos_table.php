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
        Schema::table('eventos', function (Blueprint $table) {
            $table->json('dias_activos')->nullable()->after('activo'); // Días de la semana activos
            $table->json('franjas_horarias')->nullable()->after('dias_activos'); // Franjas horarias por día
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn(['dias_activos', 'franjas_horarias']);
        });
    }
};
