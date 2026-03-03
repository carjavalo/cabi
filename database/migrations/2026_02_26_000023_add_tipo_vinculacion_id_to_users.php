<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_vinculacion_id')->nullable()->after('tipo_vinculacion');
        });

        // Map existing textual tipo_vinculacion to vinculaciones.id where names match
        DB::statement("UPDATE users u JOIN vinculaciones v ON TRIM(u.tipo_vinculacion) = TRIM(v.nombre) SET u.tipo_vinculacion_id = v.id");
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tipo_vinculacion_id');
        });
    }
};
