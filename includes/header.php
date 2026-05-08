<?php
/**
 * ZAMAHI Luxury Catering - Shared Header
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- SEO -->
    <title>ZAMAHI Luxury Catering | Premium Catering London &amp; UK</title>
    <meta name="description" content="ZAMAHI Luxury Catering delivers premium culinary experiences across the UK. Weddings, corporate events, private celebrations – fully customizable menus crafted by expert chefs.">
    <meta name="keywords" content="luxury catering London, wedding catering UK, corporate catering London, private chef London, event catering, premium catering service">
    <meta name="author" content="Zamahi Ltd">

    <!-- OpenGraph -->
    <meta property="og:title" content="ZAMAHI Luxury Catering | Luxury Catering. Exceptional Events.">
    <meta property="og:description" content="Premium culinary experiences for weddings, corporate events, and private celebrations across the United Kingdom.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= SITE_URL ?>">
    <meta property="og:image" content="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1200&h=630&fit=crop&crop=center&auto=format&q=90">
    <meta property="og:locale" content="en_GB">

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "CateringService",
        "name": "ZAMAHI Luxury Catering",
        "description": "Premium luxury catering service across the United Kingdom",
        "url": "<?= SITE_URL ?>",
        "telephone": "<?= SITE_PHONE ?>",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "London",
            "addressCountry": "GB"
        },
        "areaServed": "United Kingdom",
        "priceRange": "£££"
    }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>



    <!-- ═══════════════ NAVIGATION ═══════════════ -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="#hero" class="nav-logo">
                <div class="logo-plate">
                    <svg width="40" height="32" viewBox="0 0 40 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 2C15 2 12 5 12 8V14C12 18 16 20 20 20C24 20 28 18 28 14V8C28 5 25 2 20 2Z" fill="url(#plateGradient)" stroke="url(#plateBorder)" stroke-width="1.5"/>
                        <path d="M12 12C12 16 16 18 20 18C24 18 28 16 28 12" fill="none" stroke="url(#plateRim)" stroke-width="0.8" opacity="0.6"/>
                        <ellipse cx="20" cy="10" rx="8" ry="3" fill="url(#plateInner)" opacity="0.3"/>
                        <defs>
                            <linearGradient id="plateGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#D4AF37;stop-opacity:1" />
                                <stop offset="50%" style="stop-color:#F4E87C;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#B8960F;stop-opacity:1" />
                            </linearGradient>
                            <linearGradient id="plateBorder" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#B8960F;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#8B6914;stop-opacity:1" />
                            </linearGradient>
                            <linearGradient id="plateRim" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#D4AF37;stop-opacity:0.8" />
                                <stop offset="100%" style="stop-color:#B8960F;stop-opacity:0.6" />
                            </linearGradient>
                            <radialGradient id="plateInner" cx="50%" cy="50%" r="50%">
                                <stop offset="0%" style="stop-color:#FFFFFF;stop-opacity:0.2" />
                                <stop offset="100%" style="stop-color:#D4AF37;stop-opacity:0.1" />
                            </radialGradient>
                        </defs>
                    </svg>
                </div>
                <span class="logo-text">ZAMAHI</span>
                <span class="logo-sub">LUXURY CATERING</span>
            </a>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <span></span><span></span><span></span>
            </button>
            <div class="nav-overlay" id="navOverlay"></div>
            <div class="nav-menu-wrapper">
                <ul class="nav-menu" id="navMenu">
                    <li><a href="#hero" class="nav-link active">Home</a></li>
                    <li><a href="#about" class="nav-link">About</a></li>
                    <li class="nav-dropdown">
                        <a href="#events" class="nav-link">Events <i class="fas fa-chevron-down"></i></a>
                        <div class="mega-menu">
                            <div class="mega-container">
                                <div class="mega-section">
                                    <h4>Event Categories</h4>
                                    <div class="mega-grid">
                                        <a href="#events" class="mega-item" data-event="Wedding">
                                            <i class="fas fa-ring"></i>
                                            <span>Weddings</span>
                                        </a>
                                        <a href="#events" class="mega-item" data-event="Corporate">
                                            <i class="fas fa-building"></i>
                                            <span>Corporate Events</span>
                                        </a>
                                        <a href="#events" class="mega-item" data-event="Private Party">
                                            <i class="fas fa-users"></i>
                                            <span>Private Parties</span>
                                        </a>
                                        <a href="#events" class="mega-item" data-event="Sports">
                                            <i class="fas fa-tv"></i>
                                            <span>Sports Screenings</span>
                                        </a>
                                        <a href="#events" class="mega-item" data-event="Charity">
                                            <i class="fas fa-heart"></i>
                                            <span>Charity Events</span>
                                        </a>
                                        <a href="#events" class="mega-item" data-event="Launch">
                                            <i class="fas fa-rocket"></i>
                                            <span>Product Launches</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="mega-section">
                                    <h4>Popular Services</h4>
                                    <ul class="mega-list">
                                        <li><a href="#services">Waiter Hire</a></li>
                                        <li><a href="#services">Live Cooking</a></li>
                                        <li><a href="#services">Event Decoration</a></li>
                                        <li><a href="#services">BBQ Setup</a></li>
                                        <li><a href="#services">Security Staff</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li><a href="#services" class="nav-link">Services</a></li>
                    <li><a href="#menu-booking" class="nav-link">Menu</a></li>
                    <li><a href="#testimonials" class="nav-link">Reviews</a></li>
                    <li><a href="#gallery" class="nav-link">Gallery</a></li>
                </ul>
                <a href="#menu-booking" class="nav-link nav-cta nav-cta-desktop">Book Now</a>
            </div>
        </div>
    </nav>
