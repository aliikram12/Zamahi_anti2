<?php
/**
 * ZAMAHI - Process Payment API
 * Creates a pending booking and a Stripe PaymentIntent
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Check CSRF
// (Omitted for brevity in this initial implementation, but should be added)

// Use Stripe
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

try {
    // 1. Basic Validation
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $customer_name   = $_POST['customer_name'] ?? '';
    $customer_email  = $_POST['customer_email'] ?? '';
    $amount_payable  = (float)($_POST['amount_payable_now'] ?? 0);
    $grand_total     = (float)($_POST['grand_total'] ?? 0);
    $payment_percent = (int)($_POST['payment_percentage'] ?? 10);

    if (empty($customer_name) || empty($customer_email) || $amount_payable <= 0) {
        throw new Exception('Missing or invalid required fields');
    }

    // 2. Database Connection
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. Create Pending Booking
    // Generate a reference number
    $ref_number = 'ZAM-' . strtoupper(substr(uniqid(), -6)) . '-' . date('His');

    // Prepare JSON for various arrays
    $menu_options = json_encode([
        'rice' => $_POST['rice'] ?? [],
        'bread' => $_POST['bread'] ?? [],
        'salad' => $_POST['salad'] ?? [],
        'sauce' => $_POST['sauce'] ?? [],
        'desserts' => $_POST['desserts'] ?? []
    ]);
    
    $additional_services = json_encode($_POST['services'] ?? []);
    $dietary_requirements = json_encode($_POST['allergies'] ?? []);

    $sql = "INSERT INTO bookings (
                ref_number, customer_name, customer_email, customer_phone,
                event_category, event_type, event_date, event_time,
                guest_count, kids_count,
                address, postcode, venue_type, spice_level,
                menu_options, additional_services, dietary_requirements,
                total_amount, amount_paid, payment_percentage, payment_status,
                created_at
            ) VALUES (
                :ref, :name, :email, :phone,
                :cat, :type, :date, :time,
                :guests, :kids,
                :addr, :post, :venue, :spice,
                :menu, :services, :dietary,
                :total, :paid, :percent, 'pending',
                NOW()
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ref'      => $ref_number,
        ':name'     => $customer_name,
        ':email'    => $customer_email,
        ':phone'    => $_POST['customer_phone'] ?? '',
        ':cat'      => $_POST['event_category'] ?? '',
        ':type'     => $_POST['event_type'] ?? '',
        ':date'     => $_POST['event_date'] ?? '',
        ':time'     => $_POST['event_time'] ?? '',
        ':guests'   => (int)($_POST['guest_count'] ?? 0),
        ':kids'     => (int)($_POST['kids_count'] ?? 0),
        ':addr'     => $_POST['address'] ?? '',
        ':post'     => $_POST['postcode'] ?? '',
        ':venue'    => $_POST['venue_type'] ?? '',
        ':spice'    => $_POST['spice_level'] ?? '',
        ':menu'     => $menu_options,
        ':services' => $additional_services,
        ':dietary'  => $dietary_requirements,
        ':total'    => $grand_total,
        ':paid'     => $amount_payable,
        ':percent'  => $payment_percent
    ]);

    $booking_id = $pdo->lastInsertId();

    // 4. Create Stripe Payment Intent
    // Stripe expects amount in cents
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => round($amount_payable * 100),
        'currency' => STRIPE_CURRENCY,
        'metadata' => [
            'booking_id' => $booking_id,
            'ref_number' => $ref_number
        ]
    ]);

    echo json_encode([
        'success'      => true,
        'clientSecret' => $paymentIntent->client_secret,
        'booking_id'   => $booking_id,
        'ref_number'   => $ref_number
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
