<?php
// process_contact.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';
    $jenis_layanan = $_POST['jenis_layanan'] ?? '';
    $pesan = $_POST['pesan'] ?? '';
    
    if (empty($nama) || empty($email) || empty($jenis_layanan)) {
        $_SESSION['error'] = 'Harap isi semua field yang wajib diisi.';
        header("Location: layanan.php#contact");
        exit;
    }
    
    // File JSON untuk menyimpan pengaduan
    $pengaduan_file = 'data/pengaduan.json';
    
    // Buat folder data jika belum ada
    if (!file_exists('data')) {
        mkdir('data', 0777, true);
    }
    
    // Load data yang ada
    $pengaduan_data = [];
    if (file_exists($pengaduan_file)) {
        $json_data = file_get_contents($pengaduan_file);
        $pengaduan_data = json_decode($json_data, true) ?? [];
    }
    
    // Data pengaduan baru
    $new_pengaduan = [
        'id' => uniqid('pengaduan_', true),
        'nama' => htmlspecialchars($nama),
        'email' => htmlspecialchars($email),
        'jenis_layanan' => htmlspecialchars($jenis_layanan),
        'pesan' => htmlspecialchars($pesan),
        'status' => 'baru',
        'dibaca' => false,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => null
    ];
    
    // Tambahkan ke array
    $pengaduan_data[] = $new_pengaduan;
    
    // Simpan ke JSON
    if (file_put_contents($pengaduan_file, json_encode($pengaduan_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        // Kirim email notifikasi (opsional)
        // sendEmailNotification($new_pengaduan);
        
        $_SESSION['success'] = 'Pengaduan/konsultasi Anda telah berhasil dikirim. Admin akan menghubungi Anda dalam 2x24 jam.';
        header("Location: layanan.php#contact");
        exit;
    } else {
        $_SESSION['error'] = 'Terjadi kesalahan saat menyimpan pengaduan. Silakan coba lagi.';
        header("Location: layanan.php#contact");
        exit;
    }
}

// Fungsi untuk mengirim email notifikasi (opsional)
function sendEmailNotification($pengaduan) {
    $to = "admin@disdik-paser.go.id"; // Ganti dengan email admin
    $subject = "Pengaduan Baru - Website Disdik Paser";
    
    $message = "
    <html>
    <head>
        <title>Pengaduan Baru</title>
    </head>
    <body>
        <h2>Pengaduan/Konsultasi Baru</h2>
        <p><strong>Nama:</strong> {$pengaduan['nama']}</p>
        <p><strong>Email:</strong> {$pengaduan['email']}</p>
        <p><strong>Jenis Layanan:</strong> {$pengaduan['jenis_layanan']}</p>
        <p><strong>Pesan:</strong> {$pengaduan['pesan']}</p>
        <p><strong>Waktu:</strong> {$pengaduan['created_at']}</p>
        <br>
        <p>Silakan login ke admin panel untuk menanggapi pengaduan ini.</p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@disdik-paser.go.id" . "\r\n";
    
    mail($to, $subject, $message, $headers);
}