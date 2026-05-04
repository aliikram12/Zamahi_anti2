<?php
/**
 * ZAMAHI Admin - Menu Items Management
 */
require_once __DIR__ . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

$csrfToken = generateCsrfToken();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $id          = (int)($_POST['item_id'] ?? 0);
        $category    = sanitize($_POST['category'] ?? '');
        $subCategory = sanitize($_POST['sub_category'] ?? '');
        $name        = sanitize($_POST['name'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $price       = max(0, (float)($_POST['price'] ?? 0));
        $isAddon     = isset($_POST['is_addon']) ? 1 : 0;
        $isActive    = isset($_POST['is_active']) ? 1 : 0;
        $sortOrder   = max(0, (int)($_POST['sort_order'] ?? 0));

        if ($action === 'add') {
            $pdo->prepare("INSERT INTO menu_items (category, sub_category, name, description, price, is_addon, is_active, sort_order) VALUES (?,?,?,?,?,?,?,?)")
                ->execute([$category, $subCategory ?: null, $name, $description, $price, $isAddon, $isActive, $sortOrder]);
            setFlash('success', 'Menu item added successfully.');
        } else {
            $pdo->prepare("UPDATE menu_items SET category=?, sub_category=?, name=?, description=?, price=?, is_addon=?, is_active=?, sort_order=? WHERE id=?")
                ->execute([$category, $subCategory ?: null, $name, $description, $price, $isAddon, $isActive, $sortOrder, $id]);
            setFlash('success', 'Menu item updated.');
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['item_id'] ?? 0);
        $pdo->prepare("DELETE FROM menu_items WHERE id = ?")->execute([$id]);
        setFlash('success', 'Menu item deleted.');
    }
    header('Location: menu.php');
    exit;
}

$menuItems = $pdo->query("SELECT * FROM menu_items ORDER BY category, name")->fetchAll();
$categories = ['protein','rice','bread','sides','desserts','starters','drinks'];

require_once __DIR__ . '/includes/admin_header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <h3 style="font-size:1rem;">Menu Items (<?= count($menuItems) ?>)</h3>
    <button class="btn-admin btn-gold" onclick="document.getElementById('addModal').classList.add('active')"><i class="fas fa-plus"></i> Add Item</button>
</div>

<?php foreach ($categories as $cat): ?>
<?php $items = array_filter($menuItems, fn($i) => $i['category'] === $cat); if (empty($items)) continue; ?>
<div class="admin-table-wrap">
    <div class="admin-table-header">
        <h3><i class="fas fa-utensils" style="color:#D4AF37;margin-right:8px;"></i><?= ucfirst($cat) ?></h3>
    </div>
    <table class="admin-table">
        <thead><tr><th>Name</th><th>Sub-cat</th><th>Price</th><th>Add-on</th><th>Active</th><th>Order</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td>
                    <div style="font-weight:500;"><?= htmlspecialchars($item['name']) ?></div>
                    <div style="font-size:0.75rem;color:#888;"><?= htmlspecialchars($item['description'] ?? '') ?></div>
                </td>
                <td><?= htmlspecialchars($item['sub_category'] ?? '—') ?></td>
                <td style="font-weight:600;"><?= formatCurrency((float)$item['price']) ?></td>
                <td><?= $item['is_addon'] ? '<span style="color:#D4AF37;">Yes</span>' : '—' ?></td>
                <td><?= $item['is_active'] ? '<span style="color:#2ecc71;">✓</span>' : '<span style="color:#e74c3c;">✗</span>' ?></td>
                <td><?= $item['sort_order'] ?></td>
                <td>
                    <button class="btn-admin btn-outline-gold btn-sm" onclick='editItem(<?= json_encode($item) ?>)'><i class="fas fa-edit"></i></button>
                    <button type="button" class="btn-admin btn-danger btn-sm" onclick="confirmDelete(<?= $item['id'] ?>, 'delete', 'Delete this menu item?')"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endforeach; ?>

<!-- Add/Edit Modal -->
<div class="admin-modal-overlay" id="addModal">
    <div class="admin-modal">
        <h3 id="modalTitle"><i class="fas fa-plus" style="margin-right:8px;"></i>Add Menu Item</h3>
        <form method="POST" class="admin-form" id="menuForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="action" value="add" id="formAction">
            <input type="hidden" name="item_id" value="" id="formItemId">
            <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control" id="formCategory" required>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= $c ?>"><?= ucfirst($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Sub-category (optional, e.g. Chicken, Lamb)</label>
                <input type="text" name="sub_category" class="form-control" id="formSubCat" placeholder="e.g. Chicken">
            </div>
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control" id="formName" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" id="formDesc" style="min-height:60px;"></textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label>Price (£)</label>
                    <input type="number" name="price" class="form-control" id="formPrice" min="0" step="0.01" value="0">
                </div>
                <div class="form-group">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" id="formSort" min="0" value="0">
                </div>
                <div class="form-group" style="display:flex;flex-direction:column;gap:8px;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="is_addon" id="formAddon" style="accent-color:#D4AF37;"> Add-on
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="is_active" id="formActive" checked style="accent-color:#2ecc71;"> Active
                    </label>
                </div>
            </div>
            <div style="display:flex;gap:12px;margin-top:16px;">
                <button type="submit" class="btn-admin btn-gold" style="flex:1;justify-content:center;"><i class="fas fa-save"></i> Save</button>
                <button type="button" class="btn-admin btn-outline-gold" onclick="closeModal()" style="flex:1;justify-content:center;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function editItem(item) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit" style="margin-right:8px;"></i>Edit Menu Item';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('formItemId').value = item.id;
    document.getElementById('formCategory').value = item.category;
    document.getElementById('formSubCat').value = item.sub_category || '';
    document.getElementById('formName').value = item.name;
    document.getElementById('formDesc').value = item.description || '';
    document.getElementById('formPrice').value = item.price;
    document.getElementById('formSort').value = item.sort_order;
    document.getElementById('formAddon').checked = item.is_addon == 1;
    document.getElementById('formActive').checked = item.is_active == 1;
    document.getElementById('addModal').classList.add('active');
}
function closeModal() {
    document.getElementById('addModal').classList.remove('active');
    document.getElementById('menuForm').reset();
    document.getElementById('formAction').value = 'add';
    document.getElementById('formItemId').value = '';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus" style="margin-right:8px;"></i>Add Menu Item';
}
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
