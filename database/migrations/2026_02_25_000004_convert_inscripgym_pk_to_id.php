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

        // Determine current primary key columns
        $rows = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inscripgym' AND CONSTRAINT_NAME='PRIMARY'");
        $currentPk = array_map(fn($r) => $r->COLUMN_NAME, $rows);

        // If primary key already includes 'id' nothing to do
        if (in_array('id', $currentPk, true)) {
            // ensure id is AUTO_INCREMENT and primary
            try {
                DB::statement("ALTER TABLE inscripgym MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY");
            } catch (\Throwable $e) {
                // ignore failures
            }
            return;
        }

        // If there is an existing primary key on another column, drop it
        if (!empty($currentPk)) {
            try {
                DB::statement("ALTER TABLE inscripgym DROP PRIMARY KEY");
            } catch (\Throwable $e) {
                // ignore — will try next steps
            }
        }

        // Add id column as auto-increment primary if it does not exist
        if (!Schema::hasColumn('inscripgym', 'id')) {
            try {
                DB::statement("ALTER TABLE inscripgym ADD COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
            } catch (\Throwable $e) {
                // Fallback: add column then modify
                try {
                    DB::statement("ALTER TABLE inscripgym ADD COLUMN id BIGINT UNSIGNED NOT NULL FIRST");
                    DB::statement("ALTER TABLE inscripgym MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY");
                } catch (\Throwable $e2) {
                    // If everything fails, throw to notify user
                    throw $e2;
                }
            }
        } else {
            // Column exists but not primary/auto_increment — modify it
            try {
                DB::statement("ALTER TABLE inscripgym MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY");
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }

    public function down()
    {
        if (!Schema::hasTable('inscripgym')) {
            return;
        }

        // Attempt to remove primary from id and restore primaria on 'identificacion' if exists
        try {
            $rows = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='inscripgym' AND COLUMN_NAME='identificacion'");
            $hasIdent = !empty($rows);

            // If id is primary, drop primary key
            DB::statement("ALTER TABLE inscripgym DROP PRIMARY KEY");

            if ($hasIdent) {
                // set identificacion as primary
                DB::statement("ALTER TABLE inscripgym ADD PRIMARY KEY (identificacion)");
            }

            // drop id column if present
            if (Schema::hasColumn('inscripgym', 'id')) {
                Schema::table('inscripgym', function ($table) {
                    $table->dropColumn('id');
                });
            }
        } catch (\Throwable $e) {
            // ignore failures — manual intervention may be required
        }
    }
};
