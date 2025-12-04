// Hero Slider Functionality - SIMPLIFIED VERSION
let heroSlideIndex = 0;
let heroAutoSlide;

function initHeroSlider() {
    function createHeroSliderButtons() {
        console.log('🛠 Creating hero slider buttons...');
        
        const heroSection = document.querySelector('.hero');
        if (!heroSection) {
            console.error('❌ Hero section not found!');
            return;
        }
        
        // Cek apakah tombol sudah ada
        let prevBtn = document.querySelector('.hero-prev-btn');
        let nextBtn = document.querySelector('.hero-next-btn');
        
        // Jika tombol tidak ada, buat baru
        if (!prevBtn) {
            prevBtn = document.createElement('button');
            prevBtn.className = 'hero-slider-btn hero-prev-btn';
            prevBtn.innerHTML = '<span class="material-symbols-outlined">chevron_left</span>';
            heroSection.appendChild(prevBtn);
            console.log('✅ Created prev button');
        }
        
        if (!nextBtn) {
            nextBtn = document.createElement('button');
            nextBtn.className = 'hero-slider-btn hero-next-btn';
            nextBtn.innerHTML = '<span class="material-symbols-outlined">chevron_right</span>';
            heroSection.appendChild(nextBtn);
            console.log('✅ Created next button');
        }
        
        // Tambahkan styling inline untuk memastikan terlihat
        [prevBtn, nextBtn].forEach(btn => {
            btn.style.cssText = `
                position: absolute !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
                width: 60px !important;
                height: 60px !important;
                background: #FF0000 !important;
                color: white !important;
                border: 3px solid yellow !important;
                border-radius: 50% !important;
                font-size: 24px !important;
                cursor: pointer !important;
                z-index: 99999 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                opacity: 1 !important;
                visibility: visible !important;
            `;
            
            // Position
            if (btn.classList.contains('hero-prev-btn')) {
                btn.style.left = '30px !important';
            }
            if (btn.classList.contains('hero-next-btn')) {
                btn.style.right = '30px !important';
            }
        });
        
        return { prevBtn, nextBtn };
    }
    console.log('🚀 Initializing Hero Slider...');
    
    const heroSlides = document.querySelectorAll('.hero-slider .slide');
    const prevBtn = document.querySelector('.hero-prev-btn');
    const nextBtn = document.querySelector('.hero-next-btn');
    
    console.log('Hero elements found:', {
        slides: heroSlides.length,
        prevBtn: !!prevBtn,
        nextBtn: !!nextBtn
    });
    
    if (heroSlides.length === 0) {
        console.error('❌ No hero slides found!');
        return;
    }
    
    function showHeroSlide(n) {
        // Hide all slides
        heroSlides.forEach(slide => {
            slide.classList.remove('active');
        });
        
        // Calculate new index
        heroSlideIndex = n;
        if (heroSlideIndex >= heroSlides.length) heroSlideIndex = 0;
        if (heroSlideIndex < 0) heroSlideIndex = heroSlides.length - 1;
        
        // Show current slide
        heroSlides[heroSlideIndex].classList.add('active');
        
        console.log(`Showing hero slide: ${heroSlideIndex}`);
    }
    
    function nextHeroSlide() {
        showHeroSlide(heroSlideIndex + 1);
    }
    
    function prevHeroSlide() {
        showHeroSlide(heroSlideIndex - 1);
    }
    
    function startAutoSlide() {
        clearInterval(heroAutoSlide);
        heroAutoSlide = setInterval(nextHeroSlide, 5000);
        console.log('Auto slide started');
    }
    
    function stopAutoSlide() {
        clearInterval(heroAutoSlide);
        console.log('Auto slide stopped');
    }
    
    // Event Listeners for buttons
    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Prev button clicked');
            prevHeroSlide();
            stopAutoSlide();
            setTimeout(startAutoSlide, 5000);
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Next button clicked');
            nextHeroSlide();
            stopAutoSlide();
            setTimeout(startAutoSlide, 5000);
        });
    }
    
    // Pause on hover
    const heroContainer = document.querySelector('.hero');
    if (heroContainer) {
        heroContainer.addEventListener('mouseenter', stopAutoSlide);
        heroContainer.addEventListener('mouseleave', startAutoSlide);
    }
    
    // Initialize
    showHeroSlide(0);
    startAutoSlide();
    
    // Make functions globally available
    window.nextHeroSlide = nextHeroSlide;
    window.prevHeroSlide = prevHeroSlide;
    
    console.log('✅ Hero Slider initialized successfully');
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM Content Loaded - Starting debug...');
    
    // 1. Buat tombol dulu (jika belum ada)
    createHeroSliderButtons();
    
    // 2. Inisialisasi slider
    setTimeout(() => {
        initHeroSlider();
    }, 100);
    
    // 3. Log semua elemen dengan class 'hero'
    const heroElements = document.querySelectorAll('[class*="hero"]');
    console.log('DEBUG: All hero elements:', heroElements.length);
    heroElements.forEach(el => {
        console.log(el.className, el.tagName, el);
    });
    
    // Initialize hero slider
    initHeroSlider();
    
    // Initialize berita slider if exists
    if (typeof initBeritaSlider === 'function' && document.querySelector('.berita-slider')) {
        initBeritaSlider();
    }
    
    // Initialize fade in effect
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
    
    // Adjust dropdown position
    if (typeof adjustDropdownPosition === 'function') {
        adjustDropdownPosition();
        window.addEventListener('resize', adjustDropdownPosition);
    }
});

// Fallback initialization after page load
window.addEventListener('load', function() {
    console.log('🔄 Page fully loaded, checking hero slider...');
    
    // Check if hero slider initialized
    const heroSlides = document.querySelectorAll('.hero-slider .slide');
    const activeSlide = document.querySelector('.hero-slider .slide.active');
    
    if (heroSlides.length > 0 && !activeSlide) {
        console.log('🔄 Re-initializing hero slider...');
        initHeroSlider();
    }
});