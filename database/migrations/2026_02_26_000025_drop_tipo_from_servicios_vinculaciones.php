<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('servicios') && Schema::hasColumn('servicios','tipo')) {
            Schema::table('servicios', function (Blueprint $table) {
                $table->dropColumn('tipo');
            });
        }

        if (Schema::hasTable('vinculaciones') && Schema::hasColumn('vinculaciones','tipo')) {
            Schema::table('vinculaciones', function (Blueprint $table) {
                $table->dropColumn('tipo');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('servicios') && !Schema::hasColumn('servicios','tipo')) {
            Schema::table('servicios', function (Blueprint $table) {
                $table->string('tipo',50)->nullable()->after('nombre');
            });
        }

        if (Schema::hasTable('vinculaciones') && !Schema::hasColumn('vinculaciones','tipo')) {
            Schema::table('vinculaciones', function (Blueprint $table) {
                $table->string('tipo',50)->nullable()->after('nombre');
            });
        }
    }
};
