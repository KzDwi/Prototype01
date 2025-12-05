<?php
session_start();
require_once 'functions.php';
require_once 'functions_faq.php';

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
        'name' => 'Halaman Utama',
        'path' => 'index.php',
        'sections' => [
            'hero' => 'Hero Section (Slider)',
            'pimpinan' => 'Pimpinan Daerah',
            'visi-misi' => 'Visi & Misi',
            'layanan-section' => 'Layanan'
        ]
    ],
    'profil' => [
        'name' => 'Halaman Profil',
        'path' => 'profil.php',
        'sections' => [
            'hero' => 'Hero Profil',
            'visi-misi' => 'Visi & Misi',
            'tupoksi' => 'Tugas Pokok & Fungsi',
        ]
    ],  
    'statistik' => [
        'name' => 'Halaman Statistik',
        'path' => 'Statistik.php',
        'sections' => [
            'rincian' => 'Rincian Data (Tabel)'
        ]
    ],
    'layanan' => [
        'name' => 'Halaman Layanan',
        'path' => 'layanan.php',
        'sections' => [
            'layanan' => 'Daftar Layanan',
            'contact' => 'Form  '
        ]
    ],
    'faq' => [
        'name' => 'Halaman FAQ',
        'path' => 'faq.php',
        'sections' => [
            'faq-content' => 'Konten FAQ'
        ]
    ],
    // TAMBAHKAN KODE INI DI BAWAH FAQ:
    'kontak' => [
        'name' => 'Halaman Kontak',
        'path' => 'kontak.php',
        'sections' => [
            'info' => 'Alamat & Kontak'
        ]
    ]
];

// Tambahkan sebelum bagian Handle form submission
$layanan_keys = [
    1 => 'legalisir-ijazah',
    2 => 'surat-mutasi',
    3 => 'tunjangan-guru',
    4 => 'izin-pendirian'
];

// --- FUNGSI BARU UNTUK STATISTIK ---
function loadStatistikData() {
    $file_path = 'data/statistik.json';
    return file_exists($file_path) ? json_decode(file_get_contents($file_path), true) : [];
}

function saveStatistikData($data) {
    return file_put_contents('data/statistik.json', json_encode($data, JSON_PRETTY_PRINT));
}

// FUNGSI UNTUK CONTENT.JSON
function loadContentData() {
    $file_path = 'data/content.json';
    if (file_exists($file_path)) {
        $data = json_decode(file_get_contents($file_path), true);
        return $data ?: ['index' => []];
    }
    return ['index' => []];
}

function saveContentData($data) {
    $dir = 'data';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    $file_path = $dir . '/content.json';
    return file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Fungsi untuk membaca/simpan data popup
function loadLayananPopupData() {
    $file_path = 'data/layanan_popup.json';
    if (file_exists($file_path)) {
        $data = json_decode(file_get_contents($file_path), true);
        return $data ?: [];
    }
    return [];
}

function saveLayananPopupData($data) {
    $dir = 'data';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    $file_path = $dir . '/layanan_popup.json';
    return file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Handle form submission untuk edit konten
$pesan_sukses = '';
$pesan_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_faq'])) {
        // Load data saat ini
        $content_data = loadContentData();
        
        // Inisialisasi jika belum ada
        if (!isset($content_data['faq'])) {
            $content_data['faq'] = [];
        }
        
        $categories = ['informasi_umum', 'layanan_kesiswaan', 'guru_tenaga_kependidikan', 'ppdb'];
        $faq_updated = false;
        
        foreach ($categories as $category) {
            if (isset($_POST['faq'][$category]) && is_array($_POST['faq'][$category])) {
                $content_data['faq'][$category] = [];
                
                foreach ($_POST['faq'][$category] as $index => $faq_item) {
                    $question = trim($faq_item['question'] ?? '');
                    $answer = trim($faq_item['answer'] ?? '');
                    
                    // Hanya simpan jika ada pertanyaan dan jawaban
                    if (!empty($question) && !empty($answer)) {
                        $content_data['faq'][$category][] = [
                            'question' => $question,
                            'answer' => $answer
                        ];
                        $faq_updated = true;
                    }
                }
            }
        }
        
        // Save kembali
        if (saveContentData($content_data)) {
            $pesan_sukses = 'FAQ berhasil disimpan!';
            header("Location: admin-pengaturan.php?file=faq&section=faq-content&success=1");
            exit;
        } else {
            $pesan_error = 'Gagal menyimpan FAQ.';
        }
    }
    if (isset($_POST['save_content'])) {
        $file_type = $_POST['file_type'];
        $section_id = $_POST['section_id'];
        
        // LOAD DATA DARI CONTENT.JSON
        $content_data = loadContentData();
        
        if ($file_type === 'index') {
            if ($section_id === 'hero') {
                // Update data hero ke content.json
                if (!isset($content_data['index']['hero_data'])) {
                    $content_data['index']['hero_data'] = [
                        'hero_text' => '',
                        'hero_subtext' => '',
                        'hero_paragraph' => '',
                        'hero_images' => ['', '', '', '']
                    ];
                }
                
                // Update teks
                if (!empty($_POST['hero_text'])) {
                    $content_data['index']['hero_data']['hero_text'] = $_POST['hero_text'];
                }
                if (!empty($_POST['hero_subtext'])) {
                    $content_data['index']['hero_data']['hero_subtext'] = $_POST['hero_subtext'];
                }
                if (!empty($_POST['hero_paragraph'])) {
                    $content_data['index']['hero_data']['hero_paragraph'] = $_POST['hero_paragraph'];
                }
                
                // Update gambar
                for ($i = 1; $i <= 4; $i++) {
                    $image_field = "hero_image_$i";
                    if (!empty($_POST[$image_field])) {
                        $content_data['index']['hero_data']['hero_images'][$i-1] = $_POST[$image_field];
                    }
                }
                
                $success = true;
            }
            elseif ($section_id === 'pimpinan') {
                // Update data pimpinan ke content.json
                if (!isset($content_data['index']['pimpinan_data'])) {
                    $content_data['index']['pimpinan_data'] = [
                        ['nama' => '', 'jabatan' => '', 'foto' => ''],
                        ['nama' => '', 'jabatan' => '', 'foto' => ''],
                        ['nama' => '', 'jabatan' => '', 'foto' => '']
                    ];
                }
                
                for ($i = 1; $i <= 3; $i++) {
                    $nama = $_POST["pimpinan_nama_$i"] ?? '';
                    $jabatan = $_POST["pimpinan_jabatan_$i"] ?? '';
                    $foto = $_POST["pimpinan_image_$i"] ?? '';
                    
                    if (!empty($nama) || !empty($jabatan) || !empty($foto)) {
                        $content_data['index']['pimpinan_data'][$i-1]['nama'] = $nama;
                        $content_data['index']['pimpinan_data'][$i-1]['jabatan'] = $jabatan;
                        $content_data['index']['pimpinan_data'][$i-1]['foto'] = $foto;
                    }
                }
                
                $success = true;
            }
            elseif ($section_id === 'visi-misi') {
                // Update visi misi ke content.json
                if (!isset($content_data['index']['visi_misi'])) {
                    $content_data['index']['visi_misi'] = [
                        'visi' => '',
                        'misi' => []
                    ];
                }
                
                // Update visi
                $visi_text = $_POST['visi'] ?? '';
                if (!empty($visi_text)) {
                    // Format visi dari baris dengan dash menjadi array
                    $lines = explode("\n", $visi_text);
                    $visi_items = [];
                    
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (!empty($line)) {
                            // Hilangkan dash jika ada
                            if (substr($line, 0, 1) === '-') {
                                $line = trim(substr($line, 1));
                            }
                            $visi_items[] = $line;
                        }
                    }
                    
                    // Untuk visi, gabungkan semua item jadi satu string
                    $content_data['index']['visi_misi']['visi'] = implode(' ', $visi_items);
                }
                
                // Update misi
                $misi_text = $_POST['misi'] ?? '';
                if (!empty($misi_text)) {
                    $lines = explode("\n", $misi_text);
                    $misi_items = [];
                    
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (!empty($line)) {
                            // Hilangkan dash jika ada
                            if (substr($line, 0, 1) === '-') {
                                $line = trim(substr($line, 1));
                            }
                            $misi_items[] = $line;
                        }
                    }
                    
                    $content_data['index']['visi_misi']['misi'] = $misi_items;
                }
                
                $success = true;
            }
            elseif ($section_id === 'layanan-section') {
                // Update data layanan ke content.json
                if (!isset($content_data['index']['layanan_data'])) {
                    $content_data['index']['layanan_data'] = [
                        ['id' => 'legalisir-ijazah', 'title' => '', 'desc' => '', 'icon' => ''],
                        ['id' => 'surat-mutasi', 'title' => '', 'desc' => '', 'icon' => ''],
                        ['id' => 'tunjangan-guru', 'title' => '', 'desc' => '', 'icon' => ''],
                        ['id' => 'izin-pendirian', 'title' => '', 'desc' => '', 'icon' => '']
                    ];
                }
                
                for ($i = 1; $i <= 4; $i++) {
                    $title = $_POST["layanan_title_$i"] ?? '';
                    $desc = $_POST["layanan_desc_$i"] ?? '';
                    $icon = $_POST["layanan_image_$i"] ?? '';
                    
                    if (!empty($title) || !empty($desc) || !empty($icon)) {
                        $content_data['index']['layanan_data'][$i-1]['title'] = $title;
                        $content_data['index']['layanan_data'][$i-1]['desc'] = $desc;
                        $content_data['index']['layanan_data'][$i-1]['icon'] = $icon;
                    }
                }
                
                $success = true;
                
                // SIMPAN DATA POPUP KE JSON
                $popup_data = loadLayananPopupData();
                
                for ($i = 1; $i <= 4; $i++) {
                    $layanan_key = $layanan_keys[$i] ?? "layanan_$i";
                    
                    // Inisialisasi jika belum ada
                    if (!isset($popup_data[$layanan_key])) {
                        $popup_data[$layanan_key] = [
                            'popup_desc' => '',
                            'cara_kerja' => '',
                            'persyaratan' => ''
                        ];
                    }
                    
                    // Update data dari form
                    $popup_data[$layanan_key]['popup_desc'] = $_POST["layanan_popup_desc_$i"] ?? '';
                    $popup_data[$layanan_key]['cara_kerja'] = $_POST["layanan_cara_kerja_$i"] ?? '';
                    $popup_data[$layanan_key]['persyaratan'] = $_POST["layanan_persyaratan_$i"] ?? '';
                }
                
                // Simpan ke file JSON
                saveLayananPopupData($popup_data);
            }
        }
        elseif ($file_type === 'layanan') {
            // --- LOGIKA BARU UNTUK BAGIAN CONTACT ---
            if ($section_id === 'contact') {
                // Pastikan array contact_section ada
                if (!isset($content_data['layanan']['contact_section'])) {
                    $content_data['layanan']['contact_section'] = [];
                }

                // Simpan Judul dan Subjudul
                $content_data['layanan']['contact_section']['title'] = $_POST['contact_title'] ?? '';
                $content_data['layanan']['contact_section']['subtitle'] = $_POST['contact_subtitle'] ?? '';

                // Simpan Opsi Layanan (dari Textarea ke Array)
                $options_text = $_POST['contact_options'] ?? '';
                $options_arr = [];
                // Pecah per baris, trim, dan hapus baris kosong
                foreach (preg_split('/\r?\n/', $options_text) as $line) {
                    $line = trim($line);
                    if ($line !== '') {
                        $options_arr[] = $line;
                    }
                }
                $content_data['layanan']['contact_section']['service_options'] = $options_arr;

                $success = true;
            }
            // Update top-level layanan data in content.json
            if (!isset($content_data['layanan']) || !is_array($content_data['layanan'])) {
                $content_data['layanan'] = ['title' => 'Layanan Administrasi', 'layanan_data' => []];
            }

            // Ensure layanan_data exists
            if (!isset($content_data['layanan']['layanan_data']) || !is_array($content_data['layanan']['layanan_data'])) {
                $content_data['layanan']['layanan_data'] = [];
            }

            // Determine count from existing data or posted count
            $count = max(count($content_data['layanan']['layanan_data']), intval($_POST['layanan_count'] ?? 0));
            if ($count <= 0) $count = 1;

            for ($i = 0; $i < $count; $i++) {
                $idx = $i + 1; // form fields are 1-based
                $title = $_POST["layanan_title_{$idx}"] ?? '';
                $subtitle = $_POST["layanan_subtitle_{$idx}"] ?? '';
                $description = $_POST["layanan_description_{$idx}"] ?? '';
                $popup_desc = $_POST["layanan_popup_desc_{$idx}"] ?? '';
                $icon = $_POST["layanan_icon_{$idx}"] ?? '';
                $cara_text = $_POST["layanan_cara_kerja_{$idx}"] ?? '';
                $pers_text = $_POST["layanan_persyaratan_{$idx}"] ?? '';

                // Normalize cara_kerja and persyaratan into arrays
                $cara_arr = [];
                foreach (preg_split('/\r?\n/', $cara_text) as $line) {
                    $line = trim($line);
                    if ($line !== '') $cara_arr[] = $line;
                }

                $pers_arr = [];
                foreach (preg_split('/\r?\n/', $pers_text) as $line) {
                    $line = trim($line);
                    if ($line !== '') $pers_arr[] = $line;
                }

                // Ensure item exists
                if (!isset($content_data['layanan']['layanan_data'][$i])) {
                    $content_data['layanan']['layanan_data'][$i] = [
                        'id' => 'layanan_' . ($i+1),
                        'title' => '',
                        'subtitle' => '',
                        'description' => '',
                        'icon' => '',
                        'popup_desc' => '',
                        'cara_kerja' => [],
                        'persyaratan' => []
                    ];
                }

                // Update fields
                if ($title !== '') $content_data['layanan']['layanan_data'][$i]['title'] = $title;
                $content_data['layanan']['layanan_data'][$i]['subtitle'] = $subtitle;
                $content_data['layanan']['layanan_data'][$i]['description'] = $description;
                $content_data['layanan']['layanan_data'][$i]['popup_desc'] = $popup_desc;
                $content_data['layanan']['layanan_data'][$i]['icon'] = $icon;
                $content_data['layanan']['layanan_data'][$i]['cara_kerja'] = $cara_arr;
                $content_data['layanan']['layanan_data'][$i]['persyaratan'] = $pers_arr;
            }

            $success = true;
        }

        elseif ($file_type === 'statistik') {
            $stats_data = loadStatistikData();
            
            if ($section_id === 'indikator') {
                $stats_data['indikator'] = [
                    'total_sekolah' => intval($_POST['total_sekolah'] ?? 0),
                    'total_siswa'   => intval($_POST['total_siswa'] ?? 0),
                    'total_guru'    => intval($_POST['total_guru'] ?? 0),
                    'total_kelas'   => intval($_POST['total_kelas'] ?? 0)
                ];
                $success = saveStatistikData($stats_data);
            } 
            elseif ($section_id === 'rincian') {
                $stats_data['tabel_rincian'] = $_POST['rincian'] ?? [];
                $success = saveStatistikData($stats_data);
            }
        }
        
        elseif ($file_type === 'kontak') {
            if ($section_id === 'info') {
                // Pastikan array kontak_page ada di JSON
                if (!isset($content_data['kontak_page'])) {
                    $content_data['kontak_page'] = [];
                }

                // Simpan Data dari Form ke Array
                $content_data['kontak_page']['address'] = $_POST['address'] ?? '';
                $content_data['kontak_page']['phone'] = $_POST['phone'] ?? '';
                $content_data['kontak_page']['fax'] = $_POST['fax'] ?? '';
                $content_data['kontak_page']['email'] = $_POST['email'] ?? '';
                $content_data['kontak_page']['website'] = $_POST['website'] ?? '';
                $content_data['kontak_page']['hours_weekdays'] = $_POST['hours_weekdays'] ?? '';
                $content_data['kontak_page']['hours_friday'] = $_POST['hours_friday'] ?? '';
                $content_data['kontak_page']['map_embed_url'] = $_POST['map_embed_url'] ?? '';

                $success = true;
            }
        }

        elseif ($file_type === 'profil') {
            // Pastikan array profil ada
            if (!isset($content_data['profil'])) {
                $content_data['profil'] = [];
            }

            if ($section_id === 'hero') {
                $content_data['profil']['hero_title'] = $_POST['hero_title'] ?? '';
                $content_data['profil']['hero_subtitle'] = $_POST['hero_subtitle'] ?? '';
                $success = true;
            }
            elseif ($section_id === 'visi-misi') {
                $content_data['profil']['visi'] = $_POST['visi'] ?? '';
                
                // Simpan Misi (Array)
                $misi_text = $_POST['misi'] ?? '';
                $misi_arr = [];
                foreach (preg_split('/\r?\n/', $misi_text) as $line) {
                    $line = trim($line);
                    if ($line !== '') {
                        if (substr($line, 0, 1) === '-') $line = trim(substr($line, 1));
                        $misi_arr[] = $line;
                    }
                }
                $content_data['profil']['misi'] = $misi_arr;
                $success = true;
            }
            elseif ($section_id === 'tupoksi') {
                // Simpan Tupoksi (Array)
                $tupoksi_text = $_POST['tupoksi'] ?? '';
                $tupoksi_arr = [];
                foreach (preg_split('/\r?\n/', $tupoksi_text) as $line) {
                    $line = trim($line);
                    if ($line !== '') {
                        if (substr($line, 0, 1) === '-') $line = trim(substr($line, 1));
                        $tupoksi_arr[] = $line;
                    }
                }
                $content_data['profil']['tupoksi'] = $tupoksi_arr;
                $success = true;
            }
        }

        // SIMPAN SEMUA PERUBAHAN KE CONTENT.JSON
        if ($success && saveContentData($content_data)) {
            $pesan_sukses = 'Konten berhasil disimpan ke database!';
            header("Location: admin-pengaturan.php?file=$file_type&section=$section_id&success=1");
            exit;
        } else {
            $pesan_error = 'Gagal menyimpan perubahan ke database.';
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

// Handle upload icon dengan validasi ukuran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload_icon'])) {
    $target_dir = "assets/uploads/icons/";
    
    // Buat direktori jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $filename = uniqid() . '_' . basename($_FILES["upload_icon"]["name"]);
    $target_file = $target_dir . $filename;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Validasi file gambar dan ukuran
    $check = getimagesize($_FILES["upload_icon"]["tmp_name"]);
    if ($check !== false) {
        $width = $check[0];
        $height = $check[1];
        
        // Validasi dimensi (minimal 32x32, maksimal 512x512)
        if ($width < 32 || $height < 32) {
            $pesan_error = "Ukuran icon terlalu kecil. Minimal harus 32x32 px. Ukuran file Anda: {$width}x{$height} px";
        } elseif ($width > 512 || $height > 512) {
            $pesan_error = "Ukuran icon terlalu besar. Maksimal harus 512x512 px. Ukuran file Anda: {$width}x{$height} px";
        } elseif ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif" || $imageFileType == "svg") {
            if (move_uploaded_file($_FILES["upload_icon"]["tmp_name"], $target_file)) {
                $pesan_sukses = "Icon berhasil diupload! ({$width}x{$height} px) URL: " . $target_file;
                $uploaded_icon_url = $target_file;
            } else {
                $pesan_error = "Maaf, terjadi kesalahan saat mengupload icon.";
            }
        } else {
            $pesan_error = "Maaf, hanya file JPG, JPEG, PNG, GIF & SVG yang diperbolehkan untuk icon.";
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

// AMBIL KONTEN DARI CONTENT.JSON
$current_content = [];
$content_data = loadContentData();

// === TAMBAHAN LOAD DATA ===
if ($current_file === 'statistik') {
    $stats = loadStatistikData();
    if ($current_section === 'indikator') {
        $current_content = $stats['indikator'] ?? [];
    } elseif ($current_section === 'rincian') {
        $current_content['rincian'] = $stats['tabel_rincian'] ?? [];
    }
}

if ($current_file === 'index') {
    if ($current_section === 'hero') {
        // Ambil data hero dari content.json
        if (isset($content_data['index']['hero_data'])) {
            $hero = $content_data['index']['hero_data'];
            $current_content['hero_text'] = $hero['hero_text'] ?? '';
            $current_content['hero_subtext'] = $hero['hero_subtext'] ?? '';
            $current_content['hero_paragraph'] = $hero['hero_paragraph'] ?? '';
            
            // Ambil gambar slider
            for ($i = 0; $i < 4; $i++) {
                $current_content["hero_image_" . ($i+1)] = $hero['hero_images'][$i] ?? '';
            }
        }
    }
    elseif ($current_section === 'pimpinan') {
        // Ambil data pimpinan dari content.json
        if (isset($content_data['index']['pimpinan_data'])) {
            $pimpinan = $content_data['index']['pimpinan_data'];
            for ($i = 0; $i < 3; $i++) {
                if (isset($pimpinan[$i])) {
                    $current_content["pimpinan_nama_" . ($i+1)] = $pimpinan[$i]['nama'] ?? '';
                    $current_content["pimpinan_jabatan_" . ($i+1)] = $pimpinan[$i]['jabatan'] ?? '';
                    $current_content["pimpinan_image_" . ($i+1)] = $pimpinan[$i]['foto'] ?? '';
                }
            }
        }
    }
    elseif ($current_section === 'visi-misi') {
        // Ambil data visi misi dari content.json
        if (isset($content_data['index']['visi_misi'])) {
            $visi_misi = $content_data['index']['visi_misi'];
            
            // Format visi untuk textarea (dengan dash)
            $current_content['visi'] = '- ' . ($visi_misi['visi'] ?? '');
            
            // Format misi untuk textarea (dengan dash)
            $misi_items = $visi_misi['misi'] ?? [];
            $misi_lines = [];
            foreach ($misi_items as $item) {
                if (!empty(trim($item))) {
                    $misi_lines[] = '- ' . trim($item);
                }
            }
            $current_content['misi'] = implode("\n", $misi_lines);
        }
    }
    elseif ($current_section === 'layanan-section') {
        // Ambil data layanan dari content.json
        if (isset($content_data['index']['layanan_data'])) {
            $layanan = $content_data['index']['layanan_data'];
            for ($i = 0; $i < 4; $i++) {
                if (isset($layanan[$i])) {
                    $current_content["layanan_title_" . ($i+1)] = $layanan[$i]['title'] ?? '';
                    $current_content["layanan_desc_" . ($i+1)] = $layanan[$i]['desc'] ?? '';
                    $current_content["layanan_image_" . ($i+1)] = $layanan[$i]['icon'] ?? '';
                }
            }
        }
        
        // BACA DATA POPUP DARI FILE JSON
        $popup_data = loadLayananPopupData();
        
        for ($i = 1; $i <= 4; $i++) {
            $layanan_key = $layanan_keys[$i] ?? "layanan_$i";
            
            if (isset($popup_data[$layanan_key])) {
                $current_content["layanan_popup_desc_$i"] = $popup_data[$layanan_key]['popup_desc'] ?? '';
                $current_content["layanan_cara_kerja_$i"] = $popup_data[$layanan_key]['cara_kerja'] ?? '';
                $current_content["layanan_persyaratan_$i"] = $popup_data[$layanan_key]['persyaratan'] ?? '';
            } else {
                // Nilai default jika belum ada
                $current_content["layanan_popup_desc_$i"] = '';
                $current_content["layanan_cara_kerja_$i"] = '';
                $current_content["layanan_persyaratan_$i"] = '';
            }
        }
    }
}
elseif ($current_file === 'profil') {
    $p = $content_data['profil'] ?? [];
    
    // Hero Defaults
    $current_content['hero_title'] = $p['hero_title'] ?? "Profil Dinas Pendidikan dan Kebudayaan";
    $current_content['hero_subtitle'] = $p['hero_subtitle'] ?? "Kabupaten Paser";
    
    // Visi Misi Defaults
    $current_content['visi'] = $p['visi'] ?? "Mewujudkan Pendidikan dan Kebudayaan Kabupaten Paser yang Berkualitas, Berkarakter, dan Berdaya Saing";
    
    $misi_items = $p['misi'] ?? [
        "Meningkatkan akses dan mutu pendidikan dasar dan menengah",
        "Mengembangkan pendidikan karakter berbasis kearifan lokal"
    ];
    // Convert array ke string untuk textarea
    $current_content['misi'] = "";
    foreach ($misi_items as $item) $current_content['misi'] .= "- " . $item . "\n";
    
    // Tupoksi Defaults
    $tupoksi_items = $p['tupoksi'] ?? [
        "Perumusan kebijakan teknis di bidang pendidikan",
        "Pelaksanaan kebijakan di bidang pengelolaan PAUD, SD, SMP"
    ];
    // Convert array ke string untuk textarea
    $current_content['tupoksi'] = "";
    foreach ($tupoksi_items as $item) $current_content['tupoksi'] .= "- " . $item . "\n";
}

elseif ($current_file === 'kontak') {
    // Ambil data dari JSON, kalau kosong pakai default
    $k = $content_data['kontak_page'] ?? [];
    
    $current_content['address'] = $k['address'] ?? "Jl. Jenderal Sudirman No. 27\nTanah Grogot, Kabupaten Paser\nKalimantan Timur 76251";
    $current_content['phone'] = $k['phone'] ?? "(0543) 21023";
    $current_content['fax'] = $k['fax'] ?? "(0543) 21024";
    $current_content['email'] = $k['email'] ?? "disdik@paserkab.go.id";
    $current_content['website'] = $k['website'] ?? "disdik.paserkab.go.id";
    $current_content['hours_weekdays'] = $k['hours_weekdays'] ?? "08.00 - 16.00 WITA";
    $current_content['hours_friday'] = $k['hours_friday'] ?? "08.00 - 11.00 WITA";
    $current_content['map_embed_url'] = $k['map_embed_url'] ?? "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3987.6060327839327!2d116.1914303!3d-1.9081035!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df047988e6b3c0b%3A0xdaa84941bfe1b7df!2sJl.%20Jenderal%20Sudirman%20No.27%2C%20Tanah%20Grogot%2C%20Kec.%20Tanah%20Grogot%2C%20Kabupaten%20Paser%2C%20Kalimantan%20Timur%2076251!5e0!3m2!1sid!2sid!4v1764218368388!5m2!1sid!2sid";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        /* Button Styles */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }

        .btn:active {
            transform: translateY(1px);
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

        /* === STYLE BARU TABEL EDITOR (Admin) === */

        .table-responsive-admin {
            overflow-x: auto;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: 1px solid #e0e0e0;
        }

        .stats-table-input {
            width: 100%;
            border-collapse: collapse;
            background: white;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Header Tabel */
        .stats-table-input th {
            background: #003399; /* Warna Biru Dinas */
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-right: 1px solid rgba(255,255,255,0.1);
        }

        .stats-table-input th:first-child {
            text-align: left;
            padding-left: 20px;
            width: 30%;
        }

        /* Body Tabel */
        .stats-table-input td {
            padding: 0; /* Padding 0 agar input memenuhi sel */
            border-bottom: 1px solid #eee;
            border-right: 1px solid #eee;
        }

        /* Kolom Label (Kiri) */
        .stats-table-input td:first-child {
            padding: 12px 20px;
            background-color: #f8f9fa;
            font-weight: 600;
            color: #444;
            border-right: 2px solid #e0e0e0;
        }

        /* Input Field Style - Seperti Spreadsheet */
        .stats-table-input input[type="number"] {
            width: 100%;
            height: 100%;
            border: none;
            padding: 15px 10px;
            text-align: center;
            background: transparent;
            font-size: 14px;
            color: #333;
            font-weight: 500;
            outline: none;
            transition: all 0.2s ease;
        }

        /* Efek Focus pada Input */
        .stats-table-input input[type="number"]:focus {
            background-color: #eef6ff; /* Biru sangat muda */
            box-shadow: inset 0 0 0 2px #003399; /* Border biru saat aktif */
            color: #003399;
            font-weight: bold;
        }

        /* Hover Baris */
        .stats-table-input tr:hover td:first-child {
            background-color: #e9ecef;
            color: #003399;
        }

        /* Menghilangkan panah spinner pada input number agar bersih */
        .stats-table-input input::-webkit-outer-spin-button,
        .stats-table-input input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
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
                        <span class="sidebar-menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin-berita.php" class="sidebar-menu-link">
                        <span class="icon icon-news"></span>
                        <span class="sidebar-menu-text">Kelola Berita</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="layanan_pesan.php" class="sidebar-menu-link">
                        <span class="icon icon-envelope"></span>
                        <span class="sidebar-menu-text">Pesan Pengaduan</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin-pengaturan.php" class="sidebar-menu-link active"> <span class="icon icon-settings"></span>
                        <span class="sidebar-menu-text">Pengaturan Konten</span>
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <a href="?logout=true" class="btn-sidebar-logout" onclick="return confirm('Apakah Anda yakin ingin keluar?');">
                    <span>Keluar Aplikasi</span>
                </a>
            </div>
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

                    <form method="POST" class="editor-form" id="editorForm">
                        <input type="hidden" name="file_type" value="<?php echo $current_file; ?>">
                        <input type="hidden" name="section_id" value="<?php echo $current_section; ?>">
                        
                        <?php if ($current_file === 'statistik'): ?>
    
    <?php if ($current_section === 'indikator'): ?>
        <div class="form-group"><label>Total Sekolah</label><input type="number" name="total_sekolah" class="form-control" value="<?= $current_content['total_sekolah'] ?? 0 ?>"></div>
        <div class="form-group"><label>Total Siswa</label><input type="number" name="total_siswa" class="form-control" value="<?= $current_content['total_siswa'] ?? 0 ?>"></div>
        <div class="form-group"><label>Total Guru</label><input type="number" name="total_guru" class="form-control" value="<?= $current_content['total_guru'] ?? 0 ?>"></div>
        <div class="form-group"><label>Total Ruang Kelas</label><input type="number" name="total_kelas" class="form-control" value="<?= $current_content['total_kelas'] ?? 0 ?>"></div>

    <?php elseif ($current_section === 'rincian'): ?>
        <div class="form-section">
            <h4>Rincian Data Berdasarkan Jenjang</h4>
            <div class="alert alert-info" style="margin-bottom: 20px; display:flex; gap:10px; align-items:center;">
                <i class="fas fa-info-circle" style="font-size: 20px;"></i>
                <div>
                    <strong>Mode Edit Cepat:</strong> Klik langsung pada angka di dalam tabel untuk mengedit.<br>
                    <small>Kolom "Total" akan dihitung otomatis di halaman depan pengunjung.</small>
                </div>
            </div>
            
            <?php 
                $rin = $current_content['rincian'] ?? []; 
                function gv($d, $k1, $k2) {
                    return isset($d[$k1][$k2]) ? htmlspecialchars($d[$k1][$k2]) : '0';
                }
                
                // Definisi Baris agar kode lebih rapi
                $rows = [
                    'sekolah_negeri' => 'Sekolah (Negeri)',
                    'sekolah_swasta' => 'Sekolah (Swasta)',
                    'siswa'          => 'Siswa',
                    'guru_asn'       => 'Guru (ASN)',
                    'guru_non_asn'   => 'Guru (Non-ASN)'
                ];
            ?>

            <div class="table-responsive-admin">
                <table class="stats-table-input">
                    <thead>
                        <tr>
                            <th>Indikator</th>
                            <th>PAUD</th>
                            <th>SD</th>
                            <th>SMP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rows as $key => $label): ?>
                        <tr>
                            <td><?php echo $label; ?></td>
                            <td>
                                <input type="number" name="rincian[<?php echo $key; ?>][paud]" 
                                       value="<?php echo gv($rin, $key, 'paud'); ?>" placeholder="0">
                            </td>
                            <td>
                                <input type="number" name="rincian[<?php echo $key; ?>][sd]" 
                                       value="<?php echo gv($rin, $key, 'sd'); ?>" placeholder="0">
                            </td>
                            <td>
                                <input type="number" name="rincian[<?php echo $key; ?>][smp]" 
                                       value="<?php echo gv($rin, $key, 'smp'); ?>" placeholder="0">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

<?php endif; ?>

                        <?php if ($current_file === 'index'): ?>
                            
                            <?php if ($current_section === 'hero'): ?>
                                <!-- Hero Section Editor -->
                                <div class="form-section">
                                    <h4>Judul Hero</h4>
                                    <div class="form-group">
                                        <label for="hero_text">Judul Utama (h2):</label>
                                        <input type="text" id="hero_text" name="hero_text" class="form-control" 
                                            value="<?php echo htmlspecialchars($current_content['hero_text'] ?? ''); ?>"
                                            placeholder="Contoh: Pusat Layanan dan Informasi">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="hero_subtext">Subjudul (h3):</label>
                                        <input type="text" id="hero_subtext" name="hero_subtext" class="form-control" 
                                            value="<?php echo htmlspecialchars($current_content['hero_subtext'] ?? ''); ?>"
                                            placeholder="Contoh: Pendidikan dan Kebudayaan Kabupaten Paser">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="hero_paragraph">Paragraf (p):</label>
                                        <textarea id="hero_paragraph" name="hero_paragraph" class="form-control" rows="3"
                                                placeholder="Contoh: Bersama Paser TUNTAS (Tangguh, Unggul, Transformatif, Adil, dan Sejahtera)"><?php echo htmlspecialchars($current_content['hero_paragraph'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="form-section">
                                    <h4>Gambar Slider Hero (4 Gambar)</h4>
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                        <div class="form-group">
                                            <label for="hero_image_<?php echo $i; ?>">Gambar Slider <?php echo $i; ?>:</label>
                                            <div style="display:flex; gap:8px; align-items:flex-start;">
                                                <div style="flex:1;">
                                                    <input type="text" id="hero_image_<?php echo $i; ?>" 
                                                           name="hero_image_<?php echo $i; ?>" class="form-control" 
                                                           value="<?php echo htmlspecialchars($current_content['hero_image_' . $i] ?? ''); ?>"
                                                           placeholder="URL gambar...">
                                                    <small style="color:#666;display:block;margin-top:5px;">Ukuran rekomendasi: 1920x600 px</small>
                                                </div>
                                                <button type="button" style="background:#003399;color:white;border:none;padding:8px 12px;border-radius:4px;cursor:pointer;font-weight:500;font-size:13px;white-space:nowrap;transition:all 0.2s ease;margin-top:2px;" onmouseover="this.style.background='#002280'" onmouseout="this.style.background='#003399'" onclick="openImageSelector('hero_image_<?php echo $i; ?>')">
                                                    Pilih
                                                </button>
                                            </div>
                                            <?php if (!empty($current_content['hero_image_' . $i])): ?>
                                                <div style="margin-top:10px;padding:10px;background:#f9f9f9;border:1px solid #e0e0e0;border-radius:4px;text-align:center;">
                                                    <img src="<?php echo htmlspecialchars($current_content['hero_image_' . $i]); ?>" 
                                                        alt="Slider <?php echo $i; ?>" style="max-width:100%;height:auto;max-height:120px;object-fit:cover;border-radius:4px;">
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
                                                <div style="display:flex; gap:8px; align-items:flex-start;">
                                                    <div style="flex:1;">
                                                        <input type="text" id="pimpinan_image_<?php echo $i; ?>" 
                                                               name="pimpinan_image_<?php echo $i; ?>" class="form-control" 
                                                               value="<?php echo htmlspecialchars($current_content['pimpinan_image_' . $i] ?? ''); ?>"
                                                               placeholder="URL gambar...">
                                                        <small style="color:#666;display:block;margin-top:5px;">Ukuran rekomendasi: 300x400 px</small>
                                                    </div>
                                                    <button type="button" style="background:#003399;color:white;border:none;padding:8px 12px;border-radius:4px;cursor:pointer;font-weight:500;font-size:13px;white-space:nowrap;transition:all 0.2s ease;margin-top:2px;" onmouseover="this.style.background='#002280'" onmouseout="this.style.background='#003399'" onclick="openImageSelector('pimpinan_image_<?php echo $i; ?>')">
                                                        Pilih
                                                    </button>
                                                </div>
                                               <?php if (!empty($current_content['pimpinan_image_' . $i])): ?>
                                                    <div style="margin-top:10px;padding:10px;background:#f9f9f9;border:1px solid #e0e0e0;border-radius:4px;text-align:center;">
                                                        <img src="<?php echo htmlspecialchars($current_content['pimpinan_image_' . $i]); ?>" 
                                                            alt="Pimpinan <?php echo $i; ?>" style="max-width:100%;height:auto;max-height:200px;object-fit:cover;border-radius:4px;">
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
                                    <div class="format-hint">
                                        <h5>Format Penulisan Visi (SAMA dengan Misi):</h5>
                                        <ul>
                                            <li>Gunakan tanda dash (<code>-</code>) di awal setiap poin visi</li>
                                            <li>Setiap baris akan menjadi poin visi terpisah</li>
                                            <li>Bisa ada banyak baris, semua akan ditampilkan</li>
                                            <li>Contoh format:
                                                <pre style="background:#f5f5f5;padding:10px;border-radius:4px;margin-top:5px;">
- Visi pertama disini
- Visi kedua disini  
- Visi ketiga disini
- Tambahkan poin baru dengan dash di baris baru</pre>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="visi">Teks Visi (format dengan dash):</label>
                                        <textarea id="visi" name="visi" class="form-control" rows="6" 
                                                placeholder="- Terwujudnya Paser yang Sejahtera, Berakhlak Mulia dan Berdaya Saing&#10;- Visi tambahan lainnya"><?php echo htmlspecialchars($current_content['visi'] ?? ''); ?></textarea>
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
                                <div class="form-section">
                                    <h4>Kartu Layanan (4 Kartu)</h4>
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                        <div class="form-section" style="border: 1px solid #eee; padding: 20px; margin-bottom: 25px; border-radius: 6px; background: #f9f9f9;">
                                            <h5 style="color: #003399; margin-bottom: 15px; border-bottom: 2px solid #e36159; padding-bottom: 10px;">
                                                Layanan <?php echo $i; ?>
                                            </h5>
                                            
                                            <!-- Data Kartu (Sudah Ada) -->
                                            <div class="form-group">
                                                <label for="layanan_title_<?php echo $i; ?>">Judul Kartu:</label>
                                                <input type="text" id="layanan_title_<?php echo $i; ?>" 
                                                    name="layanan_title_<?php echo $i; ?>" class="form-control" 
                                                    value="<?php echo htmlspecialchars($current_content['layanan_title_' . $i] ?? ''); ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="layanan_desc_<?php echo $i; ?>">Deskripsi Singkat (di kartu):</label>
                                                <textarea id="layanan_desc_<?php echo $i; ?>" 
                                                        name="layanan_desc_<?php echo $i; ?>" class="form-control" rows="3"
                                                        placeholder="Deskripsi singkat yang muncul di kartu..."><?php echo htmlspecialchars($current_content['layanan_desc_' . $i] ?? ''); ?></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="layanan_image_<?php echo $i; ?>">Gambar Kartu:</label>
                                                <div style="display:flex; gap:8px; align-items:flex-start;">
                                                    <div style="flex:1;">
                                                        <input type="text" id="layanan_image_<?php echo $i; ?>" 
                                                            name="layanan_image_<?php echo $i; ?>" class="form-control" 
                                                            value="<?php echo htmlspecialchars($current_content['layanan_image_' . $i] ?? ''); ?>"
                                                            placeholder="URL gambar...">
                                                        <small style="color:#666;display:block;margin-top:5px;">Ukuran rekomendasi: 400x300 px</small>
                                                    </div>
                                                    <button type="button" style="background:#003399;color:white;border:none;padding:8px 12px;border-radius:4px;cursor:pointer;font-weight:500;font-size:13px;white-space:nowrap;transition:all 0.2s ease;margin-top:2px;" onmouseover="this.style.background='#002280'" onmouseout="this.style.background='#003399'" onclick="openImageSelector('layanan_image_<?php echo $i; ?>')">
                                                        Pilih
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- ===== DATA POPUP (BARU) ===== -->
                                            <div style="margin: 20px 0; padding: 15px; background: #e3f2fd; border-radius: 4px; border-left: 4px solid #003399;">
                                                <h6 style="color: #003399; margin-top: 0;">Data untuk Popup Detail</h6>
                                                
                                                <div class="form-group">
                                                    <label for="layanan_popup_desc_<?php echo $i; ?>">Deskripsi Lengkap (di popup):</label>
                                                    <textarea id="layanan_popup_desc_<?php echo $i; ?>" 
                                                            name="layanan_popup_desc_<?php echo $i; ?>" class="form-control" rows="4"
                                                            placeholder="Deskripsi lengkap yang akan muncul di popup..."><?php echo htmlspecialchars($current_content['layanan_popup_desc_' . $i] ?? ''); ?></textarea>
                                                </div>
                                                
                                                <!-- Di bagian textarea Cara Kerja: -->
                                            <div class="form-group">
                                                <label for="layanan_cara_kerja_<?php echo $i; ?>">Cara Kerja Layanan:</label>
                                                <div class="format-hint">
                                                    <small>Contoh format:</small>
                                                    <pre style="background:#f5f5f5;padding:10px;border-radius:4px;margin-top:5px;font-size:12px;">
Pemohon datang ke kantor Dinas Pendidikan dengan membawa dokumen asli
Mengisi formulir permohonan legalisir
Petugas memverifikasi dokumen asli
Dokumen dicap dan ditandatangani oleh pejabat berwenang
Pemohon menerima dokumen yang telah dilegalisir</pre>
                                                    <small>Setiap baris akan menjadi poin terpisah dengan nomor urut</small>
                                                </div>
                                                <textarea id="layanan_cara_kerja_<?php echo $i; ?>" 
                                                        name="layanan_cara_kerja_<?php echo $i; ?>" class="form-control" rows="6"
                                                        placeholder="Masukkan langkah-langkah cara kerja layanan..."><?php echo htmlspecialchars($current_content['layanan_cara_kerja_' . $i] ?? ''); ?></textarea>
                                            </div>

                                            <!-- Di bagian textarea Persyaratan: -->
                                            <div class="form-group">
                                                <label for="layanan_persyaratan_<?php echo $i; ?>">Persyaratan:</label>
                                                <div class="format-hint">
                                                    <small>Contoh format:</small>
                                                    <pre style="background:#f5f5f5;padding:10px;border-radius:4px;margin-top:5px;font-size:12px;">
Ijazah asli yang akan dilegalisir
KTP asli dan fotokopi
Formulir permohonan yang telah diisi
Bukti pembayaran (jika ada)</pre>
                                                    <small>Setiap baris akan menjadi poin terpisah dengan bullet point</small>
                                                </div>
                                                <textarea id="layanan_persyaratan_<?php echo $i; ?>" 
                                                        name="layanan_persyaratan_<?php echo $i; ?>" class="form-control" rows="6"
                                                        placeholder="Masukkan daftar persyaratan..."><?php echo htmlspecialchars($current_content['layanan_persyaratan_' . $i] ?? ''); ?></textarea>
                                            </div>
                                            <!-- ===== AKHIR DATA POPUP ===== -->
                                            
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($current_file === 'layanan'): ?>
                            <?php if ($current_section === 'layanan'): ?>
                                <!-- CONTOH FORMAT DATA POPUP DETAIL (READ-ONLY) -->
                                <div class="form-section" style="background:#f0f8ff;border:2px solid #003399;padding:25px;margin-bottom:30px;border-radius:8px;">
                                    <h4 style="color:#003399;margin-top:0;">📋 Contoh: Format Data Popup Detail</h4>
                                    <p style="color:#666;margin-bottom:20px;">Referensi format untuk mengedit data popup di bagian "Daftar Layanan" di bawah</p>
                                    
                                    <div style="margin-bottom:20px;">
                                        <h5 style="color:#003399;margin-bottom:10px;">1. Deskripsi Lengkap (popup)</h5>
                                        <div style="background:white;padding:15px;border-radius:4px;border-left:4px solid #2196f3;font-family:monospace;font-size:13px;color:#333;line-height:1.6;">
                                            Layanan legalisir ijazah dan dokumen kelulusan untuk keperluan administrasi seperti melamar pekerjaan, melanjutkan pendidikan, atau keperluan lainnya
                                        </div>
                                    </div>

                                    <div style="margin-bottom:20px;">
                                        <h5 style="color:#003399;margin-bottom:10px;">2. Cara Kerja (satu baris = satu langkah)</h5>
                                        <div style="background:white;padding:15px;border-radius:4px;border-left:4px solid #2196f3;font-family:monospace;font-size:13px;color:#333;line-height:1.8;white-space:pre-wrap;">
Pemohon datang ke kantor Dinas Pendidikan dengan membawa dokumen asli
Mengisi formulir permohonan legalisir
Petugas memverifikasi dokumen asli
Dokumen dicap dan ditandatangani oleh pejabat berwenang
Pemohon menerima dokumen yang telah dilegalisir
                                        </div>
                                    </div>

                                    <div style="margin-bottom:20px;">
                                        <h5 style="color:#003399;margin-bottom:10px;">3. Persyaratan (satu baris = satu item)</h5>
                                        <div style="background:white;padding:15px;border-radius:4px;border-left:4px solid #2196f3;font-family:monospace;font-size:13px;color:#333;line-height:1.8;white-space:pre-wrap;">
Ijazah asli yang akan dilegalisir
KTP asli dan fotokopi
Formulir permohonan yang telah diisi
Bukti pembayaran (jika ada)
                                        </div>
                                    </div>

                                    <div style="background:#fff3cd;border:1px solid #ffc107;padding:12px;border-radius:4px;margin-top:15px;">
                                        <strong style="color:#856404;">💡 Tips:</strong> Edit data popup di bagian <strong>"Daftar Layanan"</strong> di bawah. Gunakan format contoh di atas sebagai referensi.
                                    </div>
                                </div>

                                <!-- DAFTAR LAYANAN EDITOR -->
                                <div class="form-section">
                                    <h4>Daftar Layanan (editable)</h4>
                                    <p style="color:#666">Edit data kartu layanan dan popup detail untuk setiap layanan</p>
                                    <?php
                                    $layanan_list = $content_data['layanan']['layanan_data'] ?? [];
                                    $count = max(1, count($layanan_list));
                                    ?>
                                    <input type="hidden" name="layanan_count" value="<?php echo $count; ?>">
                                    <?php for ($i = 0; $i < $count; $i++):
                                        $idx = $i + 1;
                                        $item = $layanan_list[$i] ?? [];
                                    ?>
                                    <div class="form-section" style="border:1px solid #eee; padding:20px; margin-bottom:20px; border-radius:8px; background:#f4f7f6;">
                                        <h5 style="color:#003399; margin-bottom: 20px; font-weight:700; font-size:1.2em;">
                                            <i class="fas fa-cog"></i> Edit Layanan #<?php echo $idx; ?>
                                        </h5>

                                        <div style="background: white; padding: 25px; border-radius: 6px; border: 1px solid #e0e0e0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 20px;">
                                            <h6 style="color: #444; margin-top: 0; margin-bottom: 20px; font-weight: 700; border-bottom: 2px solid #eee; padding-bottom: 10px; font-size: 1.1em;">
                                                1. Informasi Teks & Detail Popup
                                            </h6>

                                            <div class="form-group">
                                                <label for="layanan_title_<?php echo $idx; ?>">Judul Layanan</label>
                                                <input type="text" id="layanan_title_<?php echo $idx; ?>" name="layanan_title_<?php echo $idx; ?>" class="form-control" value="<?php echo htmlspecialchars($item['title'] ?? ''); ?>" style="font-weight:bold;">
                                            </div>

                                            <div class="form-group">
                                                <label for="layanan_subtitle_<?php echo $idx; ?>">Subjudul (Pendek)</label>
                                                <input type="text" id="layanan_subtitle_<?php echo $idx; ?>" name="layanan_subtitle_<?php echo $idx; ?>" class="form-control" value="<?php echo htmlspecialchars($item['subtitle'] ?? ''); ?>">
                                            </div>

                                            <div class="form-group">
                                                <label for="layanan_description_<?php echo $idx; ?>">Deskripsi Singkat (Tampil di Kartu Depan)</label>
                                                <textarea id="layanan_description_<?php echo $idx; ?>" name="layanan_description_<?php echo $idx; ?>" class="form-control" rows="2"><?php echo htmlspecialchars($item['description'] ?? ''); ?></textarea>
                                            </div>

                                            <div style="margin-top: 25px; padding-top: 20px; border-top: 2px dashed #dde2e5; background-color: #fcfcfc; padding: 20px; border-radius: 4px;">
                                                <label style="color:#003399; font-weight:bold; margin-bottom:15px; display:block; font-size: 1.05em;">
                                                    <i class="fas fa-layer-group"></i> Detail Popup
                                                </label>

                                                <div class="form-group">
                                                    <label for="layanan_popup_desc_<?php echo $idx; ?>">Deskripsi Lengkap (Popup):</label>
                                                    <textarea id="layanan_popup_desc_<?php echo $idx; ?>" name="layanan_popup_desc_<?php echo $idx; ?>" class="form-control" rows="3" placeholder="Deskripsi detail..."><?php echo htmlspecialchars($item['popup_desc'] ?? ''); ?></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label for="layanan_cara_kerja_<?php echo $idx; ?>">Cara Kerja / Prosedur:</label>
                                                    <textarea id="layanan_cara_kerja_<?php echo $idx; ?>" name="layanan_cara_kerja_<?php echo $idx; ?>" class="form-control" rows="5" placeholder="1. Langkah satu&#10;2. Langkah dua..."><?php 
                                                        $cara = $item['cara_kerja'] ?? ($item['caraKerja'] ?? []); 
                                                        echo htmlspecialchars(is_array($cara) ? implode("\n", $cara) : $cara); 
                                                    ?></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label for="layanan_persyaratan_<?php echo $idx; ?>">Persyaratan Dokumen:</label>
                                                    <textarea id="layanan_persyaratan_<?php echo $idx; ?>" name="layanan_persyaratan_<?php echo $idx; ?>" class="form-control" rows="5" placeholder="- Syarat satu&#10;- Syarat dua..."><?php 
                                                        $pers = $item['persyaratan'] ?? []; 
                                                        echo htmlspecialchars(is_array($pers) ? implode("\n", $pers) : $pers); 
                                                    ?></textarea>
                                                </div>
                                            </div>
                                            </div>

                                        <div style="background: white; padding: 25px; border-radius: 6px; border: 1px solid #e0e0e0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                            <h6 style="font-size: 1.1em; color: #444; margin-top: 0; margin-bottom: 20px; font-weight: 700; border-bottom: 2px solid #eee; padding-bottom: 10px;">
                                                2. Visual (Icon/Gambar)
                                            </h6>
                                            <div class="form-group" style="margin-bottom:0;">
                                                <label for="layanan_icon_<?php echo $idx; ?>">Icon / Gambar Layanan</label>
                                                <div style="display:flex; gap:10px; align-items:flex-start;">
                                                    <div style="flex:1;">
                                                        <input type="text" id="layanan_icon_<?php echo $idx; ?>" name="layanan_icon_<?php echo $idx; ?>" class="form-control" value="<?php echo htmlspecialchars($item['icon'] ?? ''); ?>" placeholder="URL icon atau fa-icon class (contoh: fas fa-scroll)">
                                                        <small style="color:#666;display:block;margin-top:5px;">Mendukung FontAwesome class atau URL Gambar</small>
                                                    </div>
                                                    <button type="button" class="btn btn-secondary" style="background:#003399; border:none; color:white; padding: 10px 15px;" onclick="openIconUploader(<?php echo $idx; ?>)">
                                                        <i class="fas fa-upload"></i> Upload
                                                    </button>
                                                </div>
                                                
                                                <?php if (!empty($item['icon'])): ?>
                                                <div style="margin-top:15px; padding:15px; background:#f8f9fa; border:1px solid #dee2e6; border-radius:4px; text-align:center; min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                                    <?php 
                                                    $icon = $item['icon'];
                                                    if (strpos($icon, '/') !== false || strpos($icon, '.') !== false) {
                                                        echo '<img src="' . htmlspecialchars($icon) . '" alt="Icon preview" style="max-width:80px; max-height:80px; object-fit:contain;">';
                                                    } else {
                                                        echo '<i class="' . htmlspecialchars($icon) . '" style="font-size:3.5em; color:#003399;"></i>';
                                                    }
                                                    ?>
                                                </div>
                                                <div style="text-align:center; margin-top:5px; font-size:12px; color:#666;">Preview Icon</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            
                            <?php endif; ?>
                            <?php if ($current_section === 'contact'): ?>
                                <?php
                                // Load data saat ini untuk form kontak
                                $contact_data = $content_data['layanan']['contact_section'] ?? [];
                                $cur_contact_title = $contact_data['title'] ?? 'Butuh Bantuan Administrasi Cepat?';
                                $cur_contact_subtitle = $contact_data['subtitle'] ?? 'Isi formulir di bawah ini dan Admin akan menghubungi Anda dalam 2x24 jam.';
                                
                                // Siapkan data opsi untuk textarea (gabungkan array jadi string per baris)
                                $cur_contact_options = isset($contact_data['service_options']) ? implode("\n", $contact_data['service_options']) : '';
                                ?>

                                <div class="form-section">
                                    <h4>Pengaturan Form Kontak</h4>
                                    <div class="format-hint">
                                        <h5>Info:</h5>
                                        <p>Bagian ini mengatur teks judul, subjudul, dan pilihan yang muncul di dropdown "Jenis Layanan" pada form kontak di halaman depan.</p>
                                    </div>

                                    <div class="form-group" style="margin-top: 20px;">
                                        <label for="contact_title">Judul Form (h2):</label>
                                        <input type="text" id="contact_title" name="contact_title" class="form-control" 
                                               value="<?php echo htmlspecialchars($cur_contact_title); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="contact_subtitle">Subjudul Form (p):</label>
                                        <textarea id="contact_subtitle" name="contact_subtitle" class="form-control" rows="3"><?php echo htmlspecialchars($cur_contact_subtitle); ?></textarea>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h4>Opsi Dropdown "Pilih Jenis Layanan"</h4>
                                    <div class="format-hint" style="background:#fff3cd;border-left-color:#ffc107;">
                                        <strong style="color:#856404;">💡 Cara Penggunaan:</strong>
                                        <ul>
                                            <li>Masukkan satu jenis layanan per baris.</li>
                                            <li>Jika diisi, daftar ini akan menggantikan daftar layanan default di dropdown form kontak.</li>
                                            <li>Jika dikosongkan, dropdown akan otomatis mengambil data dari "Daftar Layanan" utama.</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="contact_options">Daftar Opsi Kustom (Satu per baris):</label>
                                        <textarea id="contact_options" name="contact_options" class="form-control" rows="8" 
                                                  placeholder="Contoh:
Legalisir Ijazah
Pindah Sekolah
Konsultasi Umum"><?php echo htmlspecialchars($cur_contact_options); ?></textarea>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php if ($current_file === 'faq'): ?>
                            <div class="form-section">
                                <h4>Kelola FAQ</h4>
                                <p style="color: #666; margin-bottom: 20px;">Edit pertanyaan dan jawaban untuk setiap kategori. Hapus isi pertanyaan dan jawaban untuk menghapus item.</p>
                                
                                <?php
                                // Debug: Load fresh data untuk FAQ
                                if ($current_file === 'faq') {
                                    $content_data = loadContentData();
                                }
                                
                                $faq_data = isset($content_data['faq']) ? $content_data['faq'] : [];
                                
                                $categories = [
                                    'informasi_umum' => 'Informasi Umum',
                                    'layanan_kesiswaan' => 'Layanan Kesiswaan',
                                    'guru_tenaga_kependidikan' => 'Guru & Tenaga Kependidikan',
                                    'ppdb' => 'PPDB'
                                ];
                                
                                foreach ($categories as $cat_key => $cat_name):
                                    $faqs = isset($faq_data[$cat_key]) && is_array($faq_data[$cat_key]) ? $faq_data[$cat_key] : [];
                                ?>
                                    <div style="margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
                                        <h5 style="color: #003399; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #e36159; padding-bottom: 10px;"><?php echo $cat_name; ?></h5>
                                        
                                        <?php 
                                        // Tampilkan existing FAQs + 2 empty rows untuk entry baru
                                        $total_rows = max(2, count($faqs) + 1);
                                        for ($i = 0; $i < $total_rows; $i++): 
                                        ?>
                                            <?php $faq = $faqs[$i] ?? ['question' => '', 'answer' => '']; ?>
                                            <div style="margin-bottom: 20px; padding: 15px; background: white; border-radius: 6px; border: 1px solid #e0e0e0;">
                                                <div style="color: #666; font-size: 12px; margin-bottom: 10px;">Item <?php echo $i+1; ?></div>
                                                <div class="form-group">
                                                    <label style="font-weight: 600; color: #333;">Pertanyaan:</label>
                                                    <input type="text" name="faq[<?php echo $cat_key; ?>][<?php echo $i; ?>][question]" 
                                                        class="form-control" 
                                                        value="<?php echo htmlspecialchars($faq['question']); ?>"
                                                        placeholder="Masukkan pertanyaan...">
                                                </div>
                                                <div class="form-group">
                                                    <label style="font-weight: 600; color: #333;">Jawaban:</label>
                                                    <textarea name="faq[<?php echo $cat_key; ?>][<?php echo $i; ?>][answer]" 
                                                            class="form-control" rows="4"
                                                            placeholder="Masukkan jawaban (bisa menggunakan HTML tags)..."><?php echo htmlspecialchars($faq['answer']); ?></textarea>
                                                    <small style="color: #999; display: block; margin-top: 5px;">Anda bisa menggunakan tag HTML seperti &lt;strong&gt;, &lt;a&gt;, &lt;ol&gt;, &lt;li&gt;, dsb.</small>
                                                </div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                <?php endforeach; ?>
                                
                                <div class="form-actions">
                                    <button type="button" onclick="if(confirm('Batalkan perubahan?')) window.location.replace('admin-pengaturan.php?file=index&section=hero')" class="btn btn-danger" style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; margin-right: 10px;">
                                        <span style="margin-right: 5px;">✕</span> Batalkan
                                    </button>
                                    <button type="button" onclick="if(confirm('Reset semua form ke nilai awal?')) document.querySelector('.editor-form').reset()" class="btn btn-secondary" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; margin-right: 10px;">
                                        <span style="margin-right: 5px;">↻</span> Reset
                                    </button>
                                    <button type="button" onclick="submitFAQForm()" class="btn btn-success" style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                                        <span style="margin-right: 5px;">✓</span> Simpan Semua FAQ
                                    </button>
                                </div>
                            </div>
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
                        <?php endif; ?>
                        <?php if ($current_file === 'kontak'): ?>
                            <div class="form-section">
                                <h4>Informasi Alamat</h4>
                                <div class="form-group">
                                    <label for="address">Alamat Kantor:</label>
                                    <textarea id="address" name="address" class="form-control" rows="4"><?php echo htmlspecialchars($current_content['address'] ?? ''); ?></textarea>
                                    <small style="color:#666">Gunakan Enter untuk baris baru.</small>
                                </div>
                            </div>

                            <div class="form-section">
                                <h4>Informasi Kontak</h4>
                                <div class="form-group">
                                    <label for="phone">Nomor Telepon:</label>
                                    <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($current_content['phone'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="fax">Nomor Fax:</label>
                                    <input type="text" id="fax" name="fax" class="form-control" value="<?php echo htmlspecialchars($current_content['fax'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="email">Alamat Email:</label>
                                    <input type="text" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($current_content['email'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h4>Jam Operasional</h4>
                                <div class="form-group">
                                    <label for="hours_weekdays">Senin - Kamis:</label>
                                    <input type="text" id="hours_weekdays" name="hours_weekdays" class="form-control" value="<?php echo htmlspecialchars($current_content['hours_weekdays'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="hours_friday">Jumat:</label>
                                    <input type="text" id="hours_friday" name="hours_friday" class="form-control" value="<?php echo htmlspecialchars($current_content['hours_friday'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-section">
                                <h4>Google Maps</h4>
                                <div class="format-hint">
                                    <h5>Cara mengambil link:</h5>
                                    <ul>
                                        <li>Buka Google Maps, cari lokasi kantor.</li>
                                        <li>Klik tombol <strong>Bagikan (Share)</strong> -> <strong>Sematkan Peta (Embed a map)</strong>.</li>
                                        <li>Copy hanya bagian url di dalam tanda kutip <code>src="..."</code>.</li>
                                    </ul>
                                </div>
                                <div class="form-group">
                                    <label for="map_embed_url">Link Embed Map (src):</label>
                                    <textarea id="map_embed_url" name="map_embed_url" class="form-control" rows="3" placeholder="Contoh: https://www.google.com/maps/embed?pb=..."><?php echo htmlspecialchars($current_content['map_embed_url'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($current_file === 'profil'): ?>
                            
                            <?php if ($current_section === 'hero'): ?>
                                <div class="form-section">
                                    <h4>Hero Section</h4>
                                    <div class="form-group">
                                        <label for="hero_title">Judul Utama:</label>
                                        <input type="text" id="hero_title" name="hero_title" class="form-control" value="<?php echo htmlspecialchars($current_content['hero_title'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="hero_subtitle">Subjudul:</label>
                                        <input type="text" id="hero_subtitle" name="hero_subtitle" class="form-control" value="<?php echo htmlspecialchars($current_content['hero_subtitle'] ?? ''); ?>">
                                    </div>
                                </div>

                            <?php elseif ($current_section === 'visi-misi'): ?>
                                <div class="form-section">
                                    <h4>Visi</h4>
                                    <div class="form-group">
                                        <textarea id="visi" name="visi" class="form-control" rows="3"><?php echo htmlspecialchars($current_content['visi'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-section">
                                    <h4>Misi</h4>
                                    <div class="format-hint">Gunakan tanda dash (-) untuk setiap poin.</div>
                                    <div class="form-group">
                                        <textarea id="misi" name="misi" class="form-control" rows="8"><?php echo htmlspecialchars($current_content['misi'] ?? ''); ?></textarea>
                                    </div>
                                </div>

                            <?php elseif ($current_section === 'tupoksi'): ?>
                                <div class="form-section">
                                    <h4>Tugas Pokok & Fungsi</h4>
                                    <div class="format-hint">Gunakan tanda dash (-) untuk setiap poin tupoksi.</div>
                                    <div class="form-group">
                                        <textarea id="tupoksi" name="tupoksi" class="form-control" rows="10"><?php echo htmlspecialchars($current_content['tupoksi'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php endif; ?>
                        
                        <?php if ($current_file !== 'faq'): ?>
                        <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
                            <button type="button" onclick="if(confirm('Batalkan perubahan?')) window.location.replace('admin-pengaturan.php?file=index&section=hero')" class="btn btn-danger" style="background: #dc3545; color: white; border: none; padding: 10px 18px; border-radius: 4px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; font-size: 14px;">
                                <span style="margin-right: 6px;">✕</span> Batalkan
                            </button>
                            <button type="button" onclick="if(confirm('Reset semua form ke nilai awal?')) document.querySelector('.editor-form').reset()" class="btn btn-secondary" style="background: #6c757d; color: white; border: none; padding: 10px 18px; border-radius: 4px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; font-size: 14px;">
                                <span style="margin-right: 6px;">↻</span> Reset
                            </button>
                            <button type="submit" name="save_content" class="btn btn-success" style="background: #28a745; color: white; border: none; padding: 10px 18px; border-radius: 4px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; font-size: 14px;">
                                <span style="margin-right: 6px;">✓</span> Simpan Perubahan
                            </button>
                        </div>
                        <?php endif; ?>
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

                    <!-- Modal untuk upload icon layanan -->
                    <div id="iconUploaderModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
                        <div style="background: white; padding: 30px; border-radius: 8px; width: 90%; max-width: 500px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <h3>Upload Icon Layanan</h3>
                                <button onclick="closeIconUploader()" style="background: none; border: none; font-size: 24px; cursor: pointer;">×</button>
                            </div>
                            
                            <div style="background:#fff3cd;border:1px solid #ffc107;padding:12px;border-radius:4px;margin-bottom:20px;">
                                <strong style="color:#856404;">📐 Persyaratan Icon:</strong>
                                <ul style="margin:8px 0 0 0;padding-left:20px;font-size:14px;">
                                    <li>Ukuran minimal: 32x32 px</li>
                                    <li>Ukuran maksimal: 512x512 px</li>
                                    <li>Format: JPG, PNG, GIF, atau SVG</li>
                                </ul>
                            </div>
                            
                            <!-- Upload Area -->
                            <div id="uploadAreaIcon" style="border:2px dashed #003399;border-radius:8px;padding:40px 20px;text-align:center;margin-bottom:20px;cursor:pointer;background:#f8faff;transition:all 0.3s ease;" onclick="document.getElementById('iconFileInput').click()" onmouseover="this.style.background='#eef4ff';this.style.borderColor='#002280';this.style.boxShadow='0 2px 8px rgba(0,51,153,0.2)'" onmouseout="this.style.background='#f8faff';this.style.borderColor='#003399';this.style.boxShadow='none'">
                                <div style="color:#003399;margin-bottom:10px;"><i class="fa-solid fa-up-long" style="font-size:32px;"></i></div>
                                <h4 style="margin:0 0 5px 0;color:#003399;font-size:16px;">Klik untuk memilih file</h4>
                                <p style="margin:0;color:#999;font-size:13px;">atau seret file ke sini</p>
                            </div>
                            
                            <input type="file" id="iconFileInput" accept="image/jpeg,image/png,image/gif,image/svg+xml" style="display: none;">
                            
                            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                <button onclick="closeIconUploader()" style="background:white;border:1px solid #ddd;padding:8px 16px;border-radius:4px;cursor:pointer;font-weight:500;color:#666;font-size:13px;transition:all 0.2s ease;" onmouseover="this.style.background='#f5f5f5';this.style.borderColor='#999'" onmouseout="this.style.background='white';this.style.borderColor='#ddd'">
                                    Batal
                                </button>
                                <button onclick="uploadIconFromModal()" style="background:#003399;color:white;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;font-weight:500;font-size:13px;transition:all 0.2s ease;" onmouseover="this.style.background='#002280'" onmouseout="this.style.background='#003399'">
                                    Upload
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dashboard-footer" style="margin-top: 25px; text-align: center; color: #999; font-size: 0.8rem;">
                <p>© <?php echo date('Y'); ?> Admin Panel - Disdikbud Paser</p>
            </div>
        </main>
    </div>

    <script>
        let currentImageField = '';
        
        // Fungsi untuk submit FAQ form
        function submitFAQForm() {
            const form = document.getElementById('editorForm');
            const formData = new FormData(form);
            
            // Tambahkan flag untuk save_faq
            formData.append('save_faq', '1');
            
            fetch('admin-pengaturan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Cek jika ada success parameter di response (redirect terjadi jika berhasil)
                if (data.includes('success=1') || data.includes('FAQ berhasil')) {
                    window.location.href = 'admin-pengaturan.php?file=faq&section=faq-content&success=1';
                } else {
                    alert('FAQ berhasil disimpan!');
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menyimpan FAQ. Silakan coba lagi.');
            });
        }
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
        
        // Variabel untuk icon uploader
        let currentIconField = '';
        
        // Fungsi untuk buka icon uploader modal
        function openIconUploader(layananIdx) {
            currentIconField = 'layanan_icon_' + layananIdx;
            document.getElementById('iconUploaderModal').style.display = 'flex';
        }
        
        // Fungsi untuk tutup icon uploader modal
        function closeIconUploader() {
            document.getElementById('iconUploaderModal').style.display = 'none';
            currentIconField = '';
        }
        
        // Handle icon upload dari modal
        function uploadIconFromModal() {
            const fileInput = document.getElementById('iconFileInput');
            const formData = new FormData();
            
            if (fileInput.files.length === 0) {
                alert('Silakan pilih file icon terlebih dahulu');
                return;
            }
            
            formData.append('upload_icon', fileInput.files[0]);
            
            // Kirim ke server
            fetch('admin-pengaturan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Extract URL dari response
                const urlMatch = data.match(/URL: (assets\/uploads\/icons\/[^\s<]+)/);
                if (urlMatch && urlMatch[1]) {
                    const iconUrl = urlMatch[1];
                    document.getElementById(currentIconField).value = iconUrl;
                    
                    // Tampilkan preview
                    const previewDiv = document.getElementById(currentIconField).parentElement.querySelector('[style*="margin-top:10px"]');
                    if (previewDiv) {
                        previewDiv.innerHTML = '<img src="' + iconUrl + '" alt="Icon preview" style="max-width:100px;max-height:100px;object-fit:contain;">';
                    }
                    
                    closeIconUploader();
                    alert('Icon berhasil diupload! Jangan lupa klik "Simpan Perubahan" untuk menerapkan.');
                } else if (data.includes('berhasil diupload')) {
                    closeIconUploader();
                    location.reload(); // Reload untuk refresh
                } else {
                    const errorMatch = data.match(/Maaf,([^<]*)/);
                    alert(errorMatch ? 'Error:' + errorMatch[1] : 'Gagal mengupload icon');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal mengupload icon. Silakan coba lagi.');
            });
        }
        
        // Tutup modal jika klik di luar untuk icon uploader
        document.addEventListener('DOMContentLoaded', function() {
            const iconModal = document.getElementById('iconUploaderModal');
            if (iconModal) {
                iconModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeIconUploader();
                    }
                });
            }
        });
        
        // Fungsi untuk menutup modal
        function closeImageSelector() {
            document.getElementById('imageSelectorModal').style.display = 'none';
            currentImageField = '';
        }
        
        // Fungsi untuk memilih gambar untuk field tertentu
        function selectImageForField(imageUrl) {
            if (currentImageField) {
                document.getElementById(currentImageField).value = imageUrl;
                closeImageSelector();;
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
        // Validasi format visi saat submit
        const visiTextarea = document.getElementById('visi');
        if (visiTextarea) {
            // Auto-format visi saat mengetik
            visiTextarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    // Auto-add dash pada baris baru
                    setTimeout(() => {
                        const currentPos = this.selectionStart;
                        this.value = this.value.substr(0, currentPos) + '- ' + this.value.substr(currentPos);
                        this.selectionStart = currentPos + 2;
                        this.selectionEnd = currentPos + 2;
                    }, 0);
                }
            });
            
            document.querySelector('.editor-form').addEventListener('submit', function(e) {
                const lines = visiTextarea.value.split('\n');
                let hasValidFormat = false;
                
                // Cek apakah ada minimal satu baris dengan dash
                for (let i = 0; i < lines.length; i++) {
                    const line = lines[i].trim();
                    if (line && line !== '' && line.startsWith('-')) {
                        hasValidFormat = true;
                        break;
                    }
                }
                
                if (!hasValidFormat) {
                    e.preventDefault();
                    alert('Format visi tidak valid. Minimal satu baris harus dimulai dengan dash (-).');
                    visiTextarea.focus();
                    visiTextarea.value = '- ' + visiTextarea.value;
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

<script>
    // Handle image errors untuk semua preview gambar
    document.addEventListener('DOMContentLoaded', function() {
        const previewImages = document.querySelectorAll('.image-preview img');
        
        previewImages.forEach(img => {
            // Hapus atribut onerror yang bermasalah
            img.removeAttribute('onerror');
            
            // Tambahkan event listener untuk error
            img.addEventListener('error', function() {
                this.style.display = 'none';
                
                // Cari atau buat elemen error message
                let errorElement = this.nextElementSibling;
                if (!errorElement || !errorElement.classList.contains('image-error')) {
                    errorElement = document.createElement('div');
                    errorElement.className = 'image-error';
                    errorElement.innerHTML = '<p style="color:#dc3545; padding:10px; text-align:center;">Gambar tidak ditemukan</p>';
                    this.parentNode.insertBefore(errorElement, this.nextSibling);
                }
                errorElement.style.display = 'block';
            });
            
            // Reset jika gambar berhasil dimuat
            img.addEventListener('load', function() {
                const errorElement = this.nextElementSibling;
                if (errorElement && errorElement.classList.contains('image-error')) {
                    errorElement.style.display = 'none';
                }
                this.style.display = 'block';
            });
        });
    });
</script>
</html>