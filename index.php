<?php
// index.php
session_start();
require_once 'functions_json.php';

// Ambil data dari JSON menggunakan fungsi baru
$pimpinan_data = ambilDataPimpinan();
$visi_misi = ambilVisiMisi();
$layanan_data = ambilDataLayanan();
$hero_data = ambilDataHero();

// Ambil berita terbaru untuk slider
$berita_terbaru = [];
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
                    <!-- PERBAIKI PATH LOGO -->
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
                        <li><a href="#kontak" onclick="closeMobileMenu()">Kontak</a></li>
                        <li><a href="faq.php" onclick="closeMobileMenu()">FAQ</a></li>
                        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                        <li><a href="admin-dashboard.php" onclick="closeMobileMenu()">Admin</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section dengan Slider -->
    <section class="hero">
        <div class="hero-slider">
            <?php if (isset($hero_data['hero_images']) && is_array($hero_data['hero_images'])): ?>
                <?php foreach ($hero_data['hero_images'] as $index => $image_url): ?>
                    <?php if (!empty($image_url)): ?>
                    <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                         style="background-image: url('<?php echo htmlspecialchars($image_url); ?>')">
                        <div class="slide-overlay"></div>
                    </div>
                    <?php else: ?>
                    <!-- Default image if empty -->
                    <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>" 
                         style="background-image: url('assets/uploads/sekda-paser-2024.jpg')">
                        <div class="slide-overlay"></div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback jika tidak ada data -->
                <div class="slide active" style="background-image: url('assets/uploads/sekda-paser-2024.jpg')">
                    <div class="slide-overlay"></div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Tombol navigasi hero slider -->
        <button class="hero-slider-btn hero-prev-btn" aria-label="Slide sebelumnya">
            <span class="material-symbols-outlined">chevron_left</span>
        </button>
        
        <div class="hero-content">
            <h2><?php echo isset($hero_data['hero_text']) ? htmlspecialchars($hero_data['hero_text']) : 'Dinas Pendidikan dan Kebudayaan'; ?></h2>
            <h3 style="font-size: 2rem;">Pendidikan dan Kebudayaan Kabupaten Paser</h3><br>
            <p><?php echo isset($hero_data['hero_subtext']) ? htmlspecialchars($hero_data['hero_subtext']) : 'Mewujudkan pendidikan berkualitas untuk masyarakat Paser'; ?></p> <br> <br>
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
                <?php if (isset($pimpinan_data) && is_array($pimpinan_data)): ?>
                    <?php foreach ($pimpinan_data as $pimpinan): ?>
                    <div class="pimpinan-card">
                        <div class="pimpinan-img portrait">
                            <?php if (!empty($pimpinan['foto'])): ?>
                            <img src="<?php echo htmlspecialchars($pimpinan['foto']); ?>" 
                                 alt="<?php echo htmlspecialchars($pimpinan['nama'] . ' - ' . $pimpinan['jabatan']); ?>"
                                 onerror="this.onerror=null; this.src='assets/uploads/Bupati_Paser_Fahmi_Fadli.jpg';">
                            <?php else: ?>
                            <!-- Default image -->
                            <img src="assets/uploads/Bupati_Paser_Fahmi_Fadli.jpg" 
                                 alt="Foto Pimpinan">
                            <?php endif; ?>
                        </div>
                        <div class="pimpinan-info">
                            <h3><?php echo htmlspecialchars($pimpinan['nama'] ?? 'Nama Pimpinan'); ?></h3>
                            <p><?php echo htmlspecialchars($pimpinan['jabatan'] ?? 'Jabatan'); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback jika tidak ada data -->
                    <div class="pimpinan-card">
                        <div class="pimpinan-img portrait">
                            <img src="assets/uploads/Bupati_Paser_Fahmi_Fadli.jpg" 
                                 alt="Bupati Paser">
                        </div>
                        <div class="pimpinan-info">
                            <h3>Dr. Fahmi Fadli</h3>
                            <p>Bupati Paser</p>
                        </div>
                    </div>
                    <div class="pimpinan-card">
                        <div class="pimpinan-img portrait">
                            <img src="assets/uploads/Wakil_Bupati_Paser_Ikhwan_Antasari.jpg" 
                                 alt="Wakil Bupati Paser">
                        </div>
                        <div class="pimpinan-info">
                            <h3>Ikhwan Antasari</h3>
                            <p>Wakil Bupati Paser</p>
                        </div>
                    </div>
                    <div class="pimpinan-card">
                        <div class="pimpinan-img portrait">
                            <img src="assets/uploads/sekda-paser-2024.jpg" 
                                 alt="Sekda Paser">
                        </div>
                        <div class="pimpinan-info">
                            <h3>Sekretaris Daerah</h3>
                            <p>Sekda Kabupaten Paser</p>
                        </div>
                    </div>
                <?php endif; ?>
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
                    <p><?php echo isset($visi_misi['visi']) ? htmlspecialchars($visi_misi['visi']) : 'Terwujudnya masyarakat Paser yang berpendidikan, berbudaya, dan berdaya saing.'; ?></p>
                </div>
                <div class="misi">
                    <h3>Misi</h3>
                    <ul>
                        <?php if (isset($visi_misi['misi']) && is_array($visi_misi['misi'])): ?>
                            <?php foreach ($visi_misi['misi'] as $misi_item): ?>
                            <li><?php echo htmlspecialchars($misi_item); ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>Meningkatkan akses dan kualitas pendidikan</li>
                            <li>Melestarikan dan mengembangkan kebudayaan daerah</li>
                            <li>Meningkatkan kompetensi tenaga pendidik</li>
                            <li>Mengembangkan sarana dan prasarana pendidikan</li>
                            <li>Mendorong partisipasi masyarakat dalam pendidikan</li>
                        <?php endif; ?>
                    </ul>
                </div>
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
                <?php if (isset($layanan_data) && is_array($layanan_data)): ?>
                    <?php foreach ($layanan_data as $layanan): ?>
                    <div class="layanan-card fade-in" onclick="openLayananPopup('<?php echo htmlspecialchars($layanan['id']); ?>')">
                        <?php if (!empty($layanan['icon'])): ?>
                        <img src="<?php echo htmlspecialchars($layanan['icon']); ?>" 
                             alt="<?php echo htmlspecialchars($layanan['title']); ?>" 
                             class="icon-layanan" style="width: 100px; height: 100px;"
                             onerror="this.onerror=null; this.src='assets/uploads/legalisir.png';">
                        <?php else: ?>
                        <!-- Default icons based on service type -->
                        <img src="assets/uploads/legalisir.png" 
                             alt="<?php echo htmlspecialchars($layanan['title']); ?>" 
                             class="icon-layanan" style="width: 100px; height: 100px;">
                        <?php endif; ?>
                        <div class="layanan-card-content">
                            <h3><?php echo htmlspecialchars($layanan['title'] ?? 'Judul Layanan'); ?></h3>
                            <p><?php echo htmlspecialchars($layanan['desc'] ?? 'Deskripsi layanan'); ?></p>
                            <button class="layanan-btn">Lihat Detail</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback layanan -->
                    <div class="layanan-card fade-in" onclick="openLayananPopup('legalisir-ijazah')">
                        <img src="assets/uploads/legalisir.png" alt="Legalisir Ijazah" class="icon-layanan" style="width: 100px; height: 100px;">
                        <div class="layanan-card-content">
                            <h3>Legalisir Ijazah</h3>
                            <p>Pengesahan dokumen ijazah untuk keperluan administrasi</p>
                            <button class="layanan-btn">Lihat Detail</button>
                        </div>
                    </div>
                    <div class="layanan-card fade-in" onclick="openLayananPopup('surat-mutasi')">
                        <img src="assets/uploads/document.png" alt="Surat Mutasi" class="icon-layanan" style="width: 100px; height: 100px;">
                        <div class="layanan-card-content">
                            <h3>Surat Mutasi</h3>
                            <p>Proses mutasi guru dan tenaga kependidikan</p>
                            <button class="layanan-btn">Lihat Detail</button>
                        </div>
                    </div>
                    <div class="layanan-card fade-in" onclick="openLayananPopup('tunjangan-guru')">
                        <img src="assets/uploads/tunjangan.png" alt="Tunjangan Guru" class="icon-layanan" style="width: 100px; height: 100px;">
                        <div class="layanan-card-content">
                            <h3>Tunjangan Guru</h3>
                            <p>Pengajuan dan pengelolaan tunjangan profesi guru</p>
                            <button class="layanan-btn">Lihat Detail</button>
                        </div>
                    </div>
                    <div class="layanan-card fade-in" onclick="openLayananPopup('izin-pendirian')">
                        <img src="assets/uploads/institusi.png" alt="Izin Pendirian" class="icon-layanan" style="width: 100px; height: 100px;">
                        <div class="layanan-card-content">
                            <h3>Izin Pendirian</h3>
                            <p>Perizinan pendirian lembaga pendidikan baru</p>
                            <button class="layanan-btn">Lihat Detail</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content" id="kontak">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3987.6060327839327!2d116.1914303!3d-1.9081035!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df047988e6b3c0b%3A0xdaa84941bfe1b7df!2sJl.%20Jenderal%20Sudirman%20No.27%2C%20Tanah%20Grogot%2C%20Kec.%20Tanah%20Grogot%2C%20Kabupaten%20Paser%2C%20Kalimantan%20Timur%2076251!5e0!3m2!1sid!2sid!4v1764218368388!5m2!1sid!2sid" 
                        width="250" height="200" style="border:0; border-radius: 0.3rem;" 
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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
                        <?php if (isset($layanan_data) && is_array($layanan_data)): ?>
                            <?php foreach ($layanan_data as $layanan): ?>
                            <li><a href="layanan.php#layanan" onclick="openLayananPopup('<?php echo htmlspecialchars($layanan['id']); ?>'); return false;">
                                <?php echo htmlspecialchars($layanan['title'] ?? 'Layanan'); ?>
                            </a></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><a href="layanan.php#layanan" onclick="openLayananPopup('legalisir-ijazah'); return false;">Legalisir Ijazah</a></li>
                            <li><a href="layanan.php#layanan" onclick="openLayananPopup('surat-mutasi'); return false;">Surat Mutasi</a></li>
                            <li><a href="layanan.php#layanan" onclick="openLayananPopup('tunjangan-guru'); return false;">Tunjangan Guru</a></li>
                            <li><a href="layanan.php#layanan" onclick="openLayananPopup('izin-pendirian'); return false;">Izin Pendirian</a></li>
                        <?php endif; ?>
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