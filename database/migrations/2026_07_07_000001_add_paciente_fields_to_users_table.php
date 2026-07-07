<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Complementa la tabla users con los datos de identificación del paciente
     * requeridos por el módulo de Salud Ocupacional (Concepto Médico).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'grupo_sanguineo')) {
                $table->string('grupo_sanguineo', 5)->nullable()->after('escivil');
            }
            if (!Schema::hasColumn('users', 'lugar_nacimiento')) {
                $table->string('lugar_nacimiento', 120)->nullable()->after('grupo_sanguineo');
            }
            if (!Schema::hasColumn('users', 'numero_hijos')) {
                $table->unsignedSmallInteger('numero_hijos')->nullable()->after('lugar_nacimiento');
            }
            if (!Schema::hasColumn('users', 'escolaridad')) {
                $table->string('escolaridad', 40)->nullable()->after('numero_hijos');
            }
            if (!Schema::hasColumn('users', 'profesion')) {
                $table->string('profesion', 120)->nullable()->after('escolaridad');
            }
            if (!Schema::hasColumn('users', 'eps')) {
                $table->string('eps', 120)->nullable()->after('profesion');
            }
            if (!Schema::hasColumn('users', 'afp')) {
                $table->string('afp', 120)->nullable()->after('eps');
            }
            if (!Schema::hasColumn('users', 'arl')) {
                $table->string('arl', 120)->nullable()->after('afp');
            }
        });

        // Ampliar columnas cortas para admitir etiquetas completas del formulario
        // (p.ej. "Arrendada", "Unión libre") y permitir email nulo para pacientes
        // que no cuenten con correo. Se usa SQL directo para evitar depender de dbal.
        try {
            DB::statement("ALTER TABLE `users` MODIFY `estracto` VARCHAR(30) NULL");
            DB::statement("ALTER TABLE `users` MODIFY `tvivienda` VARCHAR(30) NULL");
            DB::statement("ALTER TABLE `users` MODIFY `escivil` VARCHAR(30) NULL");
            DB::statement("ALTER TABLE `users` MODIFY `email` VARCHAR(255) NULL");
        } catch (\Throwable $e) {
            // Si el motor no es MySQL o la columna ya tiene el tamaño, se ignora.
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['grupo_sanguineo', 'lugar_nacimiento', 'numero_hijos', 'escolaridad', 'profesion', 'eps', 'afp', 'arl'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
