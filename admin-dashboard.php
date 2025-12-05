<?php
session_start();

date_default_timezone_set('Asia/Makassar');

// Cek jika admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Load config terlebih dahulu
require_once 'config.php';
require_once 'functions.php';

// Handle logout
if (isset($_GET['logout'])) {
    // Log aktivitas logout
    if (isset($_SESSION['admin_username'])) {
        logActivity('logout', 'Logout dari sistem', $_SESSION['admin_username']);
    }
    session_destroy();
    header("Location: login.php");
    exit;
}

// Ambil koneksi database
$pdo = getDatabaseConnection();

// Log aktivitas akses dashboard (hanya sekali per session)
if (!isset($_SESSION['dashboard_accessed'])) {
    logActivity('login', 'Mengakses dashboard admin', $_SESSION['admin_username']);
    $_SESSION['dashboard_accessed'] = true;
}

// Ambil statistik dari database
$stats = [];

try {
    // 1. Jumlah berita
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM berita WHERE status = 'publish'");
    $stats['total_berita'] = $stmt->fetch()['total'];
    
    // 2. Jumlah berita draft
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM berita WHERE status = 'draft'");
    $stats['berita_draft'] = $stmt->fetch()['total'];
    
    // 3. Jumlah layanan
    $stats['total_layanan'] = 4;
    
    // 4. Jumlah FAQ
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM faq");
        $faq_count = $stmt->fetch()['total'];
        $stats['total_faq'] = $faq_count > 0 ? $faq_count : 8;
    } catch (Exception $e) {
        $stats['total_faq'] = 8;
    }
    
    // 5. Total konten
    $stats['total_konten'] = $stats['total_berita'] + $stats['total_faq'] + $stats['total_layanan'];
    
    // 6. Ambil aktivitas terbaru dari JSON
    $recent_activities = getRecentActivities(5);
    
} catch (Exception $e) {
    // Fallback values
    $stats = [
        'total_berita' => 0,
        'berita_draft' => 0,
        'total_layanan' => 4,
        'total_faq' => 8,
        'total_konten' => 12
    ];
    $recent_activities = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Dinas Pendidikan dan Kebudayaan Kabupaten Paser</title>
    <link rel="stylesheet" href="css/admin-styles.css">
    <style>
        /* Override specific styles for dashboard layout */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        @media (max-width: 1200px) {
            .dashboard-stats { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (max-width: 768px) {
            .dashboard-stats { grid-template-columns: 1fr; }
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 20px;
            transition: all 0.3s ease;
            border-top: 4px solid #003399;
            min-height: 120px;
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        }
        
        .stat-card:nth-child(2) { border-top-color: #e36159; }
        .stat-card:nth-child(3) { border-top-color: #28a745; }
        .stat-card:nth-child(4) { border-top-color: #ffc107; }
        
        /* Icon Box on Left */
        .stat-card .icon-container {
            flex-shrink: 0;
            width: 70px;
            height: 70px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(0,51,153,0.1) 0%, rgba(0,51,153,0.05) 100%);
        }
        
        .stat-card:nth-child(2) .icon-container { background: linear-gradient(135deg, rgba(227,97,89,0.1) 0%, rgba(227,97,89,0.05) 100%); }
        .stat-card:nth-child(3) .icon-container { background: linear-gradient(135deg, rgba(40,167,69,0.1) 0%, rgba(40,167,69,0.05) 100%); }
        .stat-card:nth-child(4) .icon-container { background: linear-gradient(135deg, rgba(255,193,7,0.1) 0%, rgba(255,193,7,0.05) 100%); }
        
        .stat-card .stat-icon { font-size: 2.5rem; }
        
        .stat-card .card-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .stat-card .stat-label {
            color: #555;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .stat-card .stat-number {
            font-size: 2.8rem;
            font-weight: 800;
            color: #003399;
            line-height: 1;
        }
        
        .stat-card:nth-child(2) .stat-number { color: #e36159; }
        .stat-card:nth-child(3) .stat-number { color: #28a745; }
        .stat-card:nth-child(4) .stat-number { color: #ffc107; }
        
        .stat-card small {
            color: #777;
            font-size: 0.85rem;
            display: block;
            margin-top: 2px;
        }

        .dashboard-sections {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        
        @media (max-width: 992px) {
            .dashboard-sections { grid-template-columns: 1fr; }
        }

        /* Recent Activity Styling */
        .recent-activity {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 20px;
        }
        
        .activity-item {
            display: flex;
            align-items: flex-start;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .activity-item:last-child { border-bottom: none; }
        
        .activity-item .activity-icon {
            background: #f0f7ff;
            color: #003399;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        
        .activity-item .activity-content { flex: 1; min-width: 0; }
        
        .activity-item h4 {
            margin: 0 0 4px 0;
            color: #333;
            font-size: 1rem;
        }
        
        .activity-item p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .activity-item .activity-time {
            color: #888;
            font-size: 0.85rem;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Quick Actions Styling */
        .quick-actions {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 20px;
        }

        .quick-actions h3 {
            color: #003399;
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e36159;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .action-list li { margin-bottom: 10px; }

        .action-list a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .action-list a:hover {
            background: #003399;
            color: white;
            transform: translateX(3px);
            border-left-color: #e36159;
        }
        
        .action-list .icon {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .welcome-banner {
            background: linear-gradient(135deg, #003399 0%, #002280 100%);
            color: white;
            padding: 20px 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,51,153,0.15);
        }

        /* Styling untuk memastikan tombol di kanan judul */
        .header-with-action {
            display: flex; /* Mengaktifkan Flexbox */
            justify-content: space-between; /* Memposisikan item (judul & tombol) di ujung-ujung */
            align-items: center;
            width: 100%;
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }

        .header-with-action h3 {
            /* Penting: Pastikan margin dan padding default hilang agar flexbox bekerja optimal */
            margin: 0;
            border: none;
            padding: 0;
            /* Tambahan styling untuk judul */
            font-size: 1.2rem;
            color: #003399; 
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-clear-log {
            background: #fff3cd; /* Warna latar belakang kuning muda */
            color: #856404; /* Warna teks gelap */
            border: 1px solid #ffeeba;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
        }

        .btn-clear-log:hover {
            background: #ffeeba;
            transform: translateY(-1px);
        }

        .sidebar {
            /* Harus di set display flex agar footer bisa ditaruh di bawah */
            display: flex;
            flex-direction: column;
            justify-content: space-between; 
        }

        .sidebar-menu {
            flex-grow: 1; 
            overflow-y: auto; 
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-sidebar-logout:hover {
            background: #dc3545;
            color: white;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

    </style>
</head>
<body>
    <nav class="navbar default-layout">
        <div class="navbar-brand-wrapper">
            <a class="navbar-brand" href="#">
                <span class="brand-logo">Menu Admin</span>
                <span class="brand-logo-mini">MA</span>
            </a>
        </div>
        <div class="navbar-menu-wrapper">
            <div class="navbar-brand">
                <img src="assets/logo-kabupaten.png" alt="Logo">
                <span class="brand-text">Dinas Pendidikan dan Kebudayaan Kabupaten Paser</span>
            </div>
            
        </div>
    </nav>

    <div class="main-container">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="admin-dashboard.php" class="sidebar-menu-link active"> <span class="icon icon-dashboard"></span>
                        <span class="sidebar-menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin-berita.php" class="sidebar-menu-link">
                        <span class="icon icon-news"></span>
                        <span class="sidebar-menu-text">Kelola Berita</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="layanan_pesan.php" class="sidebar-menu-link">
                        <span class="icon icon-envelope"></span>
                        <span class="sidebar-menu-text">Pesan Pengaduan</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin-pengaturan.php" class="sidebar-menu-link">
                        <span class="icon icon-settings"></span>
                        <span class="sidebar-menu-text">Pengaturan Konten</span>
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <a href="?logout=true" class="btn-sidebar-logout" onclick="return confirm('Apakah Anda yakin ingin keluar?');">
                    <span>Keluar Aplikasi</span>
                </a>
            </div>
        </aside>

        <main class="content-wrapper">
            <div class="welcome-banner">
                <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>!</h2>
                <p>Panel Admin Website Dinas Pendidikan Kabupaten Paser</p>
            </div>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="icon-container">
                        <div class="stat-icon">📰</div>
                    </div>
                    <div class="card-content">
                        <div class="stat-label">Total Berita</div>
                        <div class="stat-number"><?php echo $stats['total_berita']; ?></div>
                        <small><?php echo $stats['berita_draft']; ?> dalam draft</small>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="icon-container">
                        <div class="stat-icon">🛠️</div>
                    </div>
                    <div class="card-content">
                        <div class="stat-label">Jumlah Layanan</div>
                        <div class="stat-number"><?php echo $stats['total_layanan']; ?></div>
                        <small>Tersedia</small>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="icon-container">
                        <div class="stat-icon">❓</div>
                    </div>
                    <div class="card-content">
                        <div class="stat-label">Pertanyaan FAQ</div>
                        <div class="stat-number"><?php echo $stats['total_faq']; ?></div>
                        <small>Pertanyaan umum</small>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="icon-container">
                        <div class="stat-icon">📊</div>
                    </div>
                    <div class="card-content">
                        <div class="stat-label">Total Konten</div>
                        <div class="stat-number"><?php echo $stats['total_konten']; ?></div>
                        <small>Keseluruhan konten</small>
                    </div>
                </div>
            </div>

            <div class="dashboard-sections">
                <div class="recent-activity">
                    <div class="header-with-action">
                            <h3><span class="icon">📋</span> Aktivitas Terbaru</h3>
                            <button onclick="clearActivityLog()" class="btn-clear-log" title="Hapus semua riwayat aktivitas">
                                <span class="icon">🗑️</span> Hapus Log
                            </button>
                        </div>
                    
                    <div id="activity-list-container">
                    <?php if (!empty($recent_activities)): ?>
                        <?php foreach ($recent_activities as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <?php
                                $icons = [
                                    'login' => '🔐', 'logout' => '🚪', 'add' => '➕',
                                    'edit' => '✏️', 'delete' => '🗑️', 'update' => '🔄',
                                    'publish' => '📢', 'upload' => '📤', 'settings' => '⚙️',
                                    'clear' => '🧹' 
                                ];
                                echo $icons[$activity['action']] ?? '📝';
                                ?>
                            </div>
                            <div class="activity-content">
                                <h4>
                                    <?php 
                                    $actionLabels = [
                                        'login' => 'Login ke sistem', 'logout' => 'Logout dari sistem',
                                        'add' => 'Menambahkan konten', 'edit' => 'Mengedit konten',
                                        'delete' => 'Menghapus konten', 'update' => 'Memperbarui konten',
                                        'publish' => 'Mempublikasikan', 'upload' => 'Mengupload file',
                                        'settings' => 'Mengubah pengaturan', 'clear' => 'Membersihkan log'
                                    ];
                                    echo $actionLabels[$activity['action']] ?? $activity['action'];
                                    ?>
                                </h4>
                                <?php if (!empty($activity['details'])): ?>
                                <p><?php echo htmlspecialchars($activity['details']); ?></p>
                                <?php endif; ?>
                                <div class="activity-time">
                                    <span class="icon">👤</span> <?php echo htmlspecialchars($activity['user']); ?>
                                    • 
                                    <span class="icon">🕒</span> <?php echo waktuLalu($activity['timestamp']); ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="icon">📭</div>
                            <p>Belum ada aktivitas tercatat</p>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>
                
                <div class="quick-actions">
                    <h3><span class="icon">⚡</span> Aksi Cepat</h3>
                    
                    <ul class="action-list">
                        <li>
                            <a href="admin-berita.php?action=tambah">
                                <span class="icon">➕</span> <span>Tambah Berita</span>
                            </a>
                        </li>
                        <li>
                            <a href="admin-pengaturan.php?file=index&section=hero">
                                <span class="icon">🎨</span> <span>Edit Homepage</span>
                            </a>
                        </li>
                        <li>
                            <a href="admin-pengaturan.php?file=layanan&section=layanan">
                                <span class="icon">🛠️</span> <span>Kelola Layanan</span>
                            </a>
                        </li>
                        <li>
                            <a href="admin-pengaturan.php?file=faq&section=faq-content">
                                <span class="icon">📤</span> <span>Kelola FAQ</span>
                            </a>
                        </li>
                    </ul>
                    
                    <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee;">
                        <h4 style="font-size: 1rem; color: #003399; margin-bottom: 10px;">
                            <span class="icon">ℹ️</span> Info Sistem
                        </h4>
                        <p style="font-size: 0.85rem; margin: 5px 0;"><strong>Tanggal:</strong> <?php echo date('d/m/Y'); ?></p>
                        <p style="font-size: 0.85rem; margin: 5px 0;"><strong>Login:</strong> <?php echo $_SESSION['admin_username'] ?? 'Admin'; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-footer" style="margin-top: 25px; text-align: center; color: #999; font-size: 0.8rem;">
                <p>© <?php echo date('Y'); ?> Admin Panel - Disdikbud Paser</p>
            </div>
        </main>
    </div>

    <script>
        // Dropdown toggle function
        document.addEventListener('DOMContentLoaded', function() {
            const userDropdown = document.getElementById('userDropdown');
            const userDropdownMenu = document.getElementById('userDropdownMenu');
            
            if(userDropdown) {
                userDropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    userDropdownMenu.classList.toggle('show');
                });
                
                document.addEventListener('click', function(e) {
                    if (!userDropdown.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                        userDropdownMenu.classList.remove('show');
                    }
                });
            }
        });

        // FUNGSI CLEAR ACTIVITY LOG (Di luar DOMContentLoaded agar global)
        function clearActivityLog() {
            if (confirm('Apakah Anda yakin ingin menghapus semua log aktivitas? Tindakan ini tidak dapat dibatalkan.')) {
                // Tampilkan loading UI
                const container = document.getElementById('activity-list-container');
                const originalContent = container.innerHTML;
                
                container.innerHTML = `
                    <div style="text-align: center; padding: 40px 20px; color: #666;">
                        <div style="font-size: 2rem; margin-bottom: 15px; animation: spin 1s linear infinite;">🔄</div>
                        <p>Sedang menghapus data...</p>
                    </div>
                `;
                
                // Kirim request ke backend
                fetch('clear-activity-log.php')
                    .then(response => {
                        if (!response.ok) throw new Error('Network error');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Reload halaman agar data ter-refresh bersih
                            setTimeout(() => {
                                window.location.reload();
                            }, 800);
                        } else {
                            alert('Gagal: ' + (data.message || 'Error tidak diketahui'));
                            container.innerHTML = originalContent;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan jaringan.');
                        container.innerHTML = originalContent;
                    });
            }
        }
    </script>
</body>
</html>