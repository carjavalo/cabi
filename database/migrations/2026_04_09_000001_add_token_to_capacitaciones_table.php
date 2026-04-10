<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('capacitaciones', 'token')) {
            Schema::table('capacitaciones', function (Blueprint $table) {
                $table->string('token', 64)->nullable()->unique()->after('created_by');
            });
        }

        // Generar tokens para capacitaciones existentes
        $capacitaciones = \App\Models\Capacitacion::whereNull('token')->get();
        foreach ($capacitaciones as $cap) {
            $cap->update(['token' => Str::random(32)]);
        }
    }

    public function down(): void
    {
        Schema::table('capacitaciones', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
};
