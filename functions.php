<?php
// functions.php
require_once 'config.php';

function getDatabaseConnection() {
    global $pdo;
    
    // Jika koneksi belum ada, buat koneksi baru
    if (!isset($pdo)) {
        try {
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
        } catch (PDOException $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

function formatTanggalIndonesia($tanggal) {
    $bulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    
    $pecah = explode('-', $tanggal);
    return $pecah[2] . ' ' . $bulan[$pecah[1]] . ' ' . $pecah[0];
}

function ambilSemuaBerita($kategori = 'semua', $limit = null, $offset = 0) {
    global $pdo;
    
    $sql = "SELECT * FROM berita WHERE status = 'publish'";
    $params = [];
    
    if ($kategori !== 'semua') {
        $sql .= " AND kategori = ?";
        $params[] = $kategori;
    }
    
    $sql .= " ORDER BY tanggal_publish DESC, dibuat_pada DESC";
    
    if ($limit !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function hitungTotalBerita($kategori = 'semua') {
    global $pdo;
    
    $sql = "SELECT COUNT(*) as total FROM berita WHERE status = 'publish'";
    $params = [];
    
    if ($kategori !== 'semua') {
        $sql .= " AND kategori = ?";
        $params[] = $kategori;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

function tambahBerita($data) {
    global $pdo;
    
    $sql = "INSERT INTO berita (judul, excerpt, konten, kategori, gambar, thumbnail, penulis, tanggal_publish) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['judul'],
        $data['excerpt'],
        $data['konten'],
        $data['kategori'],
        $data['gambar'],
        $data['thumbnail'],
        $data['penulis'],
        $data['tanggal_publish']
    ]);
}

function uploadGambar($file) {
    $target_dir = "uploads/berita/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $file_name = uniqid() . '_' . time() . '.' . $file_extension;
    $target_file = $target_dir . $file_name;
    
    // Validasi file
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_extension, $allowed_types)) {
        throw new Exception('Hanya file JPG, JPEG, PNG, dan GIF yang diizinkan.');
    }
    
    if ($file["size"] > 5000000) { // 5MB
        throw new Exception('Ukuran file terlalu besar. Maksimal 5MB.');
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    } else {
        throw new Exception('Terjadi kesalahan saat mengupload file.');
    }
}

function ambilBeritaById($id) {
    global $pdo;
    
    $sql = "SELECT * FROM berita WHERE id = ? AND status = 'publish'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function cariBerita($keyword) {
    global $pdo;
    
    $sql = "SELECT * FROM berita WHERE status = 'publish' AND (judul LIKE ? OR excerpt LIKE ? OR konten LIKE ?) 
            ORDER BY tanggal_publish DESC";
    $stmt = $pdo->prepare($sql);
    $search_term = "%$keyword%";
    $stmt->execute([$search_term, $search_term, $search_term]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function ambilSemuaBeritaAdmin($limit = null, $offset = 0) {
    global $pdo;
    
    $sql = "SELECT * FROM berita ORDER BY dibuat_pada DESC";
    
    if ($limit !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function ambilBeritaByIdAdmin($id) {
    global $pdo;
    
    $sql = "SELECT * FROM berita WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateBerita($id, $data) {
    global $pdo;
    
    $sql = "UPDATE berita SET judul = ?, excerpt = ?, konten = ?, kategori = ?, gambar = ?, thumbnail = ?, tanggal_publish = ?, diupdate_pada = CURRENT_TIMESTAMP WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['judul'],
        $data['excerpt'],
        $data['konten'],
        $data['kategori'],
        $data['gambar'],
        $data['thumbnail'],
        $data['tanggal_publish'],
        $id
    ]);
}

function hapusBerita($id) {
    global $pdo;
    
    $sql = "DELETE FROM berita WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$id]);
}

function updateStatusBerita($id, $status) {
    global $pdo;
    
    $sql = "UPDATE berita SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$status, $id]);
}

function updateCounterDibaca($id) {
    global $pdo;
    
    $sql = "UPDATE berita SET dibaca = dibaca + 1 WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$id]);
}

// Fungsi untuk mendapatkan data FAQ dari database
function getFAQData() {
    try {
        $db = getDatabaseConnection();
        $faq_data = [];
        
        // Query untuk mendapatkan FAQ berdasarkan kategori
        $categories = ['informasi_umum', 'layanan_kesiswaan', 'guru_tenaga_kependidikan', 'ppdb'];
        
        foreach ($categories as $category) {
            // Cek apakah tabel faq ada
            $checkTable = $db->query("SHOW TABLES LIKE 'faq'")->fetch();
            
            if ($checkTable) {
                $stmt = $db->prepare("SELECT question, answer FROM faq WHERE category = ? ORDER BY display_order ASC");
                $stmt->execute([$category]);
                $faq_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($faq_items)) {
                    $faq_data[$category] = $faq_items;
                } else {
                    // Jika tidak ada data di database, gunakan data default
                    $faq_data[$category] = getFAQDefaultData($category);
                }
            } else {
                // Jika tabel tidak ada, gunakan data default
                $faq_data[$category] = getFAQDefaultData($category);
            }
        }
        
        // Jika semua array kosong, kembalikan data default lengkap
        if (empty(array_filter($faq_data))) {
            return getAllFAQDefaultData();
        }
        
        return $faq_data;
        
    } catch (Exception $e) {
        // Fallback ke data default jika database error
        error_log("FAQ Database Error: " . $e->getMessage());
        return getAllFAQDefaultData();
    }
}

// Fungsi untuk mendapatkan data default berdasarkan kategori
function getFAQDefaultData($category) {
    $all_default = getAllFAQDefaultData();
    return $all_default[$category] ?? [];
}

// Fungsi untuk mendapatkan semua data FAQ default
function getAllFAQDefaultData() {
    return [
        'informasi_umum' => [
            [
                'question' => 'Dimana alamat kantor Dinas Pendidikan Kabupaten Paser?',
                'answer' => 'Kantor kami beralamat di Jl. Jenderal Sudirman No. 27, Tana Paser, Kabupaten Paser, Kalimantan Timur. Jam operasional pelayanan adalah Senin – Jumat, pukul 08.00 – 15.00.'
            ],
            [
                'question' => 'Bagaimana cara menghubungi Dinas Pendidikan?',
                'answer' => 'Anda dapat menghubungi kami melalui telepon di (0543) 21023 atau mengirim email ke <a href="mailto:disdik@paserkab.go.id" style="color: #003399; text-decoration: underline;">disdik@paserkab.go.id</a>. Untuk pengaduan, kami sarankan menggunakan kanal pada halaman \'Layanan Publik\' kami.'
            ]
        ],
        'layanan_kesiswaan' => [
            [
                'question' => 'Bagaimana prosedur legalisir ijazah yang hilang atau rusak?',
                'answer' => '<p>Jika ijazah asli hilang atau rusak, Anda tidak bisa melakukan legalisir. Sebagai gantinya, Anda dapat mengurus <strong>Surat Keterangan Pengganti Ijazah (SKPI)</strong>. Prosedurnya adalah:</p>
                <ol style="margin-left: 20px; line-height: 1.6;">
                    <li>Membuat Surat Keterangan Kehilangan dari Kepolisian.</li>
                    <li>Datang ke sekolah asal yang mengeluarkan ijazah.</li>
                    <li>Jika sekolah sudah tidak beroperasi, datang ke Dinas Pendidikan dengan membawa surat dari kepolisian dan dokumen pendukung lainnya (fotokopi ijazah jika ada, KTP, dll).</li>
                </ol>'
            ],
            [
                'question' => 'Apakah pindah sekolah (mutasi) antar kabupaten/kota dikenakan biaya?',
                'answer' => 'Tidak. Seluruh layanan pengurusan surat rekomendasi pindah sekolah (mutasi) di Dinas Pendidikan Kabupaten Paser adalah <strong>gratis</strong> dan tidak dipungut biaya.'
            ]
        ],
        'guru_tenaga_kependidikan' => [
            [
                'question' => 'Bagaimana cara memeriksa status validasi data untuk Tunjangan Profesi Guru (TPG)?',
                'answer' => 'Status validasi data guru dapat dipantau secara mandiri melalui laman <a href="https://info.gtk.kemdikbud.go.id" target="_blank" style="color: #003399; text-decoration: underline;">Info GTK</a> menggunakan akun PTK masing-masing. Pastikan data Anda di Dapodik sudah sinkron dan valid melalui operator sekolah.'
            ],
            [
                'question' => 'Saya adalah guru honorer, apakah bisa mendapatkan bantuan/insentif dari dinas?',
                'answer' => 'Pemerintah Daerah Kabupaten Paser memiliki kebijakan terkait insentif atau bantuan untuk guru non-ASN. Informasi mengenai kriteria, besaran, dan jadwal pencairan akan diumumkan secara resmi melalui surat edaran ke sekolah-sekolah. Silakan berkoordinasi dengan kepala sekolah Anda.'
            ]
        ],
        'ppdb' => [
            [
                'question' => 'Kapan jadwal pelaksanaan PPDB tahun ini?',
                'answer' => 'Jadwal lengkap, petunjuk teknis, dan informasi mengenai jalur pendaftaran (Zonasi, Afirmasi, Prestasi, Perpindahan Tugas Orang Tua) akan dipublikasikan melalui website resmi PPDB Kabupaten Paser. Mohon pantau website dan media sosial resmi kami secara berkala.'
            ],
            [
                'question' => 'Apa yang harus dilakukan jika ada kendala saat pendaftaran PPDB online?',
                'answer' => 'Jika terjadi kendala teknis, Anda dapat menghubungi <strong>Help Desk PPDB</strong> yang nomor kontaknya akan kami sediakan di situs resmi PPDB selama periode pendaftaran berlangsung. Anda juga bisa datang ke posko PPDB di sekolah terdekat atau di kantor Dinas Pendidikan.'
            ]
        ]
    ];
}

// Fungsi untuk menyimpan FAQ ke database (untuk admin)
function saveFAQ($category, $question, $answer) {
    try {
        $db = getDatabaseConnection();
        
        // Cek apakah tabel faq ada
        $checkTable = $db->query("SHOW TABLES LIKE 'faq'")->fetch();
        
        if (!$checkTable) {
            // Buat tabel jika tidak ada
            $createTableSQL = "CREATE TABLE IF NOT EXISTS faq (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category VARCHAR(50) NOT NULL,
                question TEXT NOT NULL,
                answer TEXT NOT NULL,
                display_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $db->exec($createTableSQL);
        }
        
        $stmt = $db->prepare("INSERT INTO faq (category, question, answer) VALUES (?, ?, ?)");
        return $stmt->execute([$category, $question, $answer]);
        
    } catch (Exception $e) {
        error_log("Save FAQ Error: " . $e->getMessage());
        return false;
    }
}

?>