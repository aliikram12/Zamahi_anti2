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
    <meta property="og:image" content="<?= SITE_URL ?>/assets/images/og-image.jpg">
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
                    <li><a href="#events" class="nav-link">Events</a></li>
                    <li><a href="#services" class="nav-link">Services</a></li>
                    <li><a href="#menu-booking" class="nav-link">Menu</a></li>
                    <li><a href="#testimonials" class="nav-link">Reviews</a></li>
                    <li><a href="#gallery" class="nav-link">Gallery</a></li>
                </ul>
                <a href="#menu-booking" class="nav-link nav-cta nav-cta-desktop">Book Now</a>
            </div>
        </div>
    </nav>
