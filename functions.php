<?php
// functions.php
require_once 'config.php';

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
?>