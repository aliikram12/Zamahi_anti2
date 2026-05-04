<?php
/**
 * ZAMAHI Admin - Offers / Promo Codes Management
 */
require_once __DIR__ . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
$csrfToken = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    if ($action === 'add' || $action === 'edit') {
        $id          = (int)($_POST['item_id'] ?? 0);
        $code        = strtoupper(sanitize($_POST['code']));
        $description = sanitize($_POST['description']);
        $type        = $_POST['type'] === 'fixed' ? 'fixed' : 'percentage';
        $value       = max(0, (float)$_POST['value']);
        $minOrder    = max(0, (float)($_POST['min_order'] ?? 0));
        $validFrom   = $_POST['valid_from'] ?: null;
        $validTo     = $_POST['valid_to'] ?: null;
        $maxUses     = $_POST['max_uses'] ? (int)$_POST['max_uses'] : null;
        $isActive    = isset($_POST['is_active']) ? 1 : 0;

        if ($action === 'add') {
            $pdo->prepare("INSERT INTO offers (code, description, type, value, min_order, valid_from, valid_to, max_uses, is_active) VALUES (?,?,?,?,?,?,?,?,?)")
                ->execute([$code, $description, $type, $value, $minOrder, $validFrom, $validTo, $maxUses, $isActive]);
            setFlash('success', 'Offer created.');
        } else {
            $pdo->prepare("UPDATE offers SET code=?, description=?, type=?, value=?, min_order=?, valid_from=?, valid_to=?, max_uses=?, is_active=? WHERE id=?")
                ->execute([$code, $description, $type, $value, $minOrder, $validFrom, $validTo, $maxUses, $isActive, $id]);
            setFlash('success', 'Offer updated.');
        }
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM offers WHERE id = ?")->execute([(int)$_POST['item_id']]);
        setFlash('success', 'Offer deleted.');
    } elseif ($action === 'toggle') {
        $pdo->prepare("UPDATE offers SET is_active = NOT is_active WHERE id = ?")->execute([(int)$_POST['item_id']]);
        setFlash('success', 'Offer toggled.');
    }
    header('Location: offers.php'); exit;
}

$offers = $pdo->query("SELECT * FROM offers ORDER BY created_at DESC")->fetchAll();
require_once __DIR__ . '/includes/admin_header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <h3 style="font-size:1rem;">Promo Codes & Offers (<?= count($offers) ?>)</h3>
    <button class="btn-admin btn-gold" onclick="openAddModal()"><i class="fas fa-plus"></i> Add Offer</button>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead><tr><th>Code</th><th>Description</th><th>Type</th><th>Value</th><th>Min Order</th><th>Valid</th><th>Uses</th><th>Active</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($offers as $o): ?>
            <tr>
                <td style="font-weight:600;color:#D4AF37;letter-spacing:1px;"><?= htmlspecialchars($o['code']) ?></td>
                <td><?= htmlspecialchars($o['description']) ?></td>
                <td><?= ucfirst($o['type']) ?></td>
                <td style="font-weight:600;"><?= $o['type'] === 'percentage' ? $o['value'] . '%' : formatCurrency((float)$o['value']) ?></td>
                <td><?= formatCurrency((float)$o['min_order']) ?></td>
                <td style="font-size:0.8rem;"><?= $o['valid_from'] ? date('d/m/y', strtotime($o['valid_from'])) . ' — ' . date('d/m/y', strtotime($o['valid_to'])) : 'Always' ?></td>
                <td><?= $o['used_count'] ?>/<?= $o['max_uses'] ?? '∞' ?></td>
                <td><?= $o['is_active'] ? '<span style="color:#2ecc71;">✓</span>' : '<span style="color:#e74c3c;">✗</span>' ?></td>
                <td>
                    <button class="btn-admin btn-outline-gold btn-sm" onclick='editOffer(<?= json_encode($o) ?>)'><i class="fas fa-edit"></i></button>
                    <form method="POST" style="display:inline;"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"><input type="hidden" name="action" value="toggle"><input type="hidden" name="item_id" value="<?= $o['id'] ?>"><button type="submit" class="btn-admin btn-outline-gold btn-sm"><i class="fas fa-power-off"></i></button></form>
                    <button type="button" class="btn-admin btn-danger btn-sm" onclick="confirmDelete(<?= $o['id'] ?>, 'delete', 'Delete this promo code?')"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="admin-modal-overlay" id="offerModal">
    <div class="admin-modal">
        <h3 id="modalTitle"><i class="fas fa-tags" style="margin-right:8px;color:#D4AF37;"></i>Add Promo Code</h3>
        <form method="POST" class="admin-form" id="offerForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="action" value="add" id="formAction">
            <input type="hidden" name="item_id" value="" id="formItemId">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group"><label>Code</label><input type="text" name="code" id="formCode" class="form-control" placeholder="e.g. SUMMER20" required style="text-transform:uppercase;"></div>
                <div class="form-group"><label>Type</label><select name="type" id="formType" class="form-control"><option value="percentage">Percentage</option><option value="fixed">Fixed Amount</option></select></div>
            </div>
            <div class="form-group"><label>Description</label><input type="text" name="description" id="formDesc" class="form-control" placeholder="Short description"></div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <div class="form-group"><label>Value</label><input type="number" name="value" id="formVal" class="form-control" min="0" step="0.01" required></div>
                <div class="form-group"><label>Min Order (£)</label><input type="number" name="min_order" id="formMin" class="form-control" min="0" step="0.01" value="0"></div>
                <div class="form-group"><label>Max Uses</label><input type="number" name="max_uses" id="formMax" class="form-control" min="0" placeholder="Unlimited"></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group"><label>Valid From</label><input type="date" name="valid_from" id="formFrom" class="form-control"></div>
                <div class="form-group"><label>Valid To</label><input type="date" name="valid_to" id="formTo" class="form-control"></div>
            </div>
            <div class="form-group"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;"><input type="checkbox" name="is_active" id="formActive" checked style="accent-color:#2ecc71;"> Active</label></div>
            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn-admin btn-gold" style="flex:1;justify-content:center;"><i class="fas fa-save"></i> Save</button>
                <button type="button" class="btn-admin btn-outline-gold" onclick="closeModal()" style="flex:1;justify-content:center;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-tags" style="margin-right:8px;color:#D4AF37;"></i>Add Promo Code';
    document.getElementById('formAction').value = 'add';
    document.getElementById('formItemId').value = '';
    document.getElementById('offerModal').classList.add('active');
}

function editOffer(o) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit" style="margin-right:8px;color:#D4AF37;"></i>Edit Promo Code';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('formItemId').value = o.id;
    
    document.getElementById('formCode').value = o.code;
    document.getElementById('formType').value = o.type;
    document.getElementById('formDesc').value = o.description;
    document.getElementById('formVal').value = o.value;
    document.getElementById('formMin').value = o.min_order;
    document.getElementById('formMax').value = o.max_uses || '';
    document.getElementById('formFrom').value = o.valid_from ? o.valid_from.split(' ')[0] : '';
    document.getElementById('formTo').value = o.valid_to ? o.valid_to.split(' ')[0] : '';
    document.getElementById('formActive').checked = o.is_active == 1;
    
    document.getElementById('offerModal').classList.add('active');
}

function closeModal() {
    document.getElementById('offerModal').classList.remove('active');
    document.getElementById('offerForm').reset();
}
</script>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
