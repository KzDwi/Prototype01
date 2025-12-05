<?php
// config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'disdikbud_paser');
define('DB_USER', 'root');
define('DB_PASS', '');

// Inisialisasi koneksi PDO
$pdo = null;

try {
    // Coba koneksi tanpa memilih database dulu
    $pdo_temp = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo_temp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Cek apakah database exists
    $stmt = $pdo_temp->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");
    $databaseExists = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$databaseExists) {
        // Buat database jika belum ada
        $pdo_temp->exec("CREATE DATABASE " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        error_log("Database " . DB_NAME . " berhasil dibuat.");
    }
    
    // Tutup koneksi sementara
    $pdo_temp = null;
    
    // Sekarang koneksi dengan database
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Cek apakah tabel berita exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'berita'");
    $tableExists = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tableExists) {
        // Jika tidak, buat tabel berita
        $sql = "CREATE TABLE IF NOT EXISTS berita (
            id INT PRIMARY KEY AUTO_INCREMENT,
            judul VARCHAR(255) NOT NULL,
            excerpt TEXT NOT NULL,
            konten LONGTEXT NOT NULL,
            kategori ENUM('Pendidikan', 'Kebudayaan', 'Pengumuman', 'Kegiatan') NOT NULL,
            gambar VARCHAR(255),
            thumbnail VARCHAR(255),
            penulis VARCHAR(100) NOT NULL,
            tanggal_publish DATE NOT NULL,
            dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            diupdate_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            status ENUM('draft', 'publish') DEFAULT 'publish',
            dibaca INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        
        // Insert data sample
        $sampleData = [
            [
                'judul' => 'Penerimaan Peserta Didik Baru Tahun 2024 Dibuka',
                'excerpt' => 'Pemerintah Kabupaten Paser membuka pendaftaran PPDB untuk tahun ajaran 2024/2025.',
                'konten' => '<p>Pemerintah Kabupaten Paser melalui Dinas Pendidikan dan Kebudayaan resmi membuka pendaftaran Penerimaan Peserta Didik Baru (PPDB) untuk tahun ajaran 2024/2025.</p>',
                'kategori' => 'Pendidikan',
                'gambar' => 'https://via.placeholder.com/800x400/003399/ffffff?text=PPDB+2024',
                'thumbnail' => 'https://via.placeholder.com/400x200/003399/ffffff?text=PPDB+2024',
                'penulis' => 'Admin Disdikbud',
                'tanggal_publish' => '2024-01-15',
                'dibaca' => 1245
            ],
            [
                'judul' => 'Festival Budaya Paser 2024 Sukses Digelar',
                'excerpt' => 'Festival budaya tahunan Kabupaten Paser berhasil menarik ribuan pengunjung.',
                'konten' => '<p>Festival Budaya Paser 2024 yang digelar di Lapangan Merdeka Tanah Grogot berhasil menyedot perhatian ribuan pengunjung.</p>',
                'kategori' => 'Kebudayaan',
                'gambar' => 'https://via.placeholder.com/800x400/002280/ffffff?text=Festival+Budaya+Paser',
                'thumbnail' => 'https://via.placeholder.com/400x200/002280/ffffff?text=Festival+Budaya',
                'penulis' => 'Admin Disdikbud',
                'tanggal_publish' => '2024-01-12',
                'dibaca' => 892
            ]
        ];
        
        foreach ($sampleData as $data) {
            $stmt = $pdo->prepare("INSERT INTO berita (judul, excerpt, konten, kategori, gambar, thumbnail, penulis, tanggal_publish, dibaca) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['judul'], $data['excerpt'], $data['konten'], $data['kategori'], 
                $data['gambar'], $data['thumbnail'], $data['penulis'], $data['tanggal_publish'], $data['dibaca']
            ]);
        }
        
        error_log("Tabel berita berhasil dibuat dan diisi dengan data sample.");
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
        
        error_log("Tabel konten berhasil dibuat.");
    }
    
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>