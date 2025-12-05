<?php
session_start();

// Cek jika admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$id = $_GET['id'] ?? 0;

// File JSON pengaduan
$pengaduan_file = 'data/pengaduan.json';

if (!file_exists($pengaduan_file)) {
    echo json_encode(['error' => 'Data pengaduan tidak ditemukan']);
    exit;
}

$json_data = file_get_contents($pengaduan_file);
$pengaduan_data = json_decode($json_data, true) ?? [];

// Cari pengaduan berdasarkan ID
$found_pengaduan = null;
foreach ($pengaduan_data as $pengaduan) {
    if ($pengaduan['id'] == $id) {
        $found_pengaduan = $pengaduan;
        break;
    }
}

if ($found_pengaduan) {
    echo json_encode($found_pengaduan);
} else {
    echo json_encode(['error' => 'Pengaduan tidak ditemukan']);
}