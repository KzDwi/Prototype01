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

// Handle hapus berita
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    if (hapusBerita($id)) {
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
        $pesan_sukses = 'Status berita berhasil diubah!';
    } else {
        $pesan_error = 'Gagal mengubah status berita. Silakan coba lagi.';
    }
}

// Ambil semua berita untuk ditampilkan di tabel
$semua_berita = ambilSemuaBeritaAdmin();

// Pesan sukses/error
$pesan_sukses = $_GET['sukses'] ?? '';
$pesan_error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Berita - Dinas Pendidikan dan Kebudayaan Kabupaten Paser</title>
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

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        /* Status Badge */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
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
        }

        /* Thumbnail Preview */
        .thumbnail-preview {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        /* Responsive Table */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
            
            .table-actions {
                flex-direction: column;
            }
            
            .action-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
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
                <img src="https://cdn-icons-png.flaticon.com/512/3938/3938887.png" alt="Logo">
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
                    <a href="admin-dashboard.php" class="sidebar-menu-link">
                        <span class="icon icon-dashboard"></span>
                        <span class="sidebar-menu-text">Dasbor</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin-berita.php" class="sidebar-menu-link active">
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
                                    <th>No</th>
                                    <th>Thumbnail</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Penulis</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Dibaca</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($semua_berita)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">Belum ada berita</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($semua_berita as $index => $berita): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
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
                                        <td><?php echo htmlspecialchars($berita['penulis']); ?></td>
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
                                                <a href="?ubah_status=<?php echo $berita['id']; ?>&status=<?php echo $berita['status'] === 'publish' ? 'draft' : 'publish'; ?>" class="btn btn-sm btn-outline-primary">
                                                    <span class="icon icon-settings"></span><?php echo $berita['status'] === 'publish' ? 'Draft' : 'Publish'; ?>
                                                </a>
                                                <a href="?hapus=<?php echo $berita['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus berita ini?')">
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
            
            <form method="POST" action="admin-dashboard.php" enctype="multipart/form-data" id="formTambahBerita">
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

    <!-- Popup Edit Berita -->
    <div class="popup-overlay" id="popupEditBerita">
        <div class="popup-content">
            <div class="popup-header">
                <h3 class="popup-title">Edit Berita</h3>
                <button class="popup-close" onclick="tutupPopupEditBerita()">&times;</button>
            </div>
            
            <form method="POST" action="update-berita.php" enctype="multipart/form-data" id="formEditBerita">
                <input type="hidden" id="edit_id" name="id">
                
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
                    <button type="submit" name="update_berita" class="btn-success">
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

        // Fungsi untuk popup edit berita
        function bukaPopupEditBerita(id) {
            // Ambil data berita via AJAX
            fetch('get-berita.php?id=' + id)
                .then(response => response.json())
                .then(data => {
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