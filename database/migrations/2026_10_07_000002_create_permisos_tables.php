<?php

use App\Support\GestorPermisos;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea las tablas de Gestión de Permisos:
     *  - permisos:        catálogo de funciones del aplicativo (descubiertas de las rutas o manuales)
     *  - permiso_role:    permisos asignados a cada rol
     *  - role_asignable:  roles que cada rol puede asignar al crear/editar usuarios
     * y sincroniza el catálogo dejando a todos los roles con el acceso que tienen hoy.
     */
    public function up(): void
    {
        GestorPermisos::asegurarTablas();
        GestorPermisos::sincronizar();
    }

    public function down(): void
    {
        Schema::dropIfExists('role_asignable');
        Schema::dropIfExists('permiso_role');
        Schema::dropIfExists('permisos');
        if (Schema::hasColumn('roles', 'config_asignables')) {
            Schema::table('roles', fn ($t) => $t->dropColumn('config_asignables'));
        }
    }
};
