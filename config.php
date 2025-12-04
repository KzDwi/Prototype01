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
        // Jika tidak, import struktur tabel berita
        $sql = file_get_contents('database.sql');
        $pdo->exec($sql);
        echo "Tabel berita berhasil dibuat. ";
    }
    
    // Cek apakah tabel konten_website exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'konten_website'");
    $kontenTableExists = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$kontenTableExists) {
        // Jika tidak, buat tabel konten
        $pdo->exec("CREATE TABLE IF NOT EXISTS konten_website (
            id INT PRIMARY KEY AUTO_INCREMENT,
            section_key VARCHAR(100) NOT NULL,
            content_type VARCHAR(50) NOT NULL COMMENT 'hero_text, hero_subtext, pimpinan, visi, misi, layanan',
            content_data TEXT,
            display_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_section_content (section_key, content_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS gambar_konten (
            id INT PRIMARY KEY AUTO_INCREMENT,
            konten_id INT,
            gambar_type VARCHAR(50) NOT NULL COMMENT 'hero_slider, pimpinan_foto, layanan_icon',
            gambar_url VARCHAR(500),
            display_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (konten_id) REFERENCES konten_website(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        echo "Tabel konten berhasil dibuat. ";
    }
    
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Kembalikan koneksi PDO untuk digunakan di file lain
return $pdo;
?>