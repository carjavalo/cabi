<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de roles de usuario.
 *
 * La tabla `users` conserva el nombre del rol en la columna `role` (string), que es
 * lo que usa todo el aplicativo para los permisos (Auth::user()->role === '...').
 * Este catálogo solo define qué roles existen; la relación se hace por nombre.
 */
class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
        'es_sistema',
    ];

    protected $casts = [
        'activo'     => 'boolean',
        'es_sistema' => 'boolean',
    ];

    /**
     * Roles base del aplicativo. Sus nombres están referenciados en el código
     * (menú, controladores, middleware), por eso no se pueden renombrar,
     * desactivar ni eliminar desde Gestión de Roles.
     */
    public const SISTEMA = [
        'Super Admin'    => 'Acceso total al aplicativo.',
        'Administrador'  => 'Administra la configuración y los usuarios (excepto Super Admin).',
        'Coordinador'    => 'Gestiona capacitaciones y su asistencia.',
        'Operador'       => 'Opera los módulos de configuración con permisos limitados.',
        'Instructor GYM' => 'Gestiona las inscripciones y listados del gimnasio.',
        'Usuario'        => 'Usuario general del aplicativo.',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role', 'nombre');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Crea la tabla y los roles base si aún no existen (p. ej. en un servidor donde
     * no se ha ejecutado la migración). También registra cualquier rol que ya esté
     * asignado a usuarios y no exista en el catálogo.
     */
    public static function asegurarTabla(): bool
    {
        try {
            if (!Schema::hasTable('roles')) {
                DB::statement("
                    CREATE TABLE `roles` (
                        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        `nombre` varchar(60) NOT NULL,
                        `descripcion` varchar(255) DEFAULT NULL,
                        `activo` tinyint(1) NOT NULL DEFAULT 1,
                        `es_sistema` tinyint(1) NOT NULL DEFAULT 0,
                        `created_at` timestamp NULL DEFAULT NULL,
                        `updated_at` timestamp NULL DEFAULT NULL,
                        PRIMARY KEY (`id`),
                        UNIQUE KEY `roles_nombre_unique` (`nombre`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");
            }

            if (static::count() === 0) {
                static::sembrar();
            }

            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }

    /**
     * Inserta los roles base y los roles ya usados por los usuarios.
     */
    public static function sembrar(): void
    {
        $now = now();

        foreach (static::SISTEMA as $nombre => $descripcion) {
            if (!DB::table('roles')->where('nombre', $nombre)->exists()) {
                DB::table('roles')->insert([
                    'nombre' => $nombre, 'descripcion' => $descripcion,
                    'activo' => 1, 'es_sistema' => 1,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        if (Schema::hasColumn('users', 'role')) {
            $usados = DB::table('users')->whereNotNull('role')->where('role', '!=', '')
                ->distinct()->pluck('role');
            foreach ($usados as $nombre) {
                if (!DB::table('roles')->where('nombre', $nombre)->exists()) {
                    DB::table('roles')->insert([
                        'nombre' => $nombre, 'descripcion' => null,
                        'activo' => 1, 'es_sistema' => 0,
                        'created_at' => $now, 'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    /**
     * Nombres de roles disponibles para asignar a usuarios.
     * Si la tabla no está disponible, devuelve los roles base para no romper el CRUD de usuarios.
     *
     * @param bool $soloActivos
     * @return array<int,string>
     */
    public static function nombres(bool $soloActivos = true): array
    {
        if (static::asegurarTabla()) {
            $query = static::query()->orderByDesc('es_sistema')->orderBy('id');
            if ($soloActivos) {
                $query->activos();
            }
            $nombres = $query->pluck('nombre')->all();
            if (!empty($nombres)) {
                return $nombres;
            }
        }

        return array_keys(static::SISTEMA);
    }

    /**
     * Roles que el usuario autenticado puede asignar / ver según la jerarquía actual:
     * - Solo Super Admin puede asignar "Super Admin".
     * - Solo Super Admin y Administrador pueden asignar "Administrador".
     *
     * @param array<int,string> $nombres
     * @return array<int,string>
     */
    public static function filtrarPorJerarquia(array $nombres, ?string $rolActual): array
    {
        return array_values(array_filter($nombres, function ($nombre) use ($rolActual) {
            if ($nombre === 'Super Admin') {
                return $rolActual === 'Super Admin';
            }
            if ($nombre === 'Administrador') {
                return in_array($rolActual, ['Super Admin', 'Administrador'], true);
            }
            return true;
        }));
    }
}
