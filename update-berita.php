<?php
session_start();
require_once 'functions.php';

// Cek jika admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$pesan_sukses = '';
$pesan_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_berita'])) {
    try {
        $id = $_POST['id'];
        $gambar_path = $_POST['gambar_lama'] ?? '';
        $thumbnail_path = $_POST['thumbnail_lama'] ?? '';
        
        // Upload gambar baru jika ada
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
            $gambar_path = uploadGambar($_FILES['gambar']);
            $thumbnail_path = $gambar_path;
        }
        
        $data_berita = [
            'judul' => $_POST['judul'],
            'excerpt' => $_POST['excerpt'],
            'konten' => $_POST['konten'],
            'kategori' => $_POST['kategori'],
            'gambar' => $gambar_path,
            'thumbnail' => $thumbnail_path,
            'tanggal_publish' => $_POST['tanggal_publish']
        ];
        
        if (updateBerita($id, $data_berita)) {
            header("Location: admin-berita.php?sukses=Berita berhasil diupdate!");
            exit;
        } else {
            header("Location: admin-berita.php?error=Gagal mengupdate berita. Silakan coba lagi.");
            exit;
        }
    } catch (Exception $e) {
        header("Location: admin-berita.php?error=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: admin-berita.php");
    exit;
}
?>