-- File: database.sql
-- Buat database terlebih dahulu

CREATE DATABASE IF NOT EXISTS disdikbud_paser CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE disdikbud_paser;

-- Tabel berita
CREATE TABLE IF NOT EXISTS berita (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(255) NOT NULL,
    excerpt TEXT NOT NULL,
    konten LONGTEXT NOT NULL,
    kategori ENUM('Pendidikan', 'Kebudayaan', 'Pengumuman', 'Kegiatan') NOT NULL,
    gambar VARCHAR(255),
    thumbnail VARCHAR(255),
    penulis VARCHAR(100) NOT NULL,
    tanggal_publish DATE NOT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diupdate_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('draft', 'publish') DEFAULT 'publish',
    dibaca INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert data sample untuk testing
INSERT INTO berita (judul, excerpt, konten, kategori, gambar, thumbnail, penulis, tanggal_publish, dibaca) VALUES
(
    'Penerimaan Peserta Didik Baru Tahun 2024 Dibuka',
    'Pemerintah Kabupaten Paser membuka pendaftaran PPDB untuk tahun ajaran 2024/2025. Daftar sekarang melalui sistem online yang telah disediakan.',
    '<p>Pemerintah Kabupaten Paser melalui Dinas Pendidikan dan Kebudayaan resmi membuka pendaftaran Penerimaan Peserta Didik Baru (PPDB) untuk tahun ajaran 2024/2025. Pendaftaran dapat dilakukan secara online melalui website resmi Disdikbud Paser.</p><p>Berikut jadwal penting PPDB 2024:</p><ul><li>Pendaftaran: 1-15 Juni 2024</li><li>Seleksi: 16-20 Juni 2024</li><li>Pengumuman: 25 Juni 2024</li><li>Daftar Ulang: 26-30 Juni 2024</li></ul>',
    'Pendidikan',
    'https://via.placeholder.com/800x400/003399/ffffff?text=PPDB+2024',
    'https://via.placeholder.com/400x200/003399/ffffff?text=PPDB+2024',
    'Admin Disdikbud',
    '2024-01-15',
    1245
),
(
    'Festival Budaya Paser 2024 Sukses Digelar',
    'Festival budaya tahunan Kabupaten Paser berhasil menarik ribuan pengunjung dengan berbagai pertunjukan kesenian tradisional.',
    '<p>Festival Budaya Paser 2024 yang digelar di Lapangan Merdeka Tanah Grogot berhasil menyedot perhatian ribuan pengunjung. Acara yang berlangsung selama tiga hari ini menampilkan berbagai kesenian tradisional khas Paser.</p><p>Beberapa pertunjukan yang ditampilkan antara lain Tari Gantar, Musik Sampe, dan pertunjukan wayang kulit dengan cerita-cerita lokal. Festival ini juga diisi dengan bazaar kuliner tradisional dan pameran kerajinan tangan masyarakat Paser.</p>',
    'Kebudayaan',
    'https://via.placeholder.com/800x400/002280/ffffff?text=Festival+Budaya+Paser',
    'https://via.placeholder.com/400x200/002280/ffffff?text=Festival+Budaya',
    'Admin Disdikbud',
    '2024-01-12',
    892
);