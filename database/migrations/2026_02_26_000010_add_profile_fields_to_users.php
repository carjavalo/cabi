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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'apellido1')) {
                $table->string('apellido1')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'apellido2')) {
                $table->string('apellido2')->nullable()->after('apellido1');
            }
            if (!Schema::hasColumn('users', 'identificacion')) {
                $table->string('identificacion')->nullable()->after('apellido2');
            }
            if (!Schema::hasColumn('users', 'servicio')) {
                $table->string('servicio')->nullable()->after('identificacion');
            }
            if (!Schema::hasColumn('users', 'tipo_vinculacion')) {
                $table->string('tipo_vinculacion')->nullable()->after('servicio');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'apellido1')) {
                $table->dropColumn('apellido1');
            }
            if (Schema::hasColumn('users', 'apellido2')) {
                $table->dropColumn('apellido2');
            }
            if (Schema::hasColumn('users', 'identificacion')) {
                $table->dropColumn('identificacion');
            }
            if (Schema::hasColumn('users', 'servicio')) {
                $table->dropColumn('servicio');
            }
            if (Schema::hasColumn('users', 'tipo_vinculacion')) {
                $table->dropColumn('tipo_vinculacion');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
