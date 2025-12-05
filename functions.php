<?php
// functions.php

// Mulai session jika belum dimulai (Cek safety agar tidak crash jika di-include multiple kali)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan config.php sudah di-load
if (!defined('DB_HOST')) {
    // Cek path relative, sesuaikan jika perlu
    if (file_exists('config.php')) {
        require_once 'config.php';
    } elseif (file_exists('../config.php')) {
        require_once '../config.php';
    }
}

// ==============================================
// KONEKSI DATABASE & HELPER UMUM
// ==============================================

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

function cekKoneksi() {
    try {
        getDatabaseConnection();
        return true;
    } catch (Exception $e) {
        error_log("Cek koneksi gagal: " . $e->getMessage());
        return false;
    }
}

function formatTanggalIndonesia($tanggal) {
    $bulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    
    $pecah = explode('-', $tanggal);
    // Handle jika format tanggal tidak valid
    if (count($pecah) < 3) return $tanggal;
    
    return $pecah[2] . ' ' . ($bulan[$pecah[1]] ?? '') . ' ' . $pecah[0];
}

// ==============================================
// MANAJEMEN BERITA
// ==============================================

function ambilSemuaBerita($kategori = 'semua', $limit = null, $offset = 0) {
    $pdo = getDatabaseConnection();
    
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
    $pdo = getDatabaseConnection();
    
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

function cariBerita($keyword) {
    $pdo = getDatabaseConnection();
    
    $sql = "SELECT * FROM berita WHERE status = 'publish' AND (judul LIKE ? OR excerpt LIKE ? OR konten LIKE ?) 
            ORDER BY tanggal_publish DESC";
    $stmt = $pdo->prepare($sql);
    $search_term = "%$keyword%";
    $stmt->execute([$search_term, $search_term, $search_term]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function ambilSemuaBeritaAdmin($limit = null, $offset = 0) {
    $pdo = getDatabaseConnection();
    
    $sql = "SELECT * FROM berita ORDER BY dibuat_pada DESC";
    
    if ($limit !== null) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function ambilBeritaByIdAdmin($id) {
    $pdo = getDatabaseConnection();
    
    $sql = "SELECT * FROM berita WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function tambahBerita($judul, $excerpt, $konten, $kategori, $gambar, $penulis, $tanggal_publish) {
    try {
        $pdo = getDatabaseConnection();
        
        $sql = "INSERT INTO berita (judul, excerpt, konten, kategori, gambar, penulis, tanggal_publish, status, dibaca) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'draft', 0)";
        
        $stmt = $pdo->prepare($sql);
        
        // Debug: lihat parameter yang dikirim
        error_log("tambahBerita Params: " . print_r([$judul, $excerpt, $konten, $kategori, $gambar, $penulis, $tanggal_publish], true));
        
        $result = $stmt->execute([$judul, $excerpt, $konten, $kategori, $gambar, $penulis, $tanggal_publish]);
        
        return $result;
        
    } catch (PDOException $e) {
        error_log("Error tambahBerita: " . $e->getMessage());
        return false;
    }
}

function updateBerita($id, $judul, $excerpt, $konten, $kategori, $gambar, $tanggal_publish) {
    try {
        $pdo = getDatabaseConnection();
        
        // Cek apakah ada gambar baru yang diupload
        if ($gambar && trim($gambar) !== '') {
            $sql = "UPDATE berita SET judul = ?, excerpt = ?, konten = ?, kategori = ?, gambar = ?, tanggal_publish = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$judul, $excerpt, $konten, $kategori, $gambar, $tanggal_publish, $id]);
        } else {
            $sql = "UPDATE berita SET judul = ?, excerpt = ?, konten = ?, kategori = ?, tanggal_publish = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$judul, $excerpt, $konten, $kategori, $tanggal_publish, $id]);
        }
        
        return $result;
        
    } catch (PDOException $e) {
        error_log("Error updateBerita: " . $e->getMessage());
        return false;
    }
}

function ambilBeritaById($id) {
    try {
        $pdo = getDatabaseConnection();
        
        $sql = "SELECT * FROM berita WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: [];
        
    } catch (PDOException $e) {
        error_log("Error ambilBeritaById: " . $e->getMessage());
        return [];
    }
}

function hapusBerita($id) {
    $pdo = getDatabaseConnection();
    
    $sql = "DELETE FROM berita WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$id]);
}

function updateStatusBerita($id, $status) {
    $pdo = getDatabaseConnection();
    
    $sql = "UPDATE berita SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$status, $id]);
}

function updateCounterDibaca($id) {
    $pdo = getDatabaseConnection();
    
    $sql = "UPDATE berita SET dibaca = dibaca + 1 WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$id]);
}

// ==============================================
// LOG AKTIVITAS (Activity Logging)
// ==============================================

/**
 * Fungsi untuk mencatat aktivitas admin
 */
function logActivity($action, $description = '', $username = null) {
    $logFile = 'data/activity_log.json';
    $logDir = dirname($logFile);
    
    // Buat folder data jika belum ada
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }
    
    // Tentukan username
    if ($username === null) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $username = $_SESSION['admin_username'] ?? 'System';
    }
    
    // Data aktivitas
    $activity = [
        'timestamp' => time(),
        'date' => date('Y-m-d H:i:s'),
        'action' => $action,
        'details' => $description,
        'user' => $username,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
    ];
    
    // Baca log yang ada
    $logData = [];
    if (file_exists($logFile)) {
        $existingData = file_get_contents($logFile);
        $logData = json_decode($existingData, true) ?? [];
    }
    
    // Tambah aktivitas baru di awal array
    array_unshift($logData, $activity);
    
    // Simpan maksimal 100 aktivitas terbaru
    $logData = array_slice($logData, 0, 100);
    
    // Simpan ke file
    $result = file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // Panggil cleanup otomatis setiap 10 log baru
    if (count($logData) % 10 === 0) {
        cleanupOldLogs();
    }
    
    return $result !== false;
}

function getRecentActivities($limit = 10) {
    $logFile = 'data/activity_log.json';
    
    if (!file_exists($logFile)) {
        return [];
    }
    
    $data = file_get_contents($logFile);
    $activities = json_decode($data, true) ?? [];
    
    // Return aktivitas terbaru sesuai limit
    return array_slice($activities, 0, $limit);
}

function cleanupOldLogs() {
    $logFile = 'data/activity_log.json';
    
    if (!file_exists($logFile)) {
        return;
    }
    
    $data = file_get_contents($logFile);
    $activities = json_decode($data, true) ?? [];
    
    if (empty($activities)) {
        return;
    }
    
    $thirtyDaysAgo = time() - (30 * 24 * 60 * 60);
    $filteredActivities = array_filter($activities, function($activity) use ($thirtyDaysAgo) {
        return isset($activity['timestamp']) && $activity['timestamp'] > $thirtyDaysAgo;
    });
    
    // Simpan kembali
    file_put_contents($logFile, json_encode(array_values($filteredActivities), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function waktuLalu($timestamp) {
    if (is_string($timestamp) && strtotime($timestamp)) {
        $timestamp = strtotime($timestamp);
    } elseif (!is_numeric($timestamp)) {
        return 'Waktu tidak valid';
    }
    
    $currentTime = time();
    $timeDiff = $currentTime - $timestamp;
    
    if ($timeDiff < 60) {
        return 'Baru saja';
    } elseif ($timeDiff < 3600) {
        $minutes = floor($timeDiff / 60);
        return $minutes . ' menit yang lalu';
    } elseif ($timeDiff < 86400) {
        $hours = floor($timeDiff / 3600);
        return $hours . ' jam yang lalu';
    } elseif ($timeDiff < 2592000) { // 30 hari
        $days = floor($timeDiff / 86400);
        return $days . ' hari yang lalu';
    } elseif ($timeDiff < 31536000) { // 1 tahun
        $months = floor($timeDiff / 2592000);
        return $months . ' bulan yang lalu';
    } else {
        return date('d M Y', $timestamp);
    }
}

function clearAllActivityLogs() {
    $logFile = 'data/activity_log.json';
    
    $logDir = dirname($logFile);
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }
    
    $result = file_put_contents($logFile, json_encode([]));
    return $result !== false;
}

function getActivityStats() {
    $logFile = 'data/activity_log.json';
    
    if (!file_exists($logFile)) {
        return [
            'total' => 0,
            'today' => 0,
            'by_action' => [],
            'by_user' => []
        ];
    }
    
    $data = file_get_contents($logFile);
    $activities = json_decode($data, true) ?? [];
    
    $today = date('Y-m-d');
    $todayStart = strtotime($today . ' 00:00:00');
    $todayEnd = strtotime($today . ' 23:59:59');
    
    $stats = [
        'total' => count($activities),
        'today' => 0,
        'by_action' => [],
        'by_user' => []
    ];
    
    foreach ($activities as $activity) {
        // Hitung aktivitas hari ini
        if (isset($activity['timestamp']) && $activity['timestamp'] >= $todayStart && $activity['timestamp'] <= $todayEnd) {
            $stats['today']++;
        }
        
        // Hitung berdasarkan aksi
        if (isset($activity['action'])) {
            $action = $activity['action'];
            $stats['by_action'][$action] = ($stats['by_action'][$action] ?? 0) + 1;
        }
        
        // Hitung berdasarkan user
        if (isset($activity['user'])) {
            $user = $activity['user'];
            $stats['by_user'][$user] = ($stats['by_user'][$user] ?? 0) + 1;
        }
    }
    
    return $stats;
}

function getActivitiesByDateRange($startDate, $endDate) {
    $logFile = 'data/activity_log.json';
    
    if (!file_exists($logFile)) {
        return [];
    }
    
    $data = file_get_contents($logFile);
    $activities = json_decode($data, true) ?? [];
    
    $startTimestamp = strtotime($startDate . ' 00:00:00');
    $endTimestamp = strtotime($endDate . ' 23:59:59');
    
    $filteredActivities = array_filter($activities, function($activity) use ($startTimestamp, $endTimestamp) {
        if (!isset($activity['timestamp'])) {
            return false;
        }
        return $activity['timestamp'] >= $startTimestamp && $activity['timestamp'] <= $endTimestamp;
    });
    
    return array_values($filteredActivities);
}

function getActivityIcon($action) {
    $icons = [
        'login' => '🔐',
        'logout' => '🚪',
        'add' => '➕',
        'edit' => '✏️',
        'delete' => '🗑️',
        'update' => '🔄',
        'publish' => '📢',
        'unpublish' => '📋',
        'upload' => '📤',
        'settings' => '⚙️',
        'clear' => '🧹',
        'create' => '📝'
    ];
    
    return $icons[$action] ?? '📝';
}

function getActivityLabel($action) {
    $labels = [
        'login' => 'Login ke sistem',
        'logout' => 'Logout dari sistem',
        'add' => 'Menambahkan',
        'edit' => 'Mengedit',
        'delete' => 'Menghapus',
        'update' => 'Memperbarui',
        'publish' => 'Mempublikasikan',
        'unpublish' => 'Menyimpan draft',
        'upload' => 'Mengupload',
        'settings' => 'Mengubah pengaturan',
        'clear' => 'Membersihkan',
        'create' => 'Membuat'
    ];
    
    return $labels[$action] ?? $action;
}

?>