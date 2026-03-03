<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evento_franjas', function (Blueprint $table) {
            $table->integer('dia_semana')->nullable()->after('evento_id')
                  ->comment('0=Domingo, 1=Lunes, 2=Martes, 3=Miércoles, 4=Jueves, 5=Viernes, 6=Sábado');
        });
    }

    public function down(): void
    {
        Schema::table('evento_franjas', function (Blueprint $table) {
            $table->dropColumn('dia_semana');
        });
    }
};
