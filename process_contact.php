<?php
session_start();

// --- Konfigurasi ---
$json_file = 'data/pesan_layanan.json'; // Lokasi file JSON
$redirect_url = 'layanan.php#contact'; // #contact agar tetap di section form

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Sanitasi Input
    $nama = strip_tags(trim($_POST["nama"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $jenis_layanan = strip_tags(trim($_POST["jenis_layanan"]));
    $pesan = strip_tags(trim($_POST["pesan"]));

    // 2. Validasi
    $errors = [];
    if (empty($nama)) $errors[] = "Nama wajib diisi.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid.";
    if (empty($jenis_layanan)) $errors[] = "Silakan pilih jenis layanan.";

    if (!empty($errors)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = implode("<br>", $errors);
        header("Location: " . $redirect_url);
        exit;
    }

    // 3. PROSES SIMPAN KE JSON (Logika Baru)
    try {
        // A. Siapkan data baru array asosiatif
        $data_baru = [
            "id"      => uniqid(), // Membuat ID unik otomatis
            "nama"    => $nama,
            "email"   => $email,
            "layanan" => $jenis_layanan,
            "pesan"   => $pesan,
            "tanggal" => date('Y-m-d H:i:s')
        ];

        // B. Ambil data lama dari file JSON (jika ada)
        $current_data = [];
        if (file_exists($json_file)) {
            $json_content = file_get_contents($json_file);
            $decoded_data = json_decode($json_content, true);
            
            // Pastikan formatnya array
            if (is_array($decoded_data)) {
                $current_data = $decoded_data;
            }
        }

        // C. Gabungkan data lama dengan data baru
        $current_data[] = $data_baru;

        // D. Simpan kembali ke file JSON
        if (file_put_contents($json_file, json_encode($current_data, JSON_PRETTY_PRINT))) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = "Terima kasih! Pesan Anda telah tersimpan dan akan segera kami proses.";
        } else {
            throw new Exception("Gagal menulis ke database JSON.");
        }

    } catch (Exception $e) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = "Terima kasih. Pesan diterima (Log System Error: " . $e->getMessage() . ")";
    }

    // 4. Redirect kembali ke Form (menggunakan #contact)
    header("Location: " . $redirect_url);
    exit;

} else {
    header("Location: layanan.php");
    exit;
}
?>