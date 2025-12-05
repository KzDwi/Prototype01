<?php
// get-berita.php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Cek jika admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $berita = ambilBeritaByIdAdmin($id);
    
    if ($berita) {
        header('Content-Type: application/json');
        echo json_encode($berita);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Berita tidak ditemukan']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'ID tidak diberikan']);
}
?>