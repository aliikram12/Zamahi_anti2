<?php
/**
 * ZAMAHI Admin - Export Bookings to CSV
 */
require_once __DIR__ . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

// Filtering logic (same as bookings.php)
$statusFilter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$where = [];
$params = [];

if ($statusFilter) {
    $where[] = "status = ?";
    $params[] = $statusFilter;
}
if ($search) {
    $where[] = "(ref_number LIKE ? OR customer_name LIKE ? OR customer_email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$stmt = $pdo->prepare("SELECT * FROM bookings $whereClause ORDER BY created_at DESC");
$stmt->execute($params);
$bookings = $stmt->fetchAll();

// CSV Headers
$filename = "zamahi_bookings_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Column Headers
fputcsv($output, ['Ref Number', 'Date', 'Customer', 'Email', 'Phone', 'Event', 'Guests', 'Total', 'Payment', 'Status', 'Booked At']);

// Data
foreach ($bookings as $b) {
    fputcsv($output, [
        $b['ref_number'],
        $b['event_date'],
        $b['customer_name'],
        $b['customer_email'],
        $b['customer_phone'],
        $b['event_category'],
        $b['event_sub_category'],
        $b['guest_count'],
        $b['grand_total'],
        $b['payment_method'],
        $b['status'],
        $b['created_at']
    ]);
}

fclose($output);
exit;
