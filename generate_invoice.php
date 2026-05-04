<?php
/**
 * ZAMAHI Luxury Catering - Invoice Generator (Compact 2-Page)
 * Generates a professional branded HTML invoice that can be printed/saved as PDF
 * 
 * Usage:
 *   - From browser: generate_invoice.php?id=BOOKING_ID&download=1
 *   - From PHP: generateInvoice($pdo, $bookingId) returns saved file path
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

/**
 * Generate invoice HTML and optionally save to file
 */
function generateInvoice($pdo, $bookingId) {
    $booking = getBookingData($pdo, $bookingId);
    if (!$booking) return null;

    $html = buildInvoiceHtml($booking);
    
    // Save to invoices directory
    $dir = INVOICE_DIR;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    
    $filename = 'invoice_' . $booking['booking']['ref_number'] . '.html';
    $filepath = $dir . $filename;
    file_put_contents($filepath, $html);
    
    return $filename;
}

/**
 * Get all booking data for invoice
 */
function getBookingData($pdo, $bookingId) {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch();
    if (!$booking) return null;
    
    // Fallback for old bookings without new columns
    if (!isset($booking['event_category'])) {
        $booking['event_category'] = $booking['event_type'] ?? '';
    }
    if (!isset($booking['event_sub_category'])) {
        $booking['event_sub_category'] = '';
    }

    $menuStmt = $pdo->prepare("SELECT * FROM booking_menu WHERE booking_id = ?");
    $menuStmt->execute([$bookingId]);
    $menu = $menuStmt->fetchAll();

    $allergyStmt = $pdo->prepare("SELECT * FROM booking_allergies WHERE booking_id = ?");
    $allergyStmt->execute([$bookingId]);
    $allergies = $allergyStmt->fetchAll();

    $servicesStmt = $pdo->prepare("SELECT * FROM booking_services WHERE booking_id = ?");
    $servicesStmt->execute([$bookingId]);
    $services = $servicesStmt->fetchAll();

    return [
        'booking'   => $booking,
        'menu'      => $menu,
        'allergies' => $allergies,
        'services'  => $services,
    ];
}

/**
 * Build the invoice HTML — compact 2-page layout
 */
function buildInvoiceHtml($data) {
    $b = $data['booking'];
    $menu = $data['menu'];
    $allergies = $data['allergies'];
    $services = $data['services'];

    $invoiceDate = date('d F Y');
    $eventDate = date('d F Y', strtotime($b['event_date']));
    $eventTime = $b['event_time'] ? date('H:i', strtotime($b['event_time'])) : 'TBC';
    
    ob_start();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice — <?= htmlspecialchars($b['ref_number']) ?> — ZAMAHI Luxury Catering</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        @page { size: A4; margin: 12mm 14mm; }
        @media print {
            body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .no-print { display: none !important; }
            .invoice-page { box-shadow: none; margin: 0; }
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            color: #1a1a1a;
            background: #eee;
            line-height: 1.5;
            font-size: 13px;
        }
        .invoice-page {
            max-width: 780px;
            margin: 16px auto;
            background: #fff;
            box-shadow: 0 4px 30px rgba(0,0,0,0.1);
        }

        /* ─── Header ─── */
        .inv-header {
            background: linear-gradient(135deg, #0a0a0a 0%, #1C1C1C 100%);
            color: #fff;
            padding: 24px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .inv-logo .logo-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem; font-weight: 700; color: #D4AF37;
            letter-spacing: 5px;
        }
        .inv-logo .logo-sub {
            font-size: 0.48rem; letter-spacing: 3px; color: #E8D48B;
            text-transform: uppercase; opacity: 0.7;
        }
        .inv-logo .logo-tagline {
            font-size: 0.65rem; color: rgba(255,255,255,0.4); font-style: italic; margin-top: 2px;
        }
        .inv-meta { text-align: right; }
        .inv-meta h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem; color: #D4AF37; letter-spacing: 3px; margin-bottom: 6px;
        }
        .inv-meta .meta-line { font-size: 0.72rem; color: rgba(255,255,255,0.6); margin-bottom: 2px; }
        .inv-meta .meta-line strong { color: #D4AF37; }
        .inv-meta .ref-badge {
            display: inline-block; background: rgba(212,175,55,0.1);
            border: 1px solid rgba(212,175,55,0.25); color: #D4AF37;
            padding: 4px 12px; border-radius: 4px; font-weight: 600;
            letter-spacing: 1.5px; font-size: 0.78rem; margin-top: 4px;
        }

        .gold-divider { height: 2px; background: linear-gradient(90deg, #D4AF37, #E8D48B, #D4AF37); }

        /* ─── Body ─── */
        .inv-body { padding: 22px 28px; }

        /* Info Grid */
        .inv-info-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px;
        }
        .inv-info-box {
            padding: 14px 16px; background: #f9f8f6; border: 1px solid #eee; border-radius: 6px;
        }
        .inv-info-box h4 {
            font-size: 0.6rem; text-transform: uppercase; letter-spacing: 1.5px;
            color: #D4AF37; margin-bottom: 8px; padding-bottom: 5px; border-bottom: 1px solid #eee;
        }
        .inv-info-box p { font-size: 0.78rem; margin-bottom: 2px; color: #444; }
        .inv-info-box p strong { color: #1a1a1a; }

        /* Section Title */
        .inv-section-title {
            font-family: 'Playfair Display', serif;
            font-size: 0.88rem; color: #1a1a1a; margin-bottom: 8px;
            padding-bottom: 5px; border-bottom: 2px solid #D4AF37;
        }

        /* Tables */
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table th {
            text-align: left; padding: 7px 10px; font-size: 0.62rem;
            text-transform: uppercase; letter-spacing: 0.8px; color: #fff; background: #1C1C1C;
        }
        table th:last-child { text-align: right; }
        table td {
            padding: 6px 10px; font-size: 0.76rem; border-bottom: 1px solid #f0f0f0; color: #555;
        }
        table td:last-child { text-align: right; font-weight: 500; }
        table tr:nth-child(even) td { background: #fafafa; }
        table td strong { color: #333; }

        /* Totals */
        .inv-totals { display: flex; justify-content: flex-end; margin-bottom: 18px; }
        .inv-totals-box {
            width: 280px; border: 1px solid #eee; border-radius: 6px; overflow: hidden;
        }
        .inv-total-line {
            display: flex; justify-content: space-between; padding: 7px 14px;
            font-size: 0.78rem; border-bottom: 1px solid #f0f0f0;
        }
        .inv-total-line .t-label { color: #777; }
        .inv-total-line .t-value { font-weight: 500; color: #1a1a1a; }
        .inv-total-line.discount .t-value { color: #2ecc71; }
        .inv-total-line.grand {
            background: linear-gradient(135deg, #0a0a0a, #1C1C1C);
            color: #fff; padding: 10px 14px; border-bottom: none;
        }
        .inv-total-line.grand .t-label { color: #fff; font-weight: 600; font-size: 0.85rem; }
        .inv-total-line.grand .t-value { color: #D4AF37; font-weight: 700; font-size: 1.05rem; }

        /* Footer */
        .inv-footer {
            background: #f9f8f6; border-top: 1px solid #eee; padding: 16px 28px;
        }
        .inv-footer-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .inv-footer-section h5 {
            font-size: 0.58rem; text-transform: uppercase; letter-spacing: 1.5px;
            color: #D4AF37; margin-bottom: 4px;
        }
        .inv-footer-section p { font-size: 0.68rem; color: #999; line-height: 1.5; }
        .inv-bottom {
            text-align: center; padding: 10px; background: #1C1C1C;
            color: rgba(255,255,255,0.35); font-size: 0.62rem;
        }

        /* Print button */
        .print-bar {
            max-width: 780px; margin: 0 auto 16px;
            display: flex; gap: 10px; justify-content: flex-end;
        }
        .print-btn {
            padding: 8px 20px; border: none; border-radius: 100px;
            font-family: 'Poppins', sans-serif; font-size: 0.8rem;
            font-weight: 600; cursor: pointer; transition: all 0.3s;
        }
        .print-btn.gold { background: #D4AF37; color: #000; }
        .print-btn.gold:hover { background: #E8D48B; }
        .print-btn.dark { background: #1C1C1C; color: #fff; }
        .print-btn.dark:hover { background: #333; }
    </style>
</head>
<body>
    <!-- Print/Download Bar -->
    <div class="print-bar no-print">
        <button class="print-btn gold" onclick="window.print()">🖶 Print / Save as PDF</button>
        <button class="print-btn dark" onclick="window.close()">✕ Close</button>
    </div>

    <div class="invoice-page">
        <!-- Header -->
        <div class="inv-header">
            <div class="inv-logo">
                <div class="logo-name">ZAMAHI</div>
                <div class="logo-sub">LUXURY CATERING</div>
                <div class="logo-tagline">Luxury Catering. Exceptional Events.</div>
            </div>
            <div class="inv-meta">
                <h2>INVOICE</h2>
                <div class="meta-line"><strong>Date:</strong> <?= $invoiceDate ?></div>
                <div class="meta-line"><strong>Status:</strong> <?= ucfirst($b['status']) ?></div>
                <div class="ref-badge"><?= htmlspecialchars($b['ref_number']) ?></div>
            </div>
        </div>
        <div class="gold-divider"></div>

        <!-- Body -->
        <div class="inv-body">
            <!-- Client & Event Info -->
            <div class="inv-info-grid">
                <div class="inv-info-box">
                    <h4>Client Details</h4>
                    <p><strong><?= htmlspecialchars($b['customer_name']) ?></strong></p>
                    <p><?= htmlspecialchars($b['customer_email']) ?></p>
                    <p><?= htmlspecialchars($b['customer_phone']) ?></p>
                </div>
                <div class="inv-info-box">
                    <h4>Event Details</h4>
                    <p><strong>Category:</strong> <?= !empty($b['event_category']) ? htmlspecialchars($b['event_category']) : 'Event' ?></p>
                    <p><strong>Type:</strong> <?= !empty($b['event_sub_category']) ? htmlspecialchars($b['event_sub_category']) : htmlspecialchars($b['event_type'] ?? '') ?></p>
                    <p><strong>Date:</strong> <?= $eventDate ?> at <?= $eventTime ?></p>
                    <p><strong>Venue:</strong> <?= ucfirst($b['indoor_outdoor']) ?> — <?= htmlspecialchars($b['address']) ?>, <?= htmlspecialchars($b['postcode']) ?></p>
                </div>
            </div>

            <!-- Guest & Dietary in one row -->
            <div class="inv-info-grid">
                <div class="inv-info-box">
                    <h4>Guest Summary</h4>
                    <p><strong>Adults:</strong> <?= $b['guest_count'] ?> · <strong>Children:</strong> <?= $b['kids_count'] ?> (<?= $b['kids_free'] ?> free under 4)</p>
                    <p><strong>Payment:</strong> <?= ucwords(str_replace('_', ' ', $b['payment_method'])) ?></p>
                </div>
                <?php if (!empty($allergies)): ?>
                <div class="inv-info-box">
                    <h4>Dietary Requirements</h4>
                    <?php foreach ($allergies as $a): ?>
                    <p><?= htmlspecialchars($a['allergy_type']) ?>: <?= $a['guest_count'] ?> guests (<?= formatCurrency((float)$a['extra_charge']) ?>)</p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Menu Items -->
            <?php if (!empty($menu)): ?>
            <h3 class="inv-section-title">Menu Selection</h3>
            <table>
                <thead>
                    <tr><th>Item</th><th>Category</th><th>Qty</th><th>Spice</th><th>Price</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($menu as $item): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($item['item_name']) ?></strong></td>
                        <td><?= ucfirst($item['item_category']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= $item['spice_level'] ? ucfirst($item['spice_level']) : '—' ?></td>
                        <td><?= formatCurrency((float)$item['price']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <!-- Additional Services -->
            <?php if (!empty($services)): ?>
            <h3 class="inv-section-title">Additional Services</h3>
            <table>
                <thead><tr><th>Service</th><th>Price</th></tr></thead>
                <tbody>
                    <?php foreach ($services as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['service_name']) ?></td>
                        <td><?= formatCurrency((float)$s['price']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <!-- Price Breakdown -->
            <div class="inv-totals">
                <div class="inv-totals-box">
                    <div class="inv-total-line">
                        <span class="t-label">Per Head Cost</span>
                        <span class="t-value"><?= formatCurrency((float)$b['per_head_cost']) ?></span>
                    </div>
                    <div class="inv-total-line">
                        <span class="t-label">Subtotal</span>
                        <span class="t-value"><?= formatCurrency((float)$b['subtotal']) ?></span>
                    </div>
                    <?php if ($b['discount'] > 0): ?>
                    <div class="inv-total-line discount">
                        <span class="t-label">Discount</span>
                        <span class="t-value">-<?= formatCurrency((float)$b['discount']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($b['allergy_charges'] > 0): ?>
                    <div class="inv-total-line">
                        <span class="t-label">Allergy Surcharges</span>
                        <span class="t-value"><?= formatCurrency((float)$b['allergy_charges']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($b['services_total'] > 0): ?>
                    <div class="inv-total-line">
                        <span class="t-label">Services</span>
                        <span class="t-value"><?= formatCurrency((float)$b['services_total']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="inv-total-line">
                        <span class="t-label">Delivery</span>
                        <span class="t-value"><?= $b['delivery_charge'] > 0 ? formatCurrency((float)$b['delivery_charge']) : 'FREE' ?></span>
                    </div>
                    <div class="inv-total-line">
                        <span class="t-label">VAT (20%)</span>
                        <span class="t-value"><?= formatCurrency((float)$b['vat']) ?></span>
                    </div>
                    <div class="inv-total-line grand">
                        <span class="t-label">Grand Total</span>
                        <span class="t-value"><?= formatCurrency((float)$b['grand_total']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="inv-footer">
            <div class="inv-footer-grid">
                <div class="inv-footer-section">
                    <h5>Payment Information</h5>
                    <p>Method: <?= ucwords(str_replace('_', ' ', $b['payment_method'])) ?></p>
                    <p>Advance payment required for confirmation.</p>
                </div>
                <div class="inv-footer-section">
                    <h5>Cancellation Policy</h5>
                    <p>Fully refundable up to 48 hours before event. Contact us for changes or cancellations.</p>
                </div>
            </div>
        </div>
        <div class="inv-bottom">
            <p>ZAMAHI Luxury Catering — Operated by <?= LEGAL_ENTITY ?> | <?= SITE_PHONE ?> | <?= SITE_EMAIL ?></p>
            <p style="margin-top:2px;"><?= SITE_ADDRESS ?> | <?= SITE_URL ?></p>
        </div>
    </div>
</body>
</html>
    <?php
    return ob_get_clean();
}

// Direct access — display/download invoice
if (isset($_GET['id'])) {
    $bookingId = (int)$_GET['id'];
    $data = getBookingData($pdo, $bookingId);
    
    if (!$data) {
        http_response_code(404);
        die('Booking not found.');
    }
    
    $html = buildInvoiceHtml($data);
    
    // Save to file
    $dir = INVOICE_DIR;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $filename = 'invoice_' . $data['booking']['ref_number'] . '.html';
    $filepath = $dir . $filename;
    file_put_contents($filepath, $html);
    
    // If download is requested, force download
    if (isset($_GET['download'])) {
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($html));
        header('Cache-Control: no-cache, must-revalidate');
        echo $html;
        exit;
    }
    
    // Otherwise, display in browser
    echo $html;
    exit;
}
