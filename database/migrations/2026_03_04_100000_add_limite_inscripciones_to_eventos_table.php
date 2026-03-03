<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            // 0 = sin límite (ilimitado)
            $table->unsignedInteger('max_inscripciones_dia')->default(0)->after('inscritos');
            $table->unsignedInteger('max_inscripciones_semana')->default(0)->after('max_inscripciones_dia');
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn(['max_inscripciones_dia', 'max_inscripciones_semana']);
        });
    }
};
