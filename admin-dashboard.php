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
    <title>Admin Dashboard - Dinas Pendidikan dan Kebudayaan</title>
    <link rel="stylesheet" href="css/styles.css" />
    <style>
        .dashboard-container {
            min-height: calc(100vh - 200px);
            padding: 40px 0;
            background: #f5f5f5;
        }

        .dashboard-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .dashboard-header {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .welcome-message {
            color: #003399;
            margin: 0 0 10px 0;
            font-size: 2rem;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .dashboard-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .dashboard-card h3 {
            color: #003399;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .dashboard-card p {
            color: #666;
            line-height: 1.6;
        }
        
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: #c82333;
            text-decoration: none;
            color: white;
        }

        .admin-info {
            background: #e8f4ff;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .admin-info p {
            margin: 5px 0;
            color: #003399;
        }
    </style>
</head>
<body>
    <!-- Header & Navigation -->
    <header id="main-header">
        <div class="container">
            <div class="header-top">
                <div class="logo">
                    <img src="https://cdn-icons-png.flaticon.com/512/3938/3938887.png" alt="Logo Pemerintahan">
                    <div class="logo-text">
                        <h1>Dinas Pendidikan dan Kebudayaan</h1>
                        <p>Kabupaten Paser</p>
                    </div>
                </div>
                <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
                <nav id="main-nav">
                    <ul>
                        <li><a href="index.html" onclick="closeMobileMenu()">Beranda</a></li>
                        <li><a href="profil.html" onclick="closeMobileMenu()">Profil</a></li>
                        <li><a href="#" onclick="closeMobileMenu()">PPID</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle">Layanan</a>
                            <div class="dropdown-menu">
                                <div class="dropdown-section">
                                    <h4>Layanan Kesiswaan</h4>
                                    <a href="#">Legalisir Ijazah/Dokumen Kelulusan</a>
                                    <a href="#">Penerbitan Surat Keterangan Pindah Sekolah (mutasi)</a>
                                </div>
                                <div class="dropdown-section">
                                    <h4>Layanan Pendidikan & Tenaga Kependidikan</h4>
                                    <a href="#">Pengusulan Tunjangan Profesi Guru (TPG)</a>
                                    <a href="#">Pengurusan Izin Belajar dan Tugas Belajar bagi ASN</a>
                                </div>
                                <div class="dropdown-section">
                                    <h4>Layanan Perizinan</h4>
                                    <a href="#">Izin Pendirian Satuan Pendidikan (PAUD/SD/SMP/LKP)</a>
                                </div>
                            </div>
                        </li>
                        <li><a href="#" onclick="closeMobileMenu()">Berita</a></li>
                        <li><a href="#" onclick="closeMobileMenu()">Kontak</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <section class="dashboard-container">
        <div class="dashboard-content">
            <div class="dashboard-header fade-in">
                <h1 class="welcome-message">Selamat datang, <?php echo $_SESSION['admin_username']; ?>!</h1>
                <p>Ini adalah halaman dashboard administrator Dinas Pendidikan dan Kebudayaan Kabupaten Paser</p>
                
                <div class="admin-info">
                    <p><strong>Username:</strong> <?php echo $_SESSION['admin_username']; ?></p>
                    <p><strong>Role:</strong> Administrator</p>
                    <!-- <p><strong>Login Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p> -->
                </div>
                
                <a href="?logout=true" class="logout-btn">Logout</a>
            </div>
            
            <div class="dashboard-grid">
                <a href="#">
                    <div class="dashboard-card fade-in">
                        <h3>📊 Manajemen Konten</h3>
                        <p>Kelola berita, artikel, pengumuman, dan konten website lainnya. Tambah, edit, atau hapus konten sesuai kebutuhan.</p>
                    </div>
                </a>
                    
                <a href="#">
                    <div class="dashboard-card fade-in">
                        <h3>📁 Kelola Berkas</h3>
                        <p>Kelola dokumen dan berkas penting yang terkait dengan administrasi dan layanan publik.</p>
                    </div>
                </a>
                
                <a href="#">
                    <div class="dashboard-card fade-in">
                        <h3>📈 Statistik Website</h3>
                        <p>Lihat statistik pengunjung website, traffic, dan analisis performa konten.</p>
                    </div>
                </a>

                <a href="#">
                    <div class="dashboard-card fade-in">
                        <h3>📋 Laporan</h3>
                        <p>Hasilkan laporan aktivitas sistem, pengguna, dan kinerja website.</p>
                    </div>
                </a>

                <a href="#">
                    <div class="dashboard-card fade-in">
                        <h3>⚙️ Pengaturan</h3>
                        <p>Konfigurasi sistem, tema website, dan pengaturan umum lainnya.</p>
                    </div>
                </a>

                <a href="#">
                    <div class="dashboard-card fade-in">
                        <h3>🔒 Keamanan</h3>
                        <p>Kelola keamanan sistem, backup data, dan monitoring aktivitas mencurigakan.</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Kontak Kami</h3>
                    <ul>
                        <li>Jl. Jenderal Sudirman No. 27, Tanah Grogot, Kabupaten Paser, Kalimantan Timur 76251</li>
                        <li>Telp: (0543) 21023</li>
                        <li>Email: disdik@paserkab.go.id</li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Layanan Cepat</h3>
                    <ul>
                        <li><a href="#">Pengaduan Masyarakat</a></li>
                        <li><a href="#">Informasi Publik</a></li>
                        <li><a href="#">Perizinan Online</a></li>
                        <li><a href="#">Data Statistik</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Tautan Terkait</h3>
                    <ul>
                        <li><a href="#">Kementerian Dalam Negeri</a></li>
                        <li><a href="#">Badan Pusat Statistik</a></li>
                        <li><a href="#">Kementerian Keuangan</a></li>
                        <li><a href="#">Portal Nasional</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Media Sosial</h3>
                    <ul>
                        <li><a href="#">Facebook</a></li>
                        <li><a href="#">Twitter</a></li>
                        <li><a href="#">Instagram</a></li>
                        <li><a href="#">YouTube</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Pemerintah Daerah. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
        // Fade in effect untuk dashboard
        document.addEventListener('DOMContentLoaded', function() {
            const fadeElements = document.querySelectorAll('.fade-in');
            
            fadeElements.forEach(element => {
                setTimeout(() => {
                    element.classList.add('visible');
                }, 100);
            });
        });

        // Existing JavaScript functions
        function toggleMobileMenu() {
            const nav = document.getElementById('main-nav');
            nav.classList.toggle('active');
        }

        function closeMobileMenu() {
            const nav = document.getElementById('main-nav');
            nav.classList.remove('active');
            
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }
    </script>
</body>
</html>