<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Almacenamiento de archivos del aplicativo.
 *
 * - Con las credenciales de AWS en .env, todo se guarda en Amazon S3 dentro de la carpeta
 *   "talentoHumanoHUV" del bucket (config/filesystems.php → disks.s3.root), de forma privada.
 *   Los archivos se abren con enlaces temporales firmados.
 * - Sin credenciales se usa el disco local "public", para que el aplicativo siga funcionando.
 *
 * En la base de datos se guarda una referencia "disco:ruta" (p. ej. "s3:salud-ocupacional/…").
 * También se reconocen las rutas antiguas: archivos en public/img/… o en storage/app/public/….
 */
class Archivos
{
    /** Minutos de vigencia de los enlaces temporales de S3. */
    public const MINUTOS_ENLACE = 30;

    /** Ejecutables y páginas web: no se aceptan como adjuntos (todo lo demás sí). */
    public const EXTENSIONES_BLOQUEADAS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'pht', 'phps', 'inc',
        'exe', 'msi', 'msp', 'bat', 'cmd', 'com', 'scr', 'pif', 'cpl', 'dll', 'sys', 'lnk', 'reg',
        'sh', 'bash', 'ps1', 'psm1', 'vbs', 'vbe', 'js', 'mjs', 'jse', 'wsf', 'wsh', 'hta', 'jar',
        'html', 'htm', 'xhtml', 'shtml', 'asp', 'aspx', 'jsp', 'cgi', 'pl', 'py', 'htaccess', 'htpasswd',
    ];

    /** Formatos que el navegador podría interpretar como página: siempre se descargan. */
    private const SOLO_DESCARGA = ['svg', 'svgz', 'xml', 'xsl'];

    public static function s3Configurado(): bool
    {
        $c = (array) config('filesystems.disks.s3', []);
        return filled($c['bucket'] ?? null) && filled($c['key'] ?? null) && filled($c['secret'] ?? null);
    }

    /** Disco donde se guardan los archivos nuevos. */
    public static function disco(): string
    {
        return self::s3Configurado() ? 's3' : 'public';
    }

    // ------------------------------------------------------------------
    // Guardar
    // ------------------------------------------------------------------

    /**
     * Guarda un archivo subido y devuelve su referencia ("disco:ruta").
     *
     * @param string $carpeta    Subcarpeta dentro de talentoHumanoHUV (p. ej. "salud-ocupacional/16495648_Juan_Perez").
     * @param string $nombreBase Nombre del archivo sin extensión; se normaliza (sin tildes ni símbolos).
     */
    public static function guardar(UploadedFile $archivo, string $carpeta, string $nombreBase): string
    {
        $disco = self::disco();
        $carpeta = self::carpeta($carpeta);
        $nombre = self::nombreLibre($disco, $carpeta, $nombreBase, self::extension($archivo));
        $ruta = Storage::disk($disco)->putFileAs($carpeta, $archivo, $nombre, self::opciones(self::mime($archivo)));
        if (!$ruta) {
            throw new \RuntimeException('No fue posible guardar el archivo "' . $archivo->getClientOriginalName() . '".');
        }
        return $disco . ':' . $ruta;
    }

    /** Guarda un contenido (bytes) y devuelve su referencia ("disco:ruta"). */
    public static function guardarContenido(string $contenido, string $carpeta, string $nombreBase, string $extension, ?string $mime = null, ?string $disco = null): string
    {
        $disco = $disco ?: self::disco();
        $carpeta = self::carpeta($carpeta);
        $ruta = $carpeta . '/' . self::nombreLibre($disco, $carpeta, $nombreBase, $extension);
        if (!Storage::disk($disco)->put($ruta, $contenido, self::opciones($mime))) {
            throw new \RuntimeException('No fue posible guardar el archivo "' . $ruta . '".');
        }
        return $disco . ':' . $ruta;
    }

    // ------------------------------------------------------------------
    // Leer, enlazar y eliminar
    // ------------------------------------------------------------------

    /**
     * Separa una referencia en [disco, ruta].
     * Disco: s3 | public (storage/app/public) | local | app (carpeta public/ del aplicativo) | url (enlace externo)
     *        | dato (imagen guardada en la base como data URL).
     *
     * @return array{0:string,1:string}
     */
    public static function partes(string $referencia): array
    {
        if (str_starts_with($referencia, 'data:')) {
            return ['dato', $referencia];   // imagen guardada en la base (data URL)
        }
        if (preg_match('#^(s3|public|local):(.+)$#s', $referencia, $m)) {
            return [$m[1], $m[2]];
        }

        $ruta = $referencia;
        if (preg_match('#^https?://#i', $ruta)) {
            $path = ltrim((string) parse_url($ruta, PHP_URL_PATH), '/');
            if (!preg_match('#(^|/)(img|storage)/#', $path)) {
                return ['url', $referencia];
            }
            // Quita el dominio y la subcarpeta del aplicativo (p. ej. "CABI/public/") antes de img/ o storage/
            $ruta = preg_replace('#^(.*?/)?(?=(img|storage)/)#', '', $path);
        }
        $ruta = ltrim($ruta, '/');

        if (str_starts_with($ruta, 'storage/')) {
            return ['public', substr($ruta, 8)];
        }
        if (str_starts_with($ruta, 'img/')) {
            return ['app', $ruta];
        }
        return ['public', $ruta];
    }

    public static function esS3(?string $referencia): bool
    {
        return filled($referencia) && str_starts_with($referencia, 's3:');
    }

    /**
     * Enlace para abrir el archivo. En S3 es un enlace temporal firmado (MINUTOS_ENLACE).
     *
     * @param string|null $nombre    Nombre con el que se muestra o descarga (p. ej. el nombre original).
     * @param bool        $descargar true = descargar; false = abrir en el navegador si es posible.
     */
    public static function url(?string $referencia, ?string $nombre = null, bool $descargar = false): ?string
    {
        if (blank($referencia)) {
            return null;
        }
        [$disco, $ruta] = self::partes($referencia);
        try {
            if ($disco === 'url' || $disco === 'dato') {
                return $ruta;
            }
            if ($disco === 'app') {
                return asset($ruta);
            }
            if ($disco === 'public') {
                return asset('storage/' . $ruta);
            }
            if ($disco === 's3') {
                $nombre = $nombre ?: basename($ruta);
                $descargar = $descargar || in_array(strtolower(pathinfo($nombre, PATHINFO_EXTENSION)), self::SOLO_DESCARGA, true);
                return Storage::disk('s3')->temporaryUrl($ruta, now()->addMinutes(self::MINUTOS_ENLACE), [
                    'ResponseContentDisposition' => self::disposicion($nombre, $descargar),
                ]);
            }
            return Storage::disk($disco)->url($ruta);
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /** Contenido (bytes) del archivo, o null si no existe o no se pudo leer. */
    public static function contenido(?string $referencia): ?string
    {
        if (blank($referencia)) {
            return null;
        }
        [$disco, $ruta] = self::partes($referencia);
        try {
            if ($disco === 'dato') {
                $coma = strpos($ruta, ',');
                return $coma === false ? null : (base64_decode(substr($ruta, $coma + 1), true) ?: null);
            }
            if ($disco === 'app') {
                $archivo = public_path($ruta);
                return is_file($archivo) ? (string) file_get_contents($archivo) : null;
            }
            if ($disco === 'url') {
                return null;
            }
            return Storage::disk($disco)->exists($ruta) ? Storage::disk($disco)->get($ruta) : null;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /** Elimina el archivo. En la carpeta public/ solo se eliminan archivos subidos (img/publicidad, img/eventos). */
    public static function eliminar(?string $referencia): void
    {
        if (blank($referencia)) {
            return;
        }
        [$disco, $ruta] = self::partes($referencia);
        try {
            if ($disco === 'app') {
                if (preg_match('#^img/(publicidad|eventos)/[^/]+$#', $ruta) && is_file(public_path($ruta))) {
                    @unlink(public_path($ruta));
                }
            } elseif (in_array($disco, ['s3', 'public', 'local'], true)) {
                Storage::disk($disco)->delete($ruta);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    // ------------------------------------------------------------------
    // Nombres y validación
    // ------------------------------------------------------------------

    /** "16495648_Juan_Carlos_Quinones_Arroyo": cédula y nombre para nombrar carpetas y archivos de una persona. */
    public static function nombrePersona(?string $cedula, ?string $nombre): string
    {
        $partes = array_filter([preg_replace('/[^A-Za-z0-9]/', '', Str::ascii((string) $cedula)), self::limpiar((string) $nombre, 70)]);
        return $partes ? implode('_', $partes) : 'sin_identificacion';
    }

    /** Texto apto para nombres de archivo: sin tildes, sin espacios ni símbolos. */
    public static function limpiar(string $texto, int $max = 80): string
    {
        $texto = preg_replace('/[^A-Za-z0-9.\-]+/', '_', Str::ascii(trim($texto)));
        $texto = trim(preg_replace('/_{2,}/', '_', $texto), '._-');
        return rtrim(mb_substr($texto, 0, $max), '._-');
    }

    /** Extensión del archivo subido (en minúscula); si no trae, se deduce de su contenido. */
    public static function extension(UploadedFile $archivo): string
    {
        $ext = strtolower((string) $archivo->getClientOriginalExtension());
        if ($ext === '' || !preg_match('/^[a-z0-9]{1,10}$/', $ext)) {
            $ext = strtolower((string) $archivo->guessExtension());
        }
        return $ext !== '' ? $ext : 'bin';
    }

    /**
     * Tipo MIME del archivo subido: el que detecta el servidor, salvo que sea genérico
     * (p. ej. los .docx/.xlsx se detectan como zip); en ese caso, el que informa el navegador.
     */
    public static function mime(UploadedFile $archivo): string
    {
        $servidor = (string) $archivo->getMimeType();
        $cliente = (string) $archivo->getClientMimeType();
        $genericos = ['', 'application/octet-stream', 'application/zip', 'application/x-zip-compressed', 'text/plain',
            'application/CDFV2', 'application/x-ole-storage', 'application/encrypted'];
        $mime = in_array($servidor, $genericos, true) && $cliente !== '' ? $cliente : $servidor;
        // Nunca servir un adjunto como página web
        return $mime === '' || preg_match('/html|javascript|ecmascript/i', $mime) ? 'application/octet-stream' : $mime;
    }

    /** Regla de validación: acepta cualquier formato excepto ejecutables y páginas web. */
    public static function reglaFormatoPermitido(): \Closure
    {
        return function (string $atributo, $valor, \Closure $falla) {
            if ($valor instanceof UploadedFile && in_array(self::extension($valor), self::EXTENSIONES_BLOQUEADAS, true)) {
                $falla('El archivo "' . $valor->getClientOriginalName() . '" no está permitido por seguridad (programas o páginas web).');
            }
        };
    }

    // ------------------------------------------------------------------
    // Internos
    // ------------------------------------------------------------------

    private static function carpeta(string $carpeta): string
    {
        $segmentos = array_filter(array_map(fn ($s) => self::limpiar($s, 120), explode('/', $carpeta)));
        return $segmentos ? implode('/', $segmentos) : 'general';
    }

    /**
     * Nombre que aún no existe en la carpeta ("base.ext", "base_2.ext", "base_3.ext"…): un archivo
     * nunca reemplaza a otro. Si no se puede consultar el almacenamiento (p. ej. credenciales sin
     * permiso de listar), se agrega un sufijo aleatorio en lugar de arriesgar una sobrescritura.
     */
    private static function nombreLibre(string $disco, string $carpeta, string $base, string $extension): string
    {
        $base = self::limpiar($base, 180) ?: 'archivo';
        $extension = self::limpiar(strtolower($extension), 10);
        $sufijo = $extension !== '' ? '.' . $extension : '';
        $nombre = $base . $sufijo;
        try {
            for ($i = 2; Storage::disk($disco)->exists($carpeta . '/' . $nombre); $i++) {
                $nombre = $base . '_' . ($i < 500 ? $i : Str::lower(Str::random(8))) . $sufijo;
            }
        } catch (\Throwable $e) {
            report($e);
            $nombre = $base . '_' . Str::lower(Str::random(8)) . $sufijo;
        }
        return $nombre;
    }

    private static function opciones(?string $mime): array
    {
        return $mime ? ['ContentType' => $mime] : [];
    }

    /** Content-Disposition con el nombre del archivo (admite tildes). */
    private static function disposicion(string $nombre, bool $descargar): string
    {
        $ascii = str_replace(['"', '\\'], '', Str::ascii($nombre)) ?: 'archivo';
        return ($descargar ? 'attachment' : 'inline') . '; filename="' . $ascii . '"; filename*=UTF-8\'\'' . rawurlencode($nombre);
    }
}
