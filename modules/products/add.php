<?php
require_once '../../includes/auth.php';
requireAdmin();
require_once '../../config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['name'] ?? '');
    $cat_id    = (int)$_POST['category_id'];
    $desc      = trim($_POST['description'] ?? '');
    $cost      = (float)$_POST['cost_price'];
    $sell      = (float)$_POST['selling_price'];
    $stock     = (int)$_POST['stock_quantity'];
    $reorder   = (int)$_POST['reorder_level'];
    $status    = $_POST['status'] ?? 'Active';

    if (!$name) { $error = "Product name is required."; }
    elseif ($sell <= 0) { $error = "Selling price must be greater than 0."; }
    else {
        $stmt = $conn->prepare("INSERT INTO products (category_id,name,description,cost_price,selling_price,stock_quantity,reorder_level,status) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("issddiis", $cat_id, $name, $desc, $cost, $sell, $stock, $reorder, $status);
        $stmt->execute();
        header("Location: index.php?msg=saved"); exit;
    }
}

$categories  = $conn->query("SELECT id,name FROM categories WHERE status='Active' ORDER BY name");
$pageTitle   = "Add Product";
$pageSubtitle= "Create a new inventory item";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>
<div class="max-w-2xl">
    <div class="card p-8">
        <?php if ($error): ?><div class="flash-error mb-5"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" class="space-y-5">
            <div class="grid grid-cols-2 gap-5">
                <div class="col-span-2">
                    <label>Product Name *</label>
                    <input type="text" name="name" placeholder="e.g. Wireless Mouse" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                </div>
                <div class="col-span-2">
                    <label>Category</label>
                    <select name="category_id">
                        <option value="0">— Select Category —</option>
                        <?php while ($c = $categories->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>" <?= ($_POST['category_id']??'')==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-span-2">
                    <label>Description</label>
                    <textarea name="description" rows="3" placeholder="Optional description…"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
                <div>
                    <label>Cost Price (₱) *</label>
                    <input type="number" step="0.01" min="0" name="cost_price" placeholder="0.00" value="<?= htmlspecialchars($_POST['cost_price'] ?? '') ?>" required>
                </div>
                <div>
                    <label>Selling Price (₱) *</label>
                    <input type="number" step="0.01" min="0" name="selling_price" placeholder="0.00" value="<?= htmlspecialchars($_POST['selling_price'] ?? '') ?>" required>
                </div>
                <div>
                    <label>Stock Quantity *</label>
                    <input type="number" min="0" name="stock_quantity" placeholder="0" value="<?= htmlspecialchars($_POST['stock_quantity'] ?? '') ?>" required>
                </div>
                <div>
                    <label>Reorder Level *</label>
                    <input type="number" min="0" name="reorder_level" placeholder="10" value="<?= htmlspecialchars($_POST['reorder_level'] ?? '') ?>" required>
                </div>
                <div>
                    <label>Status</label>
                    <select name="status">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold">Save Product</button>
                <a href="index.php" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php require_once '../../includes/footer.php'; ?>