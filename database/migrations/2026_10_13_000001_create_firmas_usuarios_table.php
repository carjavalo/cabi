<?php

use App\Models\FirmaUsuario;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Firma registrada de los profesionales (p. ej. Médico SST) que se coloca
     * automáticamente en los conceptos médicos que emiten.
     */
    public function up(): void
    {
        FirmaUsuario::asegurarTabla();
    }

    public function down(): void
    {
        Schema::dropIfExists('firmas_usuarios');
    }
};
