-- Tabel untuk menyimpan konten website
CREATE TABLE IF NOT EXISTS konten_website (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section_key VARCHAR(100) NOT NULL,
    content_type VARCHAR(50) NOT NULL COMMENT 'hero_text, hero_subtext, pimpinan, visi, misi, layanan',
    content_data TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_section_content (section_key, content_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel untuk gambar
CREATE TABLE IF NOT EXISTS gambar_konten (
    id INT PRIMARY KEY AUTO_INCREMENT,
    konten_id INT,
    gambar_type VARCHAR(50) NOT NULL COMMENT 'hero_slider, pimpinan_foto, layanan_icon',
    gambar_url VARCHAR(500),
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (konten_id) REFERENCES konten_website(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;