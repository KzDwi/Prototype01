<?php
require_once 'config/db_connection.php';

function ambilDataLayananPopup($layanan_key) {
    global $conn;
    
    $query = "SELECT * FROM layanan_popup WHERE layanan_key = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $layanan_key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return [
            'popup_desc' => $row['popup_desc'] ?? '',
            'cara_kerja' => $row['cara_kerja'] ?? '',
            'persyaratan' => $row['persyaratan'] ?? ''
        ];
    }
    
    return [
        'popup_desc' => '',
        'cara_kerja' => '',
        'persyaratan' => ''
    ];
}

function simpanDataLayananPopup($layanan_key, $data) {
    global $conn;
    
    // Cek apakah data sudah ada
    $query_check = "SELECT id FROM layanan_popup WHERE layanan_key = ?";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->bind_param("s", $layanan_key);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        // Update
        $query = "UPDATE layanan_popup SET 
                  popup_desc = ?, 
                  cara_kerja = ?, 
                  persyaratan = ?,
                  updated_at = NOW()
                  WHERE layanan_key = ?";
    } else {
        // Insert
        $query = "INSERT INTO layanan_popup 
                  (layanan_key, popup_desc, cara_kerja, persyaratan, created_at, updated_at)
                  VALUES (?, ?, ?, ?, NOW(), NOW())";
    }
    
    $stmt = $conn->prepare($query);
    if (isset($data['popup_desc'])) {
        $stmt->bind_param("ssss", 
            $layanan_key,
            $data['popup_desc'],
            $data['cara_kerja'],
            $data['persyaratan']
        );
    } else {
        // Jika menggunakan parameter yang berbeda
        $stmt->bind_param("ssss", 
            $layanan_key,
            $data[0] ?? '',
            $data[1] ?? '',
            $data[2] ?? ''
        );
    }
    
    return $stmt->execute();
}

// Fungsi bantu untuk konten halaman lain
function ambilKonten($halaman, $kategori) {
    global $conn;
    
    $query = "SELECT konten FROM konten_halaman 
              WHERE halaman = ? AND kategori = ? 
              ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $halaman, $kategori);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $konten = json_decode($row['konten'], true);
        return $konten !== null ? $konten : $row['konten'];
    }
    
    return [];
}

function simpanKontenUmum($halaman, $kategori, $data) {
    global $conn;
    
    // Cek apakah data sudah ada
    $query_check = "SELECT id FROM konten_halaman 
                    WHERE halaman = ? AND kategori = ?";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->bind_param("ss", $halaman, $kategori);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    $konten_json = is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data;
    
    if ($result_check->num_rows > 0) {
        // Update
        $query = "UPDATE konten_halaman SET 
                  konten = ?, 
                  updated_at = NOW()
                  WHERE halaman = ? AND kategori = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $konten_json, $halaman, $kategori);
    } else {
        // Insert
        $query = "INSERT INTO konten_halaman 
                  (halaman, kategori, konten, created_at, updated_at)
                  VALUES (?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $halaman, $kategori, $konten_json);
    }
    
    return $stmt->execute();
}
?>