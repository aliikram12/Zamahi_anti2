<?php
/**
 * ZAMAHI Luxury Catering - Configuration
 */

// Prevent direct access
if (!defined('ZAMAHI_ROOT')) {
    define('ZAMAHI_ROOT', dirname(__DIR__));
}

// ─── Database ───────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'zamahi_catering');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ─── Site ───────────────────────────────────────────
// Auto-detect site URL based on server environment
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
// Remove trailing slashes and get the base path
$basePath = rtrim($scriptDir, '/');
if ($basePath === '' || $basePath === '\\' || $basePath === '/') {
    $basePath = '';
} else {
    $basePath = str_replace('\\', '/', $basePath);
}
define('SITE_URL', $protocol . '://' . $host . $basePath);

define('SITE_NAME', 'ZAMAHI LUXURY CATERING');
define('SITE_TAGLINE', 'Luxury Catering. Exceptional Events.');
define('SITE_EMAIL', 'info@zamahi.co.uk');
define('SITE_PHONE', '+44 20 7123 4567');
define('SITE_ADDRESS', 'London, United Kingdom');
define('LEGAL_ENTITY', 'Zamahi Ltd');

// ─── Pricing ────────────────────────────────────────
define('BASE_PRICE_PER_HEAD', 25.00);
define('VAT_RATE', 0.20); // 20%
define('ALLERGY_SURCHARGE', 2.50); // £2.50 per allergy guest
define('DISCOUNT_50_GUESTS', 0.05); // 5% off for 50+ guests
define('FREE_DELIVERY_THRESHOLD', 100); // 100+ guests
define('FREE_WAITER_THRESHOLD', 150); // 150+ guests
define('KIDS_FREE_AGE', 4); // Under 4 free
define('DELIVERY_CHARGE', 50.00); // Standard delivery
define('WAITER_CHARGE', 150.00); // Standard waiter service

// ─── Additional Services Pricing ────────────────────
define('SERVICES_PRICING', json_encode([
    'waiter_hire'      => ['name' => 'Professional Waiter Hire', 'price' => 150.00],
    'security_staff'   => ['name' => 'Security Staff', 'price' => 200.00],
    'live_cooking'     => ['name' => 'Live Cooking Station', 'price' => 350.00],
    'bbq_setup'        => ['name' => 'BBQ Setup & Equipment', 'price' => 250.00],
    'event_decoration' => ['name' => 'Event Decoration Assistance', 'price' => 400.00],
    'screens'          => ['name' => 'Screens (Sports/Corporate)', 'price' => 175.00],
]));

// ─── SMTP (PHPMailer) ───────────────────────────────
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_FROM_EMAIL', 'bookings@zamahi.co.uk');
define('SMTP_FROM_NAME', 'ZAMAHI Luxury Catering');

// ─── Stripe (Payments) ──────────────────────────────
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_YOUR_KEY_HERE');
define('STRIPE_SECRET_KEY', 'sk_test_YOUR_KEY_HERE');
define('STRIPE_CURRENCY', 'gbp');

// ─── Google Maps ────────────────────────────────────
define('GOOGLE_MAPS_API_KEY', 'YOUR_API_KEY_HERE');

// ─── File Paths ─────────────────────────────────────
define('INVOICE_DIR', ZAMAHI_ROOT . '/invoices/');
define('UPLOAD_DIR', ZAMAHI_ROOT . '/assets/images/uploads/');
define('GALLERY_DIR', ZAMAHI_ROOT . '/assets/images/gallery/');

// ─── Session ────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─── Timezone ───────────────────────────────────────
date_default_timezone_set('Europe/London');
