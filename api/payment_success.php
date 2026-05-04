<?php
/**
 * ZAMAHI - Payment Success API
 * Verifies payment and confirms booking
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $booking_id = $data['booking_id'] ?? 0;
    $payment_id = $data['payment_intent'] ?? '';

    if (!$booking_id || !$payment_id) {
        throw new Exception('Invalid payment confirmation data');
    }

    // 1. Verify Payment Intent with Stripe
    $intent = \Stripe\PaymentIntent::retrieve($payment_id);
    if ($intent->status !== 'succeeded') {
        throw new Exception('Payment not verified: ' . $intent->status);
    }

    // 2. Update Booking Status
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("UPDATE bookings SET 
        payment_status = 'confirmed', 
        transaction_id = :tx,
        updated_at = NOW() 
        WHERE id = :id");
    
    $stmt->execute([':tx' => $payment_id, ':id' => $booking_id]);

    // 3. Get Booking Details for Email
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = :id");
    $stmt->execute([':id' => $booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        throw new Exception('Booking not found after confirm');
    }

    // 4. Send Professional HTML Invoice Email via PHPMailer
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($booking['customer_email'], $booking['customer_name']);
        $mail->addBCC(SITE_EMAIL); // Copy to admin

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Booking Confirmation & Invoice - ' . $booking['ref_number'];
        
        // Load HTML template
        ob_start();
        include __DIR__ . '/../templates/email_invoice.php';
        $emailContent = ob_get_clean();
        
        $mail->Body = $emailContent;
        $mail->AltBody = "Thank you for your booking! Reference: " . $booking['ref_number'] . ". Amount paid: £" . number_format($booking['amount_paid'], 2);

        $mail->send();
    } catch (Exception $e) {
        // Log email error but don't fail the API response
        error_log("Email sending failed: " . $mail->ErrorInfo);
    }

    echo json_encode([
        'success'    => true,
        'message'    => 'Booking confirmed successfully',
        'ref_number' => $booking['ref_number']
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
