<?php
// clear-activity-log.php
session_start();

// Cek jika admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Pastikan folder data ada
if (!file_exists('data')) {
    mkdir('data', 0755, true);
}

$log_file = 'data/activity_log.json';

try {
    // Langsung buat log baru dengan SATU ENTRY untuk clear log
    $username = $_SESSION['admin_username'] ?? 'Admin';
    $clear_log = [[
        'timestamp' => time(),
        'date' => date('Y-m-d H:i:s'),
        'action' => 'clear',
        'details' => 'Menghapus semua log aktivitas',
        'user' => $username,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
    ]];
    
    // Tulis langsung ke file (overwrite)
    file_put_contents($log_file, json_encode($clear_log, JSON_PRETTY_PRINT));
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Log cleared successfully']);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>