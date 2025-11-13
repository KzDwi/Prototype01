
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