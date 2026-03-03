<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Ensure inscripgym.id is AUTO_INCREMENT PRIMARY KEY
        if (Schema::hasTable('inscripgym') && Schema::hasColumn('inscripgym', 'id')) {
            try {
                DB::statement('ALTER TABLE inscripgym MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
            } catch (\Throwable $e) {
                // ignore; may already be correct or DB-specific restriction
            }
        }

        // Ensure horariosgim.id is AUTO_INCREMENT PRIMARY KEY
        if (Schema::hasTable('horariosgim') && Schema::hasColumn('horariosgim', 'id')) {
            try {
                DB::statement('ALTER TABLE horariosgim MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }

    public function down()
    {
        // No-op down: leave schema as-is
    }
};
