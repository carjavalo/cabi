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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'genero')) {
                $table->string('genero', 1)->nullable()->after('cargo');
            }
            if (!Schema::hasColumn('users', 'edad')) {
                $table->integer('edad')->nullable()->after('genero');
            }
            if (!Schema::hasColumn('users', 'fnacimiento')) {
                $table->date('fnacimiento')->nullable()->after('edad');
            }
            if (!Schema::hasColumn('users', 'contacto')) {
                $table->string('contacto', 20)->nullable()->after('fnacimiento');
            }
            if (!Schema::hasColumn('users', 'direccionr')) {
                $table->string('direccionr', 100)->nullable()->after('contacto');
            }
            if (!Schema::hasColumn('users', 'estracto')) {
                $table->string('estracto', 5)->nullable()->after('direccionr');
            }
            if (!Schema::hasColumn('users', 'tvivienda')) {
                $table->string('tvivienda', 5)->nullable()->after('estracto');
            }
            if (!Schema::hasColumn('users', 'escivil')) {
                $table->string('escivil', 5)->nullable()->after('tvivienda');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['genero', 'edad', 'fnacimiento', 'contacto', 'direccionr', 'estracto', 'tvivienda', 'escivil'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
