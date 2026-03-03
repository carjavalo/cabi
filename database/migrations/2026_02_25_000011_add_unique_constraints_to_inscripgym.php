<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('inscripgym')) return;

        // Check for duplicate identificacion (only if column exists)
        if (Schema::hasColumn('inscripgym', 'identificacion')) {
            $dupIdent = DB::select("SELECT identificacion, COUNT(*) AS c FROM inscripgym WHERE identificacion IS NOT NULL AND identificacion <> '' GROUP BY identificacion HAVING c>1");
            if (!empty($dupIdent)) {
                $vals = array_map(function($r){ return $r->identificacion; }, $dupIdent);
                throw new \Exception('No se pueden crear índices UNIQUE: existen identificaciones duplicadas en inscripgym: ' . implode(', ', $vals));
            }
        }

        // Check for duplicate correolec (if column exists)
        if (Schema::hasColumn('inscripgym', 'correolec')) {
            $dupEmail = DB::select("SELECT correolec, COUNT(*) AS c FROM inscripgym WHERE correolec IS NOT NULL AND correolec <> '' GROUP BY correolec HAVING c>1");
            if (!empty($dupEmail)) {
                $vals = array_map(function($r){ return $r->correolec; }, $dupEmail);
                throw new \Exception('No se pueden crear índices UNIQUE: existen correos duplicados en inscripgym: ' . implode(', ', $vals));
            }
        }

        // Add unique indexes (wrapped in try/catch to ignore if already present)
        try {
            if (Schema::hasColumn('inscripgym', 'identificacion')) {
                DB::statement('ALTER TABLE inscripgym ADD UNIQUE INDEX inscripgym_identificacion_unique (identificacion)');
            }
        } catch (\Exception $e) {
            // ignore if index exists or other non-fatal error
        }

        try {
            if (Schema::hasColumn('inscripgym', 'correolec')) {
                DB::statement('ALTER TABLE inscripgym ADD UNIQUE INDEX inscripgym_correolec_unique (correolec)');
            }
        } catch (\Exception $e) {
            // ignore
        }
    }

    public function down()
    {
        if (!Schema::hasTable('inscripgym')) return;
        try { DB::statement('ALTER TABLE inscripgym DROP INDEX inscripgym_identificacion_unique'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE inscripgym DROP INDEX inscripgym_correolec_unique'); } catch (\Exception $e) {}
    }
};
