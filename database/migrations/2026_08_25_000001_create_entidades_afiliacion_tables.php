<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogos de seguridad social usados por el módulo de Salud Ocupacional
 * (paso 3 del concepto médico): EPS, AFP y ARL.
 *
 * Cada entidad vive en su propia tabla para poder administrarlas de forma
 * independiente desde el CRUD dinámico de la vista del concepto.
 */
return new class extends Migration
{
    /** Valores iniciales: los que antes estaban quemados en los datalist de la vista. */
    private const SEMILLA = [
        'eps' => [
            'Nueva EPS', 'EPS Sura', 'EPS Sanitas', 'Salud Total', 'Compensar', 'Famisanar',
            'Coosalud', 'Emssanar', 'Servicio Occidental de Salud (SOS)', 'Comfenalco Valle', 'Asmet Salud',
        ],
        'afps' => [
            'Porvenir', 'Protección', 'Colfondos', 'Skandia', 'Colpensiones',
        ],
        'arls' => [
            'ARL Sura', 'Positiva', 'Colmena Seguros', 'Seguros Bolívar', 'AXA Colpatria',
            'La Equidad Seguros', 'Mapfre',
        ],
    ];

    public function up(): void
    {
        foreach (array_keys(self::SEMILLA) as $tabla) {
            if (Schema::hasTable($tabla)) {
                continue;
            }
            Schema::create($tabla, function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 150)->unique();
                $table->string('codigo', 40)->nullable();
                $table->string('nit', 40)->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        $ahora = now();
        foreach (self::SEMILLA as $tabla => $nombres) {
            foreach ($nombres as $nombre) {
                if (DB::table($tabla)->where('nombre', $nombre)->exists()) {
                    continue;
                }
                DB::table($tabla)->insert([
                    'nombre'     => $nombre,
                    'activo'     => true,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);
            }
        }
    }

    public function down(): void
    {
        foreach (['arls', 'afps', 'eps'] as $tabla) {
            Schema::dropIfExists($tabla);
        }
    }
};
