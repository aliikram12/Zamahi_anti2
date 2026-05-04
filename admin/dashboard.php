<?php
/**
 * ZAMAHI Admin - Dashboard
 */
require_once __DIR__ . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

// Stats
$totalBookings  = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$totalRevenue   = $pdo->query("SELECT COALESCE(SUM(grand_total), 0) FROM bookings WHERE status NOT IN ('cancelled','refunded')")->fetchColumn();
$pendingCount   = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$confirmedCount = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'confirmed'")->fetchColumn();

// Comparative Months
$thisMonthRev   = $pdo->query("SELECT COALESCE(SUM(grand_total), 0) FROM bookings WHERE status NOT IN ('cancelled','refunded') AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())")->fetchColumn();
$lastMonthRev   = $pdo->query("SELECT COALESCE(SUM(grand_total), 0) FROM bookings WHERE status NOT IN ('cancelled','refunded') AND MONTH(created_at) = MONTH(STR_TO_DATE(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m-%d')) AND YEAR(created_at) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))")->fetchColumn();
$revDiff        = $thisMonthRev - $lastMonthRev;
$revGrowth      = $lastMonthRev > 0 ? ($revDiff / $lastMonthRev) * 100 : 0;

$avgGuests      = $pdo->query("SELECT COALESCE(AVG(guest_count), 0) FROM bookings")->fetchColumn();

// Recent bookings
$recentBookingsByStatus = $pdo->query("SELECT status, COUNT(*) as count FROM bookings GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);

require_once __DIR__ . '/includes/admin_header.php';
?>

<div style="display:grid;grid-template-columns:3fr 1fr;gap:24px;margin-bottom:24px;">
    <!-- Stats Grid -->
    <div>
        <div class="stat-cards" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(212,175,55,0.15);color:#D4AF37;"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-value"><?= $totalBookings ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(46,204,113,0.15);color:#2ecc71;"><i class="fas fa-pound-sign"></i></div>
                <div class="stat-value"><?= formatCurrency((float)$totalRevenue) ?></div>
                <div class="stat-label">Lifetime Revenue</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(155,89,182,0.15);color:#9b59b6;"><i class="fas fa-chart-line"></i></div>
                <div class="stat-value"><?= formatCurrency((float)$thisMonthRev) ?></div>
                <div class="stat-label">This Month</div>
                <div style="font-size:0.75rem;margin-top:4px;color:<?= $revDiff >= 0 ? '#2ecc71' : '#e74c3c' ?>;">
                    <i class="fas fa-caret-<?= $revDiff >= 0 ? 'up' : 'down' ?>"></i> 
                    <?= formatCurrency(abs($revDiff)) ?> vs last month (<?= round($revGrowth,1) ?>%)
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(26,188,156,0.15);color:#1abc9c;"><i class="fas fa-users"></i></div>
                <div class="stat-value"><?= round($avgGuests) ?></div>
                <div class="stat-label">Avg Guest Count</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="admin-table-wrap" style="padding:20px;margin-bottom:0;">
        <h4 style="font-size:0.85rem;text-transform:uppercase;color:#888;margin-bottom:16px;">Quick Actions</h4>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <a href="bookings.php" class="btn-admin btn-outline-gold btn-sm" style="justify-content:flex-start;"><i class="fas fa-plus-circle"></i> Add New Booking</a>
            <a href="gallery.php" class="btn-admin btn-outline-gold btn-sm" style="justify-content:flex-start;"><i class="fas fa-image"></i> Upload to Gallery</a>
            <a href="offers.php" class="btn-admin btn-outline-gold btn-sm" style="justify-content:flex-start;"><i class="fas fa-ticket-alt"></i> Create Promo Code</a>
            <a href="export_bookings.php" class="btn-admin btn-outline-gold btn-sm" style="justify-content:flex-start;"><i class="fas fa-download"></i> Export All Bookings</a>
        </div>
    </div>
</div>

<!-- Upcoming Events -->
<?php if (!empty($upcomingEvents)): ?>
<div class="admin-table-wrap">
    <div class="admin-table-header">
        <h3><i class="fas fa-calendar" style="color:#D4AF37;margin-right:8px;"></i>Upcoming Events</h3>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Ref</th>
                <th>Event</th>
                <th>Date</th>
                <th>Guests</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($upcomingEvents as $b): ?>
            <tr>
                <td style="font-weight:600;color:#D4AF37;"><?= htmlspecialchars($b['ref_number']) ?></td>
                <td><?= htmlspecialchars($b['event_category']) ?><br><small><?= htmlspecialchars($b['event_sub_category']) ?></small></td>
                <td><?= date('d M Y', strtotime($b['event_date'])) ?></td>
                <td><?= $b['guest_count'] ?></td>
                <td style="font-weight:600;"><?= formatCurrency((float)$b['grand_total']) ?></td>
                <td><span class="badge badge-<?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Recent Bookings -->
<div class="admin-table-wrap">
    <div class="admin-table-header">
        <h3><i class="fas fa-history" style="color:#D4AF37;margin-right:8px;"></i>Recent Bookings</h3>
        <a href="bookings.php" class="btn-admin btn-outline-gold btn-sm">View All <i class="fas fa-arrow-right"></i></a>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Ref</th>
                <th>Customer</th>
                <th>Event</th>
                <th>Date</th>
                <th>Guests</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recentBookings)): ?>
            <tr>
                <td colspan="8" style="text-align:center;color:#888;padding:40px;">No bookings yet. They will appear here once customers book.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($recentBookings as $b): ?>
            <tr>
                <td style="font-weight:600;color:#D4AF37;"><?= htmlspecialchars($b['ref_number']) ?></td>
                <td>
                    <div style="font-weight:500;"><?= htmlspecialchars($b['customer_name']) ?></div>
                    <div style="font-size:0.75rem;color:#888;"><?= htmlspecialchars($b['customer_email']) ?></div>
                </td>
                <td><?= htmlspecialchars($b['event_category']) ?><br><small><?= htmlspecialchars($b['event_sub_category']) ?></small></td>
                <td><?= date('d M Y', strtotime($b['event_date'])) ?></td>
                <td><?= $b['guest_count'] ?></td>
                <td style="font-weight:600;"><?= formatCurrency((float)$b['grand_total']) ?></td>
                <td><span class="badge badge-<?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span></td>
                <td>
                    <a href="bookings.php?view=<?= $b['id'] ?>" class="btn-admin btn-outline-gold btn-sm"><i class="fas fa-eye"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
