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
// Berita Slider Functionality - FIXED VERSION
function initBeritaSlider() {
    const slides = document.querySelectorAll('.berita-slider .slide-content');
    const dots = document.querySelectorAll('.slider-pagination .dot');
    const prevBtn = document.querySelector('.slider-nav-btn.prev-btn');
    const nextBtn = document.querySelector('.slider-nav-btn.next-btn');
    
    console.log('Slider Elements Found:', {
        slides: slides.length,
        dots: dots.length,
        prevBtn: !!prevBtn,
        nextBtn: !!nextBtn
    });
    
    if (slides.length === 0) {
        console.error('No slides found!');
        return;
    }
    
    let currentSlide = 0;
    let slideInterval;
    
    function showSlide(index) {
        console.log('Showing slide:', index);
        
        // Validate index
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        
        // Hide all slides
        slides.forEach(slide => {
            slide.classList.remove('active');
        });
        
        // Remove active class from all dots
        dots.forEach(dot => {
            dot.classList.remove('active');
        });
        
        // Show the selected slide
        currentSlide = index;
        slides[currentSlide].classList.add('active');
        
        // Update dots if they exist
        if (dots.length > 0 && dots[currentSlide]) {
            dots[currentSlide].classList.add('active');
        }
    }
    
    function nextSlide() {
        console.log('Next slide triggered');
        let next = (currentSlide + 1) % slides.length;
        showSlide(next);
    }
    
    function prevSlide() {
        console.log('Prev slide triggered');
        let prev = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(prev);
    }
    
    function goToSlide(index) {
        console.log('Go to slide:', index);
        showSlide(index);
        resetSliderInterval();
    }
    
    function startSlider() {
        console.log('Starting slider interval');
        slideInterval = setInterval(nextSlide, 10000); // Change slide every 5 seconds
    }
    
    function resetSliderInterval() {
        console.log('Resetting slider interval');
        clearInterval(slideInterval);
        startSlider();
    }
    
    // Event Listeners for buttons
    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('Prev button clicked');
            prevSlide();
            resetSliderInterval();
        });
    } else {
        console.error('Previous button not found!');
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('Next button clicked');
            nextSlide();
            resetSliderInterval();
        });
    } else {
        console.error('Next button not found!');
    }
    
    // Dot navigation
    dots.forEach((dot, index) => {
        dot.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('Dot clicked:', index);
            goToSlide(index);
        });
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            prevSlide();
            resetSliderInterval();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
            resetSliderInterval();
        }
    });
    
    // Pause on hover
    const sliderContainer = document.querySelector('.berita-slider-container');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', () => {
            console.log('Slider paused');
            clearInterval(slideInterval);
        });
        
        sliderContainer.addEventListener('mouseleave', () => {
            console.log('Slider resumed');
            startSlider();
        });
    }
    
    // Touch swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    if (sliderContainer) {
        sliderContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });
        
        sliderContainer.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
    }
    
    function handleSwipe() {
        const swipeThreshold = 50;
        
        if (touchEndX < touchStartX - swipeThreshold) {
            // Swipe left - next slide
            nextSlide();
            resetSliderInterval();
        }
        
        if (touchEndX > touchStartX + swipeThreshold) {
            // Swipe right - previous slide
            prevSlide();
            resetSliderInterval();
        }
    }
    
    // Start the slideshow
    console.log('Initializing slider with first slide');
    showSlide(currentSlide);
    startSlider();
    
    // Make functions globally available for testing
    window.nextBeritaSlide = nextSlide;
    window.prevBeritaSlide = prevSlide;
    window.goToBeritaSlide = goToSlide;
    window.showBeritaSlide = showSlide;
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing berita slider...');
    initBeritaSlider();
    
    // Existing initializations...
    if (typeof initHeroSlider === 'function') {
        initHeroSlider();
    }
    if (typeof fadeInOnScroll === 'function') {
        fadeInOnScroll();
        window.addEventListener('scroll', fadeInOnScroll);
    }
    
    // Mobile dropdown toggle
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
    
    if (typeof adjustDropdownPosition === 'function') {
        adjustDropdownPosition();
        window.addEventListener('resize', adjustDropdownPosition);
    }
});

// Fallback: Also try initializing after a short delay
setTimeout(() => {
    if (document.querySelector('.berita-slider')) {
        console.log('Fallback initialization...');
        initBeritaSlider();
    }
}, 1000);


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
    },
    'izin-belajar': {
        title: 'Pengurusan Izin Belajar dan Tugas Belajar bagi ASN',
        description: 'Penerbitan surat rekomendasi atau izin untuk melanjutkan pendidikan ke jenjang yang lebih tinggi bagi guru dan tenaga kependidikan ASN.',
        caraKerja: [
            'ASN mengajukan permohonan izin belajar ke Dinas Pendidikan',
            'Melampirkan dokumen persyaratan yang diperlukan',
            'Verifikasi kelengkapan dan keabsahan dokumen',
            'Evaluasi kesesuaian dengan kebutuhan dinas',
            'Penerbitan surat rekomendasi atau izin belajar',
            'Monitoring pelaksanaan tugas belajar'
        ],
        persyaratan: [
            'Surat permohonan izin belajar dari yang bersangkutan',
            'Rekomendasi dari atasan langsung',
            'Proposal rencana studi dan jadwal perkuliahan',
            'Fotokopi ijazah terakhir yang telah dilegalisir',
            'Surat keterangan diterima di perguruan tinggi',
            'SK pengangkatan sebagai ASN'
        ]
    },
};

// Fungsi untuk membuka popup deskripsi layanan
// Fungsi untuk membaca data popup dari file JSON
async function loadLayananPopupData() {
    try {
        const response = await fetch('data/layanan_popup.json');
        if (!response.ok) {
            throw new Error('Gagal memuat data layanan');
        }
        return await response.json();
    } catch (error) {
        console.error('Error loading popup data:', error);
        return null;
    }
}

// Fungsi untuk membuka popup deskripsi layanan (UPDATED - FIXED)
async function openLayananPopup(layananId) {
    console.log('🔍 openLayananPopup called with ID:', layananId);
    
    // PRIORITAS 1: Coba ambil dari layananDataJS (dari PHP)
    if (typeof layananDataJS !== 'undefined' && layananDataJS[layananId]) {
        console.log('✅ Found data in layananDataJS');
        const data = layananDataJS[layananId];
        showLayananPopup({
            title: data.title || layananId.replace(/-/g, ' ').toUpperCase(),
            description: data.popup_desc || data.description || '',
            caraKerja: data.cara_kerja || data.caraKerja || [],
            persyaratan: data.persyaratan || []
        });
        return;
    }
    
    // PRIORITAS 2: Coba load dari JSON
    try {
        console.log('📋 Attempting to load from JSON...');
        const popupData = await loadLayananPopupData();
        
        if (popupData && popupData[layananId]) {
            console.log('✅ Found data in JSON');
            const data = popupData[layananId];
            
            // Format cara kerja dan persyaratan dari string ke array
            function formatList(text) {
                if (!text) return [];
                return text.split('\n')
                    .map(line => line.trim())
                    .filter(line => line !== '');
            }

            const caraKerjaArray = formatList(data.cara_kerja);
            const persyaratanArray = formatList(data.persyaratan);
            
            showLayananPopup({
                title: data.title || layananId.replace(/-/g, ' ').toUpperCase(),
                description: data.popup_desc || 'Deskripsi tidak tersedia',
                caraKerja: caraKerjaArray,
                persyaratan: persyaratanArray
            });
            return;
        }
    } catch (error) {
        console.warn('⚠️ JSON loading failed:', error);
    }
    
    // PRIORITAS 3: Ambil dari kartu layanan yang visible
    console.log('🔎 Searching in visible cards...');
    let title = '';
    let description = '';
    const layananCards = document.querySelectorAll('.layanan-card');
    
    layananCards.forEach(card => {
        const h3 = card.querySelector('h3');
        const p = card.querySelector('p');
        
        // Check if this is the card we're looking for (by onclick or data attribute)
        const cardOnclick = card.getAttribute('onclick');
        if (cardOnclick && cardOnclick.includes(layananId)) {
            if (h3) title = h3.textContent;
            if (p) description = p.textContent;
        }
    });
    
    if (!title) {
        console.error('❌ Data not found for:', layananId);
        alert('Data layanan tidak ditemukan. Silakan refresh halaman.');
        return;
    }
    
    console.log('✅ Found data from visible cards:', title);
    showLayananPopup({
        title: title,
        description: description,
        caraKerja: [],
        persyaratan: []
    });
}

// Fungsi untuk menampilkan popup
function showLayananPopup(data) {
    // Handle caraKerja array
    let stepsHTML = '';
    if (data.caraKerja && data.caraKerja.length > 0) {
        stepsHTML = data.caraKerja.map((step, index) => 
            `<li>${step}</li>`
        ).join('');
    }
    
    // Handle persyaratan array
    let requirementsHTML = '';
    if (data.persyaratan && data.persyaratan.length > 0) {
        requirementsHTML = data.persyaratan.map(req => 
            `<li>${req}</li>`
        ).join('');
    }
    
    // Build sections conditionally
    let sectionsHTML = '';
    
    if (stepsHTML) {
        sectionsHTML += `
            <div class="popup-section">
                <h4>Cara Kerja Layanan:</h4>
                <ol>${stepsHTML}</ol>
            </div>
        `;
    }
    
    if (requirementsHTML) {
        sectionsHTML += `
            <div class="popup-section">
                <h4>Persyaratan:</h4>
                <ul>${requirementsHTML}</ul>
            </div>
        `;
    }
    
    const popupHTML = `
        <div class="popup-overlay" id="layananPopup">
            <div class="popup-content">
                <button class="popup-close" onclick="closeLayananPopup()">×</button>
                <h2 class="popup-title">${data.title}</h2>
                <p class="popup-description">${data.description}</p>
                
                ${sectionsHTML}
                
                <div class="popup-actions">
                    <button class="btn-primary" onclick="closeLayananPopup()">Tutup</button>
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

// BACKUP GAMBAR PEMIMPIN JIKA TIDAK TERTAMPILKAN
// Handle error gambar pimpinan
document.addEventListener('DOMContentLoaded', function() {
    const pimpinanImages = document.querySelectorAll('.pimpinan-img img');
    
    pimpinanImages.forEach(img => {
        img.addEventListener('error', function() {
            // Jika gambar gagal dimuat, gunakan placeholder SVG
            const placeholderSVG = `
                <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="60" cy="60" r="60" fill="#003399" fill-opacity="0.1"/>
                    <circle cx="60" cy="45" r="20" fill="#003399" fill-opacity="0.3"/>
                    <path d="M60 75 C40 75 30 85 30 95 C30 105 40 115 60 115 C80 115 90 105 90 95 C90 85 80 75 60 75 Z" fill="#003399" fill-opacity="0.3"/>
                </svg>
            `;
            this.parentElement.innerHTML = placeholderSVG;
            this.parentElement.classList.add('placeholder-avatar');
        });
    });
});

// Initialize Swiper for Layanan Section - VERSION FIXED
function initLayananSwiper() {
    // Pastikan elemen swiper ada
    const swiperEl = document.querySelector('.layanan-swiper');
    if (!swiperEl) {
        console.error('Swiper element not found');
        return null;
    }

    const layananSwiper = new Swiper('.layanan-swiper', {
        slidesPerView: 3,
        spaceBetween: 25,
        loop: true,
        autoplay: {
            delay: 2000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        speed: 800,
        navigation: {
            nextEl: '.swiper-custom-next',
            prevEl: '.swiper-custom-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
            dynamicBullets: true,
        },
        breakpoints: {
            // When window width is >= 320px
            320: {
                slidesPerView: 1,
                spaceBetween: 15,
            },
            // When window width is >= 768px
            768: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            // When window width is >= 1024px
            1024: {
                slidesPerView: 3,
                spaceBetween: 25,
            }
        },
        // Event handlers untuk debugging
        on: {
            init: function() {
                console.log('✅ Layanan Swiper initialized successfully');
                console.log('Navigation elements:', {
                    prev: this.navigation.prevEl,
                    next: this.navigation.nextEl
                });
            },
            slideChange: function() {
                console.log('Slide changed to index:', this.realIndex);
            }
        }
    });

    // Debug: Cek apakah button navigasi terdeteksi
    console.log('Navigation buttons detected:', {
        prev: document.querySelector('.swiper-custom-prev'),
        next: document.querySelector('.swiper-custom-next')
    });

    // Tambahkan event listeners manual sebagai fallback
    const prevBtn = document.querySelector('.swiper-custom-prev');
    const nextBtn = document.querySelector('.swiper-custom-next');

    if (prevBtn) {
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Prev button clicked');
            layananSwiper.slidePrev();
            // Stop autoplay sementara saat interaksi manual
            layananSwiper.autoplay.stop();
            setTimeout(() => {
                layananSwiper.autoplay.start();
            }, 5000);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Next button clicked');
            layananSwiper.slideNext();
            // Stop autoplay sementara saat interaksi manual
            layananSwiper.autoplay.stop();
            setTimeout(() => {
                layananSwiper.autoplay.start();
            }, 5000);
        });
    }

    return layananSwiper;
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Tunggu sedikit untuk memastikan semua element sudah terrender
    setTimeout(() => {
        // Initialize layanan swiper if on layanan page
        if (document.querySelector('.layanan-swiper')) {
            console.log('🔄 Initializing Layanan Swiper...');
            const swiper = initLayananSwiper();
            
            if (swiper) {
                console.log('🎉 Layanan Swiper ready!');
                
                // Test manual navigation
                window.testSwiper = swiper;
                console.log('Test commands:');
                console.log('testSwiper.slideNext() - untuk next slide');
                console.log('testSwiper.slidePrev() - untuk previous slide');
            }
        }
    }, 100);
});

// Fallback initialization
window.addEventListener('load', function() {
    if (document.querySelector('.layanan-swiper') && !window.layananSwiper) {
        console.log('🔄 Fallback initialization...');
        window.layananSwiper = initLayananSwiper();
    }
});

        // Smooth scroll to section
        function scrollToSection(sectionId) {
            document.getElementById(sectionId).scrollIntoView({
                behavior: 'smooth'
            });
        }

        // Update active nav dot based on scroll position
        function updateActiveNav() {
            const sections = document.querySelectorAll('.profil-fullscreen-section');
            const navDots = document.querySelectorAll('.nav-dot');
            
            let currentSection = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (window.scrollY >= sectionTop - 100 && window.scrollY < sectionTop + sectionHeight - 100) {
                    currentSection = section.id;
                }
            });
            
            navDots.forEach(dot => {
                dot.classList.remove('active');
                if (dot.getAttribute('data-section') === currentSection) {
                    dot.classList.add('active');
                }
            });
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Update nav on scroll
            window.addEventListener('scroll', updateActiveNav);
            
            // Click event for nav dots
            document.querySelectorAll('.nav-dot').forEach(dot => {
                dot.addEventListener('click', function(e) {
                    e.preventDefault();
                    const sectionId = this.getAttribute('data-section');
                    scrollToSection(sectionId);
                });
            });
            
            // Initial update
            updateActiveNav();
        });

// Smooth scroll to section
function scrollToSection(sectionId) {
    document.getElementById(sectionId).scrollIntoView({
        behavior: 'smooth'
    });
}

// Update active nav dot based on scroll position
function updateActiveNav() {
    const sections = document.querySelectorAll('.profil-fullscreen-section');
    const navDots = document.querySelectorAll('.nav-dot');
    
    let currentSection = '';
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        if (window.scrollY >= sectionTop - 100 && window.scrollY < sectionTop + sectionHeight - 100) {
            currentSection = section.id;
        }
    });
    
    navDots.forEach(dot => {
        dot.classList.remove('active');
        if (dot.getAttribute('data-section') === currentSection) {
            dot.classList.add('active');
        }
    });
}

// Fade in on scroll function
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

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Update nav on scroll
    window.addEventListener('scroll', updateActiveNav);
    
    // Fade in on scroll
    window.addEventListener('scroll', fadeInOnScroll);
    
    // Initial fade in check
    fadeInOnScroll();
    
    // Click event for nav dots
    document.querySelectorAll('.nav-dot').forEach(dot => {
        dot.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('data-section');
            scrollToSection(sectionId);
        });
    });
    
    // Initial update
    updateActiveNav();
    
    console.log('Fade in initialized for', document.querySelectorAll('.fade-in').length, 'elements');
});