<?php
// Load Data dari content.json
$file_path = 'data/content.json';
$profil_data = [];

// Default Data Profil
$default_profil = [
    'hero_title' => 'Profil Dinas Pendidikan dan Kebudayaan',
    'hero_subtitle' => 'Kabupaten Paser',
    'visi' => 'Terwujudnya Paser yang Sejahtera, Berakhlak Mulia dan Berdaya Saing',
    'misi' => [
        'Mewujudkan Sumber Daya Manusia yang handal dan berdaya saing',
        'Mewujudkan tata kelola pemerintahan yang baik',
        'Meningkatkan kemandirian ekonomi daerah'
    ],
    'tupoksi' => [
        'Perumusan kebijakan teknis di bidang pendidikan',
        'Pelaksanaan kebijakan di bidang pengelolaan PAUD, SD, SMP',
        'Pembinaan, pengawasan, dan pengendalian pelaksanaan tugas',
        'Pengelolaan dan pembinaan pendidik dan tenaga kependidikan'
    ]
];

// Data Kontak untuk Footer (tetap diambil meski section kontak dihapus)
$default_kontak = [
    'address' => "Jl. Jenderal Sudirman No. 27\nTanah Grogot, Kabupaten Paser",
    'phone' => "(0543) 21023",
    'email' => "disdik@paserkab.go.id",
    'map_embed_url' => "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3987.6060327839327!2d116.1914303!3d-1.9081035!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df047988e6b3c0b%3A0xdaa84941bfe1b7df!2sJl.%20Jenderal%20Sudirman%20No.27%2C%20Tanah%20Grogot%2C%20Kec.%20Tanah%20Grogot%2C%20Kabupaten%20Paser%2C%20Kalimantan%20Timur%2076251!5e0!3m2!1sid!2sid!4v1764218368388!5m2!1sid!2sid"
];

$kontak_data = $default_kontak;

if (file_exists($file_path)) {
    $json = json_decode(file_get_contents($file_path), true);
    
    // Merge Profil Data
    if (isset($json['profil'])) {
        $profil_data = array_merge($default_profil, $json['profil']);
    } else {
        $profil_data = $default_profil;
    }

    // Merge Kontak Data (untuk footer)
    if (isset($json['kontak_page'])) {
        $kontak_data = array_merge($default_kontak, $json['kontak_page']);
    }
} else {
    $profil_data = $default_profil;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/faq-styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Profil - Dinas Pendidikan dan Kebudayaan Kabupaten Paser</title>
    <style>
        /* Fullscreen Section Styles untuk Profil */
        body {
            background-color: #f9f9f9;
        }

        .profil-fullscreen-section {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .profil-section-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            width: 100%;
        }

        .profil-section-title {
            text-align: center;
            margin-bottom: 60px;
            position: relative;
        }

        .profil-section-title h2 {
            font-size: 2.5rem;
            color: #003399;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .profil-section-title p {
            font-size: 1.2rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Background Colors untuk Setiap Section */
        .profil-hero-section {
            background: linear-gradient(135deg, #003399 0%, #002280 100%);
            color: white;
            text-align: center;
            clip-path: polygon(0 0, 100% 0, 100% 90%, 0% 100%);
        }

        .visi-misi-section {
            background-color: #f8f9fa;
        }

        .tupoksi-section {
            background-color: #ffffff;
        }

        /* Card Styles */
        .profil-card {
            background: white;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .visi-card {
            border-left: 6px solid #003399;
        }

        .misi-card {
            border-left: 6px solid #4CAF50;
            text-align: left;
        }

        .tupoksi-card {
            border-left: 6px solid #FF9800;
            text-align: left;
        }

        /* List Styles */
        .profil-list {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }

        .profil-list li {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            font-size: 1.1rem;
            line-height: 1.6;
            position: relative;
            padding-left: 30px;
        }

        .profil-list li:before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #003399;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .profil-list li:last-child {
            border-bottom: none;
        }

        /* Fade In Animation Styles untuk Profil */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Visi Misi Card Styles */
        .visi-card, .misi-card {
            background-color: white;
            padding: 40px 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .visi-card:hover, .misi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .visi-card h3, .misi-card h3 {
            color: #003399;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .visi-card p {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #333;
            font-style: italic;
        }

        .misi-card ul {
            text-align: left;
            list-style: none;
            padding: 0;
        }

        .misi-card ul li {
            margin-bottom: 12px;
            padding-left: 20px;
            position: relative;
            line-height: 1.6;
        }

        .misi-card ul li:before {
            content: '✓';
            color: #003399;
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        /* Visi Misi Grid Layout */
        .visi-misi-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 30px;
        }

        /* Section Title Styles */
        .section-title {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .section-title h2 {
            font-size: 2rem;
            color: #003399;
            display: inline-block;
            padding-bottom: 10px;
        }

        .section-title h2:after {
            content: '';
            position: absolute;
            width: 80px;
            height: 3px;
            background-color: #e36159;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }

        @media (max-width: 768px) {
            .profil-fullscreen-section {
                padding: 80px 0 60px;
                min-height: 100vh;
            }
            .profil-card {
                padding: 30px 20px;
                margin: 0 15px;
            }
            .visi-misi-content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .profil-section-title h2 {
                font-size: 1.8rem;
            }
            .profil-card {
                padding: 25px 15px;
            }
        }

        .breadcrumb-nav a {
            color: #e36159;
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb-nav a:hover {
            color: #D25048;
        }

        .breadcrumb-nav .active {
            color: #DDD;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header id="main-header">
        <div class="container">
            <div class="header-top">
                <div class="logo">
                    <img src="assets/logo-kabupaten.png" alt="Logo">
                    <div class="logo-text">
                        <h1>Dinas Pendidikan dan Kebudayaan</h1>
                        <p>Kabupaten Paser</p>
                    </div>
                </div>
                <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
                <nav id="main-nav">
                    <ul>
                        <li><a href="index.php" onclick="closeMobileMenu()">Beranda</a></li>
                        <li><a href="profil.php" onclick="closeMobileMenu()" class="active">Profil</a></li>
                        <li><a href="Statistik.php" onclick="closeMobileMenu()">Data & Statistik</a></li>
                        <li><a href="layanan.php" onclick="closeMobileMenu()">Layanan</a></li>
                        <li><a href="berita.php" onclick="closeMobileMenu()">Berita</a></li>
                        <li><a href="kontak.php" onclick="closeMobileMenu()">Kontak</a></li>
                        <li><a href="faq.php" onclick="closeMobileMenu()">FAQ</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <section id="hero" class="profil-hero-section fade-in">
        <div class="container">
            <nav class="breadcrumb-nav">
                <a href="index.php">Halaman Utama</a>
                <span>/</span>
                <a href="profil.php" class="active">Profil</a>
            </nav>
            <br><br>    
        </div>
        <div class="profil-section-content">
            <div class="profil-section-title">
                <h1><?php echo htmlspecialchars($profil_data['hero_title']); ?></h1>
                <p style="color : #DDD;"><?php echo htmlspecialchars($profil_data['hero_subtitle']); ?></p>
            </div>
        </div>
    </section>

    <section id="visi-misi" class="profil-fullscreen-section visi-misi-section">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Visi dan Misi</h2>
            </div>
            <div class="visi-misi-content">
                <div class="visi-card fade-in">
                    <h3>Visi</h3>
                    <p style="font-style: italic;">
                        "<?php echo htmlspecialchars($profil_data['visi']); ?>"
                    </p>
                </div>
                <div class="misi-card fade-in">
                    <h3>Misi</h3>
                    <ul>
                        <?php foreach($profil_data['misi'] as $misi): ?>
                            <li><?php echo htmlspecialchars($misi); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="tupoksi" class="profil-fullscreen-section tupoksi-section fade-in">
        <div class="profil-section-content">
            <div class="container profil-section-title">
                <h2>Tugas Pokok dan Fungsi</h2>
            </div>
            <div class="profil-card tupoksi-card">
                <ul class="profil-list">
                    <?php foreach($profil_data['tupoksi'] as $tupoksi): ?>
                        <li><?php echo $tupoksi; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content" id="kontak">
                <iframe src="<?php echo htmlspecialchars($kontak_data['map_embed_url']); ?>" width="250" height="200" style="border:0; border-radius: 0.3rem;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <div class="footer-section">
                    <h3>Kontak Kami</h3>
                    <ul>
                        <li><?php echo htmlspecialchars(str_replace("\n", ", ", $kontak_data['address'])); ?></li>
                        <li>Telp: <?php echo htmlspecialchars($kontak_data['phone']); ?></li>
                        <li>Email: <?php echo htmlspecialchars($kontak_data['email']); ?></li>
                        <li class="social-icons">
                            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Layanan Cepat</h3>
                    <ul>
                        <li><a href="layanan.php#layanan" onclick="scrollToLayanan()">Legalisir Ijazah</a></li>
                        <li><a href="layanan.php#layanan" onclick="scrollToLayanan()">Surat Mutasi</a></li>
                        <li><a href="layanan.php#layanan" onclick="scrollToLayanan()">Tunjangan Guru</a></li>
                        <li><a href="layanan.php#layanan" onclick="scrollToLayanan()">Izin Pendirian</a></li>
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

    <script src="js/script.js"></script>
</body>
</html>