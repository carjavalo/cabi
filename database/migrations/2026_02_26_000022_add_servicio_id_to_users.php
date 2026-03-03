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
            $table->unsignedBigInteger('servicio_id')->nullable()->after('servicio');
        });

        // Try to map existing textual servicio to servicios.id where names match
        // This uses a JOIN to avoid loading large sets into memory
        DB::statement("UPDATE users u JOIN servicios s ON TRIM(u.servicio) = TRIM(s.nombre) SET u.servicio_id = s.id");
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('servicio_id');
        });
    }
};
