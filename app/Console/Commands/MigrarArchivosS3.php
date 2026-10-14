<?php

namespace App\Console\Commands;

use App\Models\ConceptoDocumento;
use App\Models\FirmaUsuario;
use App\Support\Archivos;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Copia a Amazon S3 (carpeta talentoHumanoHUV) los archivos que se guardaron antes en el servidor
 * y actualiza sus referencias en la base de datos.
 *
 * - Se puede ejecutar varias veces: lo que ya está en S3 se omite.
 * - Cada archivo se verifica en S3 (tamaño) antes de actualizar la base.
 * - Por defecto NO borra los archivos del servidor (opción --eliminar-locales).
 * - Las firmas guardadas dentro de cada concepto médico se conservan en la base de datos:
 *   el concepto es un registro clínico que no se modifica.
 */
class MigrarArchivosS3 extends Command
{
    protected $signature = 'archivos:migrar-s3
                            {--simular : Muestra lo que se migraría, sin copiar ni modificar nada}
                            {--eliminar-locales : Borra del servidor los archivos ya copiados y verificados en S3}';

    protected $description = 'Migra a Amazon S3 (carpeta talentoHumanoHUV) los documentos, imágenes y firmas guardados en el servidor';

    /** @var array<string, array{ok:int, ya:int, sin:int, error:int}> */
    private array $resumen = [];

    /** @var array<int, string> Referencias locales que se borran al final (si se pidió) */
    private array $porEliminar = [];

    public function handle(): int
    {
        $this->resumen = [];
        $this->porEliminar = [];

        if (!Archivos::s3Configurado()) {
            $this->error('S3 no está configurado. Complete AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_DEFAULT_REGION y AWS_BUCKET en .env y ejecute: php artisan config:clear');
            return self::FAILURE;
        }

        $simular = (bool) $this->option('simular');
        $this->info(($simular ? '[SIMULACIÓN] ' : '') . 'Bucket: ' . config('filesystems.disks.s3.bucket')
            . ' · carpeta: ' . config('filesystems.disks.s3.root'));
        if (!$simular && !$this->probarConexion()) {
            return self::FAILURE;
        }

        $this->migrarDocumentosConceptos($simular);
        $this->migrarColumna('Imágenes de publicidad', 'publicidad', 'banner', 'publicidad', fn ($f) => 'publicidad_' . ($f->titulo ?: 'imagen') . '_' . $f->id, $simular);
        $this->migrarColumna('Imágenes de eventos', 'eventos', 'imagen', 'eventos', fn ($f) => 'evento_' . ($f->titulo ?: 'imagen') . '_' . $f->id, $simular);
        $this->migrarFirmas($simular);

        if (!$simular && $this->option('eliminar-locales')) {
            foreach (array_unique($this->porEliminar) as $referencia) {
                Archivos::eliminar($referencia);
            }
            $this->line(count(array_unique($this->porEliminar)) . ' archivo(s) local(es) eliminado(s) del servidor.');
        }

        $this->newLine();
        $this->table(['Tipo', $simular ? 'Por migrar' : 'Migrados', 'Ya estaban en S3', 'Sin archivo', 'Errores'],
            collect($this->resumen)->map(fn ($r, $tipo) => [$tipo, $r['ok'], $r['ya'], $r['sin'], $r['error']])->values()->all());
        $this->line('Las firmas guardadas dentro de cada concepto médico se conservan en la base de datos (registro clínico que no se modifica).');

        return collect($this->resumen)->sum('error') > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function probarConexion(): bool
    {
        try {
            Storage::disk('s3')->put('.prueba-conexion.txt', 'ok ' . now());
            Storage::disk('s3')->delete('.prueba-conexion.txt');
            return true;
        } catch (\Throwable $e) {
            $this->error('No fue posible escribir en S3: ' . $e->getMessage());
            return false;
        }
    }

    /** Documentos adjuntos de los conceptos: se renombran con la cédula, el nombre del paciente y la fecha y hora de carga. */
    private function migrarDocumentosConceptos(bool $simular): void
    {
        $tipo = 'Documentos de conceptos médicos';
        $this->contar($tipo, null);
        if (!Schema::hasTable('concepto_documentos')) {
            return;
        }

        ConceptoDocumento::with('conceptoMedico')->orderBy('id')->chunkById(100, function ($documentos) use ($tipo, $simular) {
            foreach ($documentos as $doc) {
                if (Archivos::esS3($doc->ruta)) {
                    $this->contar($tipo, 'ya');
                    continue;
                }
                $bytes = Archivos::contenido($doc->ruta);
                if ($bytes === null || !$doc->conceptoMedico) {
                    $this->warn("  Documento #{$doc->id}: no se encontró el archivo ({$doc->ruta}).");
                    $this->contar($tipo, 'sin');
                    continue;
                }
                // Con la fecha y hora en que se subió originalmente (no la de la migración)
                [$carpeta, $nombre] = ConceptoDocumento::ubicacion($doc->conceptoMedico, $doc->nombre_original, $doc->created_at);
                $ext = strtolower(pathinfo($doc->nombre_original, PATHINFO_EXTENSION) ?: pathinfo($doc->ruta, PATHINFO_EXTENSION)) ?: 'bin';
                $this->copiar($tipo, $doc->ruta, $bytes, $carpeta, $nombre, $ext, $doc->mime, $simular,
                    fn ($nueva) => DB::table('concepto_documentos')->where('id', $doc->id)->update(['ruta' => $nueva, 'updated_at' => now()]));
            }
        });
    }

    /** Imágenes guardadas en una columna (publicidad.banner, eventos.imagen). */
    private function migrarColumna(string $tipo, string $tabla, string $columna, string $carpeta, \Closure $nombre, bool $simular): void
    {
        $this->contar($tipo, null);
        if (!Schema::hasTable($tabla) || !Schema::hasColumn($tabla, $columna)) {
            return;
        }

        foreach (DB::table($tabla)->whereNotNull($columna)->where($columna, '!=', '')->orderBy('id')->get() as $fila) {
            $referencia = $fila->{$columna};
            if (Archivos::esS3($referencia)) {
                $this->contar($tipo, 'ya');
                continue;
            }
            $bytes = Archivos::contenido($referencia);
            if ($bytes === null) {
                $this->warn("  {$tabla} #{$fila->id}: no se encontró el archivo ({$referencia}).");
                $this->contar($tipo, 'sin');
                continue;
            }
            [, $ruta] = Archivos::partes($referencia);
            $ext = strtolower(pathinfo(parse_url($ruta, PHP_URL_PATH) ?: $ruta, PATHINFO_EXTENSION)) ?: 'jpg';
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes) ?: null;
            $this->copiar($tipo, $referencia, $bytes, $carpeta, $nombre($fila), $ext, $mime, $simular,
                fn ($nueva) => DB::table($tabla)->where('id', $fila->id)->update([$columna => $nueva]));
        }
    }

    /** Firmas registradas de los profesionales (data URL en la base → archivo en S3). */
    private function migrarFirmas(bool $simular): void
    {
        $tipo = 'Firmas de profesionales';
        $this->contar($tipo, null);
        if (!Schema::hasTable('firmas_usuarios')) {
            return;
        }

        foreach (DB::table('firmas_usuarios')->orderBy('id')->get() as $fila) {
            if (Archivos::esS3($fila->imagen)) {
                $this->contar($tipo, 'ya');
                continue;
            }
            if (!str_starts_with((string) $fila->imagen, 'data:')) {
                $this->contar($tipo, 'sin');
                continue;
            }
            if ($simular) {
                $this->line("  → firmas/ (usuario #{$fila->user_id})");
                $this->contar($tipo, 'ok');
                continue;
            }
            try {
                $nueva = FirmaUsuario::guardarArchivo((int) $fila->user_id, $fila->imagen,
                    \Illuminate\Support\Carbon::parse($fila->updated_at ?? $fila->created_at ?? now()));
                $this->verificar($nueva, strlen((string) Archivos::contenido($fila->imagen)));
                DB::table('firmas_usuarios')->where('id', $fila->id)->update(['imagen' => $nueva, 'updated_at' => now()]);
                $this->contar($tipo, 'ok');
            } catch (\Throwable $e) {
                $this->error("  Firma del usuario #{$fila->user_id}: " . $e->getMessage());
                $this->contar($tipo, 'error');
            }
        }
    }

    /** Sube el contenido a S3, lo verifica y actualiza la base. */
    private function copiar(string $tipo, string $origen, string $bytes, string $carpeta, string $nombre, string $ext, ?string $mime, bool $simular, \Closure $actualizar): void
    {
        if ($simular) {
            $this->line("  {$origen} → {$carpeta}/" . Archivos::limpiar($nombre, 180) . ".{$ext}");
            $this->contar($tipo, 'ok');
            return;
        }
        try {
            $nueva = Archivos::guardarContenido($bytes, $carpeta, $nombre, $ext, $mime, 's3');
            $this->verificar($nueva, strlen($bytes));
            $actualizar($nueva);
            $this->porEliminar[] = $origen;
            $this->contar($tipo, 'ok');
        } catch (\Throwable $e) {
            $this->error("  {$origen}: " . $e->getMessage());
            $this->contar($tipo, 'error');
        }
    }

    private function verificar(string $referencia, int $tamano): void
    {
        [, $ruta] = Archivos::partes($referencia);
        if (Storage::disk('s3')->size($ruta) !== $tamano) {
            throw new \RuntimeException('El archivo copiado a S3 no coincide en tamaño con el original.');
        }
    }

    private function contar(string $tipo, ?string $clave): void
    {
        $this->resumen[$tipo] ??= ['ok' => 0, 'ya' => 0, 'sin' => 0, 'error' => 0];
        if ($clave) {
            $this->resumen[$tipo][$clave]++;
        }
    }
}
