<?php
session_start();

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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Dinas Pendidikan dan Kebudayaan Kabupaten Paser</title>
    <link rel="stylesheet" href="css/admin-styles.css">
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
                    <a href="#" class="sidebar-menu-link active">
                        <span class="icon icon-dashboard"></span>
                        <span class="sidebar-menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link">
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
                                <button class="btn btn-primary w-100 mb-2">
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

    <script>
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