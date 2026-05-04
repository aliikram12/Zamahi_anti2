<?php
/**
 * ZAMAHI Luxury Catering - Helper Functions
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─── CSRF Protection ────────────────────────────────
function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCsrfToken()) . '">';
}

// ─── Input Sanitization ─────────────────────────────
function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function sanitizeEmail(string $email): string {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

// ─── Booking Reference ──────────────────────────────
function generateBookingRef(): string {
    return 'ZAM-' . strtoupper(date('Ymd')) . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}

// ─── Currency Formatting ─────────────────────────────
function formatCurrency(float $amount): string {
    return '£' . number_format($amount, 2);
}

// ─── Pricing Calculation ─────────────────────────────
function calculatePricing(array $data): array {
    $guestCount   = max(1, (int)($data['guest_count'] ?? 1));
    $kidsCount    = max(0, (int)($data['kids_count'] ?? 0));
    $kidsFree     = max(0, (int)($data['kids_free'] ?? 0));
    $billableGuests = $guestCount + ($kidsCount - $kidsFree);

    $perHead  = BASE_PRICE_PER_HEAD;
    $subtotal = $billableGuests * $perHead;

    // Guest discount
    $discount = 0;
    if ($guestCount >= 50) {
        $discount = $subtotal * DISCOUNT_50_GUESTS;
    }

    // Allergy surcharges
    $allergyCharges = 0;
    if (!empty($data['allergies']) && is_array($data['allergies'])) {
        foreach ($data['allergies'] as $allergy) {
            $count = max(0, (int)($allergy['guest_count'] ?? 0));
            $allergyCharges += $count * ALLERGY_SURCHARGE;
        }
    }

    // Starter add-ons
    $starterCharges = 0;
    if (!empty($data['starters']) && is_array($data['starters'])) {
        foreach ($data['starters'] as $starter) {
            $starterCharges += (float)($starter['price'] ?? 0) * $billableGuests;
        }
    }

    // Additional services
    $servicesTotal = 0;
    if (!empty($data['services']) && is_array($data['services'])) {
        foreach ($data['services'] as $service) {
            $servicesTotal += (float)($service['price'] ?? 0);
        }
    }

    // Delivery
    $deliveryCharge = DELIVERY_CHARGE;
    if ($guestCount >= FREE_DELIVERY_THRESHOLD) {
        $deliveryCharge = 0;
    }

    // Waiter (free for 150+ guests if not already added as service)
    if ($guestCount >= FREE_WAITER_THRESHOLD) {
        // Remove waiter charge if already in services
        // This is handled on the frontend
    }

    // Drinks
    $drinksCharge = 0;
    if (!empty($data['drinks']) && is_array($data['drinks'])) {
        foreach ($data['drinks'] as $drink) {
            $drinksCharge += (float)($drink['price'] ?? 0) * $billableGuests;
        }
    }

    $preVat = ($subtotal - $discount) + $allergyCharges + $starterCharges + $servicesTotal + $deliveryCharge + $drinksCharge;
    $vat = $preVat * VAT_RATE;
    $grandTotal = $preVat + $vat;

    return [
        'per_head_cost'    => $perHead,
        'billable_guests'  => $billableGuests,
        'subtotal'         => round($subtotal, 2),
        'discount'         => round($discount, 2),
        'allergy_charges'  => round($allergyCharges, 2),
        'starter_charges'  => round($starterCharges, 2),
        'services_total'   => round($servicesTotal, 2),
        'delivery_charge'  => round($deliveryCharge, 2),
        'drinks_charge'    => round($drinksCharge, 2),
        'vat'              => round($vat, 2),
        'grand_total'      => round($grandTotal, 2),
    ];
}

// ─── Flash Messages ──────────────────────────────────
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ─── Redirect ────────────────────────────────────────
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

// ─── File Upload ─────────────────────────────────────
function uploadFile(array $file, string $dir, array $allowedTypes = ['image/jpeg', 'image/png', 'image/webp']): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if (!in_array($file['type'], $allowedTypes)) return null;
    if ($file['size'] > 5 * 1024 * 1024) return null; // 5MB max

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('zamahi_', true) . '.' . $ext;
    $dest     = rtrim($dir, '/') . '/' . $filename;

    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return $filename;
    }
    return null;
}

// ─── Summer Season Check ─────────────────────────────
function isSummerSeason(): bool {
    $month = (int)date('n');
    return $month >= 6 && $month <= 8;
}

// ─── Status Badge ────────────────────────────────────
function statusBadge(string $status): string {
    $colors = [
        'pending'    => '#D4AF37',
        'confirmed'  => '#2ecc71',
        'preparing'  => '#3498db',
        'completed'  => '#27ae60',
        'cancelled'  => '#e74c3c',
        'refunded'   => '#95a5a6',
    ];
    $color = $colors[$status] ?? '#999';
    return '<span style="background:' . $color . ';color:#fff;padding:4px 12px;border-radius:20px;font-size:0.8rem;text-transform:uppercase;">' . htmlspecialchars($status) . '</span>';
}
