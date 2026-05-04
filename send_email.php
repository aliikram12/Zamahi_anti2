<?php
/**
 * ZAMAHI Luxury Catering - Email Sender
 * Sends booking confirmation emails
 * 
 * NOTE: This scaffolds PHPMailer usage. When PHPMailer is installed via Composer,
 * uncomment the PHPMailer sections. Without it, falls back to PHP mail().
 */

require_once __DIR__ . '/includes/config.php';

/**
 * Send booking confirmation email
 */
function sendBookingConfirmation($pdo, $bookingId, $invoicePath = null) {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch();
    if (!$booking) return false;

    $to      = $booking['customer_email'];
    $name    = $booking['customer_name'];
    $ref     = $booking['ref_number'];
    $event   = $booking['event_type'];
    $date    = date('d F Y', strtotime($booking['event_date']));
    $total   = '£' . number_format($booking['grand_total'], 2);
    $subject = "Booking Confirmation — $ref — ZAMAHI Luxury Catering";

    $body = buildEmailHtml($name, $ref, $event, $date, $total, $booking);

    // ── Try PHPMailer if available ──
    $phpmailerAutoload = __DIR__ . '/vendor/autoload.php';
    if (file_exists($phpmailerAutoload)) {
        require_once $phpmailerAutoload;
        
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;

            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($to, $name);
            $mail->addReplyTo(SITE_EMAIL, SITE_NAME);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], "\n", $body));

            // Attach invoice if exists
            if ($invoicePath && file_exists(INVOICE_DIR . $invoicePath)) {
                $mail->addAttachment(INVOICE_DIR . $invoicePath, 'Invoice_' . $ref . '.html');
            }

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('PHPMailer Error: ' . $e->getMessage());
            return false;
        }
    }

    // ── Fallback: PHP mail() ──
    // Disabled locally to prevent XAMPP hang without SMTP server
    // $headers  = "MIME-Version: 1.0\r\n";
    // $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    // $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    // $headers .= "Reply-To: " . SITE_EMAIL . "\r\n";

    // return @mail($to, $subject, $body, $headers);
    return true;
}

/**
 * Build branded HTML email
 */
function buildEmailHtml($name, $ref, $event, $date, $total, $booking) {
    $siteUrl = SITE_URL;
    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,Helvetica,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#fff;">
        <!-- Header -->
        <tr>
            <td style="background:linear-gradient(135deg,#0a0a0a,#1C1C1C);padding:32px;text-align:center;">
                <div style="font-family:Georgia,serif;font-size:28px;font-weight:bold;color:#D4AF37;letter-spacing:6px;">ZAMAHI</div>
                <div style="font-size:9px;letter-spacing:4px;color:#E8D48B;text-transform:uppercase;margin-top:4px;">LUXURY CATERING</div>
            </td>
        </tr>
        <tr><td style="height:3px;background:linear-gradient(90deg,#D4AF37,#E8D48B,#D4AF37);"></td></tr>

        <!-- Body -->
        <tr>
            <td style="padding:32px;">
                <h2 style="font-family:Georgia,serif;color:#1a1a1a;margin-bottom:8px;">Booking Confirmed ✓</h2>
                <p style="color:#666;font-size:14px;line-height:1.6;">
                    Dear <strong>{$name}</strong>,<br><br>
                    Thank you for choosing ZAMAHI Luxury Catering. Your booking has been received and is being processed.
                </p>

                <!-- Booking Summary Box -->
                <table width="100%" cellpadding="0" cellspacing="0" style="background:#fafafa;border:1px solid #eee;border-radius:8px;margin:24px 0;">
                    <tr>
                        <td style="padding:20px;">
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:2px;color:#D4AF37;margin-bottom:12px;border-bottom:1px solid #eee;padding-bottom:8px;">Booking Summary</div>
                            <table width="100%" style="font-size:14px;color:#333;">
                                <tr><td style="padding:6px 0;color:#888;">Reference:</td><td style="padding:6px 0;font-weight:bold;color:#D4AF37;letter-spacing:1px;">{$ref}</td></tr>
                                <tr><td style="padding:6px 0;color:#888;">Event:</td><td style="padding:6px 0;font-weight:bold;">{$event}</td></tr>
                                <tr><td style="padding:6px 0;color:#888;">Date:</td><td style="padding:6px 0;">{$date}</td></tr>
                                <tr><td style="padding:6px 0;color:#888;">Guests:</td><td style="padding:6px 0;">{$booking['guest_count']} adults</td></tr>
                                <tr><td style="padding:6px 0;color:#888;">Location:</td><td style="padding:6px 0;">{$booking['address']}, {$booking['postcode']}</td></tr>
                                <tr style="border-top:1px solid #eee;"><td style="padding:10px 0 6px;color:#888;font-weight:bold;">Total:</td><td style="padding:10px 0 6px;font-size:18px;font-weight:bold;color:#D4AF37;">{$total}</td></tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <p style="color:#666;font-size:14px;line-height:1.6;">
                    <strong>What happens next?</strong><br>
                    Our team will review your booking and contact you within 24 hours to confirm all details.
                </p>

                <!-- CTA Button -->
                <p style="text-align:center;margin:28px 0;">
                    <a href="{$siteUrl}" style="display:inline-block;background:#D4AF37;color:#000;padding:14px 32px;text-decoration:none;font-weight:bold;border-radius:4px;letter-spacing:1px;font-size:14px;">
                        Visit Our Website →
                    </a>
                </p>

                <!-- Cancellation -->
                <div style="background:#fff9e6;border:1px solid #f0e4b5;border-radius:6px;padding:16px;margin-top:20px;">
                    <p style="font-size:12px;color:#856404;margin:0;">
                        <strong>Cancellation Policy:</strong> Fully refundable up to 48 hours before your event. Contact us for changes.
                    </p>
                </div>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background:#1C1C1C;padding:24px;text-align:center;">
                <p style="color:rgba(255,255,255,0.5);font-size:12px;margin:0;">
                    ZAMAHI Luxury Catering — Operated by Zamahi Ltd<br>
                    London, United Kingdom<br>
                    <a href="mailto:info@zamahi.co.uk" style="color:#D4AF37;text-decoration:none;">info@zamahi.co.uk</a> | 
                    <a href="tel:+442071234567" style="color:#D4AF37;text-decoration:none;">+44 20 7123 4567</a>
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
}
