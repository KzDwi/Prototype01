<?php
// config.php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'disdikbud_paser';

try {
    // Coba koneksi tanpa memilih database dulu
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Cek apakah database exists
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database'");
    $databaseExists = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$databaseExists) {
        // Buat database jika belum ada
        $pdo->exec("CREATE DATABASE $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "Database berhasil dibuat. ";
    }
    
    // Sekarang koneksi dengan database
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Cek apakah tabel berita exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'berita'");
    $tableExists = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tableExists) {
        // Jika tidak, import struktur tabel
        $sql = file_get_contents('database.sql');
        $pdo->exec($sql);
        echo "Tabel berita berhasil dibuat. ";
    }
    
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>