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
        Schema::table('publicidad', function (Blueprint $table) {
            $table->string('tag', 100)->nullable()->after('titulo');
            $table->string('seccion_titulo', 255)->nullable()->after('link');
            $table->string('seccion_subtitulo', 255)->nullable()->after('seccion_titulo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publicidad', function (Blueprint $table) {
            $table->dropColumn(['tag', 'seccion_titulo', 'seccion_subtitulo']);
        });
    }
};
