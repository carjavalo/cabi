<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('inscripgym')) return;
        if (!Schema::hasColumn('inscripgym', 'correolec')) {
            Schema::table('inscripgym', function (Blueprint $table) {
                $table->string('correolec', 150)->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('inscripgym') && Schema::hasColumn('inscripgym', 'correolec')) {
            Schema::table('inscripgym', function (Blueprint $table) {
                $table->dropColumn('correolec');
            });
        }
    }
};
