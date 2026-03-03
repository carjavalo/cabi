<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('horariosgim')) {
            return;
        }

        if (Schema::hasColumn('horariosgim', 'id')) {
            return; // already has id
        }

        // 1) Add nullable `id` column after identificacion
        Schema::table('horariosgim', function ($table) {
            $table->bigInteger('id')->unsigned()->nullable()->after('identificacion');
        });

        // 2) Populate `id` with sequential values ordered by identificacion (or created_at)
        DB::statement('SET @rownum = 0');
        DB::statement("UPDATE horariosgim SET id = (@rownum := @rownum + 1) ORDER BY COALESCE(identificacion, '')");

        // 3) Drop existing primary key (best-effort)
        try {
            DB::statement('ALTER TABLE horariosgim DROP PRIMARY KEY');
        } catch (\Throwable $e) {
            // ignore
        }

        // 4) Make `id` NOT NULL AUTO_INCREMENT PRIMARY KEY
        DB::statement('ALTER TABLE horariosgim MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
    }

    public function down()
    {
        if (!Schema::hasTable('horariosgim')) {
            return;
        }

        try {
            DB::statement('ALTER TABLE horariosgim DROP PRIMARY KEY');
        } catch (\Throwable $e) {
            // ignore
        }

        if (Schema::hasColumn('horariosgim', 'identificacion')) {
            try {
                DB::statement('ALTER TABLE horariosgim ADD PRIMARY KEY (identificacion)');
            } catch (\Throwable $e) {
                // ignore
            }
        }

        if (Schema::hasColumn('horariosgim', 'id')) {
            Schema::table('horariosgim', function ($table) {
                $table->dropColumn('id');
            });
        }
    }
};
