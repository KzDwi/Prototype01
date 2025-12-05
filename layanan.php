<?php
// WAJIB: session_start() harus di baris paling pertama, sebelum spasi atau HTML apapun.
session_start();
?>
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
        /* ========================================= */
        /* 1. INTERNAL STYLE (CSS) UNTUK ALERT & FORM */
        /* ========================================= */
        .alert-box {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            line-height: 1.5;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Warna Sukses (Hijau Pastel) */
        .alert-success { background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        /* Warna Error (Merah Pastel) */
        .alert-error { background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        /* Jarak Teks Deskripsi agar tidak nempel */
        .section-title p {
            margin-bottom: 35px !important;
            display: block;
            color: #555;
        }

        /* Tombol Admin Edit */
        .admin-edit-btn {
            position: absolute; top: 10px; right: 10px;
            background: rgba(0, 51, 153, 0.8); color: white;
            border: none; padding: 5px 10px; border-radius: 4px;
            font-size: 0.8rem; cursor: pointer; z-index: 100; display: none;
        }

        /* Icon Layanan */
        .layanan-icon i { font-size: 3rem !important; margin-bottom: 15px; }
        .layanan-icon img { max-width: 60px !important; max-height: 60px !important; width: auto; }
        
        /* Resize Textarea */
        .contact-form textarea {
            resize: vertical; width: 100%; min-height: 100px; max-height: 400px;
        }
        
        .btn-submit {
            margin-top: 25px; transition: all 0.3s; position: relative;
        }
        
        .layanan-card:hover .admin-edit-btn,
        .faq-item:hover .admin-edit-btn { display: block; }
        
        /* SWIPER STYLES */
        .swiper-layanan-wrapper { position: relative; width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 60px !important; }
        .layanan-swiper-container { width: 100%; overflow: hidden !important; position: relative; }
        .layanan-swiper { width: 100% !important; height: auto !important; padding: 20px 0 50px !important; margin: 30px auto 0 !important; }
        .swiper-slide { width: calc((100% - 50px) / 3) !important; max-width: 350px !important; height: auto !important; flex-shrink: 0; }
        .swiper-slide .layanan-card { width: 100% !important; height: 100% !important; min-height: 380px; display: flex; flex-direction: column; box-sizing: border-box; }
        
        .swiper-custom-prev, .swiper-custom-next {
            position: absolute; top: 50%; transform: translateY(-50%);
            width: 50px; height: 50px; background: white; border-radius: 50%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15); display: flex;
            align-items: center; justify-content: center; cursor: pointer;
            transition: all 0.3s ease; z-index: 20; border: 2px solid #003399;
        }
        .swiper-custom-prev { left: 5px; }
        .swiper-custom-next { right: 5px; }
        .swiper-custom-prev:hover, .swiper-custom-next:hover { background: #003399; transform: translateY(-50%) scale(1.1); box-shadow: 0 6px 20px rgba(0, 51, 153, 0.3); }
        .swiper-custom-prev:hover i, .swiper-custom-next:hover i { color: white; }
        .swiper-custom-prev i, .swiper-custom-next i { color: #003399; font-size: 1.3rem; font-weight: bold; transition: color 0.3s ease; }
        
        /* Responsive adjustments */
        @media (max-width: 1200px) { .swiper-slide { width: calc((100% - 40px) / 3) !important; } .swiper-layanan-wrapper { padding: 0 50px !important; } }
        @media (max-width: 992px) { .swiper-slide { width: calc((100% - 20px) / 2) !important; } .swiper-layanan-wrapper { padding: 0 40px !important; } .layanan-card { min-height: 360px; } }
        @media (max-width: 768px) { .swiper-slide { width: 100% !important; max-width: 400px !important; margin: 0 auto; } .swiper-layanan-wrapper { padding: 0 30px !important; } .swiper-custom-prev, .swiper-custom-next { display: none; } .layanan-card { min-height: 340px; } }
        @media (max-width: 576px) { .swiper-layanan-wrapper { padding: 0 20px !important; } .layanan-card { min-height: 320px; } }
        .swiper-wrapper { display: flex !important; width: auto !important; }
        .layanan-section .container { overflow: hidden !important; }
        
        <?php
        // Load Data Content
        $content_file = __DIR__ . '/data/content.json';
        $content = [];
        if (file_exists($content_file)) {
            $raw = file_get_contents($content_file);
            $content = json_decode($raw, true) ?? [];
        }

        // Setup Variabel
        $site_title = $content['index']['site_title'] ?? "Dinas Pendidikan dan Kebudayaan Kabupaten Paser";
        $page_title = $content['layanan']['title'] ?? ($content['index']['layanan_title'] ?? "Layanan Administrasi");
        $hero_subtitle = $content['index']['hero_data']['hero_subtext'] ?? "Memastikan Legalitas dan Kelancaran Administrasi Pendidikan Anda";
        $layanan_title = $content['layanan']['title'] ?? "Layanan Administrasi Kunci untuk Institusi & Individu";
        $faq_title = $content['faq_title'] ?? "Pertanyaan yang Sering Diajukan (FAQ)";
        $contact_section = $content['layanan']['contact_section'] ?? [];
        $contact_title = $contact_section['title'] ?? "Butuh Bantuan Administrasi Cepat?";
        $contact_subtitle = $contact_section['subtitle'] ?? "Isi formulir di bawah ini dan Admin akan menghubungi Anda dalam 2x24 jam.";

        $custom_contact_options = $contact_section['service_options'] ?? [];

        // Data Layanan
        if (isset($content['layanan']['layanan_data']) && is_array($content['layanan']['layanan_data'])) {
            $layanan_data = $content['layanan']['layanan_data'];
        } else {
            // Fallback Data
            $layanan_data = array(
                array('id' => 'legalisir-ijazah', 'icon' => 'fas fa-scroll', 'title' => 'Legalisir Dokumen Kelulusan', 'description' => 'Layanan legalisir ijazah dan dokumen kelulusan.', 'details' => 'Proses legalisir dokumen kelulusan meliputi verifikasi keabsahan dokumen asli.'),
                array('id' => 'surat-mutasi', 'icon' => 'fas fa-exchange-alt', 'title' => 'Surat Keterangan Pindah Sekolah', 'description' => 'Layanan penerbitan surat keterangan pindah sekolah.', 'details' => 'Melayani penerbitan surat mutasi untuk perpindahan sekolah antar daerah.'),
                array('id' => 'tunjangan-guru', 'icon' => 'fas fa-chalkboard-teacher', 'title' => 'Pengusulan Tunjangan Profesi Guru', 'description' => 'Layanan pengusulan tunjangan profesi guru.', 'details' => 'Membantu proses pengajuan TPG dengan verifikasi dokumen.'),
                array('id' => 'izin-pendirian', 'icon' => 'fas fa-building', 'title' => 'Izin Pendirian Satuan Pendidikan', 'description' => 'Layanan perizinan pendirian satuan pendidikan baru.', 'details' => 'Pendampingan lengkap proses perizinan dari persiapan dokumen.'),
                array('id' => 'izin-belajar', 'icon' => 'fas fa-graduation-cap', 'title' => 'Pengurusan Izin Belajar', 'description' => 'Penerbitan surat rekomendasi atau izin belajar.', 'details' => 'Melayani pengurusan izin belajar bagi ASN.')
            );
        }

        // Data FAQ
        $faq_data = array();
        if (isset($content['faq']) && is_array($content['faq'])) {
            foreach ($content['faq'] as $category => $items) {
                if (is_array($items)) {
                    foreach ($items as $qa) {
                        if (isset($qa['question']) && isset($qa['answer'])) {
                            $faq_data[] = array('question' => $qa['question'], 'answer' => $qa['answer']);
                        }
                    }
                }
            }
        }
        if (empty($faq_data)) {
            $faq_data = array(
                array('question' => 'Berapa lama waktu yang dibutuhkan?', 'answer' => 'Waktu pengerjaan standar adalah 3-5 hari kerja.'),
                array('question' => 'Apakah menangani sekolah swasta?', 'answer' => 'Ya, kami melayani sekolah negeri dan swasta.')
            );
        }

        // $is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;/
        $is_admin = false; // Paksa jadi false agar tombol hilang
        ?>
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

    <section class="layanan-hero fade-in">
        <div class="container">
            <nav class="breadcrumb-nav">
                <a href="index.html">Halaman Utama</a><span>/</span><a href="layanan.php" class="active">Layanan</a>
            </nav>
            <br><br>    
        </div>
        <div class="container">
            <div class="layanan-hero-content">
                <h1><?php echo htmlspecialchars($page_title); ?></h1>
                <h2 style="font-size: 2rem;">Dinas Pendidikan dan Kebudayaan Kabupaten Paser</h2> <br>
                <p class="hero-subtitle"><?php echo htmlspecialchars($hero_subtitle); ?></p>
                <a href="#layanan" class="btn btn-primary">Jelajahi Semua Layanan</a>
                
                <?php if ($is_admin): ?>
                <button class="admin-edit-btn" onclick="editHeroContent()"><i class="fas fa-edit"></i> Edit</button>
                <?php endif; ?>
            </div>
        </div>
        <br><br><br><br><br>
    </section>

    <section id="layanan" class="layanan-section fullscreen-section fade-in">
        <div class="container">
            <div class="section-title fade-in">
                <h2><?php echo htmlspecialchars($layanan_title); ?></h2>
                <?php if ($is_admin): ?>
                <button class="admin-edit-btn" onclick="editSectionTitle('layanan')"><i class="fas fa-edit"></i> Edit Judul</button>
                <?php endif; ?>
            </div>
            
            <div class="swiper-layanan-wrapper">
                <div class="layanan-swiper-container">
                    <div class="swiper layanan-swiper">
                        <div class="swiper-wrapper">
                            <?php foreach ($layanan_data as $index => $layanan): ?>
                            <div class="swiper-slide">
                                <div class="layanan-card fade-in" onclick="event.stopPropagation(); openLayananPopup('<?php echo $layanan['id']; ?>'); return false;">
                                    <?php if ($is_admin): ?>
                                    <button class="admin-edit-btn" onclick="event.stopPropagation(); editLayanan(<?php echo $index; ?>)"><i class="fas fa-edit"></i> Edit</button>
                                    <?php endif; ?>
                                    
                                    <div class="layanan-icon">
                                        <?php 
                                        $icon = $layanan['icon'] ?? '';
                                        if (!empty($icon) && (strpos($icon, '/') !== false || strpos($icon, '.') !== false)) {
                                            echo '<img src="' . htmlspecialchars($icon) . '" alt="' . htmlspecialchars($layanan['title'] ?? '') . '" style="max-width:80px;max-height:80px;object-fit:contain;">';
                                        } else {
                                            echo '<i class="' . htmlspecialchars($icon) . '"></i>';
                                        }
                                        ?>
                                    </div>
                                    <h3><?php echo htmlspecialchars($layanan['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($layanan['description']); ?></p>
                                    <div class="card-footer">
                                        <a href="#" class="btn-detail" onclick="event.stopPropagation(); openLayananPopup('<?php echo $layanan['id']; ?>'); return false;">Lihat Persyaratan Detail</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
                <div class="swiper-custom-prev"><i class="fas fa-chevron-left"></i></div>
                <div class="swiper-custom-next"><i class="fas fa-chevron-right"></i></div>
            </div>
            
            <?php if ($is_admin): ?>
            <div style="text-align: center; margin-top: 30px;">
                <button class="btn-detail" onclick="addNewLayanan()" style="background-color: #4CAF50;"><i class="fas fa-plus"></i> Tambah Layanan Baru</button>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <section id="faq" class="faq-section fullscreen-section fade-in">
        <div class="container">
            <div class="section-title fade-in">
                <h2><?php echo htmlspecialchars($faq_title); ?></h2>
                <?php if ($is_admin): ?>
                <button class="admin-edit-btn" onclick="editSectionTitle('faq')"><i class="fas fa-edit"></i> Edit Judul</button>
                <?php endif; ?>
            </div>
            <div class="faq-list">
                <?php foreach ($faq_data as $index => $faq): ?>
                <div class="faq-item fade-in">
                    <?php if ($is_admin): ?>
                    <button class="admin-edit-btn" onclick="editFAQ(<?php echo $index; ?>)"><i class="fas fa-edit"></i> Edit</button>
                    <button class="admin-edit-btn" onclick="deleteFAQ(<?php echo $index; ?>)" style="right: 60px; background-color: #e36159;"><i class="fas fa-trash"></i></button>
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
                <button class="btn-detail" onclick="addNewFAQ()" style="background-color: #4CAF50;"><i class="fas fa-plus"></i> Tambah FAQ Baru</button>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <section id="contact" class="contact-section fullscreen-section fade-in">
        <div class="container">
            <div class="section-title fade-in">
                <h2><?php echo htmlspecialchars($contact_title); ?></h2>
                <p><?php echo htmlspecialchars($contact_subtitle); ?></p>
            </div>

            <?php if (isset($_SESSION['status']) && isset($_SESSION['message'])): ?>
                <?php 
                    $isSuccess = ($_SESSION['status'] == 'success');
                    $alertClass = $isSuccess ? 'alert-success' : 'alert-error';
                    // SVG Icons
                    $icon = $isSuccess 
                        ? '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                        : '<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                ?>
                <div class="alert-box <?php echo $alertClass; ?> fade-in">
                    <?php echo $icon; ?>
                    <span><?php echo $_SESSION['message']; ?></span>
                </div>
                <?php 
                    unset($_SESSION['status']);
                    unset($_SESSION['message']);
                ?>
            <?php endif; ?>

            <div class="contact-form fade-in">
                <form id="consultationForm" method="POST" action="process_contact.php" onsubmit="return konfirmasiKirim(this);">
                    <div class="form-group">
                        <input type="text" name="nama" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email Aktif" required>
                    </div>
                    <div class="form-group">
                        <select name="jenis_layanan" required>
                            <option value="">Pilih Jenis Layanan...</option>
                            <?php 
                            if (!empty($custom_contact_options) && is_array($custom_contact_options)): 
                                foreach ($custom_contact_options as $option):
                            ?>
                                <option value="<?php echo htmlspecialchars($option); ?>"><?php echo htmlspecialchars($option); ?></option>
                            <?php 
                                endforeach;
                            else: 
                                foreach ($layanan_data as $layanan): 
                            ?>
                                <option value="<?php echo htmlspecialchars($layanan['title']); ?>"><?php echo htmlspecialchars($layanan['title']); ?></option>
                            <?php 
                                endforeach;
                            endif; 
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <textarea name="pesan" placeholder="Jelaskan Kebutuhan Anda Secara Singkat" rows="4"></textarea>
                    </div>
                    <button type="submit" id="btnKirim" class="btn btn-primary btn-submit">Kirim Konsultasi</button>
                </form>
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
            </div>
            <div class="footer-bottom">
                <p>© 2025 Dinas Pendidikan dan Kebudayaan Kabupaten Paser. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="js/script.js"></script>

    <script>
        // 4. JAVASCRIPT KONFIRMASI (FITUR BARU)
        function konfirmasiKirim(form) {
            // Validasi sederhana
            var nama = form.nama.value;
            var layanan = form.jenis_layanan.value;
            if(nama == "" || layanan == "") {
                alert("Mohon lengkapi data terlebih dahulu.");
                return false;
            }

            // Popup Konfirmasi
            var yakin = confirm("Halo " + nama + ",\n\nApakah Anda yakin data yang diisi sudah benar?\nTekan OK untuk mengirim.");

            if (yakin) {
                // Efek Loading pada Tombol
                var btn = document.getElementById('btnKirim');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
                btn.style.opacity = '0.7';
                btn.style.cursor = 'not-allowed';
                return true; // Kirim ke process_contact.php
            } else {
                return false; // Batal
            }
        }

        // Data PHP ke JS
        const layananDataJS = {
            <?php 
            $first = true;
            foreach ($layanan_data as $item) {
                if (!$first) echo ',';
                $cara = isset($item['cara_kerja']) ? $item['cara_kerja'] : (isset($item['caraKerja']) ? $item['caraKerja'] : []);
                $pers = isset($item['persyaratan']) ? $item['persyaratan'] : (isset($item['persyaratan']) ? $item['persyaratan'] : []);
                echo "'" . $item['id'] . "': " . json_encode([
                    'title' => $item['title'] ?? '',
                    'subtitle' => $item['subtitle'] ?? '',
                    'description' => $item['description'] ?? '',
                    'popup_desc' => $item['popup_desc'] ?? ($item['details'] ?? ''),
                    'cara_kerja' => $cara,
                    'persyaratan' => $pers
                ]);
                $first = false;
            }
            ?>
        };

        // Logic FAQ & Swiper
        document.addEventListener('DOMContentLoaded', function() {
            // FAQ Toggle
            const faqQuestions = document.querySelectorAll('.faq-question');
            faqQuestions.forEach(question => {
                question.addEventListener('click', () => {
                    const answer = question.nextElementSibling;
                    const isActive = question.classList.contains('active');
                    faqQuestions.forEach(q => {
                        const qAnswer = q.nextElementSibling;
                        if (qAnswer.style.display === 'block') {
                            qAnswer.style.display = 'none';
                            q.classList.remove('active');
                        }
                    });
                    if (!isActive) {
                        answer.style.display = 'block';
                        question.classList.add('active');
                    }
                });
            });
            
            // Swiper Initialization
            if (document.querySelector('.layanan-swiper')) {
                const layananSwiper = new Swiper('.layanan-swiper', {
                    slidesPerView: 3,
                    spaceBetween: 25,
                    loop: false,
                    navigation: { nextEl: '.swiper-custom-next', prevEl: '.swiper-custom-prev' },
                    pagination: { el: '.swiper-pagination', clickable: true, dynamicBullets: true },
                    breakpoints: {
                        320: { slidesPerView: 1, spaceBetween: 15, centeredSlides: true },
                        768: { slidesPerView: 2, spaceBetween: 20 },
                        1024: { slidesPerView: 3, spaceBetween: 25 }
                    }
                });
                
                // Fix width issue
                setTimeout(() => {
                    const slides = document.querySelectorAll('.swiper-slide');
                    slides.forEach(slide => {
                        slide.style.width = 'calc((100% - 50px) / 3)';
                        slide.style.maxWidth = '350px';
                    });
                    layananSwiper.update();
                }, 100);
            }
        });

        <?php if ($is_admin): ?>
        // Admin functions (Placeholder)
        function editHeroContent() { alert('Fitur edit hero content tersedia di admin panel.'); }
        function editSectionTitle(section) { alert('Fitur edit judul ' + section + ' tersedia di admin panel.'); }
        function editLayanan(index) { alert('Fitur edit layanan tersedia di admin panel.'); }
        function addNewLayanan() { alert('Fitur tambah layanan tersedia di admin panel.'); }
        function editFAQ(index) { alert('Fitur edit FAQ tersedia di admin panel.'); }
        function deleteFAQ(index) { if (confirm('Hapus FAQ?')) alert('Fitur hapus tersedia di admin panel.'); }
        function addNewFAQ() { alert('Fitur tambah FAQ tersedia di admin panel.'); }
        <?php endif; ?>
    </script>
</body>
</html>