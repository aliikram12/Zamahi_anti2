<?php
/**
 * ZAMAHI - API: Check Promo Code
 */
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/includes/db.php';

$code = trim($_GET['code'] ?? '');
if (!$code) {
    echo json_encode(['valid' => false]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT * FROM offers 
    WHERE code = ? AND is_active = 1 
    AND (valid_from IS NULL OR valid_from <= CURDATE()) 
    AND (valid_to IS NULL OR valid_to >= CURDATE())
    AND (max_uses IS NULL OR used_count < max_uses)
");
$stmt->execute([$code]);
$offer = $stmt->fetch();

if ($offer) {
    echo json_encode([
        'valid'       => true,
        'type'        => $offer['type'],
        'value'       => (float)$offer['value'],
        'description' => $offer['description'],
    ]);
} else {
    echo json_encode(['valid' => false]);
}
