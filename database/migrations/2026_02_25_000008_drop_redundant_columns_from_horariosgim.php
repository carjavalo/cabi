<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('horariosgim')) {
            return;
        }

        $cols = [];
        if (Schema::hasColumn('horariosgim', 'nombre')) $cols[] = 'nombre';
        if (Schema::hasColumn('horariosgim', 'primer_apellido')) $cols[] = 'primer_apellido';
        if (Schema::hasColumn('horariosgim', 'segundo_apellido')) $cols[] = 'segundo_apellido';
        if (Schema::hasColumn('horariosgim', 'servicio')) $cols[] = 'servicio';

        if (!empty($cols)) {
            Schema::table('horariosgim', function (Blueprint $table) use ($cols) {
                $table->dropColumn($cols);
            });
        }
    }

    public function down()
    {
        if (!Schema::hasTable('horariosgim')) {
            return;
        }

        if (!Schema::hasColumn('horariosgim', 'nombre')) {
            Schema::table('horariosgim', function (Blueprint $table) {
                $table->string('nombre')->nullable();
            });
        }
        if (!Schema::hasColumn('horariosgim', 'primer_apellido')) {
            Schema::table('horariosgim', function (Blueprint $table) {
                $table->string('primer_apellido')->nullable();
            });
        }
        if (!Schema::hasColumn('horariosgim', 'segundo_apellido')) {
            Schema::table('horariosgim', function (Blueprint $table) {
                $table->string('segundo_apellido')->nullable();
            });
        }
        if (!Schema::hasColumn('horariosgim', 'servicio')) {
            Schema::table('horariosgim', function (Blueprint $table) {
                $table->string('servicio')->nullable();
            });
        }
    }
};
