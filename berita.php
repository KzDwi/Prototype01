<?php
session_start();

// Simulasi data berita (nanti diganti dengan database)
$semua_berita = [
    [
        'id' => 1,
        'judul' => 'Penerimaan Peserta Didik Baru Tahun 2024 Dibuka',
        'excerpt' => 'Pemerintah Kabupaten Paser membuka pendaftaran PPDB untuk tahun ajaran 2024/2025. Daftar sekarang melalui sistem online...',
        'kategori' => 'Pendidikan',
        'gambar' => 'https://via.placeholder.com/400x200/003399/ffffff?text=PPDB+2024',
        'tanggal' => '2024-01-15',
        'penulis' => 'Admin Disdikbud'
    ],
    [
        'id' => 2,
        'judul' => 'Festival Budaya Paser 2024 Sukses Digelar',
        'excerpt' => 'Festival budaya tahunan Kabupaten Paser berhasil menarik ribuan pengunjung. Menampilkan berbagai kesenian tradisional...',
        'kategori' => 'Kebudayaan',
        'gambar' => 'https://via.placeholder.com/400x200/002280/ffffff?text=Festival+Budaya',
        'tanggal' => '2024-01-12',
        'penulis' => 'Admin Disdikbud'
    ],
    // ... tambahkan data lainnya
];

// Handle filter kategori
$kategori_aktif = $_GET['kategori'] ?? 'semua';
$halaman_aktif = $_GET['halaman'] ?? 1;

// Filter berita berdasarkan kategori
if ($kategori_aktif !== 'semua') {
    $berita_tampil = array_filter($semua_berita, function($berita) use ($kategori_aktif) {
        return $berita['kategori'] === $kategori_aktif;
    });
} else {
    $berita_tampil = $semua_berita;
}

// Pagination
$jumlah_per_halaman = 6;
$total_berita = count($berita_tampil);
$total_halaman = ceil($total_berita / $jumlah_per_halaman);
$berita_tampil = array_slice($berita_tampil, ($halaman_aktif - 1) * $jumlah_per_halaman, $jumlah_per_halaman);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css" />
    <title>Berita - Dinas Pendidikan dan Kebudayaan Kabupaten Paser</title>
    <style>
        /* CSS styles dari sebelumnya... */
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
        </div>
    </section>

    <!-- Berita Grid -->
    <section class="berita-section">
        <div class="container">
            <?php if (empty($berita_tampil)): ?>
                <div class="no-berita" style="text-align: center; padding: 50px;">
                    <h3>Tidak ada berita ditemukan</h3>
                    <p>Silakan pilih kategori lain atau coba lagi nanti.</p>
                </div>
            <?php else: ?>
                <div class="berita-grid">
                    <?php foreach ($berita_tampil as $berita): ?>
                    <article class="berita-card fade-in">
                        <div class="berita-image">
                            <img src="<?php echo htmlspecialchars($berita['gambar']); ?>" alt="<?php echo htmlspecialchars($berita['judul']); ?>">
                        </div>
                        <div class="berita-content">
                            <span class="berita-category"><?php echo htmlspecialchars($berita['kategori']); ?></span>
                            <h3 class="berita-title"><?php echo htmlspecialchars($berita['judul']); ?></h3>
                            <p class="berita-excerpt"><?php echo htmlspecialchars($berita['excerpt']); ?></p>
                            <div class="berita-meta">
                                <span class="berita-date">📅 <?php echo date('d F Y', strtotime($berita['tanggal'])); ?></span>
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
                        <a href="berita.php?kategori=<?php echo $kategori_aktif; ?>&halaman=<?php echo $halaman_aktif - 1; ?>" class="page-btn">← Sebelumnya</a>
                    <?php else: ?>
                        <span class="page-btn disabled">← Sebelumnya</span>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                        <a href="berita.php?kategori=<?php echo $kategori_aktif; ?>&halaman=<?php echo $i; ?>" class="page-btn <?php echo $i == $halaman_aktif ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($halaman_aktif < $total_halaman): ?>
                        <a href="berita.php?kategori=<?php echo $kategori_aktif; ?>&halaman=<?php echo $halaman_aktif + 1; ?>" class="page-btn">Selanjutnya →</a>
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
        <!-- Footer content sama seperti sebelumnya -->
    </footer>

    <script>
        // JavaScript tetap sama...
    </script>
</body>
</html>