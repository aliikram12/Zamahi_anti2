<?php
/**
 * ZAMAHI Admin - Gallery Management
 */
require_once __DIR__ . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
$csrfToken = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    if ($action === 'add' || $action === 'edit') {
        $id          = (int)($_POST['item_id'] ?? 0);
        $category    = sanitize($_POST['category'] ?? '');
        $caption     = sanitize($_POST['caption'] ?? '');
        $sortOrder   = (int)($_POST['sort_order'] ?? 0);
        $isActive    = isset($_POST['is_active']) ? 1 : 0;
        
        if ($action === 'add' && isset($_FILES['image'])) {
            $galleryDir = GALLERY_DIR;
            if (!is_dir($galleryDir)) mkdir($galleryDir, 0755, true);
            $filename = uploadFile($_FILES['image'], $galleryDir);
            if ($filename) {
                $pdo->prepare("INSERT INTO gallery (category, image_path, caption, is_active, sort_order) VALUES (?,?,?,?,?)")
                    ->execute([$category, $filename, $caption, $isActive, $sortOrder]);
                setFlash('success', 'Image added to gallery.');
            } else {
                setFlash('error', 'Image upload failed. Check file type and size (max 5MB).');
            }
        } else {
            // Edit mode
            $pdo->prepare("UPDATE gallery SET category=?, caption=?, is_active=?, sort_order=? WHERE id=?")
                ->execute([$category, $caption, $isActive, $sortOrder, $id]);
            setFlash('success', 'Gallery item updated.');
        }
    } elseif ($action === 'delete') {
        $id = (int)$_POST['item_id'];
        $img = $pdo->prepare("SELECT image_path FROM gallery WHERE id = ?");
        $img->execute([$id]);
        $img = $img->fetchColumn();
        if ($img && file_exists(GALLERY_DIR . $img)) unlink(GALLERY_DIR . $img);
        $pdo->prepare("DELETE FROM gallery WHERE id = ?")->execute([$id]);
        setFlash('success', 'Image removed.');
    } elseif ($action === 'toggle') {
        $id = (int)$_POST['item_id'];
        $pdo->prepare("UPDATE gallery SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
        setFlash('success', 'Visibility toggled.');
    }
    header('Location: gallery.php'); exit;
}

$gallery = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC")->fetchAll();
require_once __DIR__ . '/includes/admin_header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <h3 style="font-size:1rem;">Gallery (<?= count($gallery) ?> images)</h3>
    <button class="btn-admin btn-gold" onclick="openAddModal()"><i class="fas fa-plus"></i> Upload Image</button>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px;">
    <?php foreach ($gallery as $g): ?>
    <div style="background:var(--dark-grey);border:1px solid rgba(212,175,55,0.1);border-radius:12px;overflow:hidden;position:relative;">
        <img src="<?= SITE_URL ?>/assets/images/gallery/<?= htmlspecialchars($g['image_path']) ?>" alt="<?= htmlspecialchars($g['caption']) ?>" style="width:100%;height:200px;object-fit:cover;">
        <div style="padding:16px;">
            <div style="font-size:0.88rem;font-weight:500;margin-bottom:4px;"><?= htmlspecialchars($g['caption'] ?: $g['category']) ?></div>
            <div style="font-size:0.75rem;color:#888;"><?= ucfirst($g['category']) ?> | <?= $g['is_active'] ? '<span style="color:#2ecc71;">Active</span>' : '<span style="color:#e74c3c;">Hidden</span>' ?></div>
            <div style="display:flex;gap:8px;margin-top:12px;">
                <button type="button" class="btn-admin btn-outline-gold btn-sm" style="flex:1;justify-content:center;" onclick='editGallery(<?= json_encode($g) ?>)'><i class="fas fa-edit"></i></button>
                <form method="POST" style="flex:1;"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"><input type="hidden" name="action" value="toggle"><input type="hidden" name="item_id" value="<?= $g['id'] ?>"><button type="submit" class="btn-admin btn-outline-gold btn-sm" style="width:100%;justify-content:center;background:rgba(255,255,255,0.05);"><i class="fas fa-eye<?= $g['is_active'] ? '-slash' : '' ?>"></i></button></form>
                <button type="button" class="btn-admin btn-danger btn-sm" style="width:100%;justify-content:center;" onclick="confirmDelete(<?= $g['id'] ?>, 'delete', 'Delete this gallery image?')"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($gallery)): ?>
    <div style="grid-column:1/-1;text-align:center;color:#888;padding:60px;">No gallery images yet. Upload your first image.</div>
    <?php endif; ?>
</div>

<div class="admin-modal-overlay" id="galleryModal">
    <div class="admin-modal">
        <h3 id="modalTitle"><i class="fas fa-image" style="margin-right:8px;color:#D4AF37;"></i>Upload Gallery Image</h3>
        <form method="POST" enctype="multipart/form-data" class="admin-form" id="galleryForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="action" value="add" id="formAction">
            <input type="hidden" name="item_id" value="" id="formItemId">
            
            <div class="form-group" id="fileGroup">
                <label>Image File (JPEG, PNG, WebP — Max 5MB)</label>
                <input type="file" name="image" id="formImage" class="form-control" accept="image/jpeg,image/png,image/webp" required style="padding:10px;">
            </div>
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" id="formCategory" class="form-control">
                        <option value="weddings">Weddings</option>
                        <option value="corporate">Corporate</option>
                        <option value="private">Private Parties</option>
                        <option value="food">Food</option>
                        <option value="general">General</option>
                    </select>
                </div>
                <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" id="formSort" class="form-control" value="0" min="0"></div>
            </div>
            <div class="form-group"><label>Caption</label><input type="text" name="caption" id="formCaption" class="form-control" placeholder="Brief description"></div>
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
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-image" style="margin-right:8px;color:#D4AF37;"></i>Upload Gallery Image';
    document.getElementById('formAction').value = 'add';
    document.getElementById('formItemId').value = '';
    document.getElementById('fileGroup').style.display = 'block';
    document.getElementById('formImage').required = true;
    document.getElementById('galleryModal').classList.add('active');
}

function editGallery(item) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit" style="margin-right:8px;color:#D4AF37;"></i>Edit Gallery Item';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('formItemId').value = item.id;
    document.getElementById('fileGroup').style.display = 'none';
    document.getElementById('formImage').required = false;
    
    document.getElementById('formCategory').value = item.category;
    document.getElementById('formSort').value = item.sort_order;
    document.getElementById('formCaption').value = item.caption || '';
    document.getElementById('formActive').checked = item.is_active == 1;
    
    document.getElementById('galleryModal').classList.add('active');
}

function closeModal() {
    document.getElementById('galleryModal').classList.remove('active');
    document.getElementById('galleryForm').reset();
}
</script>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
