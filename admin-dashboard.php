<?php
session_start();
require_once 'functions.php';

// Cek jika admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Handle form tambah berita
$pesan_sukses = '';
$pesan_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_berita'])) {
    try {
        $gambar_path = '';
        $thumbnail_path = '';
        
        // Upload gambar jika ada
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
            'penulis' => $_SESSION['admin_username'],
            'tanggal_publish' => $_POST['tanggal_publish']
        ];
        
        if (tambahBerita($data_berita)) {
            $pesan_sukses = 'Berita berhasil ditambahkan!';
            // Refresh halaman untuk menampilkan berita baru
            echo "<script>alert('Berita berhasil ditambahkan!'); window.location.href = 'admin-dashboard.php';</script>";
            exit;
        } else {
            $pesan_error = 'Gagal menambahkan berita. Silakan coba lagi.';
        }
    } catch (Exception $e) {
        $pesan_error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Admin - Dinas Pendidikan dan Kebudayaan Kabupaten Paser</title>
    <link rel="stylesheet" href="css/admin-styles.css">
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
            max-width: 600px;
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
            
            <ul class="navbar-nav navbar-nav-right">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="icon icon-bell"></span>
                    </a>
                </li>
                <li class="nav-item user-dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button">
                        <span class="icon icon-user"></span>
                        <?php echo $_SESSION['admin_username']; ?>
                    </a>
                    <div class="dropdown-menu" id="userDropdownMenu">
                        <div class="dropdown-header">
                            <h6><?php echo $_SESSION['admin_username']; ?></h6>
                            <span class="text-muted">Administrator</span>
                        </div>
                        <a class="dropdown-item" href="#">
                            <span class="icon icon-user"></span> Profil
                        </a>
                        <a class="dropdown-item" href="#">
                            <span class="icon icon-settings"></span> Pengaturan
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="?logout=true">
                            <span class="icon icon-logout"></span> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link active">
                        <span class="icon icon-dashboard"></span>
                        <span class="sidebar-menu-text">Dasbor</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin-berita.php" class="sidebar-menu-link">
                        <span class="icon icon-news"></span>
                        <span class="sidebar-menu-text">Berita</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link">
                        <span class="icon icon-users"></span>
                        <span class="sidebar-menu-text">Pengguna</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link">
                        <span class="icon icon-student"></span>
                        <span class="sidebar-menu-text">Siswa</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link">
                        <span class="icon icon-stats"></span>
                        <span class="sidebar-menu-text">Statistik</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link">
                        <span class="icon icon-settings"></span>
                        <span class="sidebar-menu-text">Pengaturan</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="content-wrapper">
            <!-- Welcome Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3>Selamat Datang, <?php echo $_SESSION['admin_username']; ?>!</h3>
                    <p class="text-muted mb-0">Sistem Administrasi Dinas Pendidikan dan Kebudayaan Kabupaten Paser</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="stats-card">
                                <div class="icon icon-users"></div>
                                <div class="number">1,254</div>
                                <div class="label">Total Siswa</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stats-card">
                                <div class="icon icon-school"></div>
                                <div class="number">48</div>
                                <div class="label">Sekolah</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stats-card">
                                <div class="icon icon-teacher"></div>
                                <div class="number">326</div>
                                <div class="label">Guru</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stats-card">
                                <div class="icon icon-file"></div>
                                <div class="number">89</div>
                                <div class="label">Berita</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity and Quick Actions -->
            <div class="row">
                <div class="col-6">
                    <div class="card">
                        <div class="card-header header-sm">
                            <h5 class="mb-0">Aktivitas Terbaru</h5>
                        </div>
                        <div class="card-body">
                            <div class="activity-list">
                                <div class="activity-item p-3">
                                    <div class="d-flex align-items-center">
                                        <span class="icon icon-success me-3"></span>
                                        <div>
                                            <p class="mb-1">Admin menambahkan pengguna baru</p>
                                            <small class="text-muted">2 menit yang lalu</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="activity-item p-3">
                                    <div class="d-flex align-items-center">
                                        <span class="icon icon-primary me-3"></span>
                                        <div>
                                            <p class="mb-1">Berita terbaru diperbarui</p>
                                            <small class="text-muted">1 jam yang lalu</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="activity-item p-3">
                                    <div class="d-flex align-items-center">
                                        <span class="icon icon-warning me-3"></span>
                                        <div>
                                            <p class="mb-1">Laporan statistik di-generate</p>
                                            <small class="text-muted">3 jam yang lalu</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-6">
                    <div class="card">
                        <div class="card-header header-sm">
                            <h5 class="mb-0">Aksi Cepat</h5>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions">
                                <button class="btn btn-primary w-100 mb-2" onclick="bukaPopupTambahBerita()">
                                    <span class="icon icon-plus"></span>Tambah Berita
                                </button>
                                <button class="btn btn-outline-primary w-100 mb-2">
                                    <span class="icon icon-upload"></span>Upload Dokumen
                                </button>
                                <button class="btn btn-outline-primary w-100 mb-2">
                                    <span class="icon icon-chart"></span>Lihat Statistik
                                </button>
                                <button class="btn btn-outline-primary w-100">
                                    <span class="icon icon-settings"></span>Pengaturan Sistem
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Data Table -->
            <div class="card mt-3">
                <div class="card-header header-sm">
                    <h5 class="mb-0">Data Terbaru</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Budi Santoso</td>
                                <td>Guru Matematika</td>
                                <td><span class="badge bg-success">Aktif</span></td>
                                <td>2024-01-15</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <span class="icon icon-edit"></span>Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger">
                                        <span class="icon icon-delete"></span>Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Siti Rahayu</td>
                                <td>Staff Administrasi</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>2024-01-14</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <span class="icon icon-edit"></span>Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger">
                                        <span class="icon icon-delete"></span>Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Ahmad Fadli</td>
                                <td>Kepala Sekolah</td>
                                <td><span class="badge bg-success">Aktif</span></td>
                                <td>2024-01-13</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <span class="icon icon-edit"></span>Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger">
                                        <span class="icon icon-delete"></span>Hapus
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
            
            <?php if ($pesan_error): ?>
                <div class="alert alert-error"><?php echo $pesan_error; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" id="formTambahBerita">
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
                    <button type="submit" name="tambah_berita" class="btn-success">
                        <span class="icon icon-plus"></span> Tambah Berita
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fungsi untuk popup
        function bukaPopupTambahBerita() {
            document.getElementById('popupTambahBerita').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function tutupPopupTambahBerita() {
            document.getElementById('popupTambahBerita').classList.remove('active');
            document.body.style.overflow = 'auto';
            // Reset form
            document.getElementById('formTambahBerita').reset();
        }

        // Tutup popup ketika klik di luar content
        document.getElementById('popupTambahBerita').addEventListener('click', function(e) {
            if (e.target === this) {
                tutupPopupTambahBerita();
            }
        });

        // Tutup popup dengan ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                tutupPopupTambahBerita();
            }
        });

        // Dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const userDropdown = document.getElementById('userDropdown');
            const userDropdownMenu = document.getElementById('userDropdownMenu');
            
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
        });
    </script>
</body>
</html>