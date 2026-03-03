<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Renombrar columnas para estandarizar nombres
        if (Schema::hasColumn('inscripgym', 'Nombre')) {
            Schema::table('inscripgym', function (Blueprint $table) {
                $table->renameColumn('Nombre', 'nombres');
            });
        }
        
        if (Schema::hasColumn('inscripgym', 'Apellido1')) {
            Schema::table('inscripgym', function (Blueprint $table) {
                $table->renameColumn('Apellido1', 'primer_apellido');
            });
        }
        
        if (Schema::hasColumn('inscripgym', 'Apellido2')) {
            Schema::table('inscripgym', function (Blueprint $table) {
                $table->renameColumn('Apellido2', 'segundo_apellido');
            });
        }
        
        if (Schema::hasColumn('inscripgym', 'tipoVinculacion')) {
            Schema::table('inscripgym', function (Blueprint $table) {
                $table->renameColumn('tipoVinculacion', 'tipo_vinculacion');
            });
        }
        
        if (Schema::hasColumn('inscripgym', 'Servicio')) {
            Schema::table('inscripgym', function (Blueprint $table) {
                $table->renameColumn('Servicio', 'servicio_unidad');
            });
        }
        
        if (Schema::hasColumn('inscripgym', 'emerconta')) {
            Schema::table('inscripgym', function (Blueprint $table) {
                $table->renameColumn('emerconta', 'contacto_emergencia');
            });
        }
        
        // Eliminar ndocumento si existe (ya tenemos identificacion)
        if (Schema::hasColumn('inscripgym', 'ndocumento')) {
            Schema::table('inscripgym', function (Blueprint $table) {
                $table->dropColumn('ndocumento');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir cambios
        if (Schema::hasColumn('inscripgym', 'nombres')) {
            Schema::table('inscripgym', function (Blueprint $table) {
                $table->renameColumn('nombres', 'Nombre');
            });
        }
        
        if (Schema::hasColumn('inscripgym', 'primer_apellido')) {
            Schema::table('inscripgym', function (Blueprint $table) {
                $table->renameColumn('primer_apellido', 'Apellido1');
            });
        }
        
        if (Schema::hasColumn('inscripgym', 'segundo_apellido')) {
            Schema::table('inscripgym', function (Blueprint $table) {
                $table->renameColumn('segundo_apellido', 'Apellido2');
            });
        }
        
        if (Schema::hasColumn('inscripgym', 'tipo_vinculacion')) {
            Schema::table('inscripgym', function (Blueprint $table) {
                $table->renameColumn('tipo_vinculacion', 'tipoVinculacion');
            });
        }
        
        if (Schema::hasColumn('inscripgym', 'servicio_unidad')) {
            Schema::table('inscripgym', function (Blueprint $table) {
                $table->renameColumn('servicio_unidad', 'Servicio');
            });
        }
        
        if (Schema::hasColumn('inscripgym', 'contacto_emergencia')) {
            Schema::table('inscripgym', function (Blueprint $table) {
                $table->renameColumn('contacto_emergencia', 'emerconta');
            });
        }
    }
};
