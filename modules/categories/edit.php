<?php
require_once '../../includes/auth.php';
requireAdmin();
require_once '../../config/database.php';

$id = (int)($_GET['id'] ?? 0);
$cat = $conn->query("SELECT * FROM categories WHERE id=$id")->fetch_assoc();
if (!$cat) { header("Location: index.php"); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['name'] ?? '');
    $desc   = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'Active';
    if (!$name) { $error = "Category name is required."; }
    else {
        $stmt = $conn->prepare("UPDATE categories SET name=?, description=?, status=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $desc, $status, $id);
        $stmt->execute();
        header("Location: index.php?msg=saved"); exit;
    }
}

$pageTitle    = "Edit Category";
$pageSubtitle = "Update category details";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>
<div class="max-w-xl">
    <div class="card p-8">
        <?php if ($error): ?><div class="flash-error mb-5"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" class="space-y-5">
            <div>
                <label>Category Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($cat['name']) ?>" required>
            </div>
            <div>
                <label>Description</label>
                <textarea name="description" rows="3"><?= htmlspecialchars($cat['description'] ?? '') ?></textarea>
            </div>
            <div>
                <label>Status</label>
                <select name="status">
                    <option value="Active" <?= $cat['status']==='Active'?'selected':'' ?>>Active</option>
                    <option value="Inactive" <?= $cat['status']==='Inactive'?'selected':'' ?>>Inactive</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold">Update Category</button>
                <a href="index.php" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php require_once '../../includes/footer.php'; ?>