<?php
// functions_content.php

require_once 'config.php';


// Pastikan $pdo tersedia
global $pdo;

// Tambahkan ini untuk debug
function debugLog($message) {
    error_log(date('Y-m-d H:i:s') . ' - ' . $message);
}

/**
 * Ambil data konten dari database
 */
function ambilKonten($section_key, $content_type) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT content_data FROM konten_website 
                               WHERE section_key = ? AND content_type = ?");
        $stmt->execute([$section_key, $content_type]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && !empty($result['content_data'])) {
            $decoded = json_decode($result['content_data'], true);
            return $decoded !== null ? $decoded : $result['content_data'];
        }
        return null;
    } catch(PDOException $e) {
        error_log("Error ambil konten: " . $e->getMessage());
        return null;
    }
}

/**
 * Simpan data konten ke database
 */
function simpanKonten($section_key, $content_type, $data) {
    global $pdo;
    
    try {
        $json_data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
        $stmt = $pdo->prepare("INSERT INTO konten_website (section_key, content_type, content_data) 
                               VALUES (?, ?, ?)
                               ON DUPLICATE KEY UPDATE 
                               content_data = VALUES(content_data),
                               updated_at = CURRENT_TIMESTAMP");
        
        return $stmt->execute([$section_key, $content_type, $json_data]);
    } catch(PDOException $e) {
        error_log("Error simpan konten: " . $e->getMessage());
        return false;
    }
}

/**
 * Ambil semua data pimpinan dari database
 */
function ambilDataPimpinan() {
    $data = ambilKonten('index', 'pimpinan_data');
    
    // Jika tidak ada di database, kembalikan default
    if (!$data) {
        return [
            [
                'nama' => 'Dr. Fahmi Fadli',
                'jabatan' => 'Bupati Paser',
                'foto' => 'assets/Bupati_Paser_Fahmi_Fadli.jpg'
            ],
            [
                'nama' => 'H. Ikhwan Antasari, S.Sos.',
                'jabatan' => 'Wakil Bupati Paser',
                'foto' => 'assets/Wakil_Bupati_Paser_Ikhwan_Antasari.jpg'
            ],
            [
                'nama' => 'Drs. Katsul Wijaya, M.Si',
                'jabatan' => 'Sekretaris Daerah',
                'foto' => 'assets/sekda-paser-2024.jpg'
            ]
        ];
    }
    
    return $data;
}

/**
 * Ambil data visi misi dari database
 */
function ambilVisiMisi() {
    $data = ambilKonten('index', 'visi_misi');
    
    // Jika tidak ada di database, kembalikan default
    if (!$data) {
        return [
            'visi' => 'Terwujudnya Paser yang Sejahtera, Berakhlak Mulia dan Berdaya Saing',
            'misi' => [
                'Mewujudkan Sumber Daya Manusia yang handal dan berdaya saing melalui Peningkatan Mutu Pendidikan, Derajat Kesehatan serta Kesejahteraan Sosial',
                'Mewujudkan tata kelola pemerintahan yang baik (Good Governance) yang bersih, efektif, efesien, transparan dan akuntabel berbasis Teknologi Informasi dan Komunikasi',
                'Mewujudkan Pembangunan yang merata dan berkesinambungan yang berwawasan lingkungan',
                'Meningkatkan kemandirian ekonomi daerah dan masyarakat berbasis potensi lokal',
                'Menciptakan Kota yang Aman, Nyaman, dan Kondusif'
            ]
        ];
    }
    
    return $data;
}

/**
 * Ambil data layanan dari database
 */
function ambilDataLayanan() {
    $data = ambilKonten('index', 'layanan_data');
    
    // Jika tidak ada di database, kembalikan default
    if (!$data) {
        return [
            [
                'id' => 'legalisir-ijazah',
                'title' => 'Legalisir Ijazah/Dokumen Kelulusan',
                'desc' => 'Layanan legalisir ijazah dan dokumen kelulusan untuk berbagai keperluan administrasi.',
                'icon' => 'assets/legalisir.png'
            ],
            [
                'id' => 'surat-mutasi',
                'title' => 'Surat Keterangan Pindah Sekolah',
                'desc' => 'Layanan penerbitan surat mutasi untuk siswa yang akan berpindah sekolah.',
                'icon' => 'assets/document.png'
            ],
            [
                'id' => 'tunjangan-guru',
                'title' => 'Pengusulan Tunjangan Profesi Guru',
                'desc' => 'Layanan pengusulan tunjangan profesi guru bagi guru yang memenuhi syarat.',
                'icon' => 'assets/tunjangan.png'
            ],
            [
                'id' => 'izin-pendirian',
                'title' => 'Izin Pendirian Satuan Pendidikan',
                'desc' => 'Layanan perizinan pendirian PAUD, SD, SMP, dan Lembaga Kursus.',
                'icon' => 'assets/institusi.png'
            ]
        ];
    }
    
    return $data;
}

/**
 * Ambil data hero dari database
 */
function ambilDataHero() {
    $data = ambilKonten('index', 'hero_data');
    
    // Jika tidak ada di database, kembalikan default
    if (!$data) {
        return [
            'hero_text' => 'Pusat Layanan dan Informasi',
            'hero_subtext' => 'Bersama Paser TUNTAS (Tangguh, Unggul, Transformatif, Adil, dan Sejahtera)',
            'hero_images' => [
                'https://upload.wikimedia.org/wikipedia/commons/c/c1/Pemandangan_Tanah_Grogot.jpg?20100504072853',
                'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fc/Tari_Ronggeng_Paser.JPG/1200px-Tari_Ronggeng_Paser.JPG',
                'https://images.unsplash.com/photo-1523580494863-6f3031224c94?ixlib=rb-4.0.1&auto=format&fit=crop&w=1350&q=80',
                'https://media.suara.com/images/2024/12/30/68716-ilustrasi-wisata-di-kabupaten-paser-kaltim.jpg'
            ]
        ];
    }
    
    return $data;
}

/**
 * Ambil gambar konten dari database
 */
function ambilGambarKonten($section_key, $gambar_type) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM gambar_konten 
                               WHERE section_key = ? AND gambar_type = ? 
                               ORDER BY display_order");
        $stmt->execute([$section_key, $gambar_type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error ambil gambar konten: " . $e->getMessage());
        return [];
    }
}

/**
 * Simpan gambar konten ke database
 */
function simpanGambarKonten($section_key, $gambar_type, $gambar_url, $display_order = 0) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO gambar_konten (section_key, gambar_type, gambar_url, display_order) 
                               VALUES (?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE 
                               gambar_url = VALUES(gambar_url),
                               display_order = VALUES(display_order)");
        
        return $stmt->execute([$section_key, $gambar_type, $gambar_url, $display_order]);
    } catch(PDOException $e) {
        error_log("Error simpan gambar: " . $e->getMessage());
        return false;
    }
}

/**
 * Hapus semua gambar untuk section tertentu
 */
function hapusGambarKontenBySection($section_key, $gambar_type) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM gambar_konten 
                               WHERE section_key = ? AND gambar_type = ?");
        return $stmt->execute([$section_key, $gambar_type]);
    } catch(PDOException $e) {
        error_log("Error hapus gambar: " . $e->getMessage());
        return false;
    }
}

/**
 * Ambil semua konten untuk admin
 */
function ambilSemuaKonten() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM konten_website ORDER BY section_key, content_type");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error ambil semua konten: " . $e->getMessage());
        return [];
    }
}

/**
 * Hapus konten dari database
 */
function hapusKonten($section_key, $content_type) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM konten_website 
                               WHERE section_key = ? AND content_type = ?");
        return $stmt->execute([$section_key, $content_type]);
    } catch(PDOException $e) {
        error_log("Error hapus konten: " . $e->getMessage());
        return false;
    }
}

/**
 * Inisialisasi data default jika belum ada
 */
function inisialisasiDataDefault() {
    global $pdo;
    
    try {
        // Cek apakah sudah ada data konten
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM konten_website");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total'] == 0) {
            // Data pimpinan default
            $pimpinan_data = [
                [
                    'nama' => 'Dr. Fahmi Fadli',
                    'jabatan' => 'Bupati Paser',
                    'foto' => 'assets/Bupati_Paser_Fahmi_Fadli.jpg'
                ],
                [
                    'nama' => 'H. Ikhwan Antasari, S.Sos.',
                    'jabatan' => 'Wakil Bupati Paser',
                    'foto' => 'assets/Wakil_Bupati_Paser_Ikhwan_Antasari.jpg'
                ],
                [
                    'nama' => 'Drs. Katsul Wijaya, M.Si',
                    'jabatan' => 'Sekretaris Daerah',
                    'foto' => 'assets/sekda-paser-2024.jpg'
                ]
            ];
            simpanKonten('index', 'pimpinan_data', $pimpinan_data);
            
            // Data visi misi default
            $visi_misi = [
                'visi' => 'Terwujudnya Paser yang Sejahtera, Berakhlak Mulia dan Berdaya Saing',
                'misi' => [
                    'Mewujudkan Sumber Daya Manusia yang handal dan berdaya saing melalui Peningkatan Mutu Pendidikan, Derajat Kesehatan serta Kesejahteraan Sosial',
                    'Mewujudkan tata kelola pemerintahan yang baik (Good Governance) yang bersih, efektif, efesien, transparan dan akuntabel berbasis Teknologi Informasi dan Komunikasi',
                    'Mewujudkan Pembangunan yang merata dan berkesinambungan yang berwawasan lingkungan',
                    'Meningkatkan kemandirian ekonomi daerah dan masyarakat berbasis potensi lokal',
                    'Menciptakan Kota yang Aman, Nyaman, dan Kondusif'
                ]
            ];
            simpanKonten('index', 'visi_misi', $visi_misi);
            
            // Data layanan default
            $layanan_data = [
                [
                    'id' => 'legalisir-ijazah',
                    'title' => 'Legalisir Ijazah/Dokumen Kelulusan',
                    'desc' => 'Layanan legalisir ijazah dan dokumen kelulusan untuk berbagai keperluan administrasi.',
                    'icon' => 'assets/legalisir.png'
                ],
                [
                    'id' => 'surat-mutasi',
                    'title' => 'Surat Keterangan Pindah Sekolah',
                    'desc' => 'Layanan penerbitan surat mutasi untuk siswa yang akan berpindah sekolah.',
                    'icon' => 'assets/document.png'
                ],
                [
                    'id' => 'tunjangan-guru',
                    'title' => 'Pengusulan Tunjangan Profesi Guru',
                    'desc' => 'Layanan pengusulan tunjangan profesi guru bagi guru yang memenuhi syarat.',
                    'icon' => 'assets/tunjangan.png'
                ],
                [
                    'id' => 'izin-pendirian',
                    'title' => 'Izin Pendirian Satuan Pendidikan',
                    'desc' => 'Layanan perizinan pendirian PAUD, SD, SMP, dan Lembaga Kursus.',
                    'icon' => 'assets/institusi.png'
                ]
            ];
            simpanKonten('index', 'layanan_data', $layanan_data);
            
            // Data hero default
            $hero_data = [
                'hero_text' => 'Pusat Layanan dan Informasi',
                'hero_subtext' => 'Bersama Paser TUNTAS (Tangguh, Unggul, Transformatif, Adil, dan Sejahtera)',
                'hero_images' => [
                    'https://upload.wikimedia.org/wikipedia/commons/c/c1/Pemandangan_Tanah_Grogot.jpg?20100504072853',
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fc/Tari_Ronggeng_Paser.JPG/1200px-Tari_Ronggeng_Paser.JPG',
                    'https://images.unsplash.com/photo-1523580494863-6f3031224c94?ixlib=rb-4.0.1&auto=format&fit=crop&w=1350&q=80',
                    'https://media.suara.com/images/2024/12/30/68716-ilustrasi-wisata-di-kabupaten-paser-kaltim.jpg'
                ]
            ];
            simpanKonten('index', 'hero_data', $hero_data);
            
            // Simpan gambar hero ke tabel gambar_konten
            foreach ($hero_data['hero_images'] as $index => $image_url) {
                simpanGambarKonten('index', 'hero_slider', $image_url, $index);
            }
            
            error_log("Data default berhasil diinisialisasi");
        }
    } catch(PDOException $e) {
        error_log("Error inisialisasi data default: " . $e->getMessage());
    }
}

// Jalankan inisialisasi saat file ini di-load
inisialisasiDataDefault();
?>