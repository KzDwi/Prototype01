// Smooth Scroll Functionality
function smoothScrollTo(targetId, duration = 1000) {
    const targetElement = document.getElementById(targetId);
    if (!targetElement) return;

    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
    const startPosition = window.pageYOffset;
    const distance = targetPosition - startPosition - 80; // Adjust for header height
    let startTime = null;

    function animation(currentTime) {
        if (startTime === null) startTime = currentTime;
        const timeElapsed = currentTime - startTime;
        const run = easeInOutQuad(timeElapsed, startPosition, distance, duration);
        window.scrollTo(0, run);
        
        if (timeElapsed < duration) {
            requestAnimationFrame(animation);
        } else {
            // Add highlight effect when scroll completes
            targetElement.classList.add('highlight-section');
            setTimeout(() => {
                targetElement.classList.remove('highlight-section');
            }, 2000);
        }
    }

    // Easing function for smooth acceleration and deceleration
    function easeInOutQuad(t, b, c, d) {
        t /= d / 2;
        if (t < 1) return c / 2 * t * t + b;
        t--;
        return -c / 2 * (t * (t - 2) - 1) + b;
    }

    requestAnimationFrame(animation);
}

// Enhanced scroll function with progress indicator
function enhancedScrollTo(targetId) {
    const targetElement = document.getElementById(targetId);
    if (!targetElement) return;

    // Calculate scroll distance
    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
    const startPosition = window.pageYOffset;
    const distance = targetPosition - startPosition;
    
    // Only animate if distance is significant
    if (Math.abs(distance) < 100) {
        targetElement.scrollIntoView({ behavior: 'smooth' });
        return;
    }

    // Create scroll progress indicator
    const progressBar = document.createElement('div');
    progressBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, #003399, #002280);
        z-index: 10000;
        transition: width 0.1s;
    `;
    document.body.appendChild(progressBar);

    let startTime = null;
    const duration = 800; // ms

    function scrollStep(timestamp) {
        if (!startTime) startTime = timestamp;
        const progress = timestamp - startTime;
        const percentage = Math.min(progress / duration, 1);
        
        // Easing function for smooth scroll
        const easeOutQuart = 1 - Math.pow(1 - percentage, 4);
        const scrollTo = startPosition + distance * easeOutQuart;
        
        window.scrollTo(0, scrollTo);
        progressBar.style.width = `${percentage * 100}%`;
        
        if (progress < duration) {
            requestAnimationFrame(scrollStep);
        } else {
            // Clean up
            document.body.removeChild(progressBar);
            // Add highlight effect
            targetElement.classList.add('highlight-section');
            setTimeout(() => {
                targetElement.classList.remove('highlight-section');
            }, 2000);
        }
    }

    requestAnimationFrame(scrollStep);
}

// Initialize smooth scroll functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add click event to "Jelajahi Layanan" button
    const exploreButton = document.querySelector('.hero-content .btn[href="#layanan"]');
    if (exploreButton) {
        exploreButton.addEventListener('click', function(e) {
            e.preventDefault();
            enhancedScrollTo('layanan');
        });
    }

    // Add smooth scroll to all anchor links with hash
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            const targetId = href.substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                e.preventDefault();
                enhancedScrollTo(targetId);
            }
        });
    });
});

// Dropdown Menu Functionality
let activeDropdown = null;

function toggleDropdown(event, dropdownId) {
    event.preventDefault();
    event.stopPropagation();
    
    const dropdown = document.getElementById(dropdownId);
    
    // Close all other dropdowns
    if (activeDropdown && activeDropdown !== dropdown) {
        activeDropdown.removeAttribute('data-menu-open');
    }
    
    // Toggle current dropdown
    if (dropdown.hasAttribute('data-menu-open')) {
        dropdown.removeAttribute('data-menu-open');
        activeDropdown = null;
    } else {
        dropdown.setAttribute('data-menu-open', '');
        activeDropdown = dropdown;
    }
}

function closeDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    dropdown.removeAttribute('data-menu-open');
    activeDropdown = null;
}

function showMenuGroup(event, menuGroupId) {
    event.preventDefault();
    event.stopPropagation();
    
    if (!activeDropdown) return;
    
    // Get all menu groups in the current dropdown
    const menuGroups = activeDropdown.querySelectorAll('[data-menugroup]');
    
    // Hide all menu groups
    menuGroups.forEach(group => {
        group.style.display = 'none';
    });
    
    // Show the selected menu group
    const selectedGroup = activeDropdown.querySelector(`[data-menugroup="${menuGroupId}"]`);
    if (selectedGroup) {
        selectedGroup.style.display = 'block';
    }
    
    // Update active state in the menu items
    const menuItems = activeDropdown.querySelectorAll('.level-menu-item_second a');
    menuItems.forEach(item => {
        item.classList.remove('second-active');
    });
    
    // Add active class to the clicked item
    event.target.classList.add('second-active');
}

// Mobile menu functionality
function toggleMobileMenu() {
    const nav = document.getElementById('main-nav');
    nav.classList.toggle('active');
}

function closeMobileMenu() {
    const nav = document.getElementById('main-nav');
    nav.classList.remove('active');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (activeDropdown && !activeDropdown.contains(event.target) && 
        !event.target.closest('nav ul li')) {
        activeDropdown.removeAttribute('data-menu-open');
        activeDropdown = null;
    }
});

// Close dropdown when scrolling
window.addEventListener('scroll', function() {
    if (activeDropdown) {
        activeDropdown.removeAttribute('data-menu-open');
        activeDropdown = null;
    }
    
    // Add scrolled class to header for color change effect
    const header = document.getElementById('main-header');
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});

// Mobile menu functionality
function toggleMobileMenu() {
    const nav = document.getElementById('main-nav');
    nav.classList.toggle('active');
}

function closeMobileMenu() {
    const nav = document.getElementById('main-nav');
    nav.classList.remove('active');
    
    // Tutup semua dropdown di mobile
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        dropdown.classList.remove('active');
    });
}

// Mobile dropdown toggle
document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const dropdown = this.parentElement;
                dropdown.classList.toggle('active');
            }
        });
    });
});

// Tutup dropdown saat klik di luar (untuk desktop)
document.addEventListener('click', function(event) {
    if (window.innerWidth > 768) {
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(event.target)) {
                // Reset state jika diperlukan
            }
        });
    }
});

// Kode untuk berita banner slider tetap sama
// ...

// Berita Banner Slider
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.banner-slide');
    let currentSlide = 0;
    let slideInterval;
    
    function showSlide(index) {
        // Hide all slides
        slides.forEach(slide => {
            slide.classList.remove('active');
        });
        
        // Show the selected slide
        currentSlide = index;
        slides[currentSlide].classList.add('active');
    }
    
    function nextSlide() {
        let next = (currentSlide + 1) % slides.length;
        showSlide(next);
    }
    
    function prevSlide() {
        let prev = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(prev);
    }
    
    function startSlideShow() {
        slideInterval = setInterval(nextSlide, 5000);
    }
    
    function stopSlideShow() {
        clearInterval(slideInterval);
    }
    
    // Start the slideshow
    showSlide(currentSlide);
    startSlideShow();
    
    // Pause on hover
    const banner = document.querySelector('.berita-banner');
    banner.addEventListener('mouseenter', stopSlideShow);
    banner.addEventListener('mouseleave', startSlideShow);
    
    // Make functions globally available for button clicks
    window.nextSlide = nextSlide;
    window.prevSlide = prevSlide;
});

// Efek Tamabahan dari Profil.html
// Efek Tamabahan dari Profil.html
// Efek Tamabahan dari Profil.html
// Efek Tamabahan dari Profil.html
// Efek Tamabahan dari Profil.html
// Efek Tamabahan dari Profil.html


// Fade in animation on scroll
function fadeInOnScroll() {
    const fadeElements = document.querySelectorAll('.fade-in');
    
    fadeElements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;
        
        if (elementTop < window.innerHeight - elementVisible) {
            element.classList.add('visible');
        }
    });
}

// Initialize fade in effect
document.addEventListener('DOMContentLoaded', function() {
    // Trigger initial check
    fadeInOnScroll();
    
    // Check on scroll
    window.addEventListener('scroll', fadeInOnScroll);
    
    // Mobile dropdown toggle (existing code)
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const dropdown = this.parentElement;
                dropdown.classList.toggle('active');
            }
        });
    });
    
    // Atur posisi dropdown secara dinamis
    adjustDropdownPosition();
    window.addEventListener('resize', adjustDropdownPosition);
});

// Fungsi untuk menyesuaikan posisi dropdown
function adjustDropdownPosition() {
    if (window.innerWidth > 768) {
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            const dropdownMenu = dropdown.querySelector('.dropdown-menu');
            const rect = dropdown.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            
            // Reset posisi
            dropdownMenu.style.left = '50%';
            dropdownMenu.style.transform = 'translateX(-50%) translateY(10px)';
            
            // Cek jika dropdown keluar dari viewport di kanan
            const dropdownWidth = dropdownMenu.offsetWidth;
            const rightEdge = rect.left + (dropdownWidth / 2);
            
            if (rightEdge > viewportWidth - 20) {
                const overflow = rightEdge - viewportWidth + 20;
                dropdownMenu.style.left = `calc(50% - ${overflow}px)`;
            }
            
            // Cek jika dropdown keluar dari viewport di kiri
            const leftEdge = rect.left - (dropdownWidth / 2);
            if (leftEdge < 20) {
                const overflow = 20 - leftEdge;
                dropdownMenu.style.left = `calc(50% + ${overflow}px)`;
            }
        });
    }
}

// Mobile menu functionality (existing code)
function toggleMobileMenu() {
    const nav = document.getElementById('main-nav');
    nav.classList.toggle('active');
}

function closeMobileMenu() {
    const nav = document.getElementById('main-nav');
    nav.classList.remove('active');
    
    // Tutup semua dropdown di mobile
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        dropdown.classList.remove('active');
    });
}

// Tutup dropdown saat klik di luar (untuk desktop)
document.addEventListener('click', function(event) {
    if (window.innerWidth > 768) {
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(event.target)) {
                // Reset state jika diperlukan
            }
        });
    }
});

// ===== FUNGSI UNTUK POPUP DESKRIPSI LAYANAN =====

// Data deskripsi layanan
const layananData = {
    'legalisir-ijazah': {
        title: 'Legalisir Ijazah/Dokumen Kelulusan',
        description: 'Layanan legalisir ijazah dan dokumen kelulusan untuk keperluan administrasi seperti melamar pekerjaan, melanjutkan pendidikan, atau keperluan lainnya.',
        caraKerja: [
            'Pemohon datang ke kantor Dinas Pendidikan dengan membawa dokumen asli',
            'Mengisi formulir permohonan legalisir',
            'Petugas memverifikasi dokumen asli',
            'Dokumen dicap dan ditandatangani oleh pejabat berwenang',
            'Pemohon menerima dokumen yang telah dilegalisir'
        ],
        persyaratan: [
            'Ijazah asli yang akan dilegalisir',
            'KTP asli dan fotokopi',
            'Formulir permohonan yang telah diisi',
            'Bukti pembayaran (jika ada)'
        ]
    },
    'surat-mutasi': {
        title: 'Penerbitan Surat Keterangan Pindah Sekolah',
        description: 'Layanan penerbitan surat keterangan pindah sekolah untuk siswa yang akan berpindah ke sekolah lain.',
        caraKerja: [
            'Orang tua/wali mengajukan permohonan mutasi ke sekolah asal',
            'Sekolah asal menerbitkan surat keterangan pindah',
            'Permohonan diajukan ke Dinas Pendidikan',
            'Petugas memverifikasi dokumen dan alasan mutasi',
            'Surat mutasi diterbitkan dan diberikan kepada pemohon'
        ],
        persyaratan: [
            'Surat permohonan dari orang tua/wali',
            'Rapor siswa',
            'Surat keterangan dari sekolah asal',
            'KTP orang tua/wali',
            'Alasan pindah yang jelas'
        ]
    },
    'tunjangan-guru': {
        title: 'Pengusulan Tunjangan Profesi Guru (TPG)',
        description: 'Layanan pengusulan tunjangan profesi guru bagi guru yang memenuhi persyaratan.',
        caraKerja: [
            'Guru mengajukan berkas persyaratan ke Dinas Pendidikan',
            'Petugas memverifikasi kelengkapan dan keabsahan dokumen',
            'Data diverifikasi dan divalidasi',
            'Pengusulan diajukan ke pemerintah provinsi/pusat',
            'Monitoring proses pencairan tunjangan'
        ],
        persyaratan: [
            'Foto kopi sertifikat pendidik',
            'SK pengangkatan sebagai guru',
            'SK mengajar minimal 24 jam/minggu',
            'Laporan kinerja guru',
            'Berkas administrasi lainnya'
        ]
    },
    'izin-pendirian': {
        title: 'Izin Pendirian Satuan Pendidikan',
        description: 'Layanan perizinan pendirian satuan pendidikan baru meliputi PAUD, SD, SMP, dan Lembaga Kursus dan Pelatihan (LKP).',
        caraKerja: [
            'Pemohon mengajukan permohonan izin pendirian',
            'Kelengkapan administrasi diverifikasi',
            'Survey lokasi dan fasilitas dilakukan',
            'Evaluasi kelayakan satuan pendidikan',
            'Izin diterbitkan jika memenuhi syarat'
        ],
        persyaratan: [
            'Proposal pendirian satuan pendidikan',
            'Dokumen kepemilikan tanah/bangunan',
            'Rencana induk pengembangan',
            'Struktur organisasi dan tenaga pendidik',
            'Dokumen administrasi lainnya'
        ]
    }
};

// Fungsi untuk membuka popup deskripsi layanan
function openLayananPopup(layananId) {
    const layanan = layananData[layananId];
    if (!layanan) return;
    
    const stepsHTML = layanan.caraKerja.map((step, index) => 
        `<li>${step}</li>`
    ).join('');
    
    const requirementsHTML = layanan.persyaratan.map(req => 
        `<li>${req}</li>`
    ).join('');
    
    const popupHTML = `
        <div class="popup-overlay" id="layananPopup">
            <div class="popup-content">
                <button class="popup-close" onclick="closeLayananPopup()">×</button>
                <h2 class="popup-title">${layanan.title}</h2>
                <p class="popup-description">${layanan.description}</p>
                
                <div class="popup-steps">
                    <h4>Cara Kerja Layanan:</h4>
                    <ol>${stepsHTML}</ol>
                </div>
                
                <div class="popup-requirements">
                    <h4>Persyaratan:</h4>
                    <ul>${requirementsHTML}</ul>
                </div>
            </div>
        </div>
    `;
    
    // Tambahkan popup ke body
    document.body.insertAdjacentHTML('beforeend', popupHTML);
    
    // Tampilkan popup
    setTimeout(() => {
        document.getElementById('layananPopup').style.display = 'flex';
    }, 10);
}

// Fungsi untuk menutup popup layanan
function closeLayananPopup() {
    const popup = document.getElementById('layananPopup');
    if (popup) {
        popup.remove();
    }
}

// Fungsi untuk scroll ke bagian layanan
function scrollToLayanan() {
    const layananSection = document.getElementById('layanan-section');
    if (layananSection) {
        layananSection.scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Tutup popup ketika klik di luar konten
document.addEventListener('click', function(event) {
    const popup = document.getElementById('layananPopup');
    if (popup && event.target === popup) {
        closeLayananPopup();
    }
});

// Tutup popup dengan tombol ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeLayananPopup();
    }
});