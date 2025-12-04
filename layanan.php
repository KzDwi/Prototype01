<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/layanan-styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <title>Layanan - Dinas Pendidikan dan Kebudayaan Kabupaten Paser</title>
    <style>
        /* Tambahan gaya untuk integrasi PHP */
        .admin-edit-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 51, 153, 0.8);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            cursor: pointer;
            z-index: 100;
            display: none;
        }
        
        .layanan-card:hover .admin-edit-btn,
        .faq-item:hover .admin-edit-btn {
            display: block;
        }
        
        /* ========== PERBAIKAN SWIPER - SOLUSI BARU ========== */
        .swiper-layanan-wrapper {
            position: relative;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 60px !important; /* Memberi ruang untuk tombol navigasi */
        }
        
        /* Container swiper yang ketat */
        .layanan-swiper-container {
            width: 100%;
            overflow: hidden !important;
            position: relative;
        }
        
        .layanan-swiper {
            width: 100% !important;
            height: auto !important;
            padding: 20px 0 50px !important;
            margin: 30px auto 0 !important;
        }
        
        /* Slide dengan width yang dikontrol ketat */
        .swiper-slide {
            width: calc((100% - 50px) / 3) !important; /* Tiga kartu dengan gap */
            max-width: 350px !important; /* Batas maksimal */
            height: auto !important;
            flex-shrink: 0;
        }
        
        /* Kartu layanan - width 100% dari slide */
        .swiper-slide .layanan-card {
            width: 100% !important;
            height: 100% !important;
            min-height: 380px;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }
        
        /* Navigation buttons yang tidak keluar dari container */
        .swiper-custom-prev,
        .swiper-custom-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 20;
            border: 2px solid #003399;
        }
        
        .swiper-custom-prev {
            left: 5px;
        }
        
        .swiper-custom-next {
            right: 5px;
        }
        
        .swiper-custom-prev:hover,
        .swiper-custom-next:hover {
            background: #003399;
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 51, 153, 0.3);
        }
        
        .swiper-custom-prev:hover i,
        .swiper-custom-next:hover i {
            color: white;
        }
        
        .swiper-custom-prev i,
        .swiper-custom-next i {
            color: #003399;
            font-size: 1.3rem;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        
        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .swiper-slide {
                width: calc((100% - 40px) / 3) !important;
            }
            
            .swiper-layanan-wrapper {
                padding: 0 50px !important;
            }
        }
        
        @media (max-width: 992px) {
            .swiper-slide {
                width: calc((100% - 20px) / 2) !important; /* 2 kartu di tablet */
            }
            
            .swiper-layanan-wrapper {
                padding: 0 40px !important;
            }
            
            .layanan-card {
                min-height: 360px;
            }
        }
        
        @media (max-width: 768px) {
            .swiper-slide {
                width: 100% !important; /* 1 kartu di mobile */
                max-width: 400px !important;
                margin: 0 auto;
            }
            
            .swiper-layanan-wrapper {
                padding: 0 30px !important;
            }
            
            .swiper-custom-prev,
            .swiper-custom-next {
                display: none;
            }
            
            .layanan-card {
                min-height: 340px;
            }
        }
        
        @media (max-width: 576px) {
            .swiper-layanan-wrapper {
                padding: 0 20px !important;
            }
            
            .layanan-card {
                min-height: 320px;
            }
        }
        
        /* Override untuk memastikan swiper tidak overflow */
        .swiper-wrapper {
            display: flex !important;
            width: auto !important;
        }
        
        /* Hide overflow secara ketat */
        .layanan-section .container {
            overflow: hidden !important;
        }
        
        <?php
        // Variabel untuk konten dinamis (akan diambil dari database nantinya)
        $site_title = "Dinas Pendidikan dan Kebudayaan Kabupaten Paser";
        $page_title = "Layanan Administrasi";
        $hero_subtitle = "Memastikan Legalitas dan Kelancaran Administrasi Pendidikan Anda";
        $layanan_title = "Layanan Administrasi Kunci untuk Institusi & Individu";
        $faq_title = "Pertanyaan yang Sering Diajukan (FAQ)";
        $contact_title = "Butuh Bantuan Administrasi Cepat?";
        $contact_subtitle = "Isi formulir di bawah ini dan Admin akan menghubungi Anda dalam 2x24 jam.";
        
        // Data layanan (akan diambil dari database nantinya)
        $layanan_data = array(
            array(
                'id' => 'legalisir-ijazah',
                'icon' => 'fas fa-scroll',
                'title' => 'Legalisir Dokumen Kelulusan',
                'description' => 'Layanan legalisir ijazah dan dokumen kelulusan untuk keperluan administrasi.',
                'details' => 'Proses legalisir dokumen kelulusan meliputi verifikasi keabsahan dokumen asli, pemberian cap dan tanda tangan resmi, serta pengembalian dokumen yang sudah dilegalisir.'
            ),
            array(
                'id' => 'surat-mutasi',
                'icon' => 'fas fa-exchange-alt',
                'title' => 'Surat Keterangan Pindah Sekolah',
                'description' => 'Layanan penerbitan surat keterangan pindah sekolah untuk siswa yang akan berpindah.',
                'details' => 'Melayani penerbitan surat mutasi untuk perpindahan sekolah antar daerah, termasuk verifikasi dokumen dan koordinasi dengan sekolah tujuan.'
            ),
            array(
                'id' => 'tunjangan-guru',
                'icon' => 'fas fa-chalkboard-teacher',
                'title' => 'Pengusulan Tunjangan Profesi Guru',
                'description' => 'Layanan pengusulan tunjangan profesi guru bagi guru yang memenuhi persyaratan.',
                'details' => 'Membantu proses pengajuan TPG dengan verifikasi dokumen kelengkapan dan pengusulan ke pihak berwenang.'
            ),
            array(
                'id' => 'izin-pendirian',
                'icon' => 'fas fa-building',
                'title' => 'Izin Pendirian Satuan Pendidikan',
                'description' => 'Layanan perizinan pendirian satuan pendidikan baru meliputi PAUD, SD, SMP, LKP.',
                'details' => 'Pendampingan lengkap proses perizinan dari persiapan dokumen hingga penerbitan izin operasional.'
            ),
            array(
                'id' => 'izin-belajar',
                'icon' => 'fas fa-graduation-cap',
                'title' => 'Pengurusan Izin Belajar dan Tugas Belajar',
                'description' => 'Penerbitan surat rekomendasi atau izin untuk melanjutkan pendidikan ke jenjang yang lebih tinggi',
                'details' => 'Melayani pengurusan izin belajar bagi ASN yang ingin melanjutkan pendidikan formal.'
            )
        );
        
        // Data FAQ (akan diambil dari database nantinya)
        $faq_data = array(
            array(
                'question' => 'Berapa lama waktu yang dibutuhkan untuk proses legalisir ijazah?',
                'answer' => 'Waktu pengerjaan standar adalah 3-5 hari kerja setelah semua dokumen persyaratan lengkap diterima. Kami juga menawarkan opsi layanan ekspres dengan biaya tambahan.'
            ),
            array(
                'question' => 'Apakah layanan ini menangani perizinan pendirian sekolah swasta dan negeri?',
                'answer' => 'Kami memiliki spesialisasi dalam pendampingan perizinan Satuan Pendidikan Swasta (formal dan non-formal), yang meliputi penyusunan proposal, studi kelayakan, hingga pengajuan ke Dinas terkait.'
            ),
            array(
                'question' => 'Dokumen apa saja yang diperlukan untuk mengurus pindah sekolah antar provinsi?',
                'answer' => 'Secara umum memerlukan: Surat Permohonan Pindah dari Orang Tua/Wali, Rapor Asli dan Fotokopi (terakhir), Surat Keterangan diterima di sekolah tujuan, dan Surat Keterangan Keluar dari sekolah asal.'
            ),
            array(
                'question' => 'Apa keunggulan menggunakan jasa konsultan dibandingkan mengurus sendiri?',
                'answer' => 'Keunggulan utama adalah efisiensi waktu, kepastian hukum (regulasi terbaru), dan menghindari kesalahan teknis yang dapat menyebabkan penolakan dokumen, berkat pengalaman Dr. Angga Praja.'
            )
        );
        
        // Cek apakah admin sedang login (nanti akan diintegrasikan dengan session)
        $is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
        ?>
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
                        <li><a href="#" onclick="closeMobileMenu(); scrollToLayanan();" class="active">Layanan</a></li>
                        <li><a href="berita.php" onclick="closeMobileMenu()">Berita</a></li>
                        <li><a href="kontak.php" onclick="closeMobileMenu()">Kontak</a></li>
                        <li><a href="faq.php" onclick="closeMobileMenu()">FAQ</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section Layanan -->
    <section class="layanan-hero fade-in">
        <div class="container">
            <nav class="breadcrumb-nav">
                <a href="index.html">Halaman Utama</a>
                <span>/</span>
                <a href="layanan.php" class="active">Layanan</a>
            </nav>
            <br>
            <br>    
        </div>
        <div class="container">
            <div class="layanan-hero-content">
                <h1><?php echo htmlspecialchars($page_title); ?></h1>
                <h2 style="font-size: 2rem;">Dinas Pendidikan dan Kebudayaan Kabupaten Paser</h2> <br>
                <p class="hero-subtitle"><?php echo htmlspecialchars($hero_subtitle); ?></p>
                <a href="#layanan" class="btn btn-primary">Jelajahi Semua Layanan</a>
                
                <?php if ($is_admin): ?>
                <button class="admin-edit-btn" onclick="editHeroContent()">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <?php endif; ?>
            </div>
        </div>
        <br> <br>
        <br> <br>
        <br>
    </section>

    <!-- Layanan Section -->
    <section id="layanan" class="layanan-section fullscreen-section fade-in">
        <div class="container">
            <div class="section-title fade-in">
                <h2><?php echo htmlspecialchars($layanan_title); ?></h2>
                
                <?php if ($is_admin): ?>
                <button class="admin-edit-btn" onclick="editSectionTitle('layanan')">
                    <i class="fas fa-edit"></i> Edit Judul
                </button>
                <?php endif; ?>
            </div>
            
            <!-- Swiper Container yang lebih ketat -->
            <div class="swiper-layanan-wrapper">
                <div class="layanan-swiper-container">
                    <div class="swiper layanan-swiper">
                        <div class="swiper-wrapper">
                            <?php foreach ($layanan_data as $index => $layanan): ?>
                            <div class="swiper-slide">
                                <div class="layanan-card fade-in" onclick="openLayananPopup('<?php echo $layanan['id']; ?>')">
                                    <?php if ($is_admin): ?>
                                    <button class="admin-edit-btn" onclick="editLayanan(<?php echo $index; ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <?php endif; ?>
                                    
                                    <div class="layanan-icon">
                                        <i class="<?php echo $layanan['icon']; ?>"></i>
                                    </div>
                                    <h3><?php echo htmlspecialchars($layanan['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($layanan['description']); ?></p>
                                    <div class="card-footer">
                                        <a href="#" class="btn-detail" onclick="openLayananPopup('<?php echo $layanan['id']; ?>')">Lihat Persyaratan Detail</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Pagination dots -->
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
                
                <!-- Navigation buttons -->
                <div class="swiper-custom-prev">
                    <i class="fas fa-chevron-left"></i>
                </div>
                <div class="swiper-custom-next">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            
            <?php if ($is_admin): ?>
            <div style="text-align: center; margin-top: 30px;">
                <button class="btn-detail" onclick="addNewLayanan()" style="background-color: #4CAF50;">
                    <i class="fas fa-plus"></i> Tambah Layanan Baru
                </button>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="faq-section fullscreen-section fade-in">
        <div class="container">
            <div class="section-title fade-in">
                <h2><?php echo htmlspecialchars($faq_title); ?></h2>
                
                <?php if ($is_admin): ?>
                <button class="admin-edit-btn" onclick="editSectionTitle('faq')">
                    <i class="fas fa-edit"></i> Edit Judul
                </button>
                <?php endif; ?>
            </div>
            <div class="faq-list">
                <?php foreach ($faq_data as $index => $faq): ?>
                <div class="faq-item fade-in">
                    <?php if ($is_admin): ?>
                    <button class="admin-edit-btn" onclick="editFAQ(<?php echo $index; ?>)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="admin-edit-btn" onclick="deleteFAQ(<?php echo $index; ?>)" style="right: 60px; background-color: #e36159;">
                        <i class="fas fa-trash"></i>
                    </button>
                    <?php endif; ?>
                    
                    <div class="faq-question">
                        <span><?php echo htmlspecialchars($faq['question']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p><?php echo htmlspecialchars($faq['answer']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($is_admin): ?>
            <div style="text-align: center; margin-top: 30px;">
                <button class="btn-detail" onclick="addNewFAQ()" style="background-color: #4CAF50;">
                    <i class="fas fa-plus"></i> Tambah FAQ Baru
                </button>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section fullscreen-section fade-in">
        <div class="container">
            <div class="section-title fade-in">
                <h2><?php echo htmlspecialchars($contact_title); ?></h2>
                <p><?php echo htmlspecialchars($contact_subtitle); ?></p><br>
                
                <?php if ($is_admin): ?>
                <button class="admin-edit-btn" onclick="editSectionTitle('contact')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <?php endif; ?>
            </div>
            <div class="contact-form fade-in">
                <form method="POST" action="process_contact.php">
                    <div class="form-group">
                        <input type="text" name="nama" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email Aktif" required>
                    </div>
                    <div class="form-group">
                        <select name="jenis_layanan" required>
                            <option value="">Pilih Jenis Layanan...</option>
                            <?php foreach ($layanan_data as $layanan): ?>
                            <option value="<?php echo $layanan['id']; ?>"><?php echo htmlspecialchars($layanan['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <textarea name="pesan" placeholder="Jelaskan Kebutuhan Anda Secara Singkat" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-submit">Kirim Konsultasi</button>
                </form>
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
                        <li><a href="layanan.php#layanan" onclick="scrollToLayanan()"><?php echo htmlspecialchars($layanan['title']); ?></a></li>
                        <?php endforeach; ?>
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

    <!-- Tambahkan Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="js/script.js"></script>

    <script>
        // Fungsi FAQ toggle
        document.addEventListener('DOMContentLoaded', function() {
            const faqQuestions = document.querySelectorAll('.faq-question');

            faqQuestions.forEach(question => {
                question.addEventListener('click', () => {
                    const answer = question.nextElementSibling;
                    const isActive = question.classList.contains('active');

                    // Tutup semua jawaban yang terbuka
                    faqQuestions.forEach(q => {
                        const qAnswer = q.nextElementSibling;
                        if (qAnswer.style.display === 'block') {
                            qAnswer.style.display = 'none';
                            q.classList.remove('active');
                        }
                    });

                    // Buka/tutup jawaban yang diklik
                    if (!isActive) {
                        answer.style.display = 'block';
                        question.classList.add('active');
                    }
                });
            });
            
            // Initialize Swiper dengan pengaturan yang sangat ketat
            if (document.querySelector('.layanan-swiper')) {
                const layananSwiper = new Swiper('.layanan-swiper', {
                    // HARD LIMIT: 3 slides maksimal
                    slidesPerView: 3,
                    spaceBetween: 25,
                    
                    // NO LOOP - untuk mencegah efek "lebih dari 3"
                    loop: false,
                    
                    // Strict width control
                    watchOverflow: true,
                    centeredSlides: false,
                    freeMode: false,
                    
                    // No auto calculation
                    autoHeight: false,
                    
                    autoplay: {
                        delay: 4000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    speed: 600,
                    
                    // Navigation
                    navigation: {
                        nextEl: '.swiper-custom-next',
                        prevEl: '.swiper-custom-prev',
                    },
                    
                    // Pagination
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                        dynamicBullets: true,
                    },
                    
                    // Breakpoints yang sangat ketat
                    breakpoints: {
                        // Mobile: 1 slide
                        320: {
                            slidesPerView: 1,
                            spaceBetween: 15,
                            centeredSlides: true,
                        },
                        // Tablet: 2 slides
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 20,
                            centeredSlides: false,
                        },
                        // Desktop: TEPAT 3 slides - TIDAK LEBIH
                        1024: {
                            slidesPerView: 3,
                            spaceBetween: 25,
                            centeredSlides: false,
                        }
                    },
                    
                    on: {
                        init: function() {
                            console.log('✅ Swiper initialized with EXACTLY 3 slides visible');
                            console.log('Container width:', this.el.offsetWidth);
                            console.log('Slide width:', this.slides[0]?.offsetWidth);
                            console.log('Visible slides:', this.slidesPerView);
                        }
                    }
                });
                
                // Force strict width control
                setTimeout(() => {
                    const slides = document.querySelectorAll('.swiper-slide');
                    slides.forEach(slide => {
                        slide.style.width = 'calc((100% - 50px) / 3)';
                        slide.style.maxWidth = '350px';
                    });
                    layananSwiper.update();
                }, 100);
                
                // Simpan reference untuk debugging
                window.layananSwiper = layananSwiper;
            }
        });
        
        // Fungsi untuk admin editing (placeholder - akan diimplementasi di admin-pengaturan.php)
        <?php if ($is_admin): ?>
        function editHeroContent() {
            alert('Fitur edit hero content akan tersedia di admin panel.');
            // window.location.href = 'admin-pengaturan.php?section=hero';
        }
        
        function editSectionTitle(section) {
            alert('Fitur edit judul section ' + section + ' akan tersedia di admin panel.');
            // window.location.href = 'admin-pengaturan.php?section=' + section + '&action=edit_title';
        }
        
        function editLayanan(index) {
            alert('Fitur edit layanan #' + (index + 1) + ' akan tersedia di admin panel.');
            // window.location.href = 'admin-pengaturan.php?section=layanan&id=' + index;
        }
        
        function addNewLayanan() {
            alert('Fitur tambah layanan baru akan tersedia di admin panel.');
            // window.location.href = 'admin-pengaturan.php?section=layanan&action=add';
        }
        
        function editFAQ(index) {
            alert('Fitur edit FAQ #' + (index + 1) + ' akan tersedia di admin panel.');
            // window.location.href = 'admin-pengaturan.php?section=faq&id=' + index;
        }
        
        function deleteFAQ(index) {
            if (confirm('Apakah Anda yakin ingin menghapus FAQ ini?')) {
                alert('Fitur hapus FAQ akan tersedia di admin panel.');
                // window.location.href = 'admin-pengaturan.php?section=faq&id=' + index + '&action=delete';
            }
        }
        
        function addNewFAQ() {
            alert('Fitur tambah FAQ baru akan tersedia di admin panel.');
            // window.location.href = 'admin-pengaturan.php?section=faq&action=add';
        }
        <?php endif; ?>
        
        // Debug function untuk cek width
        function debugSwiper() {
            if (window.layananSwiper) {
                console.log('=== DEBUG SWIPER ===');
                console.log('Container width:', window.layananSwiper.el.offsetWidth);
                console.log('Wrapper width:', window.layananSwiper.wrapperEl.offsetWidth);
                console.log('Slides count:', window.layananSwiper.slides.length);
                console.log('Slides per view:', window.layananSwiper.params.slidesPerView);
                
                const slides = document.querySelectorAll('.swiper-slide');
                slides.forEach((slide, i) => {
                    console.log(`Slide ${i} width:`, slide.offsetWidth);
                });
            }
        }
    </script>
</body>
</html>