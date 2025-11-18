<?php

// Muat file .env lokal (loader ringan) jika ada — untuk kemudahan dev lokal
if (!function_exists('load_simple_env')) {
    function load_simple_env(string $path): void {
        if (!is_readable($path)) {
            return;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            // Dukungan KEY=VALUE dan KEY="VALUE"
            if (strpos($line, '=') === false) {
                continue;
            }
            list($name, $value) = array_map('trim', explode('=', $line, 2));
            // Hapus kutipan di awal/akhir jika ada
            if ((strlen($value) >= 2) && ($value[0] === '"' && substr($value, -1) === '"' || $value[0] === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
            }
            // Jangan overwrite variabel environment yang sudah diset di sistem
            if (getenv($name) === false) {
                putenv("$name=$value");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

// Coba muat .env dari root proyek jika ada
$projectRoot = dirname(__DIR__);
load_simple_env($projectRoot . '/.env');

// Database configuration - gunakan variabel lingkungan jika tersedia
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'dgitech_db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log('Koneksi database gagal: ' . $e->getMessage());
    die('Koneksi database gagal. Silakan coba lagi nanti.');
}

?>
