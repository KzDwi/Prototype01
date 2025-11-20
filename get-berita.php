<?php
session_start();
require_once 'functions.php';

// Cek jika admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

$id = $_GET['id'] ?? 0;
$berita = ambilBeritaByIdAdmin($id);

if ($berita) {
    header('Content-Type: application/json');
    echo json_encode($berita);
} else {
    header('HTTP/1.1 404 Not Found');
}
?>