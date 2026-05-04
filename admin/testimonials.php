<?php
/**
 * ZAMAHI Admin - Testimonials Management
 */
require_once __DIR__ . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
$csrfToken = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $pdo->prepare("INSERT INTO testimonials (name, event_type, rating, review, is_active) VALUES (?,?,?,?,?)")
            ->execute([
                sanitize($_POST['name']),
                sanitize($_POST['event_type']),
                max(1, min(5, (int)$_POST['rating'])),
                sanitize($_POST['review']),
                isset($_POST['is_active']) ? 1 : 0
            ]);
        setFlash('success', 'Testimonial added.');
    } elseif ($action === 'edit') {
        $pdo->prepare("UPDATE testimonials SET name=?, event_type=?, rating=?, review=?, is_active=? WHERE id=?")
            ->execute([
                sanitize($_POST['name']),
                sanitize($_POST['event_type']),
                max(1, min(5, (int)$_POST['rating'])),
                sanitize($_POST['review']),
                isset($_POST['is_active']) ? 1 : 0,
                (int)$_POST['item_id']
            ]);
        setFlash('success', 'Testimonial updated.');
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM testimonials WHERE id = ?")->execute([(int)$_POST['item_id']]);
        setFlash('success', 'Testimonial deleted.');
    }
    header('Location: testimonials.php'); exit;
}

$testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC")->fetchAll();
require_once __DIR__ . '/includes/admin_header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <h3 style="font-size:1rem;">Testimonials (<?= count($testimonials) ?>)</h3>
    <button class="btn-admin btn-gold" onclick="document.getElementById('addModal').classList.add('active')"><i class="fas fa-plus"></i> Add Testimonial</button>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead><tr><th>Name</th><th>Event</th><th>Rating</th><th>Review</th><th>Active</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($testimonials as $t): ?>
            <tr>
                <td style="font-weight:500;"><?= htmlspecialchars($t['name']) ?></td>
                <td><?= htmlspecialchars($t['event_type']) ?></td>
                <td style="color:#D4AF37;"><?= str_repeat('★', $t['rating']) ?><?= str_repeat('☆', 5 - $t['rating']) ?></td>
                <td style="max-width:300px;"><div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($t['review']) ?></div></td>
                <td><?= $t['is_active'] ? '<span style="color:#2ecc71;">✓</span>' : '<span style="color:#e74c3c;">✗</span>' ?></td>
                <td>
                    <button class="btn-admin btn-outline-gold btn-sm" onclick='editTestimonial(<?= json_encode($t) ?>)'><i class="fas fa-edit"></i></button>
                    <button type="button" class="btn-admin btn-danger btn-sm" onclick="confirmDelete(<?= $t['id'] ?>, 'delete', 'Delete this testimonial?')"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="admin-modal-overlay" id="addModal">
    <div class="admin-modal">
        <h3 id="modalTitle"><i class="fas fa-star" style="margin-right:8px;color:#D4AF37;"></i>Add Testimonial</h3>
        <form method="POST" class="admin-form" id="testForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="action" value="add" id="formAction">
            <input type="hidden" name="item_id" value="" id="formItemId">
            <div class="form-group"><label>Client Name</label><input type="text" name="name" class="form-control" id="fName" required></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label>Event Type</label>
                    <select name="event_type" class="form-control" id="fEvent">
                        <option value="Wedding">Wedding</option><option value="Corporate">Corporate</option><option value="Private Party">Private Party</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Rating</label>
                    <select name="rating" class="form-control" id="fRating">
                        <option value="5">★★★★★ (5)</option><option value="4">★★★★☆ (4)</option><option value="3">★★★☆☆ (3)</option>
                    </select>
                </div>
            </div>
            <div class="form-group"><label>Review</label><textarea name="review" class="form-control" id="fReview" required style="min-height:100px;"></textarea></div>
            <div class="form-group"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;"><input type="checkbox" name="is_active" id="fActive" checked style="accent-color:#2ecc71;"> Active</label></div>
            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn-admin btn-gold" style="flex:1;justify-content:center;"><i class="fas fa-save"></i> Save</button>
                <button type="button" class="btn-admin btn-outline-gold" onclick="document.getElementById('addModal').classList.remove('active')" style="flex:1;justify-content:center;">Cancel</button>
            </div>
        </form>
    </div>
</div>
<script>
function editTestimonial(t) {
    document.getElementById('formAction').value = 'edit';
    document.getElementById('formItemId').value = t.id;
    document.getElementById('fName').value = t.name;
    document.getElementById('fEvent').value = t.event_type;
    document.getElementById('fRating').value = t.rating;
    document.getElementById('fReview').value = t.review;
    document.getElementById('fActive').checked = t.is_active == 1;
    document.getElementById('addModal').classList.add('active');
}
</script>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
