<?php
// functions_json.php
// Fungsi untuk menyimpan dan mengambil data dari file JSON

function getJsonData($file = 'data/content.json') {
    $dataFile = __DIR__ . '/' . $file;
    
    if (!file_exists($dataFile)) {
        // Buat file JSON dengan struktur kosong
        $initialData = [
            'index' => [
                'hero_data' => [
                    'hero_text' => 'Selamat Datang di Dinas Pendidikan dan Kebudayaan Kabupaten Paser',
                    'hero_subtext' => 'Melayani dengan hati, membangun pendidikan berkualitas',
                    'hero_images' => [
                        'assets/images/hero1.jpg',
                        'assets/images/hero2.jpg',
                        'assets/images/hero3.jpg',
                        'assets/images/hero4.jpg'
                    ]
                ],
                'pimpinan_data' => [
                    [
                        'nama' => 'Dr. Fahmi Fadli',
                        'jabatan' => 'Bupati Paser',
                        'foto' => 'assets/images/pimpinan1.jpg'
                    ],
                    [
                        'nama' => 'H. Muhammad Idris',
                        'jabatan' => 'Wakil Bupati Paser',
                        'foto' => 'assets/images/pimpinan2.jpg'
                    ],
                    [
                        'nama' => 'Drs. Ahmad Yani, M.Pd.',
                        'jabatan' => 'Kepala Dinas Pendidikan dan Kebudayaan',
                        'foto' => 'assets/images/pimpinan3.jpg'
                    ]
                ],
                'visi_misi' => [
                    'visi' => 'Mewujudkan Pendidikan dan Kebudayaan Kabupaten Paser yang Berkualitas, Berkarakter, dan Berdaya Saing',
                    'misi' => [
                        'Meningkatkan akses dan mutu pendidikan dasar dan menengah',
                        'Mengembangkan pendidikan karakter berbasis kearifan lokal',
                        'Meningkatkan kompetensi dan kesejahteraan pendidik dan tenaga kependidikan',
                        'Mengembangkan potensi seni dan budaya daerah',
                        'Memperkuat tata kelola pendidikan yang transparan dan akuntabel'
                    ]
                ],
                'layanan_data' => [
                    [
                        'id' => 'legalisir-ijazah',
                        'title' => 'Legalisir Ijazah',
                        'desc' => 'Pelayanan legalisir ijazah dan transkrip nilai',
                        'icon' => 'assets/icons/legalisir.png'
                    ],
                    [
                        'id' => 'surat-mutasi',
                        'title' => 'Surat Mutasi',
                        'desc' => 'Pengurusan surat mutasi siswa antar sekolah',
                        'icon' => 'assets/icons/mutasi.png'
                    ],
                    [
                        'id' => 'tunjangan-guru',
                        'title' => 'Tunjangan Guru',
                        'desc' => 'Pengajuan dan pencairan tunjangan profesi guru',
                        'icon' => 'assets/icons/tunjangan.png'
                    ],
                    [
                        'id' => 'izin-pendirian',
                        'title' => 'Izin Pendirian Sekolah',
                        'desc' => 'Perizinan pendirian satuan pendidikan baru',
                        'icon' => 'assets/icons/izin.png'
                    ]
                ]
            ],
            'profil' => [
                'hero_data' => [
                    'title' => 'Profil Dinas Pendidikan dan Kebudayaan',
                    'subtitle' => 'Mengenal lebih dekat tentang kami',
                    'image' => 'assets/images/profil-hero.jpg'
                ],
                'visi_misi' => [
                    'visi' => 'Mewujudkan Pendidikan dan Kebudayaan Kabupaten Paser yang Berkualitas, Berkarakter, dan Berdaya Saing',
                    'misi' => [
                        'Meningkatkan akses dan mutu pendidikan dasar dan menengah',
                        'Mengembangkan pendidikan karakter berbasis kearifan lokal',
                        'Meningkatkan kompetensi dan kesejahteraan pendidik dan tenaga kependidikan',
                        'Mengembangkan potensi seni dan budaya daerah',
                        'Memperkuat tata kelola pendidikan yang transparan dan akuntabel'
                    ]
                ],
                'tupoksi' => [
                    'Menyelenggarakan urusan pemerintahan bidang pendidikan',
                    'Mengelola pendidikan anak usia dini, pendidikan dasar, dan pendidikan menengah',
                    'Melakukan pembinaan dan pengembangan tenaga kependidikan',
                    'Mengembangkan kurikulum dan evaluasi pendidikan',
                    'Mengelola sarana dan prasarana pendidikan',
                    'Melakukan pembinaan dan pengembangan kesenian dan kebudayaan',
                    'Melakukan kerjasama dengan berbagai pihak dalam bidang pendidikan dan kebudayaan'
                ],
                'kontak' => [
                    'alamat' => 'Jl. Jenderal Sudirman No. 27, Tanah Grogot, Kabupaten Paser, Kalimantan Timur 76251',
                    'telepon' => '(0543) 21023',
                    'email' => 'disdik@paserkab.go.id',
                    'jam_kerja' => 'Senin - Kamis: 08.00 - 16.00 WITA<br>Jumat: 08.00 - 11.00 WITA<br>Sabtu & Minggu: Libur'
                ]
            ],
            'layanan' => [
                'hero_data' => [
                    'title' => 'Layanan Kami',
                    'subtitle' => 'Berbagai layanan yang disediakan untuk masyarakat',
                    'image' => 'assets/images/layanan-hero.jpg'
                ],
                'layanan_detail' => [
                    [
                        'id' => 'legalisir-ijazah',
                        'title' => 'Legalisir Ijazah',
                        'description' => 'Layanan legalisir ijazah dan transkrip nilai untuk berbagai keperluan seperti melanjutkan pendidikan, melamar pekerjaan, atau keperluan administratif lainnya.',
                        'image' => 'assets/images/layanan-legalisir.jpg'
                    ],
                    [
                        'id' => 'surat-mutasi',
                        'title' => 'Surat Mutasi',
                        'description' => 'Pengurusan surat mutasi siswa untuk pindah sekolah baik dalam maupun luar daerah Kabupaten Paser.',
                        'image' => 'assets/images/layanan-mutasi.jpg'
                    ],
                    [
                        'id' => 'tunjangan-guru',
                        'title' => 'Tunjangan Guru',
                        'description' => 'Pengajuan dan pencairan berbagai jenis tunjangan untuk guru termasuk tunjangan profesi, tunjangan khusus, dan tunjangan lainnya.',
                        'image' => 'assets/images/layanan-tunjangan.jpg'
                    ],
                    [
                        'id' => 'izin-pendirian',
                        'title' => 'Izin Pendirian Sekolah',
                        'description' => 'Perizinan pendirian satuan pendidikan baru mulai dari TK, SD, SMP, SMA/SMK sesuai dengan peraturan yang berlaku.',
                        'image' => 'assets/images/layanan-izin.jpg'
                    ]
                ],
                'faq' => []
            ],
            'faq' => [
                'faq_content' => []
            ]
        ];
        
        saveJsonData($initialData, $file);
        return $initialData;
    }
    
    $jsonContent = file_get_contents($dataFile);
    return json_decode($jsonContent, true);
}

function saveJsonData($data, $file = 'data/content.json') {
    $dataFile = __DIR__ . '/' . $file;
    $dir = dirname($dataFile);
    
    // Buat direktori jika belum ada
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    
    $jsonString = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($dataFile, $jsonString) !== false;
}

function getSectionData($page, $section, $file = 'data/content.json') {
    $data = getJsonData($file);
    return $data[$page][$section] ?? null;
}

function saveSectionData($page, $section, $newData, $file = 'data/content.json') {
    $data = getJsonData($file);
    $data[$page][$section] = $newData;
    return saveJsonData($data, $file);
}

// Fungsi khusus untuk layanan popup
function saveLayananPopup($layananKey, $popupData, $file = 'data/layanan_popup.json') {
    $popupFile = __DIR__ . '/' . $file;
    
    if (!file_exists($popupFile)) {
        $initialData = [];
    } else {
        $jsonContent = file_get_contents($popupFile);
        $initialData = json_decode($jsonContent, true) ?? [];
    }
    
    $initialData[$layananKey] = $popupData;
    $jsonString = json_encode($initialData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($popupFile, $jsonString) !== false;
}

function getLayananPopup($layananKey, $file = 'data/layanan_popup.json') {
    $popupFile = __DIR__ . '/' . $file;
    
    if (!file_exists($popupFile)) {
        return ['popup_desc' => '', 'cara_kerja' => '', 'persyaratan' => ''];
    }
    
    $jsonContent = file_get_contents($popupFile);
    $data = json_decode($jsonContent, true);
    return $data[$layananKey] ?? ['popup_desc' => '', 'cara_kerja' => '', 'persyaratan' => ''];
}

// ============================================
// FUNGSI UNTUK DIGUNAKAN DI FRONTEND (index.php)
// ============================================

function ambilDataHero() {
    $hero_data = getSectionData('index', 'hero_data');
    if (!$hero_data) {
        return [
            'hero_text' => 'Selamat Datang di Dinas Pendidikan dan Kebudayaan Kabupaten Paser',
            'hero_subtext' => 'Melayani dengan hati, membangun pendidikan berkualitas',
            'hero_images' => [
                'assets/images/hero1.jpg',
                'assets/images/hero2.jpg',
                'assets/images/hero3.jpg',
                'assets/images/hero4.jpg'
            ]
        ];
    }
    return $hero_data;
}

function ambilDataPimpinan() {
    $pimpinan_data = getSectionData('index', 'pimpinan_data');
    if (!$pimpinan_data) {
        return [
            [
                'nama' => 'Dr. Fahmi Fadli',
                'jabatan' => 'Bupati Paser',
                'foto' => 'assets/images/pimpinan1.jpg'
            ],
            [
                'nama' => 'H. Muhammad Idris',
                'jabatan' => 'Wakil Bupati Paser',
                'foto' => 'assets/images/pimpinan2.jpg'
            ],
            [
                'nama' => 'Drs. Ahmad Yani, M.Pd.',
                'jabatan' => 'Kepala Dinas Pendidikan dan Kebudayaan',
                'foto' => 'assets/images/pimpinan3.jpg'
            ]
        ];
    }
    return $pimpinan_data;
}

function ambilVisiMisi() {
    $visi_misi = getSectionData('index', 'visi_misi');
    if (!$visi_misi) {
        return [
            'visi' => 'Mewujudkan Pendidikan dan Kebudayaan Kabupaten Paser yang Berkualitas, Berkarakter, dan Berdaya Saing',
            'misi' => [
                'Meningkatkan akses dan mutu pendidikan dasar dan menengah',
                'Mengembangkan pendidikan karakter berbasis kearifan lokal',
                'Meningkatkan kompetensi dan kesejahteraan pendidik dan tenaga kependidikan',
                'Mengembangkan potensi seni dan budaya daerah',
                'Memperkuat tata kelola pendidikan yang transparan dan akuntabel'
            ]
        ];
    }
    return $visi_misi;
}

function ambilDataLayanan() {
    $layanan_data = getSectionData('index', 'layanan_data');
    if (!$layanan_data) {
        return [
            [
                'id' => 'legalisir-ijazah',
                'title' => 'Legalisir Ijazah',
                'desc' => 'Pelayanan legalisir ijazah dan transkrip nilai',
                'icon' => 'assets/icons/legalisir.png'
            ],
            [
                'id' => 'surat-mutasi',
                'title' => 'Surat Mutasi',
                'desc' => 'Pengurusan surat mutasi siswa antar sekolah',
                'icon' => 'assets/icons/mutasi.png'
            ],
            [
                'id' => 'tunjangan-guru',
                'title' => 'Tunjangan Guru',
                'desc' => 'Pengajuan dan pencairan tunjangan profesi guru',
                'icon' => 'assets/icons/tunjangan.png'
            ],
            [
                'id' => 'izin-pendirian',
                'title' => 'Izin Pendirian Sekolah',
                'desc' => 'Perizinan pendirian satuan pendidikan baru',
                'icon' => 'assets/icons/izin.png'
            ]
        ];
    }
    return $layanan_data;
}

// Fungsi untuk halaman profil
function ambilDataProfil() {
    return [
        'hero_data' => getSectionData('profil', 'hero_data') ?? [],
        'visi_misi' => getSectionData('profil', 'visi_misi') ?? [],
        'tupoksi' => getSectionData('profil', 'tupoksi') ?? [],
        'kontak' => getSectionData('profil', 'kontak') ?? []
    ];
}

// Fungsi untuk halaman layanan
function ambilDataLayananDetail() {
    return [
        'hero_data' => getSectionData('layanan', 'hero_data') ?? [],
        'layanan_detail' => getSectionData('layanan', 'layanan_detail') ?? [],
        'faq' => getSectionData('layanan', 'faq') ?? []
    ];
}

// Fungsi untuk halaman FAQ
function ambilDataFaq() {
    return getSectionData('faq', 'faq_content') ?? [];
}