/**
 * ZAMAHI Luxury Catering - Main JavaScript (Enhanced)
 * Navigation, animations, carousel, gallery, lightbox
 */

document.addEventListener('DOMContentLoaded', () => {
    initNavbar();
    initTopProjectsBar();
    initToastSystem();
    initScrollAnimations();
    initRevealAnimations();
    initTestimonialCarousel();
    initTestimonialFilters();
    initEventCards();
    initSmoothScroll();
    initParallax();
});

/* ═══════════════ TOP PROJECTS BAR ═══════════════ */
function initTopProjectsBar() {
    const trigger = document.getElementById('projectsTrigger');
    const bar = document.getElementById('topProjectsBar');
    const dropdown = document.getElementById('projectsDropdown');

    if (!trigger || !bar) return;

    trigger.addEventListener('click', () => {
        bar.classList.toggle('active');
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!bar.contains(e.target)) {
            bar.classList.remove('active');
        }
    });

    // Project card interactions
    document.querySelectorAll('.project-card').forEach(card => {
        card.addEventListener('click', () => {
            const projectType = card.dataset.project;
            // Scroll to booking section
            document.getElementById('menu-booking').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            // Close dropdown
            bar.classList.remove('active');
        });
    });
}

/* ═══════════════ NAVBAR ═══════════════ */
function initNavbar() {
    const navbar = document.getElementById('navbar');
    const toggle = document.getElementById('navToggle');
    const menuWrapper = document.querySelector('.nav-menu-wrapper');
    const menu = document.getElementById('navMenu');
    const overlay = document.getElementById('navOverlay');
    const links  = document.querySelectorAll('.nav-link');

    // Scroll effect with throttle
    let lastScroll = 0;
    window.addEventListener('scroll', () => {
        const currentScroll = window.scrollY;
        
        // Add scrolled class (accounting for top projects bar height)
        navbar.classList.toggle('scrolled', currentScroll > 117);
        
        // Hide/show navbar on scroll direction
        if (currentScroll > 200) {
            if (currentScroll > lastScroll && !menuWrapper.classList.contains('active')) {
                navbar.style.transform = 'translateY(-157px)'; // Hide above top projects bar
            } else {
                navbar.style.transform = 'translateY(0)';
            }
        } else {
            navbar.style.transform = 'translateY(0)';
        }
        
        lastScroll = currentScroll;
    });

    // Mobile toggle with animation
    toggle.addEventListener('click', () => {
        toggle.classList.toggle('active');
        menuWrapper.classList.toggle('active');
        if (overlay) overlay.classList.toggle('active');
        
        // Prevent body scroll when menu is open
        document.body.style.overflow = menuWrapper.classList.contains('active') ? 'hidden' : '';
        
        // Animate menu items
        const menuItems = menu.querySelectorAll('.nav-link');
        menuItems.forEach((item, index) => {
            if (menuWrapper.classList.contains('active')) {
                item.style.animation = `menuItemFadeIn 0.4s ease forwards ${index * 0.05}s`;
            } else {
                item.style.animation = 'none';
            }
        });
    });

    // Close on overlay click
    if (overlay) {
        overlay.addEventListener('click', () => {
            toggle.classList.remove('active');
            menuWrapper.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }

    // Close on link click (including CTA)
    const menuLinks = menuWrapper.querySelectorAll('.nav-link');
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            toggle.classList.remove('active');
            menuWrapper.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    });

    // Active link on scroll with smooth transitions

    const sections = document.querySelectorAll('section[id]');
    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY + 150;
        sections.forEach(section => {
            const top = section.offsetTop;
            const height = section.offsetHeight;
            const id = section.getAttribute('id');
            const link = document.querySelector(`.nav-link[href="#${id}"]`);
            if (link) {
                if (scrollY >= top && scrollY < top + height) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            }
        });
    });
}

// Add CSS animation for menu items
const menuItemStyle = document.createElement('style');
menuItemStyle.textContent = `
    @keyframes menuItemFadeIn {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
`;
document.head.appendChild(menuItemStyle);

/* ═══════════════ SCROLL ANIMATIONS ═══════════════ */
function initScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { 
        threshold: 0.1, 
        rootMargin: '0px 0px -50px 0px' 
    });

    document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
}

/* ═══════════════ REVEAL ANIMATIONS ═══════════════ */
function initRevealAnimations() {
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { 
        threshold: 0.15, 
        rootMargin: '0px 0px -80px 0px' 
    });

    // Observe all reveal elements
    document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right').forEach(el => {
        revealObserver.observe(el);
    });
}

/* ═══════════════ SMOOTH SCROLL ═══════════════ */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/* ═══════════════ PARALLAX EFFECT ═══════════════ */
function initParallax() {
    const heroBg = document.querySelector('.hero-bg');
    if (!heroBg) return;

    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;
        if (scrolled < window.innerHeight) {
            heroBg.style.transform = `translateY(${scrolled * 0.3}px) scale(1.05)`;
        }
    });
}

/* ═══════════════ TESTIMONIAL CAROUSEL ═══════════════ */
let carouselIndex = 0;
let carouselCards = [];
let autoSlideInterval;

function initTestimonialCarousel() {
    const track = document.getElementById('testimonialTrack');
    if (!track) return;

    carouselCards = Array.from(track.children);
    if (carouselCards.length === 0) return;

    // Create dots
    const dotsContainer = document.getElementById('carouselDots');
    carouselCards.forEach((_, i) => {
        const dot = document.createElement('button');
        dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
        dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
        dot.onclick = () => goToSlide(i);
        dotsContainer.appendChild(dot);
    });

    // Add navigation buttons
    const carousel = document.querySelector('.testimonial-carousel');
    if (carousel) {
        // Check if buttons already exist to avoid duplication
        const existingBtns = carousel.querySelectorAll('.carousel-prev, .carousel-next');
        if (existingBtns.length > 0) return; // Buttons already exist, don't add more
        
        const prevBtn = document.createElement('button');
        prevBtn.className = 'carousel-btn carousel-prev';
        prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prevBtn.setAttribute('aria-label', 'Previous testimonial');
        prevBtn.onclick = () => moveCarousel(-1);
        
        const nextBtn = document.createElement('button');
        nextBtn.className = 'carousel-btn carousel-next';
        nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
        nextBtn.setAttribute('aria-label', 'Next testimonial');
        nextBtn.onclick = () => moveCarousel(1);
        
        const controls = carousel.querySelector('.carousel-controls');
        if (controls) {
            // Clear any existing buttons in controls first
            controls.innerHTML = '';
            controls.appendChild(prevBtn);
            controls.appendChild(nextBtn);
        }
    }

    updateCarousel();
    startAutoSlide();
}

function moveCarousel(dir) {
    carouselIndex = (carouselIndex + dir + carouselCards.length) % carouselCards.length;
    updateCarousel();
    resetAutoSlide();
}

function goToSlide(index) {
    carouselIndex = index;
    updateCarousel();
    resetAutoSlide();
}

function updateCarousel() {
    const track = document.getElementById('testimonialTrack');
    if (!track) return;
    
    // Add smooth transition
    track.style.transition = 'transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
    track.style.transform = `translateX(-${carouselIndex * 100}%)`;

    document.querySelectorAll('.carousel-dot').forEach((dot, i) => {
        dot.classList.toggle('active', i === carouselIndex);
    });
}

function startAutoSlide() {
    autoSlideInterval = setInterval(() => moveCarousel(1), 6000);
}

function resetAutoSlide() {
    clearInterval(autoSlideInterval);
    startAutoSlide();
}

/* ═══════════════ TESTIMONIAL FILTERS ═══════════════ */
function initTestimonialFilters() {
    const filterBtns = document.querySelectorAll('.testimonial-filters .filter-btn');
    const track = document.getElementById('testimonialTrack');
    if (!track) return;

    const allCards = Array.from(track.querySelectorAll('.testimonial-card'));

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active state with animation
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filter = btn.dataset.filter;
            track.style.opacity = '0';
            track.style.transform = 'translateY(10px)';
            
            setTimeout(() => {
                track.innerHTML = '';

                const filtered = filter === 'all'
                    ? allCards
                    : allCards.filter(c => c.dataset.category === filter);

                filtered.forEach(card => track.appendChild(card.cloneNode(true)));
                carouselCards = Array.from(track.children);
                carouselIndex = 0;
                
                // Reset carousel position
                track.style.transition = 'none';
                track.style.transform = 'translateX(0)';
                updateCarousel();

                // Fade back in
                track.style.opacity = '1';
                track.style.transform = 'translateY(0)';
                track.style.transition = 'opacity 0.4s ease, transform 0.4s ease';

                // Rebuild dots
                const dotsContainer = document.getElementById('carouselDots');
                dotsContainer.innerHTML = '';
                carouselCards.forEach((_, i) => {
                    const dot = document.createElement('button');
                    dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
                    dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
                    dot.onclick = () => goToSlide(i);
                    dotsContainer.appendChild(dot);
                });
            }, 200);
        });
    });
}

/* ═══════════════ EVENT CARDS ═══════════════ */
function initEventCards() {
    document.querySelectorAll('.event-card').forEach(card => {
        card.addEventListener('click', () => {
            const eventName = card.dataset.event;
            const select = document.querySelector('select[name="event_type"]');
            if (select) {
                select.value = eventName;
            }
            document.getElementById('menu-booking').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        });
    });
}

/* ═══════════════ LIGHTBOX ═══════════════ */
function openLightbox(src) {
    const lb = document.getElementById('lightbox');
    const img = document.getElementById('lightboxImg');
    if (!lb || !img) return;
    
    img.src = src;
    lb.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Focus trap for accessibility
    lb.focus();
}

function closeLightbox() {
    const lb = document.getElementById('lightbox');
    if (!lb) return;
    
    lb.classList.remove('active');
    document.body.style.overflow = '';
}

// Close on ESC or backdrop click
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeLightbox();
});
document.getElementById('lightbox')?.addEventListener('click', (e) => {
    if (e.target === e.currentTarget) closeLightbox();
});

/* ═══════════════ TOAST NOTIFICATIONS ═══════════════ */
let toastContainer = null;

function initToastSystem() {
    toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container';
    document.body.appendChild(toastContainer);
}

function showToast(message, type = 'info', title = '', duration = 5000) {
    if (!toastContainer) initToastSystem();

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;

    const iconMap = {
        success: 'check-circle',
        error: 'times-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };

    toast.innerHTML = `
        <div class="toast-icon">
            <i class="fas fa-${iconMap[type] || 'info-circle'}"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">${title || type.charAt(0).toUpperCase() + type.slice(1)}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="hideToast(this.parentElement)">
            <i class="fas fa-times"></i>
        </button>
    `;

    toastContainer.appendChild(toast);

    // Trigger animation
    setTimeout(() => toast.classList.add('show'), 10);

    // Auto hide
    if (duration > 0) {
        setTimeout(() => hideToast(toast), duration);
    }

    return toast;
}

function hideToast(toast) {
    toast.classList.remove('show');
    setTimeout(() => {
        if (toast.parentElement) {
            toast.parentElement.removeChild(toast);
        }
    }, 300);
}

/* ═══════════════ LOADING SYSTEM ═══════════════ */
function showLoading(message = 'Loading...', subtext = '') {
    let overlay = document.querySelector('.loading-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = `
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <div class="loading-text">${message}</div>
                <div class="loading-subtext">${subtext}</div>
            </div>
        `;
        document.body.appendChild(overlay);
    } else {
        overlay.querySelector('.loading-text').textContent = message;
        overlay.querySelector('.loading-subtext').textContent = subtext;
    }
    overlay.classList.add('active');
}

function hideLoading() {
    const overlay = document.querySelector('.loading-overlay');
    if (overlay) {
        overlay.classList.remove('active');
        setTimeout(() => overlay.remove(), 300);
    }
}

/* ═══════════════ ENHANCED BUTTON INTERACTIONS ═══════════════ */
// Add ripple effect to buttons
document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        const rect = this.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const ripple = document.createElement('span');
        ripple.style.cssText = `
            position: absolute;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            pointer-events: none;
            width: 100px;
            height: 100px;
            left: ${x - 50}px;
            top: ${y - 50}px;
            transform: scale(0);
            animation: rippleEffect 0.6s ease-out;
        `;

        this.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    });
});

// Add ripple animation
const rippleStyle = document.createElement('style');
rippleStyle.textContent = `
    @keyframes rippleEffect {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(rippleStyle);

/* ═══════════════ CARD HOVER EFFECTS ═══════════════ */
// Add tilt effect to service cards
document.querySelectorAll('.service-card, .event-card').forEach(card => {
    card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        const rotateX = (y - centerY) / 20;
        const rotateY = (centerX - x) / 20;
        
        card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-10px)`;
    });
    
    card.addEventListener('mouseleave', () => {
        card.style.transform = '';
    });
});
