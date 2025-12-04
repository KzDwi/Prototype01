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

        .struktur-section {
            background-color: #f0f8ff;
        }

        .pimpinan-section {
            background-color: #f9f9f9;
        }

        .kontak-section {
            background: linear-gradient(135deg, #2B557E 0%, #003399 100%);
            color: white;
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

        /* Navigation Dots */
        .section-nav {
            border: solid #DDD;
            background-color: #f9f9f9;
            border-radius: 0.7rem;
            padding: 1rem;
            position: fixed;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1000;
        }

        .nav-dot {
            display: block;
            width: 12px;
            height: 12px;
            margin: 15px 0;
            border-radius: 50%;
            background: rgba(0,51,153,0.3);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .nav-dot.active {
            background: #003399;
            transform: scale(1.3);
        }

        .nav-dot:hover {
            background: #003399;
            transform: scale(1.2);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .profil-fullscreen-section {
                padding: 80px 0 60px;
                min-height: 100vh;
            }

            .profil-section-title h2 {
                font-size: 2rem;
            }

            .profil-card {
                padding: 30px 20px;
                margin: 0 15px;
            }

            .section-nav {
                right: 15px;
            }

            .nav-dot {
                width: 10px;
                height: 10px;
                margin: 12px 0;
            }
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

        .visi-icon, .misi-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #003399;
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

        @media (max-width: 480px) {
            .profil-section-title h2 {
                font-size: 1.8rem;
            }

            .profil-card {
                padding: 25px 15px;
            }

            .profil-list li {
                font-size: 1rem;
                padding-left: 25px;
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
                        <li><a href="index.php" onclick="closeMobileMenu()">Beranda</a></li>
                        <li><a href="profil.php" onclick="closeMobileMenu()" class="active">Profil</a></li>
                        <li><a href="layanan.html" onclick="closeMobileMenu(); scrollToLayanan();">Layanan</a></li>
                        <li><a href="berita.php" onclick="closeMobileMenu()">Berita</a></li>
                        <li><a href="#kontak" onclick="closeMobileMenu()">Kontak</a></li>
                        <li><a href="faq.html" onclick="closeMobileMenu()">FAQ</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Navigation Dots -->
    <!-- <div class="section-nav">
        <a href="#hero" class="nav-dot active" data-section="hero"></a>
        <a href="#visi-misi" class="nav-dot" data-section="visi-misi"></a>
        <a href="#tupoksi" class="nav-dot" data-section="tupoksi"></a>
        <a href="#kontak" class="nav-dot" data-section="kontak"></a>
    </div> -->

    <!-- Hero Section -->
    <section id="hero" class="profil-hero-section fade-in">
        <div class="container">
            <nav class="breadcrumb-nav">
                <a href="index.php">Halaman Utama</a>
                <span>/</span>
                <a href="layanan.html" class="active">Layanan</a>
            </nav>
            <br>
            <br>    
        </div>
        <div class="profil-section-content">
            <div class="profil-section-title">
                <h1>Profil Dinas Pendidikan dan Kebudayaan</h1>
                <p style="color : #DDD;">Kabupaten Paser</p>
            </div>
        </div>
    </section>

    <!-- Visi Misi Section -->
    <section id="visi-misi" class="profil-fullscreen-section visi-misi-section">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Visi dan Misi</h2>
            </div>
            <div class="visi-misi-content">
                <div class="visi-card fade-in">
                    <h3>Visi</h3>
                    <p><?php
                        // Ambil konten dari file profil.html
                        $file_content = file_get_contents('profil.php');
                        
                        // Pattern untuk mengambil visi
                        $pattern = '/<div class="visi-card fade-in">\s*<h3>Visi<\/h3>\s*<p>([^<]+)<\/p>\s*<\/div>/s';
                        preg_match($pattern, $file_content, $matches);
                        
                        if (!empty($matches[1])) {
                            echo htmlspecialchars($matches[1]);
                        } else {
                            // Fallback jika tidak ditemukan
                            echo "“Terwujudnya Paser yang Sejahtera, Berakhlak Mulia dan Berdaya Saing”";
                        }
                    ?></p>
                </div>
                <div class="misi-card fade-in">
                    <h3><center>Misi</center></h3>
                    <ul>
                        <li>Mewujudkan Sumber Daya Manusia yang handal dan berdaya saing melalui Peningkatan Mutu Pendidikan, Derajat Kesehatan serta Kesejahteraan Sosial</li>
                        <li>Mewujudkan tata kelola pemerintahan yang baik (Good Governance) yang bersih, efektif, efesien, transparan dan akuntabel berbasis Teknologi Informasi dan Komunikasi</li>
                        <li>Mewujudkan Pembangunan yang merata dan berkesinambungan yang berwawasan lingkungan</li>
                        <li>Meningkatkan kemandirian ekonomi daerah dan masyarakat berbasis potensi lokal</li>
                        <li>Menciptakan Kota yang Aman, Nyaman, dan Kondusif</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Tugas Pokok dan Fungsi Section -->
    <section id="tupoksi" class="profil-fullscreen-section tupoksi-section fade-in">
        <div class="profil-section-content">
            <div class="container profil-section-title">
                <h2>Tugas Pokok dan Fungsi</h2>
            </div>
            <div class="profil-card tupoksi-card">
                <div style="text-align: center; margin-bottom: 40px;">
                </div>
                <ul class="profil-list">
                    <li><strong>Perumusan kebijakan teknis</strong> di bidang pendidikan dan kebudayaan</li>
                    <li><strong>Pelaksanaan kebijakan</strong> di bidang pengelolaan PAUD, SD, SMP, serta pendidikan kesetaraan dan keaksaraan</li>
                    <li><strong>Pembinaan, pengawasan, dan pengendalian</strong> pelaksanaan tugas di bidang pendidikan</li>
                    <li><strong>Pengelolaan dan pembinaan</strong> pendidik dan tenaga kependidikan</li>
                    <li><strong>Pelaksanaan evaluasi dan pelaporan</strong> bidang pendidikan</li>
                    <li><strong>Pelaksanaan administrasi dinas</strong> sesuai dengan lingkup tugasnya</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Kontak Section -->
    <section id="kontak" class="profil-fullscreen-section kontak-section">
        <div class="profil-section-content fade-in">
            <div class="profil-section-title">
                <h2 style="color: #DDD;">Kontak Kami</h2>
                <p style="color: #DDD;">Hubungi kami untuk informasi lebih lanjut</p>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; max-width: 1200px; margin: 0 auto; align-items: start;">
                <div>
                    <div class="profil-card" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                        <h3 style="color: white; margin-bottom: 30px; text-align: center;">Informasi Kontak</h3>
                        
                        <div style="margin-bottom: 25px;">
                            <h4 style="color: white; margin-bottom: 10px;">Alamat Kantor</h4>
                            <p style="color: rgba(255,255,255,0.9); line-height: 1.6;">
                                Jl. Jenderal Sudirman No. 27<br>
                                Tanah Grogot, Kabupaten Paser<br>
                                Kalimantan Timur 76251
                            </p>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <h4 style="color: white; margin-bottom: 10px;">Kontak</h4>
                            <p style="color: rgba(255,255,255,0.9);">
                                <strong>Telepon:</strong> (0543) 21023<br>
                                <strong>Email:</strong> disdik@paserkab.go.id
                            </p>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <h4 style="color: white; margin-bottom: 10px;">Jam Operasional</h4>
                            <p style="color: rgba(255,255,255,0.9);">
                                <strong>Senin - Kamis:</strong> 08.00 - 16.00 WITA<br>
                                <strong>Jumat:</strong> 08.00 - 11.00 WITA<br>
                                <strong>Sabtu - Minggu:</strong> Libur
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <br><br><br>
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3987.6060327839327!2d116.1914303!3d-1.9081035!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df047988e6b3c0b%3A0xdaa84941bfe1b7df!2sJl.%20Jenderal%20Sudirman%20No.27%2C%20Tanah%20Grogot%2C%20Kec.%20Tanah%20Grogot%2C%20Kabupaten%20Paser%2C%20Kalimantan%20Timur%2076251!5e0!3m2!1sid!2sid!4v1764218368388!5m2!1sid!2sid" 
                        width="100%" 
                        height="400" 
                        style="border:0; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade" class="fade-in">
                    </iframe>
                </div>
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
                <p>&copy; 2025 Dinas Pendidikan dan Kebudayaan Kabupaten Paser. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>