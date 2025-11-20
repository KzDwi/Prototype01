<?php
session_start();
require_once 'functions.php';

// Handle filter kategori dan pencarian
$kategori_aktif = $_GET['kategori'] ?? 'semua';
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$cari_keyword = $_GET['cari'] ?? '';

// Pagination settings
$jumlah_per_halaman = 6;
$offset = ($halaman_aktif - 1) * $jumlah_per_halaman;

// Ambil data dari database berdasarkan filter
if (!empty($cari_keyword)) {
    // Jika ada pencarian
    $berita_tampil = cariBerita($cari_keyword);
    $total_berita = count($berita_tampil);
    $total_halaman = ceil($total_berita / $jumlah_per_halaman);
    $berita_tampil = array_slice($berita_tampil, $offset, $jumlah_per_halaman);
} else {
    // Jika tidak ada pencarian, ambil berdasarkan kategori
    $berita_tampil = ambilSemuaBerita($kategori_aktif, $jumlah_per_halaman, $offset);
    $total_berita = hitungTotalBerita($kategori_aktif);
    $total_halaman = ceil($total_berita / $jumlah_per_halaman);
}

// Validasi halaman
if ($halaman_aktif < 1) $halaman_aktif = 1;
if ($halaman_aktif > $total_halaman && $total_halaman > 0) $halaman_aktif = $total_halaman;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css" />
    <title>Berita - Dinas Pendidikan dan Kebudayaan Kabupaten Paser</title>
    <style>
        /* Additional Styles for Berita Page */
        .berita-hero {
            background: linear-gradient(135deg, #003399 0%, #002280 100%);
            color: white;
            padding: 80px 0 60px;
            text-align: center;
            margin-top: 80px;
        }

        .berita-hero h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .berita-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        .berita-filter {
            background: #f8f9fa;
            padding: 30px 0;
            border-bottom: 1px solid #eee;
        }

        .filter-container {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }

        .filter-btn {
            padding: 10px 20px;
            border: 2px solid #003399;
            background: white;
            color: #003399;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: #003399;
            color: white;
            text-decoration: none;
        }

        .search-box {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            justify-content: center;
        }

        .search-box input {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 25px;
            width: 300px;
            max-width: 100%;
        }

        .search-box button {
            padding: 10px 20px;
            background: #003399;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
        }

        .berita-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            padding: 50px 0;
        }

        .berita-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .berita-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .berita-image {
            height: 200px;
            overflow: hidden;
        }

        .berita-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .berita-card:hover .berita-image img {
            transform: scale(1.05);
        }

        .berita-content {
            padding: 25px;
        }

        .berita-category {
            display: inline-block;
            background: #e6f7ff;
            color: #003399;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .berita-title {
            font-size: 1.3rem;
            color: #003399;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .berita-excerpt {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .berita-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #888;
            font-size: 0.85rem;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        .berita-date {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .read-more {
            color: #003399;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: gap 0.3s ease;
        }

        .read-more:hover {
            gap: 10px;
            text-decoration: none;
            color: #002280;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 40px 0;
        }

        .page-btn {
            padding: 10px 15px;
            border: 1px solid #ddd;
            background: white;
            color: #333;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .page-btn:hover,
        .page-btn.active {
            background: #003399;
            color: white;
            border-color: #003399;
            text-decoration: none;
        }

        .page-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .no-berita {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-berita h3 {
            color: #003399;
            margin-bottom: 10px;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .berita-hero {
                padding: 60px 0 40px;
                margin-top: 70px;
            }

            .berita-hero h1 {
                font-size: 2rem;
            }

            .berita-grid {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 30px 0;
            }

            .filter-container {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-btn {
                text-align: center;
            }

            .search-box {
                flex-direction: column;
                align-items: center;
            }

            .search-box input {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .berita-hero h1 {
                font-size: 1.8rem;
            }

            .berita-content {
                padding: 20px;
            }

            .berita-title {
                font-size: 1.1rem;
            }
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
                        <li><a href="berita.php" onclick="closeMobileMenu()" class="active">Berita</a></li>
                        <li><a href="#" onclick="closeMobileMenu()">Kontak</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Breadcrumb -->
    <section class="breadcrumb-section">
        <div class="container">
            <nav class="breadcrumb-nav">
                <a href="index.html">Halaman Utama</a>
                <span>/</span>
                <a href="berita.php" class="active">Berita</a>
                <?php if ($kategori_aktif !== 'semua'): ?>
                    <span>/</span>
                    <span class="active"><?php echo htmlspecialchars($kategori_aktif); ?></span>
                <?php endif; ?>
                <?php if (!empty($cari_keyword)): ?>
                    <span>/</span>
                    <span class="active">Pencarian: "<?php echo htmlspecialchars($cari_keyword); ?>"</span>
                <?php endif; ?>
            </nav>
        </div>
    </section>

    <!-- Berita Hero Section -->
    <section class="berita-hero">
        <div class="container">
            <h1>Berita Terkini</h1>
            <p>Informasi terbaru seputar pendidikan dan kebudayaan di Kabupaten Paser</p>
        </div>
    </section>

    <!-- Berita Filter -->
    <section class="berita-filter">
        <div class="container">
            <div class="filter-container">
                <a href="berita.php?kategori=semua" class="filter-btn <?php echo $kategori_aktif === 'semua' ? 'active' : ''; ?>">Semua Berita</a>
                <a href="berita.php?kategori=Pendidikan" class="filter-btn <?php echo $kategori_aktif === 'Pendidikan' ? 'active' : ''; ?>">Pendidikan</a>
                <a href="berita.php?kategori=Kebudayaan" class="filter-btn <?php echo $kategori_aktif === 'Kebudayaan' ? 'active' : ''; ?>">Kebudayaan</a>
                <a href="berita.php?kategori=Pengumuman" class="filter-btn <?php echo $kategori_aktif === 'Pengumuman' ? 'active' : ''; ?>">Pengumuman</a>
                <a href="berita.php?kategori=Kegiatan" class="filter-btn <?php echo $kategori_aktif === 'Kegiatan' ? 'active' : ''; ?>">Kegiatan</a>
            </div>
            
            <!-- Search Box -->
            <form method="GET" class="search-box">
                <input type="text" name="cari" placeholder="Cari berita..." value="<?php echo htmlspecialchars($cari_keyword); ?>">
                <button type="submit">Cari</button>
                <?php if ($kategori_aktif !== 'semua'): ?>
                    <input type="hidden" name="kategori" value="<?php echo htmlspecialchars($kategori_aktif); ?>">
                <?php endif; ?>
            </form>
        </div>
    </section>

    <!-- Berita Grid -->
    <section class="berita-section">
        <div class="container">
            <?php if (empty($berita_tampil)): ?>
                <div class="no-berita">
                    <h3>Tidak ada berita ditemukan</h3>
                    <p>Silakan pilih kategori lain atau gunakan kata kunci pencarian yang berbeda.</p>
                    <a href="berita.php" class="btn" style="margin-top: 15px;">Lihat Semua Berita</a>
                </div>
            <?php else: ?>
                <div class="berita-grid">
                    <?php foreach ($berita_tampil as $berita): ?>
                    <article class="berita-card fade-in">
                        <div class="berita-image">
                            <?php if (!empty($berita['thumbnail'])): ?>
                                <img src="<?php echo htmlspecialchars($berita['thumbnail']); ?>" alt="<?php echo htmlspecialchars($berita['judul']); ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/400x200/003399/ffffff?text=Berita+Disdikbud" alt="<?php echo htmlspecialchars($berita['judul']); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="berita-content">
                            <span class="berita-category"><?php echo htmlspecialchars($berita['kategori']); ?></span>
                            <h3 class="berita-title"><?php echo htmlspecialchars($berita['judul']); ?></h3>
                            <p class="berita-excerpt"><?php echo htmlspecialchars($berita['excerpt']); ?></p>
                            <div class="berita-meta">
                                <span class="berita-date">📅 <?php echo formatTanggalIndonesia($berita['tanggal_publish']); ?></span>
                                <a href="berita-detail.php?id=<?php echo $berita['id']; ?>" class="read-more">Baca Selengkapnya →</a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_halaman > 1): ?>
                <div class="pagination">
                    <?php if ($halaman_aktif > 1): ?>
                        <a href="berita.php?<?php 
                            echo 'halaman=' . ($halaman_aktif - 1);
                            if ($kategori_aktif !== 'semua') echo '&kategori=' . urlencode($kategori_aktif);
                            if (!empty($cari_keyword)) echo '&cari=' . urlencode($cari_keyword);
                        ?>" class="page-btn">← Sebelumnya</a>
                    <?php else: ?>
                        <span class="page-btn disabled">← Sebelumnya</span>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                        <a href="berita.php?<?php 
                            echo 'halaman=' . $i;
                            if ($kategori_aktif !== 'semua') echo '&kategori=' . urlencode($kategori_aktif);
                            if (!empty($cari_keyword)) echo '&cari=' . urlencode($cari_keyword);
                        ?>" class="page-btn <?php echo $i == $halaman_aktif ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($halaman_aktif < $total_halaman): ?>
                        <a href="berita.php?<?php 
                            echo 'halaman=' . ($halaman_aktif + 1);
                            if ($kategori_aktif !== 'semua') echo '&kategori=' . urlencode($kategori_aktif);
                            if (!empty($cari_keyword)) echo '&cari=' . urlencode($cari_keyword);
                        ?>" class="page-btn">Selanjutnya →</a>
                    <?php else: ?>
                        <span class="page-btn disabled">Selanjutnya →</span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
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
                <p>&copy; 2024 Dinas Pendidikan dan Kebudayaan Kabupaten Paser. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Fade in effect for news cards
            const fadeElements = document.querySelectorAll('.fade-in');
            
            function fadeInOnScroll() {
                fadeElements.forEach(element => {
                    const elementTop = element.getBoundingClientRect().top;
                    const elementVisible = 150;
                    
                    if (elementTop < window.innerHeight - elementVisible) {
                        element.classList.add('visible');
                    }
                });
            }

            // Initialize fade in
            fadeInOnScroll();
            window.addEventListener('scroll', fadeInOnScroll);
        });

        // Existing mobile menu functions
        function toggleMobileMenu() {
            const nav = document.getElementById('main-nav');
            nav.classList.toggle('active');
        }

        function closeMobileMenu() {
            const nav = document.getElementById('main-nav');
            nav.classList.remove('active');
        }
    </script>
</body>
</html>