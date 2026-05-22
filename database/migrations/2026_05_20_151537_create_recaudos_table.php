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
        Schema::create('recaudos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_recibo')->unique();
            $table->date('fecha');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('valor', 15, 2)->default(0);
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('valor_parcial', 15, 2)->default(0);
            $table->string('concepto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recaudos');
    }
};
