<?php
/**
 * Script de corrección de correo para producción (cPanel)
 * 
 * INSTRUCCIONES:
 * 1. Subir este archivo a /public/ en el servidor
 * 2. Acceder desde el navegador: https://cabi.huv.gov.co/fix_mail_production.php
 * 3. Ejecutar las correcciones
 * 4. ELIMINAR ESTE ARCHIVO después de usar
 * 
 * SEGURIDAD: Protegido con token temporal
 */

// ── Token de seguridad (cambiar si se desea) ──
$SECURITY_TOKEN = 'fix_cabi_2026_mail';

// Verificar token
$providedToken = $_GET['token'] ?? '';
if ($providedToken !== $SECURITY_TOKEN) {
    http_response_code(403);
    echo "<h1>Acceso denegado</h1>";
    echo "<p>Uso: <code>fix_mail_production.php?token={$SECURITY_TOKEN}</code></p>";
    exit;
}

$basePath = dirname(__DIR__);
$envPath = $basePath . '/.env';
$results = [];
$action = $_GET['action'] ?? 'diagnostico';

// ══════════════════════════════════════════════
// FUNCIONES
// ══════════════════════════════════════════════

function getEnvValue(string $envContent, string $key): ?string {
    if (preg_match('/^' . preg_quote($key, '/') . '=(.*)$/m', $envContent, $matches)) {
        return trim($matches[1], '"\'');
    }
    return null;
}

function setEnvValue(string $envContent, string $key, string $value): string {
    $needsQuotes = str_contains($value, ' ') || str_contains($value, '#');
    $formattedValue = $needsQuotes ? '"' . $value . '"' : $value;
    
    if (preg_match('/^' . preg_quote($key, '/') . '=.*$/m', $envContent)) {
        return preg_replace(
            '/^' . preg_quote($key, '/') . '=.*$/m',
            $key . '=' . $formattedValue,
            $envContent
        );
    }
    return $envContent . "\n" . $key . '=' . $formattedValue;
}

function testSmtpConnection(string $host, int $port, int $timeout = 5): array {
    $conn = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if ($conn) {
        fclose($conn);
        return ['ok' => true, 'msg' => "Puerto $port ABIERTO"];
    }
    return ['ok' => false, 'msg' => "Puerto $port CERRADO: $errstr"];
}

function clearConfigCache(string $basePath): array {
    $results = [];
    
    // Eliminar archivos de caché de configuración
    $cacheFiles = [
        $basePath . '/bootstrap/cache/config.php',
        $basePath . '/bootstrap/cache/routes-v7.php',
        $basePath . '/bootstrap/cache/services.php',
        $basePath . '/bootstrap/cache/packages.php',
    ];
    
    foreach ($cacheFiles as $file) {
        if (file_exists($file)) {
            if (unlink($file)) {
                $results[] = "✅ Eliminado: " . basename($file);
            } else {
                $results[] = "❌ No se pudo eliminar: " . basename($file);
            }
        } else {
            $results[] = "ℹ️ No existía: " . basename($file);
        }
    }
    
    // Intentar ejecutar artisan config:clear
    $artisan = $basePath . '/artisan';
    if (file_exists($artisan)) {
        $output = [];
        exec("cd " . escapeshellarg($basePath) . " && php artisan config:clear 2>&1", $output, $exitCode);
        $results[] = ($exitCode === 0 ? "✅" : "⚠️") . " artisan config:clear: " . implode(' ', $output);
        
        $output2 = [];
        exec("cd " . escapeshellarg($basePath) . " && php artisan config:cache 2>&1", $output2, $exitCode2);
        $results[] = ($exitCode2 === 0 ? "✅" : "⚠️") . " artisan config:cache: " . implode(' ', $output2);
        
        $output3 = [];
        exec("cd " . escapeshellarg($basePath) . " && php artisan route:clear 2>&1", $output3, $exitCode3);
        $results[] = ($exitCode3 === 0 ? "✅" : "⚠️") . " artisan route:clear: " . implode(' ', $output3);
    }
    
    return $results;
}

function sendTestEmail(string $basePath, string $to): array {
    // Cargar Laravel bootstrap
    require $basePath . '/vendor/autoload.php';
    $app = require_once $basePath . '/bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    try {
        \Illuminate\Support\Facades\Mail::raw(
            "Este es un correo de prueba desde el sistema CABI.\n\nFecha: " . date('Y-m-d H:i:s') . "\nServidor: " . gethostname(),
            function ($message) use ($to) {
                $message->to($to)
                    ->subject('Test CABI - Verificación de correo funcional');
            }
        );
        return ['ok' => true, 'msg' => "Correo enviado exitosamente a $to"];
    } catch (\Exception $e) {
        return ['ok' => false, 'msg' => "Error: " . $e->getMessage()];
    }
}

// ══════════════════════════════════════════════
// VALORES CORRECTOS PARA PRODUCCIÓN
// ══════════════════════════════════════════════
$correctValues = [
    'APP_URL' => 'https://cabi.huv.gov.co',
    'MAIL_MAILER' => 'smtp',
    'MAIL_HOST' => 'smtp.gmail.com',
    'MAIL_PORT' => '587',
    'MAIL_SCHEME' => '',
    'MAIL_USERNAME' => 'oficinapic@correohuv.gov.co',
    'MAIL_PASSWORD' => 'ovap vyuy fkvt vaai',
    'MAIL_FROM_ADDRESS' => 'oficinapic@correohuv.gov.co',
    'MAIL_FROM_NAME' => 'Plan Institucional de Capacitación HUV',
];

// ══════════════════════════════════════════════
// ACCIONES
// ══════════════════════════════════════════════

if (!file_exists($envPath)) {
    die("<h1>❌ Archivo .env no encontrado en: $envPath</h1>");
}

$envContent = file_get_contents($envPath);

if ($action === 'corregir') {
    // Aplicar correcciones
    foreach ($correctValues as $key => $value) {
        $envContent = setEnvValue($envContent, $key, $value);
    }
    
    if (file_put_contents($envPath, $envContent)) {
        $results[] = ['ok' => true, 'msg' => '✅ Archivo .env ACTUALIZADO correctamente'];
    } else {
        $results[] = ['ok' => false, 'msg' => '❌ No se pudo escribir en .env (permisos?)'];
    }
    
    // Limpiar caché
    $cacheResults = clearConfigCache($basePath);
    foreach ($cacheResults as $cr) {
        $results[] = ['ok' => true, 'msg' => $cr];
    }
}

if ($action === 'test_email') {
    $testTo = $_GET['email'] ?? 'carjavalosistem@gmail.com';
    $cacheResults = clearConfigCache($basePath);
    $emailResult = sendTestEmail($basePath, $testTo);
    $results[] = $emailResult;
}

// ══════════════════════════════════════════════
// DIAGNÓSTICO (siempre se muestra)
// ══════════════════════════════════════════════
$envContent = file_get_contents($envPath); // Releer después de posibles cambios
$diagnostico = [];

$keysToCheck = ['APP_URL', 'MAIL_MAILER', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_SCHEME', 'MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME'];
foreach ($keysToCheck as $key) {
    $current = getEnvValue($envContent, $key);
    $expected = $correctValues[$key] ?? null;
    $isPassword = $key === 'MAIL_PASSWORD';
    $displayCurrent = $isPassword ? (empty($current) ? '***VACÍO***' : '***CONFIGURADO***') : ($current ?? '(no definido)');
    $displayExpected = $isPassword ? '***CONFIGURADO***' : ($expected ?? '-');
    
    $status = 'ℹ️';
    if ($expected !== null) {
        $status = ($current === $expected) ? '✅' : '❌';
    }
    
    $diagnostico[] = [
        'key' => $key,
        'current' => $displayCurrent,
        'expected' => $displayExpected,
        'status' => $status,
    ];
}

// Test de puertos SMTP
$smtp587 = testSmtpConnection('smtp.gmail.com', 587);
$smtp465 = testSmtpConnection('smtp.gmail.com', 465);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Fix Mail - CABI Producción</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; max-width: 900px; margin: 30px auto; padding: 20px; background: #f5f5f5; }
        .card { background: white; border-radius: 8px; padding: 25px; margin: 20px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { color: #2e3a75; }
        h2 { color: #333; border-bottom: 2px solid #2e3a75; padding-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #2e3a75; color: white; }
        .ok { color: #27ae60; font-weight: bold; }
        .fail { color: #e74c3c; font-weight: bold; }
        .btn { display: inline-block; padding: 12px 25px; border-radius: 6px; text-decoration: none; color: white; font-weight: bold; margin: 5px; }
        .btn-fix { background: #27ae60; }
        .btn-test { background: #2e3a75; }
        .btn-danger { background: #e74c3c; }
        .alert { padding: 15px; border-radius: 6px; margin: 10px 0; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔧 Fix Mail - CABI Producción</h1>
    <p>Servidor: <code><?= gethostname() ?></code> | PHP: <code><?= phpversion() ?></code> | Ruta: <code><?= $basePath ?></code></p>

    <?php if (!empty($results)): ?>
    <div class="card">
        <h2>📋 Resultado de la acción: <?= htmlspecialchars($action) ?></h2>
        <?php foreach ($results as $r): ?>
            <?php if (is_array($r)): ?>
                <div class="alert <?= ($r['ok'] ?? false) ? 'alert-success' : 'alert-error' ?>">
                    <?= htmlspecialchars($r['msg']) ?>
                </div>
            <?php else: ?>
                <div class="alert alert-success"><?= htmlspecialchars($r) ?></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="card">
        <h2>📊 Diagnóstico de Configuración .env</h2>
        <table>
            <tr><th>Variable</th><th>Valor Actual</th><th>Valor Esperado</th><th>Estado</th></tr>
            <?php foreach ($diagnostico as $d): ?>
            <tr>
                <td><code><?= htmlspecialchars($d['key']) ?></code></td>
                <td><?= htmlspecialchars($d['current']) ?></td>
                <td><?= htmlspecialchars($d['expected']) ?></td>
                <td><?= $d['status'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="card">
        <h2>🌐 Conectividad SMTP (smtp.gmail.com)</h2>
        <p>Puerto 587 (TLS): <span class="<?= $smtp587['ok'] ? 'ok' : 'fail' ?>"><?= $smtp587['msg'] ?></span></p>
        <p>Puerto 465 (SSL): <span class="<?= $smtp465['ok'] ? 'ok' : 'fail' ?>"><?= $smtp465['msg'] ?></span></p>
        <p>OpenSSL: <span class="<?= extension_loaded('openssl') ? 'ok' : 'fail' ?>"><?= extension_loaded('openssl') ? 'Disponible' : 'NO disponible' ?></span></p>
    </div>

    <div class="card">
        <h2>🚀 Acciones</h2>
        
        <div class="alert alert-warning">
            ⚠️ <strong>IMPORTANTE:</strong> Después de corregir el .env, elimine este archivo del servidor por seguridad.
        </div>

        <p>
            <a href="?token=<?= $SECURITY_TOKEN ?>&action=corregir" class="btn btn-fix" 
               onclick="return confirm('¿Corregir el archivo .env con los valores de producción correctos?')">
                ✅ Paso 1: Corregir .env
            </a>
        </p>
        <p>
            <a href="?token=<?= $SECURITY_TOKEN ?>&action=test_email&email=carjavalosistem@gmail.com" class="btn btn-test"
               onclick="return confirm('¿Enviar correo de prueba a carjavalosistem@gmail.com?')">
                📧 Paso 2: Enviar correo de prueba
            </a>
        </p>
        <p style="margin-top: 20px; color: #888; font-size: 13px;">
            <strong>Paso 3:</strong> Después de confirmar que funciona, <strong>ELIMINE ESTE ARCHIVO</strong> del servidor.
        </p>
    </div>

    <div class="card" style="background: #fff3cd;">
        <h2>📝 Checklist de verificación</h2>
        <ol>
            <li>Hacer clic en "Corregir .env" → verificar que todo quede en ✅</li>
            <li>Hacer clic en "Enviar correo de prueba" → verificar que llegue</li>
            <li>Registrar un usuario nuevo → verificar que llegue el correo de verificación</li>
            <li><strong>Eliminar este archivo</strong> del servidor (por seguridad)</li>
        </ol>
    </div>
</body>
</html>
