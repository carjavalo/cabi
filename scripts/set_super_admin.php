<?php
$host = '127.0.0.1';
$db   = 'cabi';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$email = 'carjavalosistem@gmail.com';
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->prepare("UPDATE users SET role = :role, rol = :rol, nombre = :nombre WHERE email = :email");
    $stmt->execute([':role'=>'Super Admin', ':rol'=>'Super Admin', ':nombre'=>'Carlos Jairton', ':email'=>$email]);
    echo "Updated rows: " . $stmt->rowCount() . "\n";
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
}
