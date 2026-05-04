<?php
/**
 * ZAMAHI - API: Get Menu Items
 */
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/includes/db.php';

$category = $_GET['category'] ?? null;

if ($category) {
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE category = ? AND is_active = 1");
    $stmt->execute([$category]);
} else {
    $stmt = $pdo->query("SELECT * FROM menu_items WHERE is_active = 1 ORDER BY category");
}

echo json_encode($stmt->fetchAll());
