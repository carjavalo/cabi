<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega el nivel jerárquico a los roles (1 = más alto) y lo inicializa en los roles base.
     */
    public function up(): void
    {
        // Role::asegurarTabla() agrega la columna y asigna los niveles de los roles base
        Role::asegurarTabla();
    }

    public function down(): void
    {
        if (Schema::hasColumn('roles', 'nivel')) {
            Schema::table('roles', fn ($t) => $t->dropColumn('nivel'));
        }
    }
};
