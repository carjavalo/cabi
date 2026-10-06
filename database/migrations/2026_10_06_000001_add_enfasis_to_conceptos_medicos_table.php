<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('conceptos_medicos') && !Schema::hasColumn('conceptos_medicos', 'enfasis')) {
            Schema::table('conceptos_medicos', function (Blueprint $table) {
                $table->json('enfasis')->nullable()->after('tipo_atencion');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('conceptos_medicos', 'enfasis')) {
            Schema::table('conceptos_medicos', function (Blueprint $table) {
                $table->dropColumn('enfasis');
            });
        }
    }
};
