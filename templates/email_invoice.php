<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f9f9f9; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { background: #1a1a1a; padding: 40px 20px; text-align: center; color: #d4af37; }
        .header h1 { margin: 0; font-size: 24px; text-transform: uppercase; letter-spacing: 2px; }
        .content { padding: 40px; }
        .invoice-box { background: #fdfbf5; border: 1px solid #eee; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .ref-number { font-size: 18px; font-weight: bold; color: #d4af37; margin-bottom: 20px; text-align: center; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
        .detail-label { color: #888; }
        .detail-value { font-weight: 500; text-align: right; }
        .total-row { border-top: 2px solid #333; padding-top: 15px; margin-top: 15px; font-size: 18px; font-weight: bold; color: #1a1a1a; }
        .paid-tag { background: #2ecc71; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; text-transform: uppercase; }
        .footer { background: #f4f4f4; padding: 20px; text-align: center; font-size: 12px; color: #999; }
        .btn { display: inline-block; padding: 12px 30px; background: #d4af37; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ZAMAHI Luxury Catering</h1>
            <p style="margin-top:10px; color:#999; font-size:14px;">Booking Confirmation & Invoice</p>
        </div>
        
        <div class="content">
            <h2 style="margin-top:0;">Dear <?php echo htmlspecialchars($booking['customer_name']); ?>,</h2>
            <p>Thank you for choosing ZAMAHI Luxury Catering. We are delighted to confirm your booking for your upcoming event.</p>
            
            <div class="invoice-box">
                <div class="ref-number">REFERENCE: <?php echo htmlspecialchars($booking['ref_number']); ?></div>
                
                <div class="detail-row">
                    <span class="detail-label">Event Type</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['event_type']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Event Date</span>
                    <span class="detail-value"><?php echo date('l, F j, Y', strtotime($booking['event_date'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Event Time</span>
                    <span class="detail-value"><?php echo date('g:i A', strtotime($booking['event_time'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Guest Count</span>
                    <span class="detail-value"><?php echo $booking['guest_count']; ?> Guests (inc. <?php echo $booking['kids_count']; ?> child)</span>
                </div>
                <div class="detail-row" style="margin-top:20px; font-weight:bold; border-top:1px solid #eee; padding-top:10px;">
                    <span class="detail-label">Booking Total</span>
                    <span class="detail-value">£<?php echo number_format($booking['total_amount'], 2); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Tier (<?php echo $booking['payment_percentage']; ?>%)</span>
                    <span class="detail-value">£<?php echo number_format($booking['amount_paid'], 2); ?></span>
                </div>
                <div class="detail-row total-row">
                    <span>Payment Confirmed</span>
                    <span class="paid-tag">Paid Via Stripe</span>
                </div>
            </div>
            
            <p>Our team will be in touch within 24 hours to review your menu selections and event details in more depth.</p>
            
            <div style="text-align:center;">
                <p style="font-size:14px; color:#666;">If you have any questions, please reply directly to this email or call us at +44 20 7123 4567.</p>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; <?php echo date('Y'); ?> ZAMAHI Luxury Catering. All rights reserved.</p>
            <p>London, United Kingdom | www.zamahi.co.uk</p>
        </div>
    </div>
</body>
</html>
