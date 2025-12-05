<?php
session_start();
require_once 'config.php'; // Memastikan koneksi database tersedia
require_once 'functions.php';

$id_berita = $_GET['id'] ?? 0;

// 1. Ambil detail berita utama
$berita = ambilBeritaById($id_berita);

if (!$berita) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>Berita tidak ditemukan</h1>";
    exit;
}

// Update counter dibaca
updateCounterDibaca($id_berita);

// 2. Ambil Berita Lain untuk Sidebar (Terbaru)
$berita_terbaru = ambilSemuaBerita('semua', 5);

// 3. Ambil Berita Populer
try {
    $pdo = getDatabaseConnection();
    $stmt_populer = $pdo->prepare("SELECT id, judul, dibaca, tanggal_publish FROM berita WHERE status = 'publish' AND id != ? ORDER BY dibaca DESC LIMIT 5");
    $stmt_populer->execute([$id_berita]);
    $berita_populer = $stmt_populer->fetchAll();
} catch (Exception $e) {
    $berita_populer = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($berita['judul']); ?> - Disdikbud Paser</title>
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* === PERBAIKAN LAYOUT DETAIL BERITA === */
        .berita-detail-section {
            padding: 40px 0 80px;
            background-color: #f8f9fa;
        }

        .berita-grid-layout {
            display: grid;
            grid-template-columns: 2.5fr 1fr;
            gap: 40px;
            align-items: start;
        }

        /* === MAIN CONTENT STYLES === */
        .berita-main-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .berita-header {
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }

        .kategori-badge {
            display: inline-block;
            background: #e3f2fd;
            color: #003399;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .berita-title {
            font-size: 2.2rem;
            color: #1a1a1a;
            line-height: 1.3;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .berita-meta {
            display: flex;
            gap: 20px;
            color: #666;
            font-size: 0.9rem;
            flex-wrap: wrap;
        }

        .berita-meta i {
            color: #e36159;
            margin-right: 5px;
        }

        .featured-image-wrapper {
            width: 100%;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .featured-image-wrapper img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.3s ease;
        }

        .featured-image-wrapper:hover img {
            transform: scale(1.02);
        }

        .berita-body {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
        }

        .berita-body p {
            margin-bottom: 20px;
            text-align: justify;
        }

        /* === SIDEBAR STYLES === */
        .sidebar-widget {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            border-top: 4px solid #003399;
        }

        .widget-title {
            font-size: 1.2rem;
            color: #1a1a1a;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
            position: relative;
        }

        .widget-title::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 50px;
            height: 2px;
            background: #e36159;
        }

        .widget-news-item {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        .widget-news-item:last-child {
            margin-bottom: 0;
        }

        .widget-thumb {
            width: 100%;
            height: 150px;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 10px;
            position: relative;
        }

        .widget-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .widget-news-item:hover .widget-thumb img {
            transform: scale(1.1);
        }

        .widget-content h5 {
            font-size: 1rem;
            line-height: 1.4;
            margin-bottom: 5px;
        }

        .widget-content h5 a {
            color: #333;
            transition: color 0.3s;
        }

        .widget-content h5 a:hover {
            color: #003399;
        }

        .widget-date {
            font-size: 0.8rem;
            color: #888;
        }

        .list-news-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #eee;
        }

        .list-news-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .list-number {
            font-size: 1.2rem;
            font-weight: 700;
            color: #e3f2fd;
            -webkit-text-stroke: 1px #003399;
            margin-right: 15px;
            min-width: 25px;
        }

        .list-content h6 {
            font-size: 0.95rem;
            line-height: 1.4;
            margin-bottom: 5px;
        }

        .list-content h6 a {
            color: #444;
        }

        .list-content h6 a:hover {
            color: #e36159;
        }

        /* Responsive Breakpoints */
        @media (max-width: 992px) {
            .berita-grid-layout {
                grid-template-columns: 1fr;
            }
            .sidebar-wrapper {
                margin-top: 30px;
            }
            .widget-news-item {
                flex-direction: row;
                gap: 15px;
            }
            .widget-thumb {
                width: 120px;
                height: 90px;
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>
    <header id="main-header">
        <div class="container">
            <div class="header-top">
                <div class="logo">
                    <img src="assets/logo-kabupaten.png" alt="Logo Pemerintahan">
                    <div class="logo-text">
                        <h1>Dinas Pendidikan dan Kebudayaan</h1>
                        <p>Kabupaten Paser</p>
                    </div>
                </div>
                <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
                <nav id="main-nav">
                    <ul>
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="profil.php">Profil</a></li>
                        <li><a href="layanan.php">Layanan</a></li>
                        <li><a href="berita.php" class="active">Berita</a></li>
                        <li><a href="kontak.php">Kontak</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <section class="breadcrumb-section" style="background: #fff; padding: 20px 0; border-bottom: 1px solid #eee;">
        <div class="container">
            <nav class="breadcrumb-nav" style="width:100%; background: transparent;">
                <a href="index.php">Beranda</a>
                <span>/</span>
                <a href="berita.php">Berita</a>
                <span>/</span>
                <span class="active" style="color: #666;"><?php echo substr($berita['judul'], 0, 50) . '...'; ?></span>
            </nav>
        </div>
    </section>

    <section class="berita-detail-section">
        <div class="container"> 
            <div class="berita-grid-layout">
                
                <main class="berita-main-card fade-in">
                    <div class="berita-header">
                        <span class="kategori-badge"><?php echo htmlspecialchars($berita['kategori']); ?></span>
                        <h1 class="berita-title"><?php echo htmlspecialchars($berita['judul']); ?></h1>
                        <div class="berita-meta">
                            <span><i class="far fa-calendar-alt"></i> <?php echo formatTanggalIndonesia($berita['tanggal_publish']); ?></span>
                            <span><i class="far fa-user"></i> <?php echo htmlspecialchars($berita['penulis']); ?></span>
                            <span><i class="far fa-eye"></i> <?php echo number_format($berita['dibaca']); ?>x dibaca</span>
                        </div>
                    </div>

                    <?php if (!empty($berita['gambar'])): ?>
                    <div class="featured-image-wrapper">
                        <img src="<?php echo htmlspecialchars($berita['gambar']); ?>" alt="<?php echo htmlspecialchars($berita['judul']); ?>">
                    </div>
                    <?php endif; ?>

                    <div class="berita-body">
                        <?php echo $berita['konten']; ?>
                    </div>

                    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #eee;">
                        <a href="berita.php" class="btn" style="border-radius: 30px;"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Berita</a>
                    </div>
                </main>

                <aside class="sidebar-wrapper">
                    
                    <div class="sidebar-widget fade-in">
                        <h4 class="widget-title">Berita Terbaru</h4>
                        
                        <?php 
                        $count_shown = 0;
                        foreach ($berita_terbaru as $item): 
                            if ($item['id'] == $id_berita) continue;
                            if ($count_shown >= 2) break;
                            $thumb = !empty($item['thumbnail']) ? $item['thumbnail'] : (!empty($item['gambar']) ? $item['gambar'] : 'assets/placeholder.jpg');
                        ?>
                            <div class="widget-news-item">
                                <div class="widget-thumb">
                                    <a href="berita-detail.php?id=<?php echo $item['id']; ?>">
                                        <img src="<?php echo htmlspecialchars($thumb); ?>" alt="Thumbnail">
                                    </a>
                                </div>
                                <div class="widget-content">
                                    <h5><a href="berita-detail.php?id=<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['judul']); ?></a></h5>
                                    <span class="widget-date"><i class="far fa-clock"></i> <?php echo waktulalu($item['tanggal_publish']); ?></span>
                                </div>
                            </div>
                        <?php 
                            $count_shown++;
                        endforeach; 
                        ?>
                    </div>

                    <div class="sidebar-widget fade-in" style="background: #f0f8ff; border-top-color: #e36159;">
                        <h4 class="widget-title">Info</h4>
                        <p style="font-size: 0.9rem; margin-bottom: 10px;">Bagikan berita ini jika bermanfaat:</p>
                        <div style="display: flex; gap: 10px;">
                            <a href="#" class="btn" style="padding: 5px 10px; background: #3b5998;"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn" style="padding: 5px 10px; background: #25d366;"><i class="fab fa-whatsapp"></i></a>
                            <a href="#" class="btn" style="padding: 5px 10px; background: #1da1f2;"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>

                    <div class="sidebar-widget fade-in">
                        <h4 class="widget-title">Terpopuler</h4>
                        
                        <?php if(!empty($berita_populer)): ?>
                            <?php foreach ($berita_populer as $index => $populer): ?>
                                <div class="list-news-item">
                                    <div class="list-number"><?php echo $index + 1; ?></div>
                                    <div class="list-content">
                                        <h6><a href="berita-detail.php?id=<?php echo $populer['id']; ?>"><?php echo htmlspecialchars($populer['judul']); ?></a></h6>
                                        <small style="color: #999; font-size: 0.75rem;"><?php echo number_format($populer['dibaca']); ?> tayangan</small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Belum ada data populer.</p>
                        <?php endif; ?>
                    </div>

                </aside>

            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content" id="kontak">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3987.6060327839327!2d116.1914303!3d-1.9081035!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df047988e6b3c0b%3A0xdaa84941bfe1b7df!2sJl.%20Jenderal%20Sudirman%20No.27%2C%20Tanah%20Grogot%2C%20Kec.%20Tanah%20Grogot%2C%20Kabupaten%20Paser%2C%20Kalimantan%20Timur%2076251!5e0!3m2!1sid!2sid!4v1764218368388!5m2!1sid!2sid" width="250" height="200" style="border:0; border-radius: 0.3rem;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <div class="footer-section">
                    <h3>Kontak Kami</h3>
                    <ul>
                        <li>Jl. Jenderal Sudirman No. 27, Tanah Grogot, Kabupaten Paser, Kalimantan Timur 76251</li>
                        <li>Telp: (0543) 21023</li>
                        <li>Email: disdik@paserkab.go.id</li>
                        <li class="social-icons">
                            <a href="#" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" aria-label="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" aria-label="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Layanan Cepat</h3>
                    <ul>
                        <li><a href="layanan.html%20#layanan" onclick="scrollToLayanan()">Legalisir Ijazah/Dokumen Kelulusan</a></li>
                        <li><a href="layanan.html%20#layanan" onclick="scrollToLayanan()">Surat Keterangan Pindah Sekolah</a></li>
                        <li><a href="layanan.html%20#layanan" onclick="scrollToLayanan()">Tunjangan Profesi Guru</a></li>
                        <li><a href="layanan.html%20#layanan" onclick="scrollToLayanan()">Izin Pendirian Satuan Pendidikan</a></li>
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
            </div>
            <div class="footer-bottom">
                <p>© 2025 Dinas Pendidikan dan Kebudayaan Kabupaten Paser. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>
    
    <script>
        function toggleMobileMenu() {
            const nav = document.getElementById('main-nav');
            nav.classList.toggle('active');
        }

        // === SCRIPT TRANSISI FADE-IN ===
        // Script ini akan mencari semua elemen dengan class 'fade-in'
        // dan menambahkan class 'visible' saat elemen tersebut masuk ke layar
        document.addEventListener("DOMContentLoaded", function() {
            const fadeElements = document.querySelectorAll('.fade-in');

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        // Optional: Stop observing once visible
                        // observer.unobserve(entry.target); 
                    }
                });
            }, {
                threshold: 0.1 // Animasi mulai saat 10% elemen terlihat
            });

            fadeElements.forEach(el => observer.observe(el));
        });
    </script>
</body>
</html>