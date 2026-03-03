<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inscripgym', function (Blueprint $table) {
            if (!Schema::hasColumn('inscripgym', 'autorizado')) {
                $table->boolean('autorizado')->default(false)->after('identificacion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscripgym', function (Blueprint $table) {
            if (Schema::hasColumn('inscripgym', 'autorizado')) {
                $table->dropColumn('autorizado');
            }
        });
    }
};
