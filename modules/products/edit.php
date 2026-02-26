<?php
require_once '../../includes/auth.php';
requireAdmin();
require_once '../../config/database.php';

$id = (int)($_GET['id'] ?? 0);
$product = $conn->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.id=$id")->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name          = trim($_POST['name'] ?? '');
    $category_id   = (int)($_POST['category_id'] ?? 0);
    $description   = trim($_POST['description'] ?? '');
    $cost_price    = (float)($_POST['cost_price'] ?? 0);
    $selling_price = (float)($_POST['selling_price'] ?? 0);
    $stock_quantity= (int)($_POST['stock_quantity'] ?? 0);
    $reorder_level = (int)($_POST['reorder_level'] ?? 10);
    $status        = $_POST['status'] ?? 'Active';

    if (!$name) {
        $error = "Product name is required.";
    } else {
        $stmt = $conn->prepare("UPDATE products SET name=?, category_id=?, description=?, cost_price=?, selling_price=?, stock_quantity=?, reorder_level=?, status=? WHERE id=?");
        $stmt->bind_param("sisdiiisi", $name, $category_id, $description, $cost_price, $selling_price, $stock_quantity, $reorder_level, $status, $id);
        
        if ($stmt->execute()) {
            header("Location: index.php?msg=saved");
            exit;
        } else {
            $error = "Failed to update product. Please try again.";
        }
    }
    // Keep original product data and update with POST data safely
    $product['name'] = $name;
    $product['category_id'] = $category_id;
    $product['description'] = $description;
    $product['cost_price'] = $cost_price;
    $product['selling_price'] = $selling_price;
    $product['stock_quantity'] = $stock_quantity;
    $product['reorder_level'] = $reorder_level;
    $product['status'] = $status;
}

$categories = $conn->query("SELECT id, name FROM categories WHERE status='Active' ORDER BY name");

$pageTitle    = "Edit Product";
$pageSubtitle = "Update product details";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>
<div class="max-w-xl">
    <div class="card p-8">
        <?php if ($error): ?>
        <div class="flash-error mb-5"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-5">
            <div>
                <label>Product Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>
            
            <div>
                <label>Category</label>
                <select name="category_id">
                    <option value="">-- Select Category --</option>
                    <?php while ($c = $categories->fetch_assoc()): ?>
                    <option value="<?= $c['id'] ?>" <?= $product['category_id']==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div>
                <label>Description</label>
                <textarea name="description" rows="3"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Cost Price (₱)</label>
                    <input type="number" name="cost_price" step="0.01" min="0" value="<?= htmlspecialchars($product['cost_price']) ?>">
                </div>
                <div>
                    <label>Selling Price (₱)</label>
                    <input type="number" name="selling_price" step="0.01" min="0" value="<?= htmlspecialchars($product['selling_price']) ?>">
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label>Stock Quantity</label>
                    <input type="number" name="stock_quantity" min="0" value="<?= htmlspecialchars($product['stock_quantity']) ?>">
                </div>
                <div>
                    <label>Reorder Level</label>
                    <input type="number" name="reorder_level" min="0" value="<?= htmlspecialchars($product['reorder_level']) ?>">
                </div>
                <div>
                    <label>Status</label>
                    <select name="status">
                        <option value="Active" <?= $product['status']==='Active'?'selected':'' ?>>Active</option>
                        <option value="Inactive" <?= $product['status']==='Inactive'?'selected':'' ?>>Inactive</option>
                    </select>
                </div>
            </div>
            
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold">Update Product</button>
                <a href="index.php" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php require_once '../../includes/footer.php'; ?>

