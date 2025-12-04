<?php
session_start();
require_once 'functions.php';

// Cek jika admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Konfigurasi file yang dapat diedit
$editable_files = [
    'index' => [
        'name' => 'Halaman Utama (index.html)',
        'path' => 'index.html',
        'sections' => [
            'hero' => 'Hero Section (Slider)',
            'pimpinan' => 'Pimpinan Daerah',
            'visi-misi' => 'Visi & Misi',
            'berita' => 'Berita Cepat',
            'layanan-section' => 'Layanan'
        ]
    ],
    'profil' => [
        'name' => 'Halaman Profil (profil.php)',
        'path' => 'profil.php',
        'sections' => [
            'hero' => 'Hero Profil',
            'visi-misi' => 'Visi & Misi',
            'tupoksi' => 'Tugas Pokok & Fungsi',
            'kontak' => 'Kontak Profil'
        ]
    ],  
    'layanan' => [
        'name' => 'Halaman Layanan (layanan.html)',
        'path' => 'layanan.html',
        'sections' => [
            'layanan-hero' => 'Hero Layanan',
            'layanan' => 'Daftar Layanan',
            'faq' => 'FAQ Layanan',
            'contact' => 'Form Kontak'
        ]
    ],
    'faq' => [
        'name' => 'Halaman FAQ (faq.html)',
        'path' => 'faq.html',
        'sections' => [
            'faq-content' => 'Konten FAQ'
        ]
    ]
];

// Handle form submission untuk edit konten
$pesan_sukses = '';
$pesan_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_content'])) {
        $file_type = $_POST['file_type'];
        $section_id = $_POST['section_id'];
        
        if (isset($editable_files[$file_type])) {
            $file_path = $editable_files[$file_type]['path'];
            
            // Baca isi file
            $file_content = file_get_contents($file_path);
            $new_content = $file_content;
            $success = false;
            
            if ($file_type === 'index') {
                if ($section_id === 'hero') {
                    // Update teks hero - HANYA di Hero Section
                    $hero_text = $_POST['hero_text'] ?? '';
                    if (!empty($hero_text)) {
                        // Pattern yang lebih spesifik: cari <h2> yang ada di dalam .hero-content
                        $pattern = '/<div class="hero-content">\s*<h2>([^<]*)<\/h2>/s';
                        if (preg_match($pattern, $new_content)) {
                            $new_content = preg_replace($pattern, "<div class=\"hero-content\">\n                <h2>$hero_text</h2>", $new_content);
                        } else {
                            // Fallback: cari <h2> yang ada setelah .hero-slider
                            $pattern = '/<div class="hero-slider">.*?<\/div>\s*<div class="hero-content">\s*<h2>([^<]*)<\/h2>/s';
                            if (preg_match($pattern, $new_content)) {
                                $new_content = preg_replace($pattern, "<div class=\"hero-slider\">\n        <div class=\"slide active\" style=\"background-image: url('" . ($current_content['hero_image_1'] ?? '') . "')\">\n            <div class=\"slide-overlay\"></div>\n        </div>\n        <div class=\"slide\" style=\"background-image: url('" . ($current_content['hero_image_2'] ?? '') . "')\">\n            <div class=\"slide-overlay\"></div>\n        </div>\n        <div class=\"slide\" style=\"background-image: url('" . ($current_content['hero_image_3'] ?? '') . "')\">\n            <div class=\"slide-overlay\"></div>\n        </div>\n        <div class=\"slide\" style=\"background-image: url('" . ($current_content['hero_image_4'] ?? '') . "')\">\n            <div class=\"slide-overlay\"></div>\n        </div>\n    </div>\n    <div class=\"hero-content\">\n        <h2>$hero_text</h2>", $new_content);
                            }
                        }
                    }
                    
                    // Update subtext hero - HANYA di Hero Section
                    $hero_subtext = $_POST['hero_subtext'] ?? '';
                    if (!empty($hero_subtext)) {
                        // Pattern yang spesifik: cari <p> yang langsung setelah <h2> di .hero-content
                        $pattern = '/<div class="hero-content">\s*<h2>[^<]*<\/h2>\s*<p>([^<]*)<\/p>/s';
                        if (preg_match($pattern, $new_content)) {
                            $new_content = preg_replace($pattern, "<div class=\"hero-content\">\n                <h2>" . ($hero_text ?: ($current_content['hero_text'] ?? '')) . "</h2>\n                <p>$hero_subtext</p>", $new_content);
                        } else {
                            // Fallback pattern
                            $pattern = '/<p>([^<]*)<\/p>\s*<a href="#layanan-section"/s';
                            if (preg_match($pattern, $new_content)) {
                                $new_content = preg_replace($pattern, "<p>$hero_subtext</p>\n        <a href=\"#layanan-section\"", $new_content);
                            }
                        }
                    }
                    
                    // Update gambar slider
                    for ($i = 1; $i <= 4; $i++) {
                        $image_field = "hero_image_$i";
                        if (!empty($_POST[$image_field])) {
                            $image_url = $_POST[$image_field];
                            // Cari semua slide
                            $pattern = '/<div class="slide(.*?)style="background-image: url\(\'([^\']*)\'\)">/s';
                            preg_match_all($pattern, $new_content, $slides, PREG_OFFSET_CAPTURE);
                            
                            if (isset($slides[0][$i-1])) {
                                $old_slide = $slides[0][$i-1][0];
                                $old_url = $slides[2][$i-1][0];
                                $new_slide = str_replace($old_url, $image_url, $old_slide);
                                $new_content = str_replace($old_slide, $new_slide, $new_content);
                            }
                        }
                    }
                    $success = true;
                }
                elseif ($section_id === 'pimpinan') {
                    // Update data pimpinan (3 pimpinan)
                    for ($i = 1; $i <= 3; $i++) {
                        $nama_field = "pimpinan_nama_$i";
                        $jabatan_field = "pimpinan_jabatan_$i";
                        $image_field = "pimpinan_image_$i";
                        
                        $nama = $_POST[$nama_field] ?? '';
                        $jabatan = $_POST[$jabatan_field] ?? '';
                        $image = $_POST[$image_field] ?? '';
                        
                        if (!empty($nama) || !empty($jabatan) || !empty($image)) {
                            // Cari card pimpinan ke-i
                            $pattern = '/<div class="pimpinan-card">\s*<div class="pimpinan-img portrait">(.*?)<\/div>\s*<div class="pimpinan-info">(.*?)<\/div>\s*<\/div>/s';
                            preg_match_all($pattern, $new_content, $cards, PREG_OFFSET_CAPTURE);
                            
                            if (isset($cards[0][$i-1])) {
                                $card_html = $cards[0][$i-1][0];
                                $card_pos = $cards[0][$i-1][1];
                                
                                // Update nama jika ada
                                if (!empty($nama)) {
                                    $nama_pattern = '/<h3>([^<]*)<\/h3>/';
                                    if (preg_match($nama_pattern, $card_html, $matches)) {
                                        $old_nama = $matches[0];
                                        $new_nama = "<h3>$nama</h3>";
                                        $card_html = str_replace($old_nama, $new_nama, $card_html);
                                    }
                                }
                                
                                // Update jabatan jika ada
                                if (!empty($jabatan)) {
                                    $jabatan_pattern = '/<p>([^<]*)<\/p>/';
                                    if (preg_match($jabatan_pattern, $card_html, $matches)) {
                                        $old_jabatan = $matches[0];
                                        $new_jabatan = "<p>$jabatan</p>";
                                        $card_html = str_replace($old_jabatan, $new_jabatan, $card_html);
                                    }
                                }
                                
                                // Update gambar jika ada
                                if (!empty($image)) {
                                    // Update gambar di dalam card TANPA menghilangkan onerror
                                    $img_pattern = '/<img[^>]+src="[^"]*"[^>]*>/';
                                    if (preg_match($img_pattern, $card_html, $matches)) {
                                        $old_img = $matches[0];
                                        // Buat gambar baru dengan onerror tetap ada
                                        $new_img = "<img src=\"$image\" alt=\"$nama - $jabatan\" onerror=\"this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiBmaWxsPSIjMDAzMzk5IiBmaWxsLW9wYWNpdHk9IjAuMSIvPgo8Y2lyY2xlIGN4PSI2MCIgY3k9IjQ1IiByPSIyMCIgZmlsbD0iIzAwMzM5OSIgZmlsbC1vcGFjaXR5PSIwLjMiLz4KPHBhdGggZD0iTTYwIDc1IEM0MCA3NSAzMCA4NSAzMCA5NSBDMzAgMTA1IDQwIDExNSA2MCAxMTUgQzgwIDExNSA5MCAxMDUgOTAgOTUgQzkwIDg1IDgwIDc1IDYwIDc1IFoiIGZpbGw9IiMwMDMzOTkiIGZpbGwtb3BhY2l0eT0iMC4zIi8+Cjwvc3ZnPg=='\">";
                                        $card_html = str_replace($old_img, $new_img, $card_html);
                                    }
                                }
                                
                                // Ganti card di konten
                                $new_content = substr_replace($new_content, $card_html, $card_pos, strlen($cards[0][$i-1][0]));
                            }
                        }
                    }
                    $success = true;
                }
                elseif ($section_id === 'visi-misi') {
                    // Update visi
                    $visi = $_POST['visi'] ?? '';
                    if (!empty($visi)) {
                        $pattern = '/<div class="visi">\s*<h3>Visi<\/h3>\s*<p>([^<]*)<\/p>\s*<\/div>/s';
                        if (preg_match($pattern, $new_content)) {
                            $new_content = preg_replace($pattern, "<div class=\"visi\">\n                    <h3>Visi</h3>\n                    <p>$visi</p>\n                </div>", $new_content);
                        }
                    }
                    
                    // Update misi dari format baris dengan dash
                    $misi_text = $_POST['misi'] ?? '';
                    if (!empty($misi_text)) {
                        // Pisahkan berdasarkan baris
                        $lines = explode("\n", $misi_text);
                        $misi_items = [];
                        
                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (!empty($line)) {
                                // Jika baris dimulai dengan dash, hilangkan dash-nya
                                if (substr($line, 0, 1) === '-') {
                                    $line = trim(substr($line, 1));
                                }
                                $misi_items[] = $line;
                            }
                        }
                        
                        // Generate HTML untuk misi
                        if (!empty($misi_items)) {
                            $misi_html = "<div class=\"misi\">\n                    <h3>Misi</h3>\n                    <ul>\n";
                            foreach ($misi_items as $item) {
                                $misi_html .= "                        <li>$item</li>\n";
                            }
                            $misi_html .= "                    </ul>\n                </div>";
                            
                            // Ganti section misi
                            $pattern = '/<div class="misi">\s*<h3>Misi<\/h3>\s*<ul>(.*?)<\/ul>\s*<\/div>/s';
                            if (preg_match($pattern, $new_content)) {
                                $new_content = preg_replace($pattern, $misi_html, $new_content);
                            }
                        }
                    }
                    $success = true;
                }
                elseif ($section_id === 'layanan-section') {
                    // Update layanan cards
                    for ($i = 1; $i <= 4; $i++) {
                        $title_field = "layanan_title_$i";
                        $desc_field = "layanan_desc_$i";
                        $image_field = "layanan_image_$i";
                        
                        $title = $_POST[$title_field] ?? '';
                        $desc = $_POST[$desc_field] ?? '';
                        $image = $_POST[$image_field] ?? '';
                        
                        if (!empty($title) || !empty($desc) || !empty($image)) {
                            // Cari semua layanan cards
                            $pattern = '/<div class="layanan-card fade-in" onclick="openLayananPopup\(\'[^\']+\'\)">(.*?)<button class="layanan-btn">/s';
                            preg_match_all($pattern, $new_content, $cards, PREG_OFFSET_CAPTURE);
                            
                            if (isset($cards[0][$i-1])) {
                                $card_html = $cards[0][$i-1][0];
                                $card_pos = $cards[0][$i-1][1];
                                
                                // Update title
                                if (!empty($title)) {
                                    $title_pattern = '/<h3>([^<]*)<\/h3>/';
                                    if (preg_match($title_pattern, $card_html)) {
                                        $card_html = preg_replace($title_pattern, "<h3>$title</h3>", $card_html);
                                    }
                                }
                                
                                // Update description
                                if (!empty($desc)) {
                                    $desc_pattern = '/<p>([^<]*)<\/p>/';
                                    if (preg_match($desc_pattern, $card_html)) {
                                        $card_html = preg_replace($desc_pattern, "<p>$desc</p>", $card_html);
                                    }
                                }
                                
                                // Update image
                                if (!empty($image)) {
                                    $img_pattern = '/<img[^>]+>/';
                                    if (preg_match($img_pattern, $card_html)) {
                                        $new_img = "<img src=\"$image\" alt=\"$title\" class=\"icon-layanan\" style=\"width: 100px; height: 100px;\">";
                                        $card_html = preg_replace($img_pattern, $new_img, $card_html);
                                    }
                                }
                                
                                // Ganti card
                                $new_content = substr_replace($new_content, $card_html, $card_pos, strlen($cards[0][$i-1][0]));
                            }
                        }
                    }
                    $success = true;
                }
            }
            elseif ($file_type === 'profil') {
                if ($section_id === 'visi-misi') {
                    // Update visi profil
                    $visi = $_POST['visi'] ?? '';
                    if (!empty($visi)) {
                        $pattern = '/<div class="visi-card fade-in">\s*<h3>Visi<\/h3>\s*<p>([^<]*)<\/p>\s*<\/div>/s';
                        if (preg_match($pattern, $new_content)) {
                            $new_content = preg_replace($pattern, "<div class=\"visi-card fade-in\">\n                    <h3>Visi</h3>\n                    <p>$visi</p>\n                </div>", $new_content);
                        }
                    }
                    
                    // Update misi profil dari format baris dengan dash
                    $misi_text = $_POST['misi'] ?? '';
                    if (!empty($misi_text)) {
                        // Pisahkan berdasarkan baris
                        $lines = explode("\n", $misi_text);
                        $misi_items = [];
                        
                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (!empty($line)) {
                                // Jika baris dimulai dengan dash, hilangkan dash-nya
                                if (substr($line, 0, 1) === '-') {
                                    $line = trim(substr($line, 1));
                                }
                                $misi_items[] = $line;
                            }
                        }
                        
                        // Generate HTML untuk misi profil
                        if (!empty($misi_items)) {
                            $misi_html = "<div class=\"misi-card fade-in\">\n                    <h3><center>Misi</center></h3>\n                    <ul>\n";
                            foreach ($misi_items as $item) {
                                $misi_html .= "                        <li>$item</li>\n";
                            }
                            $misi_html .= "                    </ul>\n                </div>";
                            
                            // Ganti section misi
                            $pattern = '/<div class="misi-card fade-in">\s*<h3><center>Misi<\/center><\/h3>\s*<ul>(.*?)<\/ul>\s*<\/div>/s';
                            if (preg_match($pattern, $new_content)) {
                                $new_content = preg_replace($pattern, $misi_html, $new_content);
                            }
                        }
                    }
                    $success = true;
                }
            }
            
            // Simpan perubahan ke file
            if ($success && file_put_contents($file_path, $new_content)) {
                $pesan_sukses = 'Konten berhasil diperbarui!';
                // Refresh untuk melihat perubahan
                header("Location: admin-pengaturan.php?file=$file_type&section=$section_id&success=1");
                exit;
            } else if (!$success) {
                $pesan_error = 'Tidak ada perubahan yang dilakukan.';
            } else {
                $pesan_error = 'Gagal menyimpan perubahan. Periksa permission file.';
            }
        }
    }
}

// Handle upload gambar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload_image'])) {
    $target_dir = "assets/uploads/";
    
    // Buat direktori jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $filename = uniqid() . '_' . basename($_FILES["upload_image"]["name"]);
    $target_file = $target_dir . $filename;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Validasi file gambar
    $check = getimagesize($_FILES["upload_image"]["tmp_name"]);
    if ($check !== false) {
        // Izinkan hanya format tertentu
        if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
            if (move_uploaded_file($_FILES["upload_image"]["tmp_name"], $target_file)) {
                $pesan_sukses = "Gambar berhasil diupload! URL: " . $target_file;
                $uploaded_image_url = $target_file;
            } else {
                $pesan_error = "Maaf, terjadi kesalahan saat mengupload gambar.";
            }
        } else {
            $pesan_error = "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
        }
    } else {
        $pesan_error = "File yang diupload bukan gambar.";
    }
}

// Ambil konten yang akan diedit
$current_file = $_GET['file'] ?? 'index';
$current_section = $_GET['section'] ?? 'hero';

if (!isset($editable_files[$current_file])) {
    $current_file = 'index';
}

if (!isset($editable_files[$current_file]['sections'][$current_section])) {
    $current_section = array_key_first($editable_files[$current_file]['sections']);
}

// Ambil konten saat ini dari file
$current_content = [];

if (file_exists($editable_files[$current_file]['path'])) {
    $file_content = file_get_contents($editable_files[$current_file]['path']);
    
    // Ekstrak konten berdasarkan file dan section
    if ($current_file === 'index') {
        if ($current_section === 'hero') {
            // Ambil teks hero - HANYA dari Hero Section
            // Pattern spesifik: cari <h2> yang ada di dalam .hero-content
            $pattern = '/<div class="hero-content">\s*<h2>([^<]+)<\/h2>/s';
            preg_match($pattern, $file_content, $matches);
            $current_content['hero_text'] = $matches[1] ?? '';
            
            // Ambil subtext hero - HANYA dari Hero Section
            // Pattern spesifik: cari <p> yang langsung setelah <h2> di .hero-content
            $pattern = '/<div class="hero-content">\s*<h2>[^<]*<\/h2>\s*<p>([^<]+)<\/p>/s';
            preg_match($pattern, $file_content, $matches);
            $current_content['hero_subtext'] = $matches[1] ?? '';
            
            // Fallback jika pattern pertama tidak ketemu
            if (empty($current_content['hero_subtext'])) {
                $pattern = '/<p>([^<]+)<\/p>\s*<a href="#layanan-section"/s';
                preg_match($pattern, $file_content, $matches);
                $current_content['hero_subtext'] = $matches[1] ?? '';
            }
            
            // Ambil semua gambar slider (4 gambar)
            preg_match_all('/<div class="slide.*?style="background-image: url\(\'([^\']+)\'\)">/', $file_content, $matches);
            for ($i = 0; $i < 4; $i++) {
                $current_content["hero_image_" . ($i+1)] = $matches[1][$i] ?? '';
            }
        }
        elseif ($current_section === 'pimpinan') {
            // Ambil data pimpinan (3 pimpinan)
            $pattern = '/<div class="pimpinan-card">\s*<div class="pimpinan-img portrait">.*?<img[^>]+src="([^"]+)"[^>]*>.*?<\/div>\s*<div class="pimpinan-info">\s*<h3>([^<]+)<\/h3>\s*<p>([^<]+)<\/p>/s';
            preg_match_all($pattern, $file_content, $matches);

            for ($i = 0; $i < 3; $i++) {
                if (isset($matches[2][$i])) {
                    $current_content["pimpinan_nama_" . ($i+1)] = $matches[2][$i];
                    $current_content["pimpinan_jabatan_" . ($i+1)] = $matches[3][$i];
                    $current_content["pimpinan_image_" . ($i+1)] = $matches[1][$i];
                }
            }
        }
        elseif ($current_section === 'visi-misi') {
            // Ambil visi
            preg_match('/<div class="visi">\s*<h3>Visi<\/h3>\s*<p>([^<]+)<\/p>\s*<\/div>/s', $file_content, $matches);
            $current_content['visi'] = $matches[1] ?? '';
            
            // Ambil semua misi items dan format ke dalam satu textarea dengan dash
            preg_match_all('/<li>([^<]+)<\/li>/', $file_content, $matches);
            $misi_items = [];
            foreach ($matches[1] as $item) {
                if (!empty(trim($item))) {
                    $misi_items[] = '- ' . trim($item);
                }
            }
            $current_content['misi'] = implode("\n", $misi_items);
        }
        elseif ($current_section === 'layanan-section') {
            // Ambil data layanan cards
            preg_match_all('/<div class="layanan-card fade-in"[^>]*>\s*<img[^>]*>\s*<div class="layanan-card-content">\s*<h3>([^<]+)<\/h3>\s*<p>([^<]+)<\/p>/s', $file_content, $matches);
            
            for ($i = 0; $i < 4; $i++) {
                if (isset($matches[1][$i])) {
                    $current_content["layanan_title_" . ($i+1)] = $matches[1][$i];
                    $current_content["layanan_desc_" . ($i+1)] = $matches[2][$i];
                }
            }
            
            // Ambil gambar layanan
            preg_match_all('/<img src="([^"]+)" alt="[^"]+" class="icon-layanan" style="width: 100px; height: 100px;">/', $file_content, $img_matches);
            for ($i = 0; $i < 4; $i++) {
                $current_content["layanan_image_" . ($i+1)] = $img_matches[1][$i] ?? '';
            }
        }
    }
    elseif ($current_file === 'profil' && $current_section === 'visi-misi') {
        // Ambil visi profil
        preg_match('/<div class="visi-card fade-in">\s*<h3>Visi<\/h3>\s*<p>([^<]+)<\/p>\s*<\/div>/s', $file_content, $matches);
        $current_content['visi'] = $matches[1] ?? '';
        
        // Ambil semua misi items profil dan format ke dalam satu textarea dengan dash
        preg_match_all('/<li>([^<]+)<\/li>/', $file_content, $matches);
        $misi_items = [];
        foreach ($matches[1] as $item) {
            if (!empty(trim($item))) {
                $misi_items[] = '- ' . trim($item);
            }
        }
        $current_content['misi'] = implode("\n", $misi_items);
    }
}

// Check for success message
if (isset($_GET['success'])) {
    $pesan_sukses = 'Konten berhasil diperbarui!';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Konten - Dinas Pendidikan dan Kebudayaan Kabupaten Paser</title>
    <link rel="stylesheet" href="css/admin-styles.css">
    <style>
        /* Custom Styles untuk Halaman Pengaturan */
        .settings-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .settings-sidebar {
            width: 250px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            height: fit-content;
        }

        .settings-content {
            flex: 1;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }

        .file-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .file-list li {
            margin-bottom: 5px;
        }

        .file-list a {
            display: block;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .file-list a:hover {
            background: #f8f9fa;
            color: #003399;
        }

        .file-list a.active {
            background: #003399;
            color: white;
        }

        .section-list {
            margin-top: 15px;
            padding-left: 15px;
            border-left: 2px solid #eee;
        }

        .section-list a {
            padding: 8px 12px;
            font-size: 14px;
        }

        .editor-form {
            margin-top: 20px;
        }

        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .form-section h4 {
            color: #003399;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e36159;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
            font-family: monospace;
            line-height: 1.5;
        }

        .image-preview-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .image-preview {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            text-align: center;
        }

        .image-preview img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .pimpinan-preview img {
            height: 250px;
            object-fit: cover;
        }

        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-area:hover {
            border-color: #003399;
            background: #f8f9fa;
        }

        .upload-area input[type="file"] {
            display: none;
        }

        .upload-icon {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 10px;
        }

        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .preview-item {
            position: relative;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid #ddd;
        }

        .preview-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }

        .preview-item .select-btn {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 51, 153, 0.9);
            color: white;
            padding: 5px;
            text-align: center;
            font-size: 12px;
            cursor: pointer;
            display: none;
        }

        .preview-item:hover .select-btn {
            display: block;
        }

        .current-content-preview {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .section-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #003399;
        }

        .section-info h4 {
            color: #003399;
            margin: 0 0 10px 0;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .image-input-group {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }

        .image-input-group .form-group {
            flex: 1;
        }

        .select-image-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .select-image-btn:hover {
            background: #5a6268;
        }

        .format-hint {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 12px;
            border-radius: 4px;
            margin: 10px 0;
            font-size: 14px;
        }

        .format-hint h5 {
            color: #003399;
            margin-top: 0;
            margin-bottom: 8px;
        }

        .format-hint ul {
            margin: 0;
            padding-left: 20px;
        }

        .format-hint code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
            color: #d63384;
        }

        .pimpinan-card-editor {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #f9f9f9;
        }

        .pimpinan-card-editor h5 {
            color: #003399;
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        @media (max-width: 768px) {
            .settings-container {
                flex-direction: column;
            }
            
            .settings-sidebar {
                width: 100%;
            }
            
            .image-input-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar default-layout">
        <div class="navbar-brand-wrapper">
            <a class="navbar-brand" href="#">
                <span class="brand-logo">Menu Admin</span>
                <span class="brand-logo-mini">MA</span>
            </a>
        </div>
        <div class="navbar-menu-wrapper">
            <!-- Logo dan Nama Instansi -->
            <div class="navbar-brand">
                <img src="assets/logo-kabupaten.png" alt="Logo">
                <span class="brand-text">Dinas Pendidikan dan Kebudayaan Kabupaten Paser</span>
            </div>
            
            <ul class="navbar-nav navbar-nav-right">
                <li class="nav-item">
                    <a class="nav-link" href="admin-dashboard.php">
                        <span class="icon icon-dashboard"></span> Dasbor
                    </a>
                </li>
                <li class="nav-item user-dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button">
                        <span class="icon icon-user"></span>
                        <?php echo $_SESSION['admin_username']; ?>
                    </a>
                    <div class="dropdown-menu" id="userDropdownMenu">
                        <div class="dropdown-header">
                            <h6><?php echo $_SESSION['admin_username']; ?></h6>
                            <span class="text-muted">Administrator</span>
                        </div>
                        <a class="dropdown-item" href="#">
                            <span class="icon icon-user"></span> Profil
                        </a>
                        <a class="dropdown-item" href="#">
                            <span class="icon icon-settings"></span> Pengaturan
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="?logout=true">
                            <span class="icon icon-logout"></span> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="admin-dashboard.php" class="sidebar-menu-link">
                        <span class="icon icon-dashboard"></span>
                        <span class="sidebar-menu-text">Dasbor</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin-berita.php" class="sidebar-menu-link">
                        <span class="icon icon-news"></span>
                        <span class="sidebar-menu-text">Berita</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin-pengaturan.php" class="sidebar-menu-link active">
                        <span class="icon icon-settings"></span>
                        <span class="sidebar-menu-text">Pengaturan Konten</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link">
                        <span class="icon icon-users"></span>
                        <span class="sidebar-menu-text">Pengguna</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="content-wrapper">
            <!-- Page Header -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3>Pengaturan Konten Website</h3>
                    <p class="text-muted mb-0">Edit konten halaman website secara dinamis</p>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if ($pesan_sukses): ?>
                <div class="alert alert-success"><?php echo $pesan_sukses; ?></div>
            <?php endif; ?>

            <?php if ($pesan_error): ?>
                <div class="alert alert-error"><?php echo $pesan_error; ?></div>
            <?php endif; ?>

            <!-- Settings Container -->
            <div class="settings-container">
                <!-- Sidebar Navigasi -->
                <div class="settings-sidebar">
                    <h4>Halaman Website</h4>
                    <ul class="file-list">
                        <?php foreach ($editable_files as $file_key => $file_info): ?>
                            <li>
                                <a href="?file=<?php echo $file_key; ?>&section=<?php echo array_key_first($file_info['sections']); ?>"
                                   class="<?php echo $current_file == $file_key ? 'active' : ''; ?>">
                                    <?php echo $file_info['name']; ?>
                                </a>
                                
                                <?php if ($current_file == $file_key): ?>
                                    <ul class="section-list">
                                        <?php foreach ($file_info['sections'] as $section_key => $section_name): ?>
                                            <li>
                                                <a href="?file=<?php echo $file_key; ?>&section=<?php echo $section_key; ?>"
                                                   class="<?php echo $current_section == $section_key ? 'active' : ''; ?>">
                                                    <?php echo $section_name; ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Konten Editor -->
                <div class="settings-content">
                    <!-- Section Info -->
                    <div class="section-info">
                        <h4>Edit: <?php echo $editable_files[$current_file]['sections'][$current_section]; ?></h4>
                        <p>File: <?php echo $editable_files[$current_file]['name']; ?></p>
                    </div>

                    <form method="POST" class="editor-form">
                        <input type="hidden" name="file_type" value="<?php echo $current_file; ?>">
                        <input type="hidden" name="section_id" value="<?php echo $current_section; ?>">
                        
                        <?php if ($current_file === 'index'): ?>
                            
                            <?php if ($current_section === 'hero'): ?>
                                <!-- Hero Section Editor -->
                                <div class="form-section">
                                    <h4>Judul Hero</h4>
                                    <div class="form-group">
                                        <label for="hero_text">Judul Utama:</label>
                                        <input type="text" id="hero_text" name="hero_text" class="form-control" 
                                               value="<?php echo htmlspecialchars($current_content['hero_text'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="hero_subtext">Subjudul:</label>
                                        <textarea id="hero_subtext" name="hero_subtext" class="form-control" rows="3"><?php echo htmlspecialchars($current_content['hero_subtext'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="form-section">
                                    <h4>Gambar Slider Hero (4 Gambar)</h4>
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                        <div class="form-group">
                                            <label for="hero_image_<?php echo $i; ?>">Gambar Slider <?php echo $i; ?>:</label>
                                            <div class="image-input-group">
                                                <input type="text" id="hero_image_<?php echo $i; ?>" 
                                                       name="hero_image_<?php echo $i; ?>" class="form-control" 
                                                       value="<?php echo htmlspecialchars($current_content['hero_image_' . $i] ?? ''); ?>"
                                                       placeholder="URL gambar...">
                                                <button type="button" class="select-image-btn" 
                                                        onclick="openImageSelector('hero_image_<?php echo $i; ?>')">
                                                    Pilih Gambar
                                                </button>
                                            </div>
                                            <?php if (!empty($current_content['hero_image_' . $i])): ?>
                                                <div class="image-preview">
                                                    <img src="<?php echo htmlspecialchars($current_content['hero_image_' . $i]); ?>" 
                                                         alt="Slider <?php echo $i; ?>" 
                                                         onerror="this.style.display='none'; this.parentElement.innerHTML='<p style=\"color:#dc3545;\">Gambar tidak ditemukan</p>';">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                
                            <?php elseif ($current_section === 'pimpinan'): ?>
                                <!-- Pimpinan Daerah Editor -->
                                <div class="form-section">
                                    <h4>Pimpinan Daerah (3 Orang)</h4>
                                    <div class="format-hint">
                                        <h5>Tips:</h5>
                                        <ul>
                                            <li>Isi nama dan jabatan untuk setiap pimpinan</li>
                                            <li>Gambar harus berupa foto portrait (tegak)</li>
                                            <li>Ukuran gambar optimal: 300x400px</li>
                                            <li>Format gambar: JPG, PNG</li>
                                        </ul>
                                    </div>
                                    
                                    <?php for ($i = 1; $i <= 3; $i++): ?>
                                        <div class="pimpinan-card-editor">
                                            <h5>Pimpinan <?php echo $i; ?></h5>
                                            
                                            <div class="form-group">
                                                <label for="pimpinan_nama_<?php echo $i; ?>">Nama Lengkap:</label>
                                                <input type="text" id="pimpinan_nama_<?php echo $i; ?>" 
                                                       name="pimpinan_nama_<?php echo $i; ?>" class="form-control" 
                                                       value="<?php echo htmlspecialchars($current_content['pimpinan_nama_' . $i] ?? ''); ?>"
                                                       placeholder="Contoh: Dr. Fahmi Fadli">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="pimpinan_jabatan_<?php echo $i; ?>">Jabatan:</label>
                                                <input type="text" id="pimpinan_jabatan_<?php echo $i; ?>" 
                                                       name="pimpinan_jabatan_<?php echo $i; ?>" class="form-control" 
                                                       value="<?php echo htmlspecialchars($current_content['pimpinan_jabatan_' . $i] ?? ''); ?>"
                                                       placeholder="Contoh: Bupati Paser">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="pimpinan_image_<?php echo $i; ?>">Foto Pimpinan:</label>
                                                <div class="image-input-group">
                                                    <input type="text" id="pimpinan_image_<?php echo $i; ?>" 
                                                           name="pimpinan_image_<?php echo $i; ?>" class="form-control" 
                                                           value="<?php echo htmlspecialchars($current_content['pimpinan_image_' . $i] ?? ''); ?>"
                                                           placeholder="URL gambar...">
                                                    <button type="button" class="select-image-btn" 
                                                            onclick="openImageSelector('pimpinan_image_<?php echo $i; ?>')">
                                                        Pilih Gambar
                                                    </button>
                                                </div>
                                                <?php if (!empty($current_content['pimpinan_image_' . $i])): ?>
                                                    <div class="image-preview pimpinan-preview">
                                                        <p><strong>Preview Foto:</strong></p>
                                                        <img src="<?php echo htmlspecialchars($current_content['pimpinan_image_' . $i]); ?>" 
                                                             alt="Pimpinan <?php echo $i; ?>" 
                                                             onerror="this.style.display='none'; this.parentElement.innerHTML='<p style=\'color:#dc3545;\'>Gambar tidak ditemukan</p>';">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                
                            <?php elseif ($current_section === 'visi-misi'): ?>
                                <!-- Visi Misi Editor -->
                                <div class="form-section">
                                    <h4>Visi</h4>
                                    <div class="form-group">
                                        <label for="visi">Teks Visi:</label>
                                        <textarea id="visi" name="visi" class="form-control" rows="4"><?php echo htmlspecialchars($current_content['visi'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="form-section">
                                    <h4>Misi</h4>
                                    <div class="format-hint">
                                        <h5>Format Penulisan Misi:</h5>
                                        <ul>
                                            <li>Gunakan tanda dash (<code>-</code>) di awal setiap poin misi</li>
                                            <li>Setiap baris akan menjadi poin misi terpisah</li>
                                            <li>Contoh format:
                                                <pre style="background:#f5f5f5;padding:10px;border-radius:4px;margin-top:5px;">
- Misi pertama disini
- Misi kedua disini  
- Misi ketiga disini
- Tambahkan poin baru dengan dash di baris baru</pre>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="misi">Teks Misi (format dengan dash):</label>
                                        <textarea id="misi" name="misi" class="form-control" rows="10" 
                                                  placeholder="- Misi pertama&#10;- Misi kedua&#10;- Misi ketiga&#10;- Misi keempat&#10;- Misi kelima"><?php echo htmlspecialchars($current_content['misi'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                
                            <?php elseif ($current_section === 'layanan-section'): ?>
                                <!-- Layanan Section Editor -->
                                <div class="form-section">
                                    <h4>Kartu Layanan (4 Kartu)</h4>
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                        <div class="form-section" style="border: 1px solid #eee; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                                            <h5>Layanan <?php echo $i; ?></h5>
                                            <div class="form-group">
                                                <label for="layanan_title_<?php echo $i; ?>">Judul:</label>
                                                <input type="text" id="layanan_title_<?php echo $i; ?>" 
                                                       name="layanan_title_<?php echo $i; ?>" class="form-control" 
                                                       value="<?php echo htmlspecialchars($current_content['layanan_title_' . $i] ?? ''); ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="layanan_desc_<?php echo $i; ?>">Deskripsi:</label>
                                                <textarea id="layanan_desc_<?php echo $i; ?>" 
                                                          name="layanan_desc_<?php echo $i; ?>" class="form-control" rows="3"><?php echo htmlspecialchars($current_content['layanan_desc_' . $i] ?? ''); ?></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="layanan_image_<?php echo $i; ?>">Gambar:</label>
                                                <div class="image-input-group">
                                                    <input type="text" id="layanan_image_<?php echo $i; ?>" 
                                                           name="layanan_image_<?php echo $i; ?>" class="form-control" 
                                                           value="<?php echo htmlspecialchars($current_content['layanan_image_' . $i] ?? ''); ?>"
                                                           placeholder="URL gambar...">
                                                    <button type="button" class="select-image-btn" 
                                                            onclick="openImageSelector('layanan_image_<?php echo $i; ?>')">
                                                        Pilih Gambar
                                                    </button>
                                                </div>
                                                <?php if (!empty($current_content['layanan_image_' . $i])): ?>
                                                    <div class="image-preview">
                                                        <img src="<?php echo htmlspecialchars($current_content['layanan_image_' . $i]); ?>" 
                                                             alt="Layanan <?php echo $i; ?>" 
                                                             onerror="this.style.display='none'; this.parentElement.innerHTML='<p style=\"color:#dc3545;\">Gambar tidak ditemukan</p>';">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                
                            <?php endif; ?>
                            
                        <?php elseif ($current_file === 'profil' && $current_section === 'visi-misi'): ?>
                        <!-- Visi Misi Profil Editor -->
                        <div class="form-section">
                            <h4>Visi Profil</h4>
                            <div class="form-group">
                                <label for="visi">Teks Visi:</label>
                                <textarea id="visi" name="visi" class="form-control" rows="4"><?php echo htmlspecialchars($current_content['visi'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h4>Misi Profil</h4>
                            <div class="format-hint">
                                <h5>Format Penulisan Misi:</h5>
                                <ul>
                                    <li>Gunakan tanda dash (<code>-</code>) di awal setiap poin misi</li>
                                    <li>Setiap baris akan menjadi poin misi terpisah</li>
                                    <li>Contoh format:
                                        <pre style="background:#f5f5f5;padding:10px;border-radius:4px;margin-top:5px;">
                    - Misi pertama disini
                    - Misi kedua disini  
                    - Misi ketiga disini
                    - Tambahkan poin baru dengan dash di baris baru</pre>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="form-group">
                                <label for="misi">Teks Misi (format dengan dash):</label>
                                <textarea id="misi" name="misi" class="form-control" rows="10" 
                                        placeholder="- Misi pertama&#10;- Misi kedua&#10;- Misi ketiga&#10;- Misi keempat&#10;- Misi kelima"><?php echo htmlspecialchars($current_content['misi'] ?? ''); ?></textarea>
                            </div>
                        </div>
                            
                        <?php else: ?>
                            <!-- Editor default untuk section lain -->
                            <div class="form-section">
                                <h4>Edit Konten</h4>
                                <div class="form-group">
                                    <label for="content">Konten:</label>
                                    <textarea id="content" name="content" class="form-control" rows="10"><?php echo htmlspecialchars($current_content['content'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-actions">
                            <button type="reset" class="btn btn-outline-primary">Reset</button>
                            <button type="submit" name="save_content" class="btn-success">
                                <span class="icon icon-success"></span> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                    
                    <!-- Modal untuk memilih gambar -->
                    <div id="imageSelectorModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
                        <div style="background: white; padding: 20px; border-radius: 8px; width: 90%; max-width: 800px; max-height: 80vh; overflow-y: auto;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <h3>Pilih Gambar</h3>
                                <button onclick="closeImageSelector()" style="background: none; border: none; font-size: 24px; cursor: pointer;">×</button>
                            </div>
                            
                            <!-- Upload Area -->
                            <div class="upload-area" onclick="document.getElementById('imageUploadModal').click()">
                                <div class="upload-icon">📁</div>
                                <h4>Upload Gambar Baru</h4>
                                <p>Klik atau seret file gambar ke sini</p>
                                <p><small>Format: JPG, PNG, GIF. Maksimal: 5MB</small></p>
                            </div>
                            
                            <form method="POST" enctype="multipart/form-data" id="uploadFormModal">
                                <input type="file" id="imageUploadModal" name="upload_image" onchange="uploadImageModal()" style="display: none;">
                            </form>
                            
                            <!-- Daftar Gambar -->
                            <div id="imageList">
                                <?php
                                $upload_dir = 'assets/uploads/';
                                if (file_exists($upload_dir)) {
                                    $images = glob($upload_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
                                    if (!empty($images)) {
                                        echo '<div class="preview-grid">';
                                        foreach ($images as $image) {
                                            echo '<div class="preview-item">';
                                            echo '<img src="' . $image . '" alt="Gambar">';
                                            echo '<div class="select-btn" onclick="selectImageForField(\'' . $image . '\')">Pilih</div>';
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                    } else {
                                        echo '<p style="text-align: center; color: #666;">Belum ada gambar yang diupload.</p>';
                                    }
                                }
                                ?>
                            </div>
                            
                            <div style="margin-top: 20px; text-align: center;">
                                <button onclick="closeImageSelector()" class="btn btn-outline-primary">Batal</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let currentImageField = '';
        
        // Fungsi untuk membuka modal pemilih gambar
        function openImageSelector(fieldId) {
            currentImageField = fieldId;
            document.getElementById('imageSelectorModal').style.display = 'flex';
        }
        
        // Fungsi untuk menutup modal
        function closeImageSelector() {
            document.getElementById('imageSelectorModal').style.display = 'none';
            currentImageField = '';
        }
        
        // Fungsi untuk memilih gambar untuk field tertentu
        function selectImageForField(imageUrl) {
            if (currentImageField) {
                document.getElementById(currentImageField).value = imageUrl;
                closeImageSelector();
                alert('Gambar telah dipilih. Jangan lupa klik "Simpan Perubahan" untuk menerapkan.');
            }
        }
        
        // Fungsi untuk upload gambar dari modal
        function uploadImageModal() {
            const form = document.getElementById('uploadFormModal');
            const formData = new FormData(form);
            
            fetch('admin-pengaturan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Reload halaman untuk menampilkan gambar baru
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal mengupload gambar');
            });
        }
        
        // Drag and drop untuk upload gambar
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.querySelector('.upload-area');
            
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.style.borderColor = '#003399';
                this.style.backgroundColor = '#e3f2fd';
            });
            
            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.style.borderColor = '#ddd';
                this.style.backgroundColor = '';
            });
            
            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.style.borderColor = '#ddd';
                this.style.backgroundColor = '';
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const input = document.getElementById('imageUploadModal');
                    input.files = files;
                    uploadImageModal();
                }
            });
        });
        
        // Dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const userDropdown = document.getElementById('userDropdown');
            const userDropdownMenu = document.getElementById('userDropdownMenu');
            
            userDropdown.addEventListener('click', function(e) {
                e.preventDefault();
                userDropdownMenu.classList.toggle('show');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userDropdown.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                    userDropdownMenu.classList.remove('show');
                }
            });
        });
        
        // Tutup modal jika klik di luar
        document.getElementById('imageSelectorModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageSelector();
            }
        });
        
        // Validasi format misi saat submit
        const misiTextarea = document.getElementById('misi');
        if (misiTextarea) {
            document.querySelector('.editor-form').addEventListener('submit', function(e) {
                const lines = misiTextarea.value.split('\n');
                let hasValidFormat = true;
                
                for (let i = 0; i < lines.length; i++) {
                    const line = lines[i].trim();
                    if (line && line !== '' && !line.startsWith('-')) {
                        if (confirm(`Baris ${i+1} tidak dimulai dengan dash (-). Tambahkan dash otomatis?\n\n"${line}"\n\nMenjadi: "- ${line}"`)) {
                            lines[i] = '- ' + line;
                        } else {
                            hasValidFormat = false;
                            break;
                        }
                    }
                }
                
                if (hasValidFormat) {
                    misiTextarea.value = lines.join('\n');
                } else {
                    e.preventDefault();
                    alert('Format misi tidak valid. Setiap poin harus dimulai dengan dash (-).');
                    misiTextarea.focus();
                }
            });
            
            // Auto-format misi saat mengetik
            misiTextarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    // Auto-add dash pada baris baru jika baris sebelumnya ada dash
                    const lines = this.value.substr(0, this.selectionStart).split('\n');
                    const lastLine = lines[lines.length - 1].trim();
                    if (lastLine.startsWith('-')) {
                        setTimeout(() => {
                            const currentPos = this.selectionStart;
                            this.value = this.value.substr(0, currentPos) + '- ' + this.value.substr(currentPos);
                            this.selectionStart = currentPos + 2;
                            this.selectionEnd = currentPos + 2;
                        }, 0);
                    }
                }
            });
        }
    </script>
</body>
</html>