<?php
/**
 * ZAMAHI Admin - Payment Management
 */
require_once __DIR__ . '/includes/admin_header.php';

// Fetch all bookings with a payment status or transaction ID
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get filter
    $status = $_GET['status'] ?? 'all';
    $where = "WHERE payment_status != 'unpaid'";
    if ($status === 'confirmed') $where .= " AND payment_status = 'confirmed'";
    if ($status === 'pending') $where .= " AND payment_status = 'pending'";

    $stmt = $pdo->prepare("SELECT * FROM bookings $where ORDER BY created_at DESC");
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Stats
    $totalRevenue = $pdo->query("SELECT SUM(amount_paid) FROM bookings WHERE payment_status = 'confirmed'")->fetchColumn();
    $pendingCount = $pdo->query("SELECT COUNT(*) FROM bookings WHERE payment_status = 'pending'")->fetchColumn();
    $confirmedCount = $pdo->query("SELECT COUNT(*) FROM bookings WHERE payment_status = 'confirmed'")->fetchColumn();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="admin-content-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:32px;">
    <div>
        <h2 style="font-family:'Playfair Display', serif;font-size:1.8rem;color:var(--gold);margin-bottom:8px;">Payment Management</h2>
        <p style="color:var(--mid-grey);font-size:0.9rem;">Track transactions and deposit payments</p>
    </div>
    <div style="display:flex;gap:12px;">
        <a href="?status=all" class="btn-admin <?= $status === 'all' ? 'btn-gold' : 'btn-outline-gold' ?>">All</a>
        <a href="?status=confirmed" class="btn-admin <?= $status === 'confirmed' ? 'btn-gold' : 'btn-outline-gold' ?>">Confirmed</a>
        <a href="?status=pending" class="btn-admin <?= $status === 'pending' ? 'btn-gold' : 'btn-outline-gold' ?>">Pending</a>
    </div>
</div>

<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(46,204,113,0.1);color:var(--success);"><i class="fas fa-pound-sign"></i></div>
        <div class="stat-value">£<?= number_format($totalRevenue, 2) ?></div>
        <div class="stat-label">Confirmed Revenue</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(212,175,55,0.1);color:var(--gold);"><i class="fas fa-clock"></i></div>
        <div class="stat-value"><?= $pendingCount ?></div>
        <div class="stat-label">Pending Deposits</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(52,152,219,0.1);color:var(--info);"><i class="fas fa-check-double"></i></div>
        <div class="stat-value"><?= $confirmedCount ?></div>
        <div class="stat-label">Total Confirmed</div>
    </div>
</div>

<div class="admin-table-wrap">
    <div class="admin-table-header">
        <h3>Recent Transactions</h3>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Customer</th>
                <th>Amount Paid</th>
                <th>Tier</th>
                <th>Total Order</th>
                <th>Status</th>
                <th>Transaction ID</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($payments)): ?>
            <tr>
                <td colspan="8" style="text-align:center;padding:40px;color:var(--mid-grey);">No transactions found matching your criteria.</td>
            </tr>
            <?php else: ?>
                <?php foreach ($payments as $p): ?>
                <tr>
                    <td><?= date('d M, Y H:i', strtotime($p['created_at'])) ?></td>
                    <td style="color:var(--gold);font-weight:600;"><?= htmlspecialchars($p['ref_number']) ?></td>
                    <td>
                        <div style="font-weight:500;color:var(--white);"><?= htmlspecialchars($p['customer_name']) ?></div>
                        <div style="font-size:0.75rem;color:var(--mid-grey);"><?= htmlspecialchars($p['customer_email']) ?></div>
                    </td>
                    <td style="font-weight:700;color:var(--white);">£<?= number_format($p['amount_paid'], 2) ?></td>
                    <td><?= $p['payment_percentage'] ?>%</td>
                    <td>£<?= number_format($p['total_amount'], 2) ?></td>
                    <td>
                        <span class="badge badge-<?= $p['payment_status'] ?>">
                            <?= ucfirst($p['payment_status']) ?>
                        </span>
                    </td>
                    <td style="font-family:monospace;font-size:0.75rem;opacity:0.6;">
                        <?= $p['transaction_id'] ? htmlspecialchars(substr($p['transaction_id'], 0, 15)) . '...' : 'N/A' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
