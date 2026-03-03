<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('encuestas')) return;

        Schema::table('encuestas', function (Blueprint $table) {
            if (!Schema::hasColumn('encuestas', 'titulo')) $table->string('titulo')->nullable()->after('id');
            if (!Schema::hasColumn('encuestas', 'codigo')) $table->string('codigo')->nullable()->after('titulo');
            if (!Schema::hasColumn('encuestas', 'estructura')) $table->longText('estructura')->nullable()->after('codigo');
            if (!Schema::hasColumn('encuestas', 'preguntas_count')) $table->integer('preguntas_count')->default(0)->after('estructura');
            if (!Schema::hasColumn('encuestas', 'activo')) $table->boolean('activo')->default(1)->after('preguntas_count');
            if (!Schema::hasColumn('encuestas', 'created_by')) $table->unsignedBigInteger('created_by')->nullable()->after('activo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('encuestas')) return;

        Schema::table('encuestas', function (Blueprint $table) {
            if (Schema::hasColumn('encuestas', 'created_by')) $table->dropColumn('created_by');
            if (Schema::hasColumn('encuestas', 'activo')) $table->dropColumn('activo');
            if (Schema::hasColumn('encuestas', 'preguntas_count')) $table->dropColumn('preguntas_count');
            if (Schema::hasColumn('encuestas', 'estructura')) $table->dropColumn('estructura');
            if (Schema::hasColumn('encuestas', 'codigo')) $table->dropColumn('codigo');
            if (Schema::hasColumn('encuestas', 'titulo')) $table->dropColumn('titulo');
        });
    }
};
