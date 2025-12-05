<?php
// functions.php
session_start();

function loadFAQData() {
    $file_path = 'data/content.json';
    
    // Cek apakah file content.json ada
    if (file_exists($file_path)) {
        $content_data = json_decode(file_get_contents($file_path), true);
        
        // Debug: Cek struktur content.json
        error_log("Content data structure: " . print_r(array_keys($content_data), true));
        
        if (isset($content_data['faq'])) {
            return $content_data['faq'];
        } else {
            error_log("FAQ section not found in content.json");
        }
    } else {
        error_log("content.json file not found at: " . $file_path);
    }
    
    // Fallback ke data default
    error_log("Using default FAQ data");
    return getAllFAQDefaultData();
}

function getAllFAQDefaultData() {
    return [
        'informasi_umum' => [
            [
                'question' => 'Dimana alamat kantor Dinas Pendidikan Kabupaten Paser?',
                'answer' => 'Kantor kami beralamat di Jl. Jenderal Sudirman No. 27, Tana Paser, Kabupaten Paser, Kalimantan Timur. Jam operasional pelayanan adalah Senin – Jumat, pukul 08.00 – 15.00.'
            ],
            [
                'question' => 'Bagaimana cara menghubungi Dinas Pendidikan?',
                'answer' => 'Anda dapat menghubungi kami melalui telepon di (0543) 21023 atau mengirim email ke <a href="mailto:disdik@paserkab.go.id" style="color: #003399; text-decoration: underline;">disdik@paserkab.go.id</a>. Untuk pengaduan, kami sarankan menggunakan kanal pada halaman \'Layanan Publik\' kami.'
            ]
        ],
        'layanan_kesiswaan' => [
            [
                'question' => 'Bagaimana prosedur legalisir ijazah yang hilang atau rusak?',
                'answer' => '<p>Jika ijazah asli hilang atau rusak, Anda tidak bisa melakukan legalisir. Sebagai gantinya, Anda dapat mengurus <strong>Surat Keterangan Pengganti Ijazah (SKPI)</strong>. Prosedurnya adalah:</p><ol style="margin-left: 20px; line-height: 1.6;"><li>Membuat Surat Keterangan Kehilangan dari Kepolisian.</li><li>Datang ke sekolah asal yang mengeluarkan ijazah.</li><li>Jika sekolah sudah tidak beroperasi, datang ke Dinas Pendidikan dengan membawa surat dari kepolisian dan dokumen pendukung lainnya (fotokopi ijazah jika ada, KTP, dll).</li></ol>'
            ],
            [
                'question' => 'Apakah pindah sekolah (mutasi) antar kabupaten/kota dikenakan biaya?',
                'answer' => 'Tidak. Seluruh layanan pengurusan surat rekomendasi pindah sekolah (mutasi) di Dinas Pendidikan Kabupaten Paser adalah <strong>gratis</strong> dan tidak dipungut biaya.'
            ]
        ],
        'guru_tenaga_kependidikan' => [
            [
                'question' => 'Bagaimana cara memeriksa status validasi data untuk Tunjangan Profesi Guru (TPG)?',
                'answer' => 'Status validasi data guru dapat dipantau secara mandiri melalui laman <a href="https://info.gtk.kemdikbud.go.id" target="_blank" style="color: #003399; text-decoration: underline;">Info GTK</a> menggunakan akun PTK masing-masing. Pastikan data Anda di Dapodik sudah sinkron dan valid melalui operator sekolah.'
            ],
            [
                'question' => 'Saya adalah guru honorer, apakah bisa mendapatkan bantuan/insentif dari dinas?',
                'answer' => 'Pemerintah Daerah Kabupaten Paser memiliki kebijakan terkait insentif atau bantuan untuk guru non-ASN. Informasi mengenai kriteria, besaran, dan jadwal pencairan akan diumumkan secara resmi melalui surat edaran ke sekolah-sekolah. Silakan berkoordinasi dengan kepala sekolah Anda.'
            ]
        ],
        'ppdb' => [
            [
                'question' => 'Kapan jadwal pelaksanaan PPDB tahun ini?',
                'answer' => 'Jadwal lengkap, petunjuk teknis, dan informasi mengenai jalur pendaftaran (Zonasi, Afirmasi, Prestasi, Perpindahan Tugas Orang Tua) akan dipublikasikan melalui website resmi PPDB Kabupaten Paser. Mohon pantau website dan media sosial resmi kami secara berkala.'
            ],
            [
                'question' => 'Apa yang harus dilakukan jika ada kendala saat pendaftaran PPDB online?',
                'answer' => 'Jika terjadi kendala teknis, Anda dapat menghubungi <strong>Help Desk PPDB</strong> yang nomor kontaknya akan kami sediakan di situs resmi PPDB selama periode pendaftaran berlangsung. Anda juga bisa datang ke posko PPDB di sekolah terdekat atau di kantor Dinas Pendidikan.'
            ]
        ]
    ];
}

// Fungsi untuk save FAQ ke JSON
function saveFAQData($data) {
    $dir = 'data';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    $file_path = $dir . '/content.json';
    return file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

?>