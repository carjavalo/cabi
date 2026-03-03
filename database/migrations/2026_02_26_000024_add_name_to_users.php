<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users','name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('name',255)->nullable()->after('id');
            });

            // Populate 'name' from 'nombre' if available
            DB::statement("UPDATE users SET name = nombre WHERE (name IS NULL OR name = '') AND (nombre IS NOT NULL)");
        }
    }

    public function down()
    {
        if (Schema::hasColumn('users','name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }
};
