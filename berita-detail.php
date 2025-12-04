<?php
session_start();
require_once 'functions.php';

$id_berita = $_GET['id'] ?? 0;

// Ambil detail berita dari database
$berita = ambilBeritaById($id_berita);

if (!$berita) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>Berita tidak ditemukan</h1>";
    exit;
}

// Update counter dibaca
updateCounterDibaca($id_berita);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($berita['judul']); ?> - Disdikbud Paser</title>
    <link rel="stylesheet" href="css/styles.css" />
    <style>
        .berita-detail {
            max-width: 800px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }
        
        .berita-detail-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .berita-detail-category {
            display: inline-block;
            background: #003399;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .berita-detail-title {
            font-size: 2.2rem;
            color: #003399;
            margin-bottom: 15px;
            line-height: 1.3;
        }
        
        .berita-detail-meta {
            display: flex;
            justify-content: center;
            gap: 20px;
            color: #666;
            font-size: 0.9rem;
            flex-wrap: wrap;
        }
        
        .berita-detail-image {
            margin: 30px 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .berita-detail-image img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .berita-detail-content {
            line-height: 1.8;
            font-size: 1.1rem;
            color: #333;
        }
        
        .berita-detail-content p {
            margin-bottom: 20px;
        }
        
        .berita-detail-content h2,
        .berita-detail-content h3 {
            color: #003399;
            margin: 30px 0 15px;
        }
        
        .berita-detail-content ul,
        .berita-detail-content ol {
            margin-bottom: 20px;
            padding-left: 20px;
        }
        
        .berita-detail-content li {
            margin-bottom: 8px;
        }
        
        .back-to-berita {
            text-align: center;
            margin-top: 40px;
        }
        
        @media (max-width: 768px) {
            .berita-detail {
                margin: 80px auto 30px;
            }
            
            .berita-detail-title {
                font-size: 1.8rem;
            }
            
            .berita-detail-meta {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header & Navigation -->
    <!-- ... (sama seperti di berita.php) ... -->

    <!-- Breadcrumb -->
    <section class="breadcrumb-section">
        <div class="container">
            <br>
            <nav class="breadcrumb-nav" style="width: 100%; background-color: #FFF !important;">
                <a href="index.html">Halaman Utama</a>
                <span>/</span>
                <a href="berita.php">Berita</a>
                <span>/</span>
                <span class="active"><?php echo htmlspecialchars($berita['judul']); ?></span>
            </nav>
        </div>
    </section>

    <!-- Berita Detail -->
    <section class="berita-detail">
        <article>
            <header class="berita-detail-header">
                <span class="berita-detail-category"><?php echo htmlspecialchars($berita['kategori']); ?></span>
                <h1 class="berita-detail-title"><?php echo htmlspecialchars($berita['judul']); ?></h1>
                <div class="berita-detail-meta">
                    <span>📅 <?php echo formatTanggalIndonesia($berita['tanggal_publish']); ?></span>
                    <span>👤 Oleh: <?php echo htmlspecialchars($berita['penulis']); ?></span>
                    <span>👁️ <?php echo number_format($berita['dibaca']); ?>x dibaca</span>
                </div>
            </header>

            <?php if (!empty($berita['gambar'])): ?>
            <div class="berita-detail-image">
                <img src="<?php echo htmlspecialchars($berita['gambar']); ?>" alt="<?php echo htmlspecialchars($berita['judul']); ?>">
            </div>
            <?php endif; ?>

            <div class="berita-detail-content">
                <?php echo $berita['konten']; ?>
            </div>
        </article>

        <div class="back-to-berita">
            <a href="berita.php" class="btn">← Kembali ke Daftar Berita</a>
        </div>
    </section>

    <!-- Footer -->
    <!-- ... (sama seperti di berita.php) ... -->
</body>
</html>