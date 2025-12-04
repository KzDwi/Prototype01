<?php
session_start();
require_once 'functions.php';
require_once 'functions_json.php'; // Pastikan ini ada

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

// Data untuk editor - menggabungkan semua halaman
$editable_sections = [
    'index' => [
        'name' => 'Halaman Utama',
        'sections' => [
            'hero' => 'Hero Section (Slider)',
            'pimpinan' => 'Pimpinan Daerah',
            'visi-misi' => 'Visi & Misi',
            'layanan-section' => 'Layanan'
        ]
    ],
    'profil' => [
        'name' => 'Halaman Profil',
        'sections' => [
            'hero' => 'Hero Profil',
            'visi-misi' => 'Visi & Misi',
            'tupoksi' => 'Tugas Pokok & Fungsi',
            'kontak' => 'Kontak Profil'
        ]
    ],
    'layanan' => [
        'name' => 'Halaman Layanan',
        'sections' => [
            'layanan-hero' => 'Hero Layanan',
            'layanan' => 'Daftar Layanan',
            'faq' => 'FAQ Layanan',
            'contact' => 'Form Kontak'
        ]
    ],
    'faq' => [
        'name' => 'Halaman FAQ',
        'sections' => [
            'faq-content' => 'Konten FAQ'
        ]
    ]
];

// Konstanta untuk layanan
$layanan_keys = [
    1 => 'legalisir-ijazah',
    2 => 'surat-mutasi',
    3 => 'tunjangan-guru',
    4 => 'izin-pendirian'
];

// Handle form submission untuk edit konten
$pesan_sukses = '';
$pesan_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_content'])) {
        $file_type = $_POST['file_type'];
        $section_id = $_POST['section_id'];
        $success = false;
        
        // === INDEX PAGE ===
        if ($file_type === 'index') {
            if ($section_id === 'hero') {
                // Update data hero
                $hero_data = [
                    'hero_text' => $_POST['hero_text'] ?? '',
                    'hero_subtext' => $_POST['hero_subtext'] ?? '',
                    'hero_images' => []
                ];
                
                // Simpan gambar hero
                for ($i = 1; $i <= 4; $i++) {
                    $image_field = "hero_image_$i";
                    if (!empty($_POST[$image_field])) {
                        $hero_data['hero_images'][] = $_POST[$image_field];
                    }
                }
                
                // Pastikan ada 4 gambar
                while (count($hero_data['hero_images']) < 4) {
                    $hero_data['hero_images'][] = '';
                }
                
                $success = saveSectionData('index', 'hero_data', $hero_data);
            }
            elseif ($section_id === 'pimpinan') {
                // Update data pimpinan
                $pimpinan_data = [];
                for ($i = 1; $i <= 3; $i++) {
                    $nama_field = "pimpinan_nama_$i";
                    $jabatan_field = "pimpinan_jabatan_$i";
                    $image_field = "pimpinan_image_$i";
                    
                    $pimpinan_data[] = [
                        'nama' => $_POST[$nama_field] ?? '',
                        'jabatan' => $_POST[$jabatan_field] ?? '',
                        'foto' => $_POST[$image_field] ?? ''
                    ];
                }
                
                $success = saveSectionData('index', 'pimpinan_data', $pimpinan_data);
            }
            elseif ($section_id === 'visi-misi') {
                // Update visi misi index
                $visi = $_POST['visi'] ?? '';
                $misi_text = $_POST['misi'] ?? '';
                
                $misi_items = [];
                if (!empty($misi_text)) {
                    $lines = explode("\n", $misi_text);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (!empty($line)) {
                            if (substr($line, 0, 1) === '-') {
                                $line = trim(substr($line, 1));
                            }
                            $misi_items[] = $line;
                        }
                    }
                }
                
                $visi_misi = [
                    'visi' => $visi,
                    'misi' => $misi_items
                ];
                
                $success = saveSectionData('index', 'visi_misi', $visi_misi);
            }
            elseif ($section_id === 'layanan-section') {
                // Update layanan index dan data popup
                $layanan_data = [];
                
                for ($i = 1; $i <= 4; $i++) {
                    $title_field = "layanan_title_$i";
                    $desc_field = "layanan_desc_$i";
                    $image_field = "layanan_image_$i";
                    
                    $layanan_data[] = [
                        'id' => $layanan_keys[$i] ?? "layanan_$i",
                        'title' => $_POST[$title_field] ?? '',
                        'desc' => $_POST[$desc_field] ?? '',
                        'icon' => $_POST[$image_field] ?? ''
                    ];
                    
                    // Simpan data popup ke file JSON terpisah
                    $popup_desc_field = "layanan_popup_desc_$i";
                    $cara_kerja_field = "layanan_cara_kerja_$i";
                    $persyaratan_field = "layanan_persyaratan_$i";
                    
                    $popup_data = [
                        'popup_desc' => $_POST[$popup_desc_field] ?? '',
                        'cara_kerja' => $_POST[$cara_kerja_field] ?? '',
                        'persyaratan' => $_POST[$persyaratan_field] ?? ''
                    ];
                    
                    // Simpan ke JSON
                    saveLayananPopup($layanan_keys[$i] ?? "layanan_$i", $popup_data);
                }
                
                $success = saveSectionData('index', 'layanan_data', $layanan_data);
            }
        }
        // === PROFIL PAGE ===
        elseif ($file_type === 'profil') {
            if ($section_id === 'visi-misi') {
                // Update visi misi profil
                $visi = $_POST['visi'] ?? '';
                $misi_text = $_POST['misi'] ?? '';
                
                $misi_items = [];
                if (!empty($misi_text)) {
                    $lines = explode("\n", $misi_text);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (!empty($line)) {
                            if (substr($line, 0, 1) === '-') {
                                $line = trim(substr($line, 1));
                            }
                            $misi_items[] = $line;
                        }
                    }
                }
                
                $visi_misi = [
                    'visi' => $visi,
                    'misi' => $misi_items
                ];
                
                $success = saveSectionData('profil', 'visi_misi', $visi_misi);
            }
            elseif ($section_id === 'hero') {
                // Update hero profil
                $hero_data = [
                    'title' => $_POST['hero_title'] ?? '',
                    'subtitle' => $_POST['hero_subtitle'] ?? '',
                    'image' => $_POST['hero_image'] ?? ''
                ];
                
                $success = saveSectionData('profil', 'hero_data', $hero_data);
            }
            elseif ($section_id === 'tupoksi') {
                // Update tupoksi profil
                $tupoksi_text = $_POST['tupoksi'] ?? '';
                
                $tupoksi_items = [];
                if (!empty($tupoksi_text)) {
                    $lines = explode("\n", $tupoksi_text);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (!empty($line)) {
                            $tupoksi_items[] = $line;
                        }
                    }
                }
                
                $success = saveSectionData('profil', 'tupoksi', $tupoksi_items);
            }
            elseif ($section_id === 'kontak') {
                // Update kontak profil
                $kontak_data = [
                    'alamat' => $_POST['alamat'] ?? '',
                    'telepon' => $_POST['telepon'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'jam_kerja' => $_POST['jam_kerja'] ?? ''
                ];
                
                $success = saveSectionData('profil', 'kontak', $kontak_data);
            }
        }
        // === LAYANAN PAGE ===
        elseif ($file_type === 'layanan') {
            if ($section_id === 'layanan-hero') {
                // Update hero layanan
                $hero_data = [
                    'title' => $_POST['hero_title'] ?? '',
                    'subtitle' => $_POST['hero_subtitle'] ?? '',
                    'image' => $_POST['hero_image'] ?? ''
                ];
                
                $success = saveSectionData('layanan', 'hero_data', $hero_data);
            }
            elseif ($section_id === 'layanan') {
                // Update daftar layanan detail
                $layanan_detail = [];
                for ($i = 1; $i <= 4; $i++) {
                    $layanan_detail[] = [
                        'id' => $layanan_keys[$i] ?? "layanan_$i",
                        'title' => $_POST["layanan_title_$i"] ?? '',
                        'description' => $_POST["layanan_desc_$i"] ?? '',
                        'image' => $_POST["layanan_image_$i"] ?? ''
                    ];
                }
                
                $success = saveSectionData('layanan', 'layanan_detail', $layanan_detail);
            }
            elseif ($section_id === 'faq') {
                // Update FAQ layanan
                $faq_items = [];
                $faq_count = $_POST['faq_count'] ?? 5;
                
                for ($i = 1; $i <= $faq_count; $i++) {
                    $question = $_POST["faq_question_$i"] ?? '';
                    $answer = $_POST["faq_answer_$i"] ?? '';
                    
                    if (!empty($question) && !empty($answer)) {
                        $faq_items[] = [
                            'question' => $question,
                            'answer' => $answer
                        ];
                    }
                }
                
                $success = saveSectionData('layanan', 'faq', $faq_items);
            }
        }
        // === FAQ PAGE ===
        elseif ($file_type === 'faq') {
            if ($section_id === 'faq-content') {
                // Update FAQ umum
                $faq_items = [];
                $faq_count = $_POST['faq_count'] ?? 10;
                
                for ($i = 1; $i <= $faq_count; $i++) {
                    $question = $_POST["faq_question_$i"] ?? '';
                    $answer = $_POST["faq_answer_$i"] ?? '';
                    
                    if (!empty($question) && !empty($answer)) {
                        $faq_items[] = [
                            'question' => $question,
                            'answer' => $answer
                        ];
                    }
                }
                
                $success = saveSectionData('faq', 'faq_content', $faq_items);
            }
        }
        
        if ($success) {
            $pesan_sukses = 'Konten berhasil diperbarui!';
            header("Location: admin-pengaturan.php?file=$file_type&section=$section_id&success=1");
            exit;
        } else {
            $pesan_error = 'Gagal menyimpan perubahan.';
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
        if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif" || $imageFileType == "webp") {
            if (move_uploaded_file($_FILES["upload_image"]["tmp_name"], $target_file)) {
                $pesan_sukses = "Gambar berhasil diupload! URL: " . $target_file;
                $uploaded_image_url = $target_file;
            } else {
                $pesan_error = "Maaf, terjadi kesalahan saat mengupload gambar.";
            }
        } else {
            $pesan_error = "Maaf, hanya file JPG, JPEG, PNG, GIF & WebP yang diperbolehkan.";
        }
    } else {
        $pesan_error = "File yang diupload bukan gambar.";
    }
}

// Ambil konten yang akan diedit
$current_file = $_GET['file'] ?? 'index';
$current_section = $_GET['section'] ?? 'hero';

if (!isset($editable_sections[$current_file])) {
    $current_file = 'index';
}

if (!isset($editable_sections[$current_file]['sections'][$current_section])) {
    $current_section = array_key_first($editable_sections[$current_file]['sections']);
}

// AMBIL DATA DARI JSON
$current_content = [];

if ($current_file === 'index') {
    if ($current_section === 'hero') {
        $hero_data = getSectionData('index', 'hero_data');
        $current_content = $hero_data;
        for ($i = 0; $i < 4; $i++) {
            $current_content["hero_image_" . ($i+1)] = $hero_data['hero_images'][$i] ?? '';
        }
    }
    elseif ($current_section === 'pimpinan') {
        $current_content = getSectionData('index', 'pimpinan_data');
    }
    elseif ($current_section === 'visi-misi') {
        $visi_misi = getSectionData('index', 'visi_misi');
        $current_content = $visi_misi;
        
        // Format misi untuk textarea
        if (isset($visi_misi['misi']) && is_array($visi_misi['misi'])) {
            $misi_items = [];
            foreach ($visi_misi['misi'] as $item) {
                if (!empty(trim($item))) {
                    $misi_items[] = '- ' . trim($item);
                }
            }
            $current_content['misi'] = implode("\n", $misi_items);
        }
    }
    elseif ($current_section === 'layanan-section') {
        $current_content = getSectionData('index', 'layanan_data');
        
        // Ambil data popup untuk setiap layanan
        for ($i = 1; $i <= 4; $i++) {
            $layanan_key = $layanan_keys[$i] ?? "layanan_$i";
            $popup_data = getLayananPopup($layanan_key);
            
            $current_content["layanan_popup_desc_$i"] = $popup_data['popup_desc'] ?? '';
            $current_content["layanan_cara_kerja_$i"] = $popup_data['cara_kerja'] ?? '';
            $current_content["layanan_persyaratan_$i"] = $popup_data['persyaratan'] ?? '';
        }
    }
}
elseif ($current_file === 'profil') {
    if ($current_section === 'visi-misi') {
        $visi_misi = getSectionData('profil', 'visi_misi');
        $current_content = $visi_misi;
        
        // Format misi untuk textarea
        if (isset($visi_misi['misi']) && is_array($visi_misi['misi'])) {
            $misi_items = [];
            foreach ($visi_misi['misi'] as $item) {
                if (!empty(trim($item))) {
                    $misi_items[] = '- ' . trim($item);
                }
            }
            $current_content['misi'] = implode("\n", $misi_items);
        }
    }
    elseif ($current_section === 'hero') {
        $current_content = getSectionData('profil', 'hero_data');
    }
    elseif ($current_section === 'tupoksi') {
        $tupoksi = getSectionData('profil', 'tupoksi');
        if (is_array($tupoksi)) {
            $current_content['tupoksi'] = implode("\n", $tupoksi);
        }
    }
    elseif ($current_section === 'kontak') {
        $current_content = getSectionData('profil', 'kontak');
    }
}
elseif ($current_file === 'layanan') {
    if ($current_section === 'layanan-hero') {
        $current_content = getSectionData('layanan', 'hero_data');
    }
    elseif ($current_section === 'layanan') {
        $current_content = getSectionData('layanan', 'layanan_detail');
    }
    elseif ($current_section === 'faq') {
        $current_content['faq'] = getSectionData('layanan', 'faq');
        if (is_array($current_content['faq'])) {
            $current_content['faq_count'] = count($current_content['faq']);
        }
    }
}
elseif ($current_file === 'faq') {
    if ($current_section === 'faq-content') {
        $faq_content = getSectionData('faq', 'faq_content');
        if (is_array($faq_content)) {
            $current_content['faq'] = $faq_content;
            $current_content['faq_count'] = count($faq_content);
        } else {
            $current_content['faq_count'] = 10;
        }
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

        .layanan-popup-section {
            background: #f0f8ff;
            border: 1px solid #cce5ff;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        .layanan-popup-section h6 {
            color: #004085;
            border-bottom: 2px solid #b8daff;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .faq-item {
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .faq-item h6 {
            color: #003399;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .add-faq-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 20px;
        }

        .add-faq-btn:hover {
            background: #218838;
        }

        .remove-faq-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            float: right;
        }

        .remove-faq-btn:hover {
            background: #c82333;
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
                    <p class="text-muted mb-0">Edit konten semua halaman website secara dinamis</p>
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
                        <?php foreach ($editable_sections as $file_key => $file_info): ?>
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
                        <h4>Edit: <?php echo $editable_sections[$current_file]['sections'][$current_section]; ?></h4>
                        <p>Halaman: <?php echo $editable_sections[$current_file]['name']; ?></p>
                    </div>

                    <form method="POST" class="editor-form" enctype="multipart/form-data">
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
                                            <li>Format gambar: JPG, PNG, WebP</li>
                                        </ul>
                                    </div>
                                    
                                    <?php for ($i = 1; $i <= 3; $i++): ?>
                                        <div class="pimpinan-card-editor">
                                            <h5>Pimpinan <?php echo $i; ?></h5>
                                            
                                            <div class="form-group">
                                                <label for="pimpinan_nama_<?php echo $i; ?>">Nama Lengkap:</label>
                                                <input type="text" id="pimpinan_nama_<?php echo $i; ?>" 
                                                       name="pimpinan_nama_<?php echo $i; ?>" class="form-control" 
                                                       value="<?php echo htmlspecialchars($current_content[$i-1]['nama'] ?? ''); ?>"
                                                       placeholder="Contoh: Dr. Fahmi Fadli">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="pimpinan_jabatan_<?php echo $i; ?>">Jabatan:</label>
                                                <input type="text" id="pimpinan_jabatan_<?php echo $i; ?>" 
                                                       name="pimpinan_jabatan_<?php echo $i; ?>" class="form-control" 
                                                       value="<?php echo htmlspecialchars($current_content[$i-1]['jabatan'] ?? ''); ?>"
                                                       placeholder="Contoh: Bupati Paser">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="pimpinan_image_<?php echo $i; ?>">Foto Pimpinan:</label>
                                                <div class="image-input-group">
                                                    <input type="text" id="pimpinan_image_<?php echo $i; ?>" 
                                                           name="pimpinan_image_<?php echo $i; ?>" class="form-control" 
                                                           value="<?php echo htmlspecialchars($current_content[$i-1]['foto'] ?? ''); ?>"
                                                           placeholder="URL gambar...">
                                                    <button type="button" class="select-image-btn" 
                                                            onclick="openImageSelector('pimpinan_image_<?php echo $i; ?>')">
                                                        Pilih Gambar
                                                    </button>
                                                </div>
                                                <?php if (!empty($current_content[$i-1]['foto'])): ?>
                                                    <div class="image-preview pimpinan-preview">
                                                        <p><strong>Preview Foto:</strong></p>
                                                        <img src="<?php echo htmlspecialchars($current_content[$i-1]['foto']); ?>" 
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
                                <!-- Layanan Section Editor dengan Popup -->
                                <div class="form-section">
                                    <h4>Kartu Layanan (4 Kartu)</h4>
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                        <div class="form-section" style="border: 2px solid #e0e0e0; padding: 25px; margin-bottom: 30px; border-radius: 8px; background: #f9f9f9;">
                                            <h5 style="color: #003399; margin-bottom: 20px; border-bottom: 2px solid #e36159; padding-bottom: 10px;">
                                                Layanan <?php echo $i; ?>
                                            </h5>
                                            
                                            <!-- Data Kartu Utama -->
                                            <div class="form-group">
                                                <label for="layanan_title_<?php echo $i; ?>">Judul Kartu:</label>
                                                <input type="text" id="layanan_title_<?php echo $i; ?>" 
                                                    name="layanan_title_<?php echo $i; ?>" class="form-control" 
                                                    value="<?php echo htmlspecialchars($current_content[$i-1]['title'] ?? ''); ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="layanan_desc_<?php echo $i; ?>">Deskripsi Singkat (di kartu):</label>
                                                <textarea id="layanan_desc_<?php echo $i; ?>" 
                                                        name="layanan_desc_<?php echo $i; ?>" class="form-control" rows="3"
                                                        placeholder="Deskripsi singkat yang muncul di kartu..."><?php echo htmlspecialchars($current_content[$i-1]['desc'] ?? ''); ?></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="layanan_image_<?php echo $i; ?>">Gambar Kartu:</label>
                                                <div class="image-input-group">
                                                    <input type="text" id="layanan_image_<?php echo $i; ?>" 
                                                        name="layanan_image_<?php echo $i; ?>" class="form-control" 
                                                        value="<?php echo htmlspecialchars($current_content[$i-1]['icon'] ?? ''); ?>"
                                                        placeholder="URL gambar...">
                                                    <button type="button" class="select-image-btn" 
                                                            onclick="openImageSelector('layanan_image_<?php echo $i; ?>')">
                                                        Pilih Gambar
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- DATA POPUP -->
                                            <div class="layanan-popup-section">
                                                <h6>Data untuk Popup Detail</h6>
                                                
                                                <div class="form-group">
                                                    <label for="layanan_popup_desc_<?php echo $i; ?>">Deskripsi Lengkap (di popup):</label>
                                                    <textarea id="layanan_popup_desc_<?php echo $i; ?>" 
                                                            name="layanan_popup_desc_<?php echo $i; ?>" class="form-control" rows="4"
                                                            placeholder="Deskripsi lengkap yang akan muncul di popup..."><?php echo htmlspecialchars($current_content['layanan_popup_desc_' . $i] ?? ''); ?></textarea>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="layanan_cara_kerja_<?php echo $i; ?>">Cara Kerja Layanan:</label>
                                                    <div class="format-hint">
                                                        <small>Contoh format (setiap baris menjadi poin terpisah):</small>
                                                        <pre style="background:#f5f5f5;padding:10px;border-radius:4px;margin-top:5px;font-size:12px;">
Pemohon datang ke kantor Dinas Pendidikan dengan membawa dokumen asli
Mengisi formulir permohonan legalisir
Petugas memverifikasi dokumen asli
Dokumen dicap dan ditandatangani oleh pejabat berwenang
Pemohon menerima dokumen yang telah dilegalisir</pre>
                                                    </div>
                                                    <textarea id="layanan_cara_kerja_<?php echo $i; ?>" 
                                                            name="layanan_cara_kerja_<?php echo $i; ?>" class="form-control" rows="6"
                                                            placeholder="Masukkan langkah-langkah cara kerja layanan..."><?php echo htmlspecialchars($current_content['layanan_cara_kerja_' . $i] ?? ''); ?></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label for="layanan_persyaratan_<?php echo $i; ?>">Persyaratan:</label>
                                                    <div class="format-hint">
                                                        <small>Contoh format (setiap baris menjadi poin terpisah):</small>
                                                        <pre style="background:#f5f5f5;padding:10px;border-radius:4px;margin-top:5px;font-size:12px;">
Ijazah asli yang akan dilegalisir
KTP asli dan fotokopi
Formulir permohonan yang telah diisi
Bukti pembayaran (jika ada)</pre>
                                                    </div>
                                                    <textarea id="layanan_persyaratan_<?php echo $i; ?>" 
                                                            name="layanan_persyaratan_<?php echo $i; ?>" class="form-control" rows="6"
                                                            placeholder="Masukkan daftar persyaratan..."><?php echo htmlspecialchars($current_content['layanan_persyaratan_' . $i] ?? ''); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                
                            <?php endif; ?>
                            
                        <?php elseif ($current_file === 'profil'): ?>
                            
                            <?php if ($current_section === 'visi-misi'): ?>
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
                                
                            <?php elseif ($current_section === 'hero'): ?>
                                <!-- Hero Profil Editor -->
                                <div class="form-section">
                                    <h4>Hero Section Profil</h4>
                                    <div class="form-group">
                                        <label for="hero_title">Judul:</label>
                                        <input type="text" id="hero_title" name="hero_title" class="form-control" 
                                               value="<?php echo htmlspecialchars($current_content['title'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="hero_subtitle">Subjudul:</label>
                                        <textarea id="hero_subtitle" name="hero_subtitle" class="form-control" rows="3"><?php echo htmlspecialchars($current_content['subtitle'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="hero_image">Gambar Background:</label>
                                        <div class="image-input-group">
                                            <input type="text" id="hero_image" name="hero_image" class="form-control" 
                                                   value="<?php echo htmlspecialchars($current_content['image'] ?? ''); ?>"
                                                   placeholder="URL gambar...">
                                            <button type="button" class="select-image-btn" 
                                                    onclick="openImageSelector('hero_image')">
                                                Pilih Gambar
                                            </button>
                                        </div>
                                        <?php if (!empty($current_content['image'])): ?>
                                            <div class="image-preview">
                                                <img src="<?php echo htmlspecialchars($current_content['image']); ?>" 
                                                     alt="Hero Profil" 
                                                     onerror="this.style.display='none'; this.parentElement.innerHTML='<p style=\"color:#dc3545;\">Gambar tidak ditemukan</p>';">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                            <?php elseif ($current_section === 'tupoksi'): ?>
                                <!-- Tupoksi Profil Editor -->
                                <div class="form-section">
                                    <h4>Tugas Pokok & Fungsi</h4>
                                    <div class="format-hint">
                                        <h5>Format Penulisan:</h5>
                                        <ul>
                                            <li>Setiap baris akan menjadi poin terpisah</li>
                                            <li>Tidak perlu tanda dash, langsung tulis saja</li>
                                            <li>Contoh format:
                                                <pre style="background:#f5f5f5;padding:10px;border-radius:4px;margin-top:5px;">
Menyelenggarakan urusan pemerintahan bidang pendidikan
Mengelola pendidikan anak usia dini, pendidikan dasar, dan pendidikan menengah
Melakukan pembinaan dan pengembangan tenaga kependidikan</pre>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="tupoksi">Teks Tugas Pokok & Fungsi:</label>
                                        <textarea id="tupoksi" name="tupoksi" class="form-control" rows="15"><?php echo htmlspecialchars($current_content['tupoksi'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                
                            <?php elseif ($current_section === 'kontak'): ?>
                                <!-- Kontak Profil Editor -->
                                <div class="form-section">
                                    <h4>Kontak Profil</h4>
                                    <div class="form-group">
                                        <label for="alamat">Alamat:</label>
                                        <textarea id="alamat" name="alamat" class="form-control" rows="4"><?php echo htmlspecialchars($current_content['alamat'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="telepon">Telepon:</label>
                                        <input type="text" id="telepon" name="telepon" class="form-control" 
                                               value="<?php echo htmlspecialchars($current_content['telepon'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="email">Email:</label>
                                        <input type="email" id="email" name="email" class="form-control" 
                                               value="<?php echo htmlspecialchars($current_content['email'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="jam_kerja">Jam Kerja:</label>
                                        <textarea id="jam_kerja" name="jam_kerja" class="form-control" rows="3"><?php echo htmlspecialchars($current_content['jam_kerja'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                
                            <?php endif; ?>
                            
                        <?php elseif ($current_file === 'layanan'): ?>
                            
                            <?php if ($current_section === 'layanan-hero'): ?>
                                <!-- Hero Layanan Editor -->
                                <div class="form-section">
                                    <h4>Hero Section Layanan</h4>
                                    <div class="form-group">
                                        <label for="hero_title">Judul:</label>
                                        <input type="text" id="hero_title" name="hero_title" class="form-control" 
                                               value="<?php echo htmlspecialchars($current_content['title'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="hero_subtitle">Subjudul:</label>
                                        <textarea id="hero_subtitle" name="hero_subtitle" class="form-control" rows="3"><?php echo htmlspecialchars($current_content['subtitle'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="hero_image">Gambar Background:</label>
                                        <div class="image-input-group">
                                            <input type="text" id="hero_image" name="hero_image" class="form-control" 
                                                   value="<?php echo htmlspecialchars($current_content['image'] ?? ''); ?>"
                                                   placeholder="URL gambar...">
                                            <button type="button" class="select-image-btn" 
                                                    onclick="openImageSelector('hero_image')">
                                                Pilih Gambar
                                            </button>
                                        </div>
                                        <?php if (!empty($current_content['image'])): ?>
                                            <div class="image-preview">
                                                <img src="<?php echo htmlspecialchars($current_content['image']); ?>" 
                                                     alt="Hero Layanan" 
                                                     onerror="this.style.display='none'; this.parentElement.innerHTML='<p style=\"color:#dc3545;\">Gambar tidak ditemukan</p>';">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                            <?php elseif ($current_section === 'layanan'): ?>
                                <!-- Detail Layanan Editor -->
                                <div class="form-section">
                                    <h4>Detail Layanan</h4>
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                        <div class="faq-item">
                                            <h6>Layanan <?php echo $i; ?></h6>
                                            <div class="form-group">
                                                <label for="layanan_title_<?php echo $i; ?>">Judul:</label>
                                                <input type="text" id="layanan_title_<?php echo $i; ?>" 
                                                       name="layanan_title_<?php echo $i; ?>" class="form-control" 
                                                       value="<?php echo htmlspecialchars($current_content[$i-1]['title'] ?? ''); ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="layanan_desc_<?php echo $i; ?>">Deskripsi Detail:</label>
                                                <textarea id="layanan_desc_<?php echo $i; ?>" 
                                                          name="layanan_desc_<?php echo $i; ?>" class="form-control" rows="5"><?php echo htmlspecialchars($current_content[$i-1]['description'] ?? ''); ?></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="layanan_image_<?php echo $i; ?>">Gambar:</label>
                                                <div class="image-input-group">
                                                    <input type="text" id="layanan_image_<?php echo $i; ?>" 
                                                           name="layanan_image_<?php echo $i; ?>" class="form-control" 
                                                           value="<?php echo htmlspecialchars($current_content[$i-1]['image'] ?? ''); ?>"
                                                           placeholder="URL gambar...">
                                                    <button type="button" class="select-image-btn" 
                                                            onclick="openImageSelector('layanan_image_<?php echo $i; ?>')">
                                                        Pilih Gambar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                
                            <?php elseif ($current_section === 'faq'): ?>
                                <!-- FAQ Layanan Editor -->
                                <div class="form-section">
                                    <h4>FAQ Layanan</h4>
                                    <div id="faq-container">
                                        <?php 
                                        $faq_count = $current_content['faq_count'] ?? 5;
                                        $faq_items = $current_content['faq'] ?? [];
                                        
                                        for ($i = 1; $i <= $faq_count; $i++): 
                                            $faq_item = $faq_items[$i-1] ?? ['question' => '', 'answer' => ''];
                                        ?>
                                            <div class="faq-item" id="faq-item-<?php echo $i; ?>">
                                                <button type="button" class="remove-faq-btn" onclick="removeFaqItem(<?php echo $i; ?>)">Hapus</button>
                                                <h6>Pertanyaan <?php echo $i; ?></h6>
                                                <div class="form-group">
                                                    <label for="faq_question_<?php echo $i; ?>">Pertanyaan:</label>
                                                    <input type="text" id="faq_question_<?php echo $i; ?>" 
                                                           name="faq_question_<?php echo $i; ?>" class="form-control" 
                                                           value="<?php echo htmlspecialchars($faq_item['question']); ?>">
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="faq_answer_<?php echo $i; ?>">Jawaban:</label>
                                                    <textarea id="faq_answer_<?php echo $i; ?>" 
                                                              name="faq_answer_<?php echo $i; ?>" class="form-control" rows="3"><?php echo htmlspecialchars($faq_item['answer']); ?></textarea>
                                                </div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                    
                                    <div class="form-group">
                                        <button type="button" class="add-faq-btn" onclick="addFaqItem()">
                                            <span>+</span> Tambah Pertanyaan
                                        </button>
                                    </div>
                                    
                                    <input type="hidden" id="faq_count" name="faq_count" value="<?php echo $faq_count; ?>">
                                </div>
                                
                            <?php endif; ?>
                            
                        <?php elseif ($current_file === 'faq' && $current_section === 'faq-content'): ?>
                            <!-- FAQ Umum Editor -->
                            <div class="form-section">
                                <h4>Konten FAQ Umum</h4>
                                <div id="faq-container">
                                    <?php 
                                    $faq_count = $current_content['faq_count'] ?? 10;
                                    $faq_items = $current_content['faq'] ?? [];
                                    
                                    for ($i = 1; $i <= $faq_count; $i++): 
                                        $faq_item = $faq_items[$i-1] ?? ['question' => '', 'answer' => ''];
                                    ?>
                                        <div class="faq-item" id="faq-item-<?php echo $i; ?>">
                                            <button type="button" class="remove-faq-btn" onclick="removeFaqItem(<?php echo $i; ?>)">Hapus</button>
                                            <h6>Pertanyaan <?php echo $i; ?></h6>
                                            <div class="form-group">
                                                <label for="faq_question_<?php echo $i; ?>">Pertanyaan:</label>
                                                <input type="text" id="faq_question_<?php echo $i; ?>" 
                                                       name="faq_question_<?php echo $i; ?>" class="form-control" 
                                                       value="<?php echo htmlspecialchars($faq_item['question']); ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="faq_answer_<?php echo $i; ?>">Jawaban:</label>
                                                <textarea id="faq_answer_<?php echo $i; ?>" 
                                                          name="faq_answer_<?php echo $i; ?>" class="form-control" rows="4"><?php echo htmlspecialchars($faq_item['answer']); ?></textarea>
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                
                                <div class="form-group">
                                    <button type="button" class="add-faq-btn" onclick="addFaqItem()">
                                        <span>+</span> Tambah Pertanyaan
                                    </button>
                                </div>
                                
                                <input type="hidden" id="faq_count" name="faq_count" value="<?php echo $faq_count; ?>">
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
                                <p><small>Format: JPG, JPEG, PNG, GIF, WebP. Maksimal: 5MB</small></p>
                            </div>
                            
                            <form method="POST" enctype="multipart/form-data" id="uploadFormModal">
                                <input type="file" id="imageUploadModal" name="upload_image" onchange="uploadImageModal()" style="display: none;">
                            </form>
                            
                            <!-- Daftar Gambar -->
                            <div id="imageList">
                                <?php
                                $upload_dir = 'assets/uploads/';
                                if (file_exists($upload_dir)) {
                                    $images = glob($upload_dir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
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
        // JavaScript untuk semua fitur
        let currentImageField = '';
        let faqCounter = <?php echo $current_file === 'faq' || ($current_file === 'layanan' && $current_section === 'faq') ? ($current_content['faq_count'] ?? 10) : 5; ?>;
        
        function openImageSelector(fieldId) {
            currentImageField = fieldId;
            document.getElementById('imageSelectorModal').style.display = 'flex';
        }
        
        function closeImageSelector() {
            document.getElementById('imageSelectorModal').style.display = 'none';
            currentImageField = '';
        }
        
        function selectImageForField(imageUrl) {
            if (currentImageField) {
                document.getElementById(currentImageField).value = imageUrl;
                closeImageSelector();
                alert('Gambar telah dipilih. Jangan lupa klik "Simpan Perubahan" untuk menerapkan.');
            }
        }
        
        function uploadImageModal() {
            const form = document.getElementById('uploadFormModal');
            const formData = new FormData(form);
            
            fetch('admin-pengaturan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal mengupload gambar');
            });
        }
        
        // Fungsi untuk menambah FAQ item
        function addFaqItem() {
            faqCounter++;
            const faqContainer = document.getElementById('faq-container');
            const newFaqItem = document.createElement('div');
            newFaqItem.className = 'faq-item';
            newFaqItem.id = 'faq-item-' + faqCounter;
            
            newFaqItem.innerHTML = `
                <button type="button" class="remove-faq-btn" onclick="removeFaqItem(${faqCounter})">Hapus</button>
                <h6>Pertanyaan ${faqCounter}</h6>
                <div class="form-group">
                    <label for="faq_question_${faqCounter}">Pertanyaan:</label>
                    <input type="text" id="faq_question_${faqCounter}" 
                           name="faq_question_${faqCounter}" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="faq_answer_${faqCounter}">Jawaban:</label>
                    <textarea id="faq_answer_${faqCounter}" 
                              name="faq_answer_${faqCounter}" class="form-control" rows="3"></textarea>
                </div>
            `;
            
            faqContainer.appendChild(newFaqItem);
            document.getElementById('faq_count').value = faqCounter;
            
            // Scroll ke item baru
            newFaqItem.scrollIntoView({ behavior: 'smooth' });
        }
        
        // Fungsi untuk menghapus FAQ item
        function removeFaqItem(itemId) {
            if (confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')) {
                const faqItem = document.getElementById('faq-item-' + itemId);
                if (faqItem) {
                    faqItem.remove();
                    updateFaqCount();
                }
            }
        }
        
        // Fungsi untuk update counter FAQ
        function updateFaqCount() {
            const faqItems = document.querySelectorAll('.faq-item');
            faqCounter = faqItems.length;
            document.getElementById('faq_count').value = faqCounter;
            
            // Update nomor urut
            faqItems.forEach((item, index) => {
                const itemId = index + 1;
                item.id = 'faq-item-' + itemId;
                item.querySelector('h6').textContent = 'Pertanyaan ' + itemId;
                item.querySelector('.remove-faq-btn').setAttribute('onclick', `removeFaqItem(${itemId})`);
                
                // Update input names
                const questionInput = item.querySelector('input[type="text"]');
                const answerTextarea = item.querySelector('textarea');
                
                if (questionInput) {
                    questionInput.name = 'faq_question_' + itemId;
                    questionInput.id = 'faq_question_' + itemId;
                }
                
                if (answerTextarea) {
                    answerTextarea.name = 'faq_answer_' + itemId;
                    answerTextarea.id = 'faq_answer_' + itemId;
                }
            });
        }
        
        // Drag and drop untuk upload gambar
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.querySelector('.upload-area');
            
            if (uploadArea) {
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
            }
        });
        
        // Dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const userDropdown = document.getElementById('userDropdown');
            const userDropdownMenu = document.getElementById('userDropdownMenu');
            
            if (userDropdown && userDropdownMenu) {
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
            }
        });
        
        // Tutup modal jika klik di luar
        const imageModal = document.getElementById('imageSelectorModal');
        if (imageModal) {
            imageModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeImageSelector();
                }
            });
        }
        
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