<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/**
 * Firma registrada de un profesional (p. ej. Médico SST). Se coloca automáticamente en el
 * paso "Recomendaciones y firma" de los conceptos médicos que emite ese profesional.
 *
 * Se guarda como imagen (data URL), igual que la firma que ya almacena cada concepto.
 */
class FirmaUsuario extends Model
{
    protected $table = 'firmas_usuarios';

    protected $fillable = ['user_id', 'imagen', 'archivo_nombre', 'archivo_tipo'];

    /** Tamaño máximo del archivo que se sube (KB). */
    public const MAX_KB = 5120;

    private static bool $tablaLista = false;

    /** Crea la tabla si aún no existe (p. ej. en un servidor donde no se ha ejecutado la migración). */
    public static function asegurarTabla(): bool
    {
        if (self::$tablaLista) {
            return true;
        }
        try {
            if (!Schema::hasTable('firmas_usuarios')) {
                Schema::create('firmas_usuarios', function (Blueprint $t) {
                    $t->id();
                    $t->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
                    $t->longText('imagen');                         // data:image/png;base64,...
                    $t->string('archivo_nombre', 255)->nullable();   // archivo que se subió (imagen o PDF)
                    $t->string('archivo_tipo', 10)->nullable();      // png | jpg | pdf
                    $t->timestamps();
                });
            }
            return self::$tablaLista = true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }

    /** Imagen de la firma registrada del usuario (data URL), o null si no tiene. */
    public static function imagenDe(?int $userId): ?string
    {
        if (!$userId || !self::asegurarTabla()) {
            return null;
        }
        return static::where('user_id', $userId)->value('imagen');
    }

    /**
     * Firma enviada en el formulario de usuario. El navegador la entrega ya procesada en
     * "firma_procesada" (recortada, con fondo transparente; los PDF convertidos a imagen).
     * Si ese paso no se pudo hacer, se acepta la imagen subida tal cual (no un PDF).
     *
     * @return array{imagen:string, archivo_nombre:?string, archivo_tipo:?string}|null  null = no se envió firma
     * @throws ValidationException
     */
    public static function desdeSolicitud(Request $request): ?array
    {
        $archivo = $request->file('firma_archivo');
        $procesada = trim((string) $request->input('firma_procesada', ''));
        $nombre = $archivo ? mb_substr($archivo->getClientOriginalName(), 0, 255) : null;
        $tipo = $archivo ? strtolower($archivo->getClientOriginalExtension()) : null;
        $tipo = $tipo === 'jpeg' ? 'jpg' : $tipo;

        if ($procesada !== '') {
            return ['imagen' => self::validarImagen($procesada), 'archivo_nombre' => $nombre, 'archivo_tipo' => $tipo ?: 'png'];
        }
        if (!$archivo) {
            return null;
        }
        if ($tipo === 'pdf') {
            throw ValidationException::withMessages([
                'firma_archivo' => 'No fue posible convertir el PDF de la firma. Intente nuevamente o suba la firma como imagen (PNG o JPG).',
            ]);
        }

        $contenido = (string) file_get_contents($archivo->getRealPath());
        $info = @getimagesizefromstring($contenido);
        if (!$info || !in_array($info['mime'], ['image/png', 'image/jpeg'], true)) {
            throw ValidationException::withMessages(['firma_archivo' => 'La firma debe ser una imagen PNG o JPG, o un archivo PDF.']);
        }
        return ['imagen' => 'data:' . $info['mime'] . ';base64,' . base64_encode($contenido), 'archivo_nombre' => $nombre, 'archivo_tipo' => $tipo];
    }

    /** Verifica que el data URL sea realmente una imagen PNG/JPG de tamaño razonable. */
    private static function validarImagen(string $dataUrl): string
    {
        $error = ValidationException::withMessages(['firma_archivo' => 'La imagen de la firma no es válida. Seleccione el archivo nuevamente.']);

        if (!preg_match('#^data:(image/(?:png|jpeg));base64,([A-Za-z0-9+/=\s]+)$#', $dataUrl, $m)) {
            throw $error;
        }
        $binario = base64_decode(preg_replace('/\s+/', '', $m[2]), true);
        $info = $binario !== false ? @getimagesizefromstring($binario) : false;
        if (!$info || $info['mime'] !== $m[1] || $info[0] > 3000 || $info[1] > 3000) {
            throw $error;
        }
        return 'data:' . $m[1] . ';base64,' . base64_encode($binario);
    }

    /** @param array{imagen:string, archivo_nombre:?string, archivo_tipo:?string} $firma */
    public static function guardar(int $userId, array $firma): bool
    {
        if (!self::asegurarTabla()) {
            return false;
        }
        static::updateOrCreate(['user_id' => $userId], $firma);
        return true;
    }

    public static function quitar(int $userId): void
    {
        if (self::asegurarTabla()) {
            static::where('user_id', $userId)->delete();
        }
    }
}
