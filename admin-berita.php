<?php
session_start();

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
    session_destroy();
    header("Location: login.php");
    exit;
}

// Inisialisasi pesan
$pesan_sukses = '';
$pesan_error = '';

// Handle hapus berita
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    if (hapusBerita($id)) {
        logActivity('delete', 'Menghapus berita ID: ' . $id);
        $pesan_sukses = 'Berita berhasil dihapus!';
    } else {
        $pesan_error = 'Gagal menghapus berita. Silakan coba lagi.';
    }
}

// Handle update status berita
if (isset($_GET['ubah_status'])) {
    $id = $_GET['ubah_status'];
    $status = $_GET['status'];
    if (updateStatusBerita($id, $status)) {
        $action = $status === 'publish' ? 'publish' : 'unpublish';
        logActivity($action, 'Mengubah status berita ID: ' . $id . ' menjadi ' . $status);
        $pesan_sukses = 'Status berita berhasil diubah!';
    } else {
        $pesan_error = 'Gagal mengubah status berita. Silakan coba lagi.';
    }
}

// ============================
// PAGINATION SETUP
// ============================
$records_per_page = 5; // Jumlah data per halaman

// Hitung total berita
$total_berita_query = "SELECT COUNT(*) as total FROM berita";
$stmt = $pdo->query($total_berita_query);
$total_berita = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Hitung total halaman
$total_pages = ceil($total_berita / $records_per_page);

// Tentukan halaman saat ini
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Pastikan halaman tidak melebihi total halaman
$current_page = min($current_page, $total_pages);

// Hitung offset untuk query
$offset = ($current_page - 1) * $records_per_page;

// ============================
// HANDLE TAMBAH BERITA (DALAM SATU FILE)
// ============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'tambah') {
        // Validasi input
        $judul = $_POST['judul'] ?? '';
        $excerpt = $_POST['excerpt'] ?? '';
        $konten = $_POST['konten'] ?? '';
        $kategori = $_POST['kategori'] ?? '';
        $tanggal_publish = $_POST['tanggal_publish'] ?? date('Y-m-d');
        $penulis = $_SESSION['admin_username'] ?? 'Admin';
        
        // Handle upload gambar
        $gambar = '';
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['gambar']['name'];
            $filetmp = $_FILES['gambar']['tmp_name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                // Buat nama file unik
                $new_filename = uniqid() . '.' . $ext;
                $upload_path = 'uploads/berita/' . $new_filename;
                
                // Pastikan folder uploads/berita ada
                if (!is_dir('uploads/berita')) {
                    mkdir('uploads/berita', 0777, true);
                }
                
                if (move_uploaded_file($filetmp, $upload_path)) {
                    $gambar = $upload_path;
                }
            }
        }
        
        // Panggil fungsi tambah berita
        if (tambahBerita($judul, $excerpt, $konten, $kategori, $gambar, $penulis, $tanggal_publish)) {
            logActivity('create', 'Menambahkan berita baru: ' . $judul);
            $pesan_sukses = 'Berita berhasil ditambahkan!';
            // Redirect untuk menghindari resubmission
            header("Location: admin-berita.php?page=$current_page&sukses=" . urlencode('Berita berhasil ditambahkan!'));
            exit;
        } else {
            $pesan_error = 'Gagal menambahkan berita. Silakan coba lagi.';
        }
    }
    
    // ============================
    // HANDLE UPDATE BERITA (DALAM SATU FILE)
    // ============================
    if ($_POST['action'] === 'update') {
        $id = $_POST['id'] ?? 0;
        $judul = $_POST['judul'] ?? '';
        $excerpt = $_POST['excerpt'] ?? '';
        $konten = $_POST['konten'] ?? '';
        $kategori = $_POST['kategori'] ?? '';
        $tanggal_publish = $_POST['tanggal_publish'] ?? '';
        
        // Handle upload gambar baru
        $gambar = '';
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['gambar']['name'];
            $filetmp = $_FILES['gambar']['tmp_name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid() . '.' . $ext;
                $upload_path = 'uploads/berita/' . $new_filename;
                
                if (!is_dir('uploads/berita')) {
                    mkdir('uploads/berita', 0777, true);
                }
                
                if (move_uploaded_file($filetmp, $upload_path)) {
                    $gambar = $upload_path;
                }
            }
        }
        
        // Panggil fungsi update berita (kirim string kosong jika tidak ada gambar baru)
        if (updateBerita($id, $judul, $excerpt, $konten, $kategori, $gambar, $tanggal_publish)) {
            logActivity('update', 'Memperbarui berita ID: ' . $id);
            $pesan_sukses = 'Berita berhasil diperbarui!';
            // Redirect untuk menghindari resubmission
            header("Location: admin-berita.php?page=$current_page&sukses=" . urlencode('Berita berhasil diperbarui!'));
            exit;
        } else {
            $pesan_error = 'Gagal memperbarui berita.';
        }
    }
}

// Ambil berita untuk halaman saat ini
$semua_berita = ambilSemuaBeritaAdmin($records_per_page, $offset);

// Pesan sukses/error dari GET
if (isset($_GET['sukses'])) {
    $pesan_sukses = $_GET['sukses'];
}
if (isset($_GET['error'])) {
    $pesan_error = $_GET['error'];
}
?>  
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Berita - Dinas Pendidikan dan Kebudayaan Kabupaten Paser</title>
    <link rel="stylesheet" href="css/admin-styles.css">
    <link rel="stylesheet" href="css/admin-berita-styles.css">
    <style>
        /* Popup Styles */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .popup-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .popup-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            transform: scale(0.7);
            transition: transform 0.3s ease;
        }

        .popup-overlay.active .popup-content {
            transform: scale(1);
        }

        .popup-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .popup-title {
            color: #003399;
            margin: 0;
            font-size: 1.5rem;
        }

        .popup-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
            padding: 5px;
        }

        .popup-close:hover {
            color: #003399;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #003399;
            box-shadow: 0 0 0 2px rgba(0, 51, 153, 0.1);
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s ease;
        }
        
        .btn-success:hover {
            background: #218838;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.3s ease;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }

        .btn-warning {
            background: #ffc107;
            color: black;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.3s ease;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        /* Table Actions */
        .table-actions {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        /* Tombol aksi dengan ukuran seragam */
        .table-actions .btn {
            min-width: 70px;
            padding: 5px 10px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 3px;
        }

        /* Status Badge */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            min-width: 60px;
            text-align: center;
        }

        .status-publish {
            background: #d4edda;
            color: #155724;
        }

        .status-draft {
            background: #fff3cd;
            color: #856404;
        }

        /* Action Header */
        .action-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        /* Thumbnail Preview */
        .thumbnail-preview {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        /* Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .pagination-info {
            font-size: 14px;
            color: #6c757d;
        }

        .pagination {
            display: flex;
            gap: 5px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .page-item {
            margin: 0;
        }

        .page-item.active .page-link {
            background: #003399;
            color: white;
            border-color: #003399;
        }

        .page-link {
            padding: 6px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #003399;
            text-decoration: none;
            font-size: 14px;
            min-width: 40px;
            text-align: center;
        }

        .page-link:hover:not(.disabled) {
            background: #f8f9fa;
        }

        .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background: #f8f9fa;
        }

        /* Responsive Table */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
            
            .table-actions {
                flex-direction: column;
                min-width: 120px;
            }
            
            .table-actions .btn {
                width: 100%;
                min-width: auto;
            }
            
            .action-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .pagination-container {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .pagination {
                flex-wrap: wrap;
            }
            .btn-sm {
                width: 2rem;
                height: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar default-layout">
        <div class="navbar-brand-wrapper">
            <a class="navbar-brand" href="#">
                <span class="brand-logo">Menu Admin</span>
                <span class="brand-logo-mini">MA</span>
            </a>
        </div>
        <div class="navbar-menu-wrapper">
            <!-- Logo dan Nama Instansi -->
            <div class="navbar-brand">
                <img src="assets/logo-kabupaten.png" alt="Logo">
                <span class="brand-text">Dinas Pendidikan dan Kebudayaan Kabupaten Paser</span>
            </div>
            
        </div>
    </nav>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="admin-dashboard.php" class="sidebar-menu-link">
                        <span class="icon icon-dashboard"></span>
                        <span class="sidebar-menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin-berita.php" class="sidebar-menu-link active"> <span class="icon icon-news"></span>
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

        <!-- Main Content -->
        <main class="content-wrapper">
            <!-- Page Header -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3>Manajemen Berita</h3>
                    <p class="text-muted mb-0">Kelola berita dan artikel untuk website Dinas Pendidikan dan Kebudayaan</p>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if ($pesan_sukses): ?>
                <div class="alert alert-success"><?php echo $pesan_sukses; ?></div>
            <?php endif; ?>

            <?php if ($pesan_error): ?>
                <div class="alert alert-error"><?php echo $pesan_error; ?></div>
            <?php endif; ?>

            <!-- Action Header -->
            <div class="action-header">
                <h4>Daftar Berita</h4>
                <button class="btn btn-primary" onclick="bukaPopupTambahBerita()">
                    <span class="icon icon-plus"></span>Tambah Berita Baru
                </button>
            </div>

            <!-- Berita Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><center>No</center></th>
                                    <th><center>Thumbnail</center></th>
                                    <th><center>Judul</center></th>
                                    <th><center>Kategori</center></th>
                                    <th><center>Tanggal</center></th>
                                    <th><center>Status</center></th>
                                    <th><center>Dibaca</center></th>
                                    <th><center>Aksi</center></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($semua_berita)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada berita</td>
                                    </tr>
                                <?php else: ?>
                                    <?php 
                                    $start_number = ($current_page - 1) * $records_per_page + 1;
                                    foreach ($semua_berita as $index => $berita): 
                                    ?>
                                    <tr>
                                        <td><?php echo $start_number + $index; ?></td>
                                        <td>
                                            <?php if (!empty($berita['thumbnail'])): ?>
                                                <img src="<?php echo htmlspecialchars($berita['thumbnail']); ?>" alt="Thumbnail" class="thumbnail-preview">
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($berita['judul']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo substr(htmlspecialchars($berita['excerpt']), 0, 50); ?>...</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($berita['kategori']); ?></span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($berita['tanggal_publish'])); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $berita['status'] === 'publish' ? 'status-publish' : 'status-draft'; ?>">
                                                <?php echo $berita['status'] === 'publish' ? 'PUBLISH' : 'DRAFT'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo number_format($berita['dibaca']); ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="berita-detail.php?id=<?php echo $berita['id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <span class="icon icon-search"></span>Lihat
                                                </a>
                                                <button class="btn btn-sm btn-warning" onclick="bukaPopupEditBerita(<?php echo $berita['id']; ?>)">
                                                    <span class="icon icon-edit"></span>Edit
                                                </button>
                                                <a href="?ubah_status=<?php echo $berita['id']; ?>&status=<?php echo $berita['status'] === 'publish' ? 'draft' : 'publish'; ?>&page=<?php echo $current_page; ?>" class="btn btn-sm btn-outline-primary">
                                                    <span class="icon icon-settings"></span><?php echo $berita['status'] === 'publish' ? 'Draft' : 'Publish'; ?>
                                                </a>
                                                <a href="?hapus=<?php echo $berita['id']; ?>&page=<?php echo $current_page; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus berita ini?')">
                                                    <span class="icon icon-delete"></span>Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <div class="pagination-container">
                        <div class="pagination-info">
                            Menampilkan <?php echo min($records_per_page, $total_berita - ($current_page - 1) * $records_per_page); ?> dari <?php echo $total_berita; ?> berita (Halaman <?php echo $current_page; ?> dari <?php echo $total_pages; ?>)
                        </div>
                        <nav>
                            <ul class="pagination">
                                <!-- Previous Button -->
                                <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" aria-label="Previous">
                                        &laquo;
                                    </a>
                                </li>
                                
                                <!-- Page Numbers -->
                                <?php
                                // Tentukan rentang halaman yang akan ditampilkan
                                $start_page = max(1, $current_page - 2);
                                $end_page = min($total_pages, $current_page + 2);
                                
                                // Jika di awal, tambah halaman di akhir
                                if ($start_page == 1) {
                                    $end_page = min($total_pages, $start_page + 4);
                                }
                                
                                // Jika di akhir, tambah halaman di awal
                                if ($end_page == $total_pages) {
                                    $start_page = max(1, $end_page - 4);
                                }
                                
                                // Tampilkan halaman pertama jika tidak termasuk
                                if ($start_page > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                                    if ($start_page > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                
                                // Tampilkan nomor halaman
                                for ($i = $start_page; $i <= $end_page; $i++):
                                ?>
                                    <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php
                                // Tampilkan halaman terakhir jika tidak termasuk
                                if ($end_page < $total_pages) {
                                    if ($end_page < $total_pages - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
                                }
                                ?>
                                
                                <!-- Next Button -->
                                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>" aria-label="Next">
                                        &raquo;
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="dashboard-footer" style="margin-top: 25px; text-align: center; color: #999; font-size: 0.8rem;">
                <p>© <?php echo date('Y'); ?> Admin Panel - Disdikbud Paser</p>
            </div>
        </main>
    </div>

    <!-- Popup Tambah Berita -->
    <div class="popup-overlay" id="popupTambahBerita">
        <div class="popup-content">
            <div class="popup-header">
                <h3 class="popup-title">Tambah Berita Baru</h3>
                <button class="popup-close" onclick="tutupPopupTambahBerita()">&times;</button>
            </div>
            
            <form method="POST" action="" enctype="multipart/form-data" id="formTambahBerita">
                <input type="hidden" name="action" value="tambah">
                
                <div class="form-group">
                    <label for="judul">Judul Berita</label>
                    <input type="text" id="judul" name="judul" class="form-control" required placeholder="Masukkan judul berita">
                </div>
                
                <div class="form-group">
                    <label for="excerpt">Ringkasan Berita</label>
                    <textarea id="excerpt" name="excerpt" class="form-control" required placeholder="Tulis ringkasan singkat berita"></textarea>
                    <small style="color: #666;">Ringkasan singkat yang akan ditampilkan di halaman berita.</small>
                </div>
                
                <div class="form-group">
                    <label for="konten">Konten Lengkap</label>
                    <textarea id="konten" name="konten" class="form-control" required placeholder="Tulis konten lengkap berita" rows="6"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="kategori">Kategori</label>
                    <select id="kategori" name="kategori" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Pendidikan">Pendidikan</option>
                        <option value="Kebudayaan">Kebudayaan</option>
                        <option value="Pengumuman">Pengumuman</option>
                        <option value="Kegiatan">Kegiatan</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="gambar">Gambar Berita</label>
                    <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*">
                    <small style="color: #666;">Format: JPG, PNG, GIF. Maksimal 5MB.</small>
                </div>
                
                <div class="form-group">
                    <label for="tanggal_publish">Tanggal Publish</label>
                    <input type="date" id="tanggal_publish" name="tanggal_publish" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline-primary" onclick="tutupPopupTambahBerita()">Batal</button>
                    <button type="submit" class="btn-success">
                        <span class="icon icon-plus"></span> Tambah Berita
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Popup Edit Berita -->
    <div class="popup-overlay" id="popupEditBerita">
        <div class="popup-content">
            <div class="popup-header">
                <h3 class="popup-title">Edit Berita</h3>
                <button class="popup-close" onclick="tutupPopupEditBerita()">&times;</button>
            </div>
            
            <form method="POST" action="" enctype="multipart/form-data" id="formEditBerita">
                <input type="hidden" id="edit_id" name="id">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="page" value="<?php echo $current_page; ?>">
                
                <div class="form-group">
                    <label for="edit_judul">Judul Berita</label>
                    <input type="text" id="edit_judul" name="judul" class="form-control" required placeholder="Masukkan judul berita">
                </div>
                
                <div class="form-group">
                    <label for="edit_excerpt">Ringkasan Berita</label>
                    <textarea id="edit_excerpt" name="excerpt" class="form-control" required placeholder="Tulis ringkasan singkat berita"></textarea>
                    <small style="color: #666;">Ringkasan singkat yang akan ditampilkan di halaman berita.</small>
                </div>
                
                <div class="form-group">
                    <label for="edit_konten">Konten Lengkap</label>
                    <textarea id="edit_konten" name="konten" class="form-control" required placeholder="Tulis konten lengkap berita" rows="6"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_kategori">Kategori</label>
                    <select id="edit_kategori" name="kategori" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Pendidikan">Pendidikan</option>
                        <option value="Kebudayaan">Kebudayaan</option>
                        <option value="Pengumuman">Pengumuman</option>
                        <option value="Kegiatan">Kegiatan</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_gambar">Gambar Berita</label>
                    <input type="file" id="edit_gambar" name="gambar" class="form-control" accept="image/*">
                    <small style="color: #666;">Format: JPG, PNG, GIF. Maksimal 5MB. Kosongkan jika tidak ingin mengubah gambar.</small>
                </div>
                
                <div class="form-group">
                    <label for="edit_tanggal_publish">Tanggal Publish</label>
                    <input type="date" id="edit_tanggal_publish" name="tanggal_publish" class="form-control" required>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline-primary" onclick="tutupPopupEditBerita()">Batal</button>
                    <button type="submit" class="btn-success">
                        <span class="icon icon-edit"></span> Update Berita
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fungsi untuk popup tambah berita
        function bukaPopupTambahBerita() {
            document.getElementById('popupTambahBerita').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function tutupPopupTambahBerita() {
            document.getElementById('popupTambahBerita').classList.remove('active');
            document.body.style.overflow = 'auto';
            document.getElementById('formTambahBerita').reset();
        }

        // Fungsi untuk popup edit berita (SIMPAN VERSI AJAX)
        function bukaPopupEditBerita(id) {
            // Ambil data berita via AJAX dari get-berita.php
            fetch('get-berita.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    
                    document.getElementById('edit_id').value = data.id;
                    document.getElementById('edit_judul').value = data.judul;
                    document.getElementById('edit_excerpt').value = data.excerpt;
                    document.getElementById('edit_konten').value = data.konten;
                    document.getElementById('edit_kategori').value = data.kategori;
                    document.getElementById('edit_tanggal_publish').value = data.tanggal_publish;
                    
                    document.getElementById('popupEditBerita').classList.add('active');
                    document.body.style.overflow = 'hidden';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat data berita');
                });
        }

        function tutupPopupEditBerita() {
            document.getElementById('popupEditBerita').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Tutup popup ketika klik di luar content
        document.getElementById('popupTambahBerita').addEventListener('click', function(e) {
            if (e.target === this) {
                tutupPopupTambahBerita();
            }
        });

        document.getElementById('popupEditBerita').addEventListener('click', function(e) {
            if (e.target === this) {
                tutupPopupEditBerita();
            }
        });

        // Tutup popup dengan ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                tutupPopupTambahBerita();
                tutupPopupEditBerita();
            }
        });

        // Set form action ke halaman ini sendiri
        document.addEventListener('DOMContentLoaded', function() {
            const currentUrl = window.location.href.split('?')[0];
            document.getElementById('formTambahBerita').action = currentUrl;
            document.getElementById('formEditBerita').action = currentUrl;
            
            // Auto-close alerts setelah 5 detik
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });

        // Dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const userDropdown = document.getElementById('userDropdown');
            const userDropdownMenu = document.getElementById('userDropdownMenu');
            
            if (userDropdown) {
                userDropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    userDropdownMenu.classList.toggle('show');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userDropdown.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                        userDropdownMenu.classList.remove('show');
                    }
                });
            }
        });
    </script>
</body>
</html>