<?php
/**
 * ZAMAHI Admin - Bookings Management
 */
require_once __DIR__ . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid security token.');
    } else {
        if ($_POST['action'] === 'update_status') {
            $id = (int)$_POST['booking_id'];
            $status = sanitize($_POST['status']);
            $allowed = ['pending','confirmed','preparing','completed','cancelled','refunded'];
            if (in_array($status, $allowed)) {
                $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?")->execute([$status, $id]);
                setFlash('success', 'Booking status updated to ' . ucfirst($status) . '.');
            }
        } elseif ($_POST['action'] === 'delete_booking') {
            $id = (int)$_POST['booking_id'];
            // Since we have ON DELETE CASCADE for foreign keys, this will clean up linked tables
            $pdo->prepare("DELETE FROM bookings WHERE id = ?")->execute([$id]);
            setFlash('success', 'Booking deleted successfully.');
            header('Location: bookings.php');
            exit;
        } elseif ($_POST['action'] === 'edit_booking') {
            $id = (int)$_POST['booking_id'];
            $name = sanitize($_POST['customer_name']);
            $email = sanitize($_POST['customer_email']);
            $phone = sanitize($_POST['customer_phone']);
            $date = $_POST['event_date'];
            $time = $_POST['event_time'];
            $address = sanitize($_POST['address']);
            
            $pdo->prepare("UPDATE bookings SET customer_name=?, customer_email=?, customer_phone=?, event_date=?, event_time=?, address=? WHERE id=?")
                ->execute([$name, $email, $phone, $date, $time, $address, $id]);
            setFlash('success', 'Booking details updated.');
        }
    }
    // For add/update, redirect back to the relevant view or list.
    // For deletion, it already redirects and exits above.
    $redirect = 'bookings.php';
    if (isset($_POST['booking_id']) && $_POST['action'] !== 'delete_booking') {
        $redirect .= '?view=' . (int)$_POST['booking_id'];
    }
    header('Location: ' . $redirect);
    exit;
}

// View single booking
$viewBooking = null;
$bookingMenu = [];
$bookingAllergies = [];
$bookingServices = [];
if (isset($_GET['view'])) {
    $viewId = (int)$_GET['view'];
    $viewBooking = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
    $viewBooking->execute([$viewId]);
    $viewBooking = $viewBooking->fetch();

    if ($viewBooking) {
        $bookingMenu = $pdo->prepare("SELECT * FROM booking_menu WHERE booking_id = ?");
        $bookingMenu->execute([$viewId]);
        $bookingMenu = $bookingMenu->fetchAll();

        $bookingAllergies = $pdo->prepare("SELECT * FROM booking_allergies WHERE booking_id = ?");
        $bookingAllergies->execute([$viewId]);
        $bookingAllergies = $bookingAllergies->fetchAll();

        $bookingServices = $pdo->prepare("SELECT * FROM booking_services WHERE booking_id = ?");
        $bookingServices->execute([$viewId]);
        $bookingServices = $bookingServices->fetchAll();
    }
}

// List bookings with filtering
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
$stmt = $pdo->prepare("SELECT * FROM bookings $whereClause ORDER BY created_at DESC LIMIT 100");
$stmt->execute($params);
$bookings = $stmt->fetchAll();

$csrfToken = generateCsrfToken();
require_once __DIR__ . '/includes/admin_header.php';
?>

<?php if ($viewBooking): ?>
<!-- ═══════ SINGLE BOOKING VIEW ═══════ -->
<div style="margin-bottom:20px;">
    <a href="bookings.php" class="btn-admin btn-outline-gold btn-sm"><i class="fas fa-arrow-left"></i> Back to all bookings</a>
    <a href="generate_invoice.php?id=<?= $viewBooking['id'] ?>&download=1" class="btn-admin btn-gold btn-sm" style="margin-left:8px;" target="_blank"><i class="fas fa-file-pdf"></i> Download Invoice</a>
    
    <button type="button" class="btn-admin btn-danger btn-sm" style="margin-left:8px;" onclick="confirmDelete(<?= $viewBooking['id'] ?>, 'delete_booking', 'CRITICAL: Permanently delete this booking? This cannot be undone.', true)"><i class="fas fa-trash"></i> Delete Booking</button>
    
    <button type="button" class="btn-admin btn-outline-gold btn-sm" style="margin-left:8px;" onclick="openEditBookingModal()"><i class="fas fa-edit"></i> Edit Details</button>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">
    <!-- Booking Details -->
    <div>
        <div class="admin-table-wrap">
            <div class="admin-table-header">
                <h3><i class="fas fa-info-circle" style="color:#D4AF37;margin-right:8px;"></i>Booking #<?= htmlspecialchars($viewBooking['ref_number']) ?></h3>
                <span class="badge badge-<?= $viewBooking['status'] ?>"><?= ucfirst($viewBooking['status']) ?></span>
            </div>
            <div style="padding:24px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;font-size:0.9rem;">
                    <div><strong style="color:#D4AF37;">Customer:</strong><br><?= htmlspecialchars($viewBooking['customer_name']) ?></div>
                    <div><strong style="color:#D4AF37;">Email:</strong><br><?= htmlspecialchars($viewBooking['customer_email']) ?></div>
                    <div><strong style="color:#D4AF37;">Phone:</strong><br><?= htmlspecialchars($viewBooking['customer_phone']) ?></div>
                    <div><strong style="color:#D4AF37;">Event:</strong><br><?= htmlspecialchars($viewBooking['event_category']) ?> - <?= htmlspecialchars($viewBooking['event_sub_category']) ?></div>
                    <div><strong style="color:#D4AF37;">Event Date:</strong><br><?= date('d M Y', strtotime($viewBooking['event_date'])) ?> <?= $viewBooking['event_time'] ? date('H:i', strtotime($viewBooking['event_time'])) : '' ?></div>
                    <div><strong style="color:#D4AF37;">Venue:</strong><br><?= htmlspecialchars($viewBooking['address']) ?>, <?= htmlspecialchars($viewBooking['postcode']) ?></div>
                    <div><strong style="color:#D4AF37;">Setting:</strong><br><?= ucfirst($viewBooking['indoor_outdoor']) ?></div>
                    <div><strong style="color:#D4AF37;">Guests:</strong><br><?= $viewBooking['guest_count'] ?> adults<?= $viewBooking['kids_count'] ? ' + ' . $viewBooking['kids_count'] . ' children' : '' ?></div>
                    <div><strong style="color:#D4AF37;">Payment:</strong><br><?= ucwords(str_replace('_', ' ', $viewBooking['payment_method'])) ?></div>
                    <div><strong style="color:#D4AF37;">Booked:</strong><br><?= date('d M Y H:i', strtotime($viewBooking['created_at'])) ?></div>
                </div>
                <?php if ($viewBooking['instructions']): ?>
                <div style="margin-top:16px;padding:12px 16px;background:rgba(0,0,0,0.3);border-radius:8px;font-size:0.88rem;">
                    <strong style="color:#D4AF37;">Special Instructions:</strong><br>
                    <?= nl2br(htmlspecialchars($viewBooking['instructions'])) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Menu Items -->
        <?php if (!empty($bookingMenu)): ?>
        <div class="admin-table-wrap">
            <div class="admin-table-header"><h3><i class="fas fa-utensils" style="color:#D4AF37;margin-right:8px;"></i>Menu Selection</h3></div>
            <table class="admin-table">
                <thead><tr><th>Item</th><th>Category</th><th>Qty</th><th>Spice</th><th>Price</th></tr></thead>
                <tbody>
                    <?php foreach ($bookingMenu as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= ucfirst($item['item_category']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= $item['spice_level'] ? ucfirst($item['spice_level']) : '—' ?></td>
                        <td><?= formatCurrency((float)$item['price']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Allergies -->
        <?php if (!empty($bookingAllergies)): ?>
        <div class="admin-table-wrap">
            <div class="admin-table-header"><h3><i class="fas fa-allergies" style="color:#D4AF37;margin-right:8px;"></i>Dietary Requirements</h3></div>
            <table class="admin-table">
                <thead><tr><th>Type</th><th>Guest Count</th><th>Surcharge</th></tr></thead>
                <tbody>
                    <?php foreach ($bookingAllergies as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['allergy_type']) ?></td>
                        <td><?= $a['guest_count'] ?></td>
                        <td><?= formatCurrency((float)$a['extra_charge']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Services -->
        <?php if (!empty($bookingServices)): ?>
        <div class="admin-table-wrap">
            <div class="admin-table-header"><h3><i class="fas fa-concierge-bell" style="color:#D4AF37;margin-right:8px;"></i>Additional Services</h3></div>
            <table class="admin-table">
                <thead><tr><th>Service</th><th>Price</th></tr></thead>
                <tbody>
                    <?php foreach ($bookingServices as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['service_name']) ?></td>
                        <td><?= formatCurrency((float)$s['price']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Price + Status Update -->
    <div>
        <!-- Price Breakdown -->
        <div class="admin-table-wrap">
            <div class="admin-table-header"><h3><i class="fas fa-receipt" style="color:#D4AF37;margin-right:8px;"></i>Price Breakdown</h3></div>
            <div style="padding:24px;font-size:0.9rem;">
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                    <span style="color:rgba(255,255,255,0.6);">Per Head</span>
                    <span><?= formatCurrency((float)$viewBooking['per_head_cost']) ?></span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                    <span style="color:rgba(255,255,255,0.6);">Subtotal</span>
                    <span><?= formatCurrency((float)$viewBooking['subtotal']) ?></span>
                </div>
                <?php if ($viewBooking['discount'] > 0): ?>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                    <span style="color:rgba(255,255,255,0.6);">Discount</span>
                    <span style="color:#2ecc71;">-<?= formatCurrency((float)$viewBooking['discount']) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($viewBooking['allergy_charges'] > 0): ?>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                    <span style="color:rgba(255,255,255,0.6);">Allergy Surcharges</span>
                    <span><?= formatCurrency((float)$viewBooking['allergy_charges']) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($viewBooking['services_total'] > 0): ?>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                    <span style="color:rgba(255,255,255,0.6);">Services</span>
                    <span><?= formatCurrency((float)$viewBooking['services_total']) ?></span>
                </div>
                <?php endif; ?>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                    <span style="color:rgba(255,255,255,0.6);">Delivery</span>
                    <span><?= $viewBooking['delivery_charge'] > 0 ? formatCurrency((float)$viewBooking['delivery_charge']) : '<span style="color:#2ecc71;">FREE</span>' ?></span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                    <span style="color:rgba(255,255,255,0.6);">VAT (20%)</span>
                    <span><?= formatCurrency((float)$viewBooking['vat']) ?></span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:12px 0 0;margin-top:8px;border-top:2px solid #D4AF37;">
                    <span style="font-weight:700;font-size:1rem;">Grand Total</span>
                    <span style="font-weight:700;font-size:1.2rem;color:#D4AF37;"><?= formatCurrency((float)$viewBooking['grand_total']) ?></span>
                </div>
            </div>
        </div>

        <!-- Update Status -->
        <div class="admin-table-wrap">
            <div class="admin-table-header"><h3><i class="fas fa-edit" style="color:#D4AF37;margin-right:8px;"></i>Update Status</h3></div>
            <div style="padding:24px;">
                <form method="POST" class="admin-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="booking_id" value="<?= $viewBooking['id'] ?>">
                    <div class="form-group">
                        <select name="status" class="form-control">
                            <?php foreach (['pending','confirmed','preparing','completed','cancelled','refunded'] as $s): ?>
                            <option value="<?= $s ?>" <?= $viewBooking['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-admin btn-gold" style="width:100%;justify-content:center;">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- ═══════ BOOKINGS LIST ═══════ -->

<!-- Filters -->
<div style="display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap;align-items:center;">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
        <select name="status" class="btn-admin btn-outline-gold" style="padding:8px 16px;background:transparent;color:#D4AF37;font-family:'Poppins';border-radius:6px;cursor:pointer;" onchange="this.form.submit()">
            <option value="" style="background:#1C1C1C;">All Statuses</option>
            <?php foreach (['pending','confirmed','preparing','completed','cancelled','refunded'] as $s): ?>
            <option value="<?= $s ?>" <?= $statusFilter === $s ? 'selected' : '' ?> style="background:#1C1C1C;"><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="search" placeholder="Search ref, name, email…" value="<?= htmlspecialchars($search) ?>"
               style="padding:8px 16px;background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.12);border-radius:6px;color:#fff;font-family:'Poppins';font-size:0.85rem;outline:none;min-width:220px;">
        <button type="submit" class="btn-admin btn-gold btn-sm"><i class="fas fa-search"></i> Search</button>
        <?php if ($statusFilter || $search): ?>
        <a href="bookings.php" class="btn-admin btn-outline-gold btn-sm"><i class="fas fa-times"></i> Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="admin-table-wrap">
    <div class="admin-table-header">
        <h3><i class="fas fa-calendar-alt" style="color:#D4AF37;margin-right:8px;"></i>All Bookings (<?= count($bookings) ?>)</h3>
        <a href="export_bookings.php<?= !empty($_GET) ? '?' . http_build_query($_GET) : '' ?>" class="btn-admin btn-outline-gold btn-sm"><i class="fas fa-file-csv"></i> Export CSV</a>
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
                <th>Payment</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bookings)): ?>
            <tr>
                <td colspan="9" style="text-align:center;color:#888;padding:40px;">No bookings found.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($bookings as $b): ?>
            <tr>
                <td style="font-weight:600;color:#D4AF37;font-size:0.82rem;"><?= htmlspecialchars($b['ref_number']) ?></td>
                <td>
                    <div style="font-weight:500;"><?= htmlspecialchars($b['customer_name']) ?></div>
                    <div style="font-size:0.73rem;color:#888;"><?= htmlspecialchars($b['customer_phone']) ?></div>
                </td>
                <td><?= htmlspecialchars($b['event_category']) ?><br><small><?= htmlspecialchars($b['event_sub_category']) ?></small></td>
                <td><?= date('d M Y', strtotime($b['event_date'])) ?></td>
                <td><?= $b['guest_count'] ?></td>
                <td style="font-weight:600;"><?= formatCurrency((float)$b['grand_total']) ?></td>
                <td style="font-size:0.8rem;"><?= ucwords(str_replace('_', ' ', $b['payment_method'])) ?></td>
                <td><span class="badge badge-<?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span></td>
                <td>
                    <a href="bookings.php?view=<?= $b['id'] ?>" class="btn-admin btn-outline-gold btn-sm"><i class="fas fa-eye"></i></a>
                    <a href="generate_invoice.php?id=<?= $b['id'] ?>&download=1" class="btn-admin btn-sm" style="background:rgba(52,152,219,0.15);color:#3498db;border:1px solid rgba(52,152,219,0.3);" target="_blank"><i class="fas fa-file-pdf"></i></a>
                    
                    <button type="button" class="btn-admin btn-danger btn-sm" onclick="confirmDelete(<?= $b['id'] ?>, 'delete_booking', 'Are you sure you want to delete booking <?= $b['ref_number'] ?>?', true)"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<!-- Edit Booking Modal -->
<?php if ($viewBooking): ?>
<div class="admin-modal-overlay" id="editBookingModal">
    <div class="admin-modal">
        <h3><i class="fas fa-edit" style="margin-right:8px;color:#D4AF37;"></i>Edit Booking #<?= htmlspecialchars($viewBooking['ref_number']) ?></h3>
        <form method="POST" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="action" value="edit_booking">
            <input type="hidden" name="booking_id" value="<?= $viewId ?>">
            
            <div class="form-group">
                <label>Customer Name</label>
                <input type="text" name="customer_name" class="form-control" value="<?= htmlspecialchars($viewBooking['customer_name']) ?>" required>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="customer_email" class="form-control" value="<?= htmlspecialchars($viewBooking['customer_email']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="customer_phone" class="form-control" value="<?= htmlspecialchars($viewBooking['customer_phone']) ?>" required>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label>Event Date</label>
                    <input type="date" name="event_date" class="form-control" value="<?= $viewBooking['event_date'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Event Time (24-hour format)</label>
                    <input type="time" name="event_time" class="form-control" step="900" value="<?= $viewBooking['event_time'] ? date('H:i', strtotime($viewBooking['event_time'])) : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Venue Address</label>
                <textarea name="address" class="form-control" style="min-height:60px;"><?= htmlspecialchars($viewBooking['address']) ?></textarea>
            </div>
            
            <div style="display:flex;gap:12px;margin-top:16px;">
                <button type="submit" class="btn-admin btn-gold" style="flex:1;justify-content:center;"><i class="fas fa-save"></i> Save Changes</button>
                <button type="button" class="btn-admin btn-outline-gold" onclick="closeEditModal()" style="flex:1;justify-content:center;">Cancel</button>
            </div>
        </form>
    </div>
</div>
<script>
function openEditBookingModal() { document.getElementById('editBookingModal').classList.add('active'); }
function closeEditModal() { document.getElementById('editBookingModal').classList.remove('active'); }
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
