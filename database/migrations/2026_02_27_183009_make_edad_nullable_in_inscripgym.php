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
        Schema::table('inscripgym', function (Blueprint $table) {
            $table->integer('edad')->nullable()->change();
            $table->string('celular', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscripgym', function (Blueprint $table) {
            $table->integer('edad')->nullable(false)->change();
            $table->string('celular', 50)->nullable(false)->change();
        });
    }
};
