<?php
// Load Data dari content.json
$file_path = 'data/content.json';
$kontak_data = [];

// Default Data (Fallback jika JSON belum ada/kosong)
$default_data = [
    'address' => "Jl. Jenderal Sudirman No. 27\nTanah Grogot, Kabupaten Paser\nKalimantan Timur 76251",
    'phone' => "(0543) 21023",
    'fax' => "(0543) 21024",
    'email' => "disdik@paserkab.go.id",
    'hours_weekdays' => "08.00 - 16.00 WITA",
    'hours_friday' => "08.00 - 11.00 WITA",
    'map_embed_url' => "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3987.6060327839327!2d116.1914303!3d-1.9081035!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df047988e6b3c0b%3A0xdaa84941bfe1b7df!2sJl.%20Jenderal%20Sudirman%20No.27%2C%20Tanah%20Grogot%2C%20Kec.%20Tanah%20Grogot%2C%20Kabupaten%20Paser%2C%20Kalimantan%20Timur%2076251!5e0!3m2!1sid!2sid!4v1764218368388!5m2!1sid!2sid"
];

if (file_exists($file_path)) {
    $json = json_decode(file_get_contents($file_path), true);
    if (isset($json['kontak_page'])) {
        $kontak_data = array_merge($default_data, $json['kontak_page']);
    } else {
        $kontak_data = $default_data;
    }
} else {
    $kontak_data = $default_data;
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
    <title>Kontak - Dinas Pendidikan dan Kebudayaan Kabupaten Paser</title>
    <style>
        /* Fullscreen Section Styles untuk Kontak */
        body {
            background-color: #f9f9f9;
        }

        .kontak-fullscreen-section {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .kontak-section-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            width: 100%;
        }

        .kontak-section-title {
            text-align: center;
            margin-bottom: 60px;
            position: relative;
        }

        .kontak-section-title h2 {
            font-size: 2.5rem;
            color: #003399;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .kontak-section-title p {
            font-size: 1.2rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Background Colors untuk Setiap Section */
        .kontak-hero-section {
            background: linear-gradient(135deg, #003399 0%, #002280 100%);
            color: white;
            text-align: center;
            clip-path: polygon(0 0, 100% 0, 100% 90%, 0% 100%);
        }

        .info-kontak-section {
            background-color: #f8f9fa;
        }

        .lokasi-section {
            background-color: #ffffff;
        }

        .jam-operasional-section {
            background-color: #f0f8ff;
        }

        .form-kontak-section {
            background-color: #f9f9f9;
        }

        .unit-kerja-section {
            background: linear-gradient(135deg, #2B557E 0%, #003399 100%);
            color: white;
        }

        /* Card Styles */
        .kontak-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .kontak-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .info-kontak-card {
            border-left: 6px solid #003399;
        }

        .jam-operasional-card {
            border-left: 6px solid #4CAF50;
        }

        .unit-kerja-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        /* Form Styles */
        .kontak-form {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #003399;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #003399;
            box-shadow: 0 0 0 3px rgba(0, 51, 153, 0.1);
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        /* Grid Layout untuk Info Kontak */
        .kontak-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        /* Icons */
        .kontak-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #003399, #002280);
            color: white;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        /* Unit Kerja Grid */
        .unit-kerja-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .unit-kerja-item {
            background: rgba(255,255,255,0.15);
            padding: 25px;
            border-radius: 10px;
            transition: transform 0.3s;
        }

        .unit-kerja-item:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.2);
        }

        .unit-kerja-item h4 {
            color: white;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .unit-kerja-item p {
            color: rgba(255,255,255,0.9);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .breadcrumb-nav a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb-nav a:hover {
            color: white;
            text-decoration: underline;
        }

        .breadcrumb-nav .active {
            color: white;
            font-weight: 600;
        }

        .breadcrumb-nav span {
            color: rgba(255,255,255,0.6);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .kontak-fullscreen-section {
                padding: 80px 0 60px;
                min-height: 100vh;
            }

            .kontak-section-title h2 {
                font-size: 2rem;
            }

            .kontak-card {
                padding: 25px 20px;
                margin: 0 15px 25px;
            }

            .kontak-info-grid {
                grid-template-columns: 1fr;
            }

            .unit-kerja-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .kontak-section-title h2 {
                font-size: 1.8rem;
            }

            .kontak-card {
                padding: 20px 15px;
            }

            .breadcrumb-nav {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
        }

        /* Fade In Animation Styles */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
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

        .unit-kerja-section .section-title h2,
        .unit-kerja-section .section-title h2:after {
            color: white;
            background-color: #e36159;
        }

        /* Button Styles */
        .btn-submit {
            background: rgba(227, 97, 89, 0.8);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 51, 153, 0.2);
            background: #d25048;
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Map Styles */
        .map-container {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            height: 550px;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
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
                        <li><a href="profil.php" onclick="closeMobileMenu()">Profil</a></li>
                        <li><a href="Statistik.php" onclick="closeMobileMenu()">Data & Statistik</a></li>
                        <li><a href="layanan.php" onclick="closeMobileMenu(); scrollToLayanan();">Layanan</a></li>
                        <li><a href="berita.php" onclick="closeMobileMenu()">Berita</a></li>
                        <li><a href="#" onclick="closeMobileMenu()" class="active">Kontak</a></li>
                        <li><a href="faq.php" onclick="closeMobileMenu()">FAQ</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="hero" class="kontak-hero-section fade-in">
        <div class="container">
            <nav class="breadcrumb-nav">
                <a href="index.html">Halaman Utama</a>
                <span>/</span>
                <a href="kontak.php" class="active">Kontak</a>
            </nav>
            <br>
            <br>    
        </div>
        <div class="kontak-section-content">
            <div class="kontak-section-title">
                <h1>Hubungi Kami</h1> <br>
                <p style="color : #DDD;">Dinas Pendidikan dan Kebudayaan Kabupaten Paser</p>
            </div>
        </div>
    </section>

    <!-- Informasi Kontak Section -->
    <section id="info-kontak" class="kontak-fullscreen-section info-kontak-section">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Informasi Kontak</h2>
                <p>Kami siap membantu Anda. Jangan ragu untuk menghubungi kami.</p>
                <br>
            </div>
            
            <div class="kontak-info-grid">
                <div class="kontak-card info-kontak-card fade-in">
                    <div class="kontak-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Alamat Kantor</h3>
                    <p>
                        <?php echo nl2br(htmlspecialchars($kontak_data['address'])); ?>
                    </p>
                </div>
                
                <div class="kontak-card info-kontak-card fade-in">
                    <div class="kontak-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h3>Telepon & Email</h3>
                    <p>
                        <strong>Telepon:</strong> <?php echo htmlspecialchars($kontak_data['phone']); ?><br>
                        <strong>Fax:</strong> <?php echo htmlspecialchars($kontak_data['fax']); ?><br>
                        <strong>Email:</strong> <?php echo htmlspecialchars($kontak_data['email']); ?><br>
                        <strong>Website:</strong> <?php echo htmlspecialchars($kontak_data['website'] ?? 'disdik.paserkab.go.id'); ?>
                    </p>
                </div>
                
                <div class="kontak-card info-kontak-card fade-in">
                    <div class="kontak-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Jam Operasional</h3>
                    <p>
                        <strong>Senin - Kamis:</strong> <?php echo htmlspecialchars($kontak_data['hours_weekdays']); ?><br>
                        <strong>Jumat:</strong> <?php echo htmlspecialchars($kontak_data['hours_friday']); ?><br>
                        <strong>Sabtu - Minggu:</strong> Libur<br>
                        <em>* Kecuali hari libur nasional</em>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Lokasi & Peta Section -->
    <section id="lokasi" class="kontak-fullscreen-section unit-kerja-section">
        <div class="container">
            <div class="kontak-section-title fade-in">
                <h2 style="color: #DDD;">Lokasi Kantor</h2>
                <p style="color: #DDD;">Temukan lokasi kantor kami di peta berikut</p>
            </div>
            
            <div class="fade-in">
                <div class="map-container">
                    <iframe 
                        src="<?php echo htmlspecialchars($kontak_data['map_embed_url']); ?>"
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                
                <div style="margin-top: 30px; text-align: center;">
                    <a href="https://maps.google.com/?q=Jl.+Jenderal+Sudirman+No.27,+Tanah+Grogot,+Kabupaten+Paser,+Kalimantan+Timur" 
                       target="_blank" 
                       class="btn-submit">
                        <i class="fas fa-directions"></i> Dapatkan Petunjuk Arah
                    </a>
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
                        <li><a href="layanan.html#layanan" onclick="scrollToLayanan()">Legalisir Ijazah/Dokumen Kelulusan</a></li>
                        <li><a href="layanan.html#layanan" onclick="scrollToLayanan()">Surat Keterangan Pindah Sekolah</a></li>
                        <li><a href="layanan.html#layanan" onclick="scrollToLayanan()">Tunjangan Profesi Guru</a></li>
                        <li><a href="layanan.html#layanan" onclick="scrollToLayanan()">Izin Pendirian Satuan Pendidikan</a></li>
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
    <script>
        // Form submission handler
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            // Simple validation
            if (!data.nama || !data.email || !data.subjek || !data.pesan) {
                alert('Harap isi semua field yang wajib diisi!');
                return;
            }
            
            // Show success message
            alert('Terima kasih! Pesan Anda telah berhasil dikirim. Kami akan menghubungi Anda segera.');
            
            // Reset form
            this.reset();
        });
        
        // Fade in on scroll function
        function fadeInOnScroll() {
            const fadeElements = document.querySelectorAll('.fade-in');
            
            fadeElements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('visible');
                }
            });
        }
        
        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            // Initial fade in check
            fadeInOnScroll();
            
            // Check on scroll
            window.addEventListener('scroll', fadeInOnScroll);
        });
    </script>
</body>
</html>