<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('horariosgim')) return;
        if (!Schema::hasColumn('horariosgim', 'identificacion')) return;

        // Check for duplicate identificacion
        $dup = DB::select("SELECT identificacion, COUNT(*) AS c FROM horariosgim WHERE identificacion IS NOT NULL AND identificacion <> '' GROUP BY identificacion HAVING c>1");
        if (!empty($dup)) {
            $vals = array_map(function($r){ return $r->identificacion; }, $dup);
            throw new \Exception('No se pueden crear índices UNIQUE: existen identificaciones duplicadas en horariosgim: ' . implode(', ', $vals));
        }

        try {
            DB::statement('ALTER TABLE horariosgim ADD UNIQUE INDEX horariosgim_identificacion_unique (identificacion)');
        } catch (\Exception $e) {
            // ignore if already exists or other non-fatal error
        }
    }

    public function down()
    {
        if (!Schema::hasTable('horariosgim')) return;
        try { DB::statement('ALTER TABLE horariosgim DROP INDEX horariosgim_identificacion_unique'); } catch (\Exception $e) {}
    }
};
