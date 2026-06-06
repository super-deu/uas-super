<?php
/**
 * Koneksi database (PDO) untuk Feane.
 *
 * Konfigurasi dibaca dari ENVIRONMENT VARIABLE supaya kode yang sama
 * bisa jalan di XAMPP lokal MAUPUN di dalam Docker tanpa diubah.
 *   - Di XAMPP : pakai nilai fallback (127.0.0.1, root, password kosong)
 *   - Di Docker: set DB_HOST=db (nama service), DB_USER, DB_PASS lewat compose
 */

function getDB(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    // Baca dari ENV (dipakai di Docker). Fallback = XAMPP lokal (root tanpa password).
    $host = getenv('DATABASE_HOST') ?: '127.0.0.1';
    $port = getenv('DATABASE_PORT') ?: '3306';
    $name = getenv('DATABASE_NAME') ?: 'feane';
    $user = getenv('DATABASE_USER') ?: 'root';
    $pass = getenv('DATABASE_PASSWORD');
    if ($pass === false) {
        $pass = '';
    }

    $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        die('Koneksi database gagal: ' . htmlspecialchars($e->getMessage()));
    }

    return $pdo;
}
