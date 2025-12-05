<?php
// index.php
session_start();
require_once 'functions.php';

// FUNGSI UNTUK MEMBACA CONTENT.JSON
function getContentData() {
    $file_path = 'data/content.json';
    if (file_exists($file_path)) {
        $data = json_decode(file_get_contents($file_path), true);
        return $data ?: ['index' => []];
    }
    return ['index' => []];
}

// Ambil semua data dari content.json
$content_data = getContentData();
$index_data = $content_data['index'] ?? [];

// Ambil data pimpinan dari content.json
$pimpinan_data = $index_data['pimpinan_data'] ?? [
    [
        'nama' => 'Dr. Fahmi Fadli',
        'jabatan' => 'Bupati Paser',
        'foto' => 'assets/Bupati_Paser_Fahmi_Fadli.jpg'
    ],
    [
        'nama' => 'H. Ikhwan Antasari, S.Sos.',
        'jabatan' => 'Wakil Bupati Paser',
        'foto' => 'assets/Wakil_Bupati_Paser_Ikhwan_Antasari.jpg'
    ],
    [
        'nama' => 'Drs. Katsul Wijaya, M.Si',
        'jabatan' => 'Sekretaris Daerah',
        'foto' => 'assets/sekda-paser-2024.jpg'
    ]
];

// Ambil berita terbaru untuk slider (tetap dari database)
$berita_terbaru = ambilSemuaBerita('semua', 3);

// Ambil data layanan dari content.json
$layanan_data = $index_data['layanan_data'] ?? [
    [
        'id' => 'legalisir-ijazah',
        'title' => 'Legalisir Ijazah/Dokumen Kelulusan',
        'desc' => 'Layanan legalisir ijazah dan dokumen kelulusan untuk berbagai keperluan administrasi.',
        'icon' => 'assets/legalisir.png'
    ],
    [
        'id' => 'surat-mutasi',
        'title' => 'Surat Keterangan Pindah Sekolah',
        'desc' => 'Layanan penerbitan surat mutasi untuk siswa yang akan berpindah sekolah.',
        'icon' => 'assets/document.png'
    ],
    [
        'id' => 'tunjangan-guru',
        'title' => 'Pengusulan Tunjangan Profesi Guru',
        'desc' => 'Layanan pengusulan tunjangan profesi guru bagi guru yang memenuhi syarat.',
        'icon' => 'assets/tunjangan.png'
    ],
    [
        'id' => 'izin-pendirian',
        'title' => 'Izin Pendirian Satuan Pendidikan',
        'desc' => 'Layanan perizinan pendirian PAUD, SD, SMP, dan Lembaga Kursus.',
        'icon' => 'assets/institusi.png'
    ]
];

// Ambil data visi misi dari content.json
$visi_misi_data = $index_data['visi_misi'] ?? [
    'visi' => 'Terwujudnya Paser yang Sejahtera, Berakhlak Mulia dan Berdaya Saing',
    'misi' => [
        'Mewujudkan Sumber Daya Manusia yang handal dan berdaya saing melalui Peningkatan Mutu Pendidikan, Derajat Kesehatan serta Kesejahteraan Sosial',
        'Mewujudkan tata kelola pemerintahan yang baik (Good Governance) yang bersih, efektif, efesien, transparan dan akuntabel berbasis Teknologi Informasi dan Komunikasi',
        'Mewujudkan Pembangunan yang merata dan berkesinambungan yang berwawasan lingkungan',
        'Meningkatkan kemandirian ekonomi daerah dan masyarakat berbasis potensi lokal',
        'Menciptakan Kota yang Aman, Nyaman, dan Kondusif'
    ]
];

// Ambil data hero dari content.json
$hero_data = $index_data['hero_data'] ?? [
    'hero_text' => 'Pusat Layanan dan Informasi',
    'hero_subtext' => 'Pendidikan dan Kebudayaan Kabupaten Paser',
    'hero_paragraph' => 'Bersama Paser TUNTAS (Tangguh, Unggul, Transformatif, Adil, dan Sejahtera)',
    'hero_images' => [
        'https://upload.wikimedia.org/wikipedia/commons/c/c1/Pemandangan_Tanah_Grogot.jpg?20100504072853',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fc/Tari_Ronggeng_Paser.JPG/1200px-Tari_Ronggeng_Paser.JPG',
        'https://images.unsplash.com/photo-1523580494863-6f3031224c94?ixlib=rb-4.0.1&auto=format&fit=crop&w=1350&q=80',
        'https://media.suara.com/images/2024/12/30/68716-ilustrasi-wisata-di-kabupaten-paser-kaltim.jpg'
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=arrow_forward" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/heroslider.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Dinas Pendidikan dan Kebudayaan Kabupaten Paser</title>
</head>
<body>
    <!-- Header & Navigation -->
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
                        <li><a href="#" onclick="closeMobileMenu()" class="active">Beranda</a></li>
                        <li><a href="profil.php" onclick="closeMobileMenu()">Profil</a></li>
                        <li><a href="layanan.php" onclick="closeMobileMenu(); scrollToLayanan();">Layanan</a></li>
                        <li><a href="berita.php" onclick="closeMobileMenu()">Berita</a></li>
                        <li><a href="kontak.php" onclick="closeMobileMenu()">Kontak</a></li>
                        <li><a href="faq.php" onclick="closeMobileMenu()">FAQ</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section dengan Slider -->
<!-- Hero Section dengan Slider -->
    <section class="hero">
        <div class="hero-slider">
            <?php for ($i = 0; $i < 4; $i++): ?>
            <div class="slide <?php echo $i === 0 ? 'active' : ''; ?>" 
                 style="background-image: url('<?php echo htmlspecialchars($hero_data['hero_images'][$i] ?? ''); ?>')">
                <div class="slide-overlay"></div>
            </div>
            <?php endfor; ?>
        </div>
        
        <!-- Tombol navigasi hero slider -->
        <button class="hero-slider-btn hero-prev-btn" aria-label="Slide sebelumnya">
            <span class="material-symbols-outlined">chevron_left</span>
        </button>
        
        <div class="hero-content">
            <h2><?php echo htmlspecialchars($hero_data['hero_text']); ?></h2>
            <h3 style="font-size: 2rem;"><?php echo htmlspecialchars($hero_data['hero_subtext']); ?></h3><br>
            <p><?php echo htmlspecialchars($hero_data['hero_paragraph']); ?></p> <br> <br>
            <a href="#layanan-section" class="btn" onclick="scrollToLayanan()">Jelajahi Layanan</a>
        </div>
        
        <button class="hero-slider-btn hero-next-btn" aria-label="Slide berikutnya">
            <span class="material-symbols-outlined">chevron_right</span>
        </button>
    </section>

    <!-- Pimpinan Daerah Section -->
    <section id="pimpinan" class="pimpinan fullscreen-section">
        <div class="container">
            <div class="section-title">
                <h2>Pimpinan Daerah</h2>
            </div>
            <div class="pimpinan-grid">
                <?php foreach ($pimpinan_data as $pimpinan): ?>
                <div class="pimpinan-card">
                    <div class="pimpinan-img portrait">
                        <img src="<?php echo htmlspecialchars($pimpinan['foto'] ?? ''); ?>" 
                             alt="<?php echo htmlspecialchars(($pimpinan['nama'] ?? '') . ' - ' . ($pimpinan['jabatan'] ?? '')); ?>" 
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiBmaWxsPSIjMDAzMzk5IiBmaWxsLW9wYWNpdHk9IjAuMSIvPgo8Y2lyY2xlIGN4PSI2MCIgY3k9IjQ1IiByPSIyMCIgZmlsbD0iIzAwMzM5OSIgZmlsbC1vcGFjaXR5PSIwLjMiLz4KPHBhdGggZD0iTTYwIDc1IEM0MCA3NSAzMCA4NSAzMDk1IEMzMDEwNSA0MDExNSA2MDExNSBDODAxMTUgOTAxMDUgOTAgOTUgQzkwIDg1IDgwIDc1IDYwIDc1IFoiIGZpbGw9IiMwMDMzOTkiIGZpbGwtb3BhY2l0eT0iMC4zIi8+Cjwvc3ZnPg=='">
                    </div>
                    <div class="pimpinan-info">
                        <h3><?php echo htmlspecialchars($pimpinan['nama'] ?? ''); ?></h3>
                        <p><?php echo htmlspecialchars($pimpinan['jabatan'] ?? ''); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Visi Misi Section -->
    <section id="visi-misi" class="visi-misi fullscreen-section">
        <div class="container">
            <div class="section-title">
                <h2>Visi dan Misi</h2>
            </div>
            <div class="visi-misi-content">
                <div class="visi">
                    <h3>Visi</h3>
                    <p><?php echo htmlspecialchars($visi_misi_data['visi'] ?? ''); ?></p>
                </div>
                <div class="misi">
                    <h3>Misi</h3>
                    <ul>
                        <?php foreach (($visi_misi_data['misi'] ?? []) as $misi_item): ?>
                        <li><?php echo htmlspecialchars($misi_item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Berita Cepat Section -->
    <section class="berita-cepat fullscreen-section">
        <div class="container">
            <div class="section-title">
                <h2>Berita Cepat</h2>
            </div>
            
            <div class="berita-slider-container">
                <div class="berita-slider">
                    <?php if (!empty($berita_terbaru)): ?>
                        <?php foreach ($berita_terbaru as $index => $berita): ?>
                        <div class="slide-content <?php echo $index === 0 ? 'active' : ''; ?>">
                            <article class="post--overlay post--overlay-bottom post--overlay-floorfade">
                                <div class="background-img" style="background-image:url('<?php echo htmlspecialchars($berita['gambar'] ?? 'https://via.placeholder.com/800x400/003399/ffffff?text=Berita+Disdikbud'); ?>')"></div>
                                <div class="post__text inverse-text">
                                    <div class="post__text-wrap">
                                        <div class="post__text-inner text-center max-width-sm">
                                            <a href="berita-detail.php?id=<?php echo $berita['id']; ?>" class="post__cat post__cat--bg cat-theme-bg">
                                                <?php echo htmlspecialchars($berita['kategori']); ?>
                                            </a>
                                            <h3 class="post__title typescale-5">
                                                <?php echo htmlspecialchars($berita['judul']); ?>
                                            </h3>
                                            <div class="post__meta">
                                                <span class="entry-author">
                                                    <i class="mdicon mdicon-person"></i> 
                                                    <a href="#" class="entry-author__name">
                                                        <?php echo htmlspecialchars($berita['penulis']); ?>
                                                    </a>
                                                </span> 
                                                <time class="time published">
                                                    <i class="mdicon mdicon-schedule"></i> 
                                                    <?php echo formatTanggalIndonesia($berita['tanggal_publish']); ?>
                                                </time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a href="berita-detail.php?id=<?php echo $berita['id']; ?>" class="link-overlay"></a>
                            </article>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <div class="slide-content active">
                        <article class="post--overlay post--overlay-bottom post--overlay-floorfade">
                            <div class="background-img" style="background-image:url('https://via.placeholder.com/800x400/003399/ffffff?text=Tidak+Ada+Berita')"></div>
                            <div class="post__text inverse-text">
                                <div class="post__text-wrap">
                                    <div class="post__text-inner text-center max-width-sm">
                                        <h3 class="post__title typescale-5">
                                            Belum ada berita tersedia
                                        </h3>
                                        <div class="post__meta">
                                            <span class="entry-author">
                                                <i class="mdicon mdicon-person"></i> 
                                                <a href="#" class="entry-author__name">
                                                    Admin Disdikbud
                                                </a>
                                            </span> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Slider Controls -->
                <?php if (!empty($berita_terbaru) && count($berita_terbaru) > 1): ?>
                <button class="slider-nav-btn prev-btn" type="button" aria-label="previous">
                    <svg viewBox="0 0 100 100" width="20" height="20">
                        <path d="M 10,50 L 60,100 L 70,90 L 30,50 L 70,10 L 60,0 Z" fill="#003399"></path>
                    </svg>
                </button>
                <button class="slider-nav-btn next-btn" type="button" aria-label="next">
                    <svg viewBox="0 0 100 100" width="20" height="20">
                        <path d="M 10,50 L 60,100 L 70,90 L 30,50 L 70,10 L 60,0 Z" fill="#003399" transform="translate(100, 100) rotate(180)"></path>
                    </svg>
                </button>
                
                <!-- Pagination Dots -->
                <div class="slider-pagination">
                    <?php for ($i = 0; $i < min(3, count($berita_terbaru)); $i++): ?>
                    <span class="dot <?php echo $i === 0 ? 'active' : ''; ?>" data-index="<?php echo $i; ?>"></span>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="text-center" style="margin-top: 30px;">
                <a href="berita.php" class="btn btn-default">
                    Lihat Semua Berita
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Layanan Section -->
    <section class="layanan fullscreen-section" id="layanan-section">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Layanan Kami</h2>
                <p>Berbagai layanan yang disediakan oleh Dinas Pendidikan dan Kebudayaan Kabupaten Paser</p>
                <br>
            </div>
            <div class="layanan-grid">
                <?php foreach ($layanan_data as $layanan): ?>
                <div class="layanan-card fade-in" onclick="openLayananPopup('<?php echo $layanan['id']; ?>')">
                    <img src="<?php echo htmlspecialchars($layanan['icon']); ?>" alt="<?php echo htmlspecialchars($layanan['title']); ?>" class="icon-layanan" style="width: 100px; height: 100px;">
                    <div class="layanan-card-content">
                        <h3><?php echo htmlspecialchars($layanan['title']); ?></h3>
                        <p><?php echo htmlspecialchars($layanan['desc']); ?></p>
                        <button class="layanan-btn">Lihat Detail</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
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
                        <?php foreach ($layanan_data as $layanan): ?>
                        <li><a href="layanan.php#layanan" onclick="openLayananPopup('<?php echo $layanan['id']; ?>'); return false;"><?php echo htmlspecialchars($layanan['title']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Tautan Terkait</h3>
                    <ul>
                        <li><a href="https://www.kemdikbud.go.id/" target="_blank">Kementerian Pendidikan dan Kebudayaan</a></li>
                        <li><a href="https://www.paserkab.go.id/" target="_blank">Pemerintah Kabupaten Paser</a></li>
                        <li><a href="https://bps.go.id/" target="_blank">Badan Pusat Statistik</a></li>
                        <li><a href="https://www.indonesia.go.id/" target="_blank">Portal Nasional</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Dinas Pendidikan dan Kebudayaan Kabupaten Paser. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>
    <script src="js/script.js"></script>
    <script src="js/heroslider.js"></script>
</body>
</html>