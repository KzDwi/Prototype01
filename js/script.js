
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