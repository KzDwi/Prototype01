<?php
// Mulai session dan include functions
session_start();
require_once 'functions.php';

// Ambil data FAQ dari database jika ada, atau gunakan default
$faq_data = getFAQData(); // Fungsi ini sudah ada di functions.php

// HAPUS fungsi getFAQData() yang ada di sini karena sudah ada di functions.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/layanan-styles.css" />
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/faq-styles.css" /> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>FAQ - Dinas Pendidikan Kabupaten Paser</title>
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
                        <li><a href="index.html" onclick="closeMobileMenu()">Beranda</a></li>
                        <li><a href="profil.html" onclick="closeMobileMenu()">Profil</a></li>
                        <li><a href="layanan.html" onclick="closeMobileMenu(); scrollToLayanan();">Layanan</a></li>
                        <li><a href="berita.php" onclick="closeMobileMenu()">Berita</a></li>
                        <li><a href="#kontak" onclick="closeMobileMenu()">Kontak</a></li>
                        <li><a href="#faq-content" onclick="closeMobileMenu()" class="active">FAQ</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <section class="layanan-hero">
        <div class="container">
            <nav class="breadcrumb-nav">
                <a href="index.html">Halaman Utama</a>
                <span>/</span>
                <a href="faq.php" class="active">FAQ</a>
            </nav>
            <br>
            <br>    
        </div>
        <div class="container">
            <div class="layanan-hero-content">
                <h1>FAQ (Tanya Jawab)</h1>
                <h2 style="color: #DDD;">Dinas Pendidikan dan Kebudayaan Paser</h2>
            </div>
        </div>
        <br> <br>
        <br> <br>
        <br>
    </section>

    <section id="faq-content" class="visi-misi" style="padding: 80px 0; background-color: #fff;">
        <div class="container">
            <div class="section-title">
                <h2>Temukan jawaban atas pertanyaan-pertanyaan yang paling sering diajukan seputar layanan, kebijakan, dan program di Dinas Pendidikan Kabupaten Paser.</h2> <br><br>
            </div>

            <!-- Informasi Umum -->
            <div class="faq-section-header">
                <span class="icon">i</span>
                Informasi Umum
            </div>

            <?php foreach ($faq_data['informasi_umum'] as $index => $faq): ?>
            <div class="faq-item">
                <input type="checkbox" id="faq-umum-<?php echo $index; ?>" class="faq-toggle">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <h3><?php echo htmlspecialchars($faq['question']); ?></h3>
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
                    <?php echo $faq['answer']; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Layanan Kesiswaan -->
            <div class="faq-section-header green">
                <span class="icon">🎓</span>
                Layanan Kesiswaan
            </div>

            <?php foreach ($faq_data['layanan_kesiswaan'] as $index => $faq): ?>
            <div class="faq-item">
                <input type="checkbox" id="faq-kesiswaan-<?php echo $index; ?>" class="faq-toggle">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <h3><?php echo htmlspecialchars($faq['question']); ?></h3>
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
                    <?php echo $faq['answer']; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Guru & Tenaga Kependidikan -->
            <div class="faq-section-header orange">
                <span class="icon">👨‍🏫</span>
                Seputar Guru & Tenaga Kependidikan (GTK)
            </div>

            <?php foreach ($faq_data['guru_tenaga_kependidikan'] as $index => $faq): ?>
            <div class="faq-item">
                <input type="checkbox" id="faq-gtk-<?php echo $index; ?>" class="faq-toggle">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <h3><?php echo htmlspecialchars($faq['question']); ?></h3>
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
                    <?php echo $faq['answer']; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- PPDB -->
            <div class="faq-section-header purple">
                <span class="icon">🏢</span>
                Seputar PPDB
            </div>

            <?php foreach ($faq_data['ppdb'] as $index => $faq): ?>
            <div class="faq-item">
                <input type="checkbox" id="faq-ppdb-<?php echo $index; ?>" class="faq-toggle">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <h3><?php echo htmlspecialchars($faq['question']); ?></h3>
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
                    <?php echo $faq['answer']; ?>
                </div>
            </div>
            <?php endforeach; ?>

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

    <!-- FAQ Toggle Script -->
    <script>
    // FAQ Functionality - SIMPLIFIED VERSION
    document.addEventListener('DOMContentLoaded', function() {
        const faqQuestions = document.querySelectorAll('.faq-question');
        
        console.log('FAQ questions found:', faqQuestions.length);
        
        faqQuestions.forEach(question => {
            question.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const faqItem = this.parentElement;
                const isActive = faqItem.classList.contains('active');
                
                console.log('FAQ clicked, active:', isActive);
                
                // Close all other FAQ items
                document.querySelectorAll('.faq-item').forEach(item => {
                    if (item !== faqItem) {
                        item.classList.remove('active');
                    }
                });
                
                // Toggle current item
                if (!isActive) {
                    faqItem.classList.add('active');
                    console.log('FAQ opened');
                } else {
                    faqItem.classList.remove('active');
                    console.log('FAQ closed');
                }
            });
        });
        
        // Debug: Check initial state
        console.log('FAQ initialization complete');
    });
    </script>
    <script src="js/script.js"></script>
</body>
</html>