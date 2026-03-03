<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('inscripgym')) {
            return;
        }

        if (Schema::hasColumn('inscripgym', 'id')) {
            return; // already has id
        }

        // 1) Add nullable `id` column
        Schema::table('inscripgym', function ($table) {
            $table->bigInteger('id')->unsigned()->nullable()->after('identificacion');
        });

        // 2) Populate `id` with sequential values ordered by identificacion (current PK)
        DB::statement('SET @rownum = 0');
        DB::statement("UPDATE inscripgym SET id = (@rownum := @rownum + 1) ORDER BY COALESCE(identificacion, '')");

        // 3) Drop existing primary key (best-effort)
        try {
            DB::statement('ALTER TABLE inscripgym DROP PRIMARY KEY');
        } catch (\Throwable $e) {
            // ignore — may fail if no PK or DB-specific restrictions
        }

        // 4) Make `id` NOT NULL AUTO_INCREMENT PRIMARY KEY
        DB::statement('ALTER TABLE inscripgym MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
    }

    public function down()
    {
        if (!Schema::hasTable('inscripgym')) {
            return;
        }

        try {
            // Remove primary from id
            DB::statement('ALTER TABLE inscripgym DROP PRIMARY KEY');
        } catch (\Throwable $e) {
            // ignore
        }

        // Try to restore 'identificacion' as primary if it exists
        if (Schema::hasColumn('inscripgym', 'identificacion')) {
            try {
                DB::statement('ALTER TABLE inscripgym ADD PRIMARY KEY (identificacion)');
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // Drop the id column if present
        if (Schema::hasColumn('inscripgym', 'id')) {
            Schema::table('inscripgym', function ($table) {
                $table->dropColumn('id');
            });
        }
    }
};
