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

        $candidates = [
            'primer_apellido', 'segundo_apellido',
            'apellido1', 'apellido2',
            'apellido_1', 'apellido_2',
            'Apellio1', 'Apellio2', // possible typos
            'Apellido1', 'Apellido2'
        ];

        $toDrop = [];
        foreach ($candidates as $col) {
            if (Schema::hasColumn('horariosgim', $col)) {
                $toDrop[] = $col;
            }
        }

        if (!empty($toDrop)) {
            foreach ($toDrop as $col) {
                try {
                    // drop individually to avoid failing when some columns do not exist
                    Schema::table('horariosgim', function (Blueprint $table) use ($col) {
                        $table->dropColumn($col);
                    });
                } catch (\Throwable $e) {
                    // fallback: try raw SQL drop (ignore failures)
                    try {
                        \Illuminate\Support\Facades\DB::statement("ALTER TABLE horariosgim DROP COLUMN `" . $col . "`");
                    } catch (\Throwable $e2) {
                        // ignore
                    }
                }
            }
        }
    }

    public function down()
    {
        if (!Schema::hasTable('horariosgim')) {
            return;
        }

        // Recreate canonical columns if missing
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
    }
};
