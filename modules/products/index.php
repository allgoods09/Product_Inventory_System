<?php
require_once '../../includes/auth.php';
requireLogin();
require_once '../../config/database.php';

if (isset($_GET['delete']) && isAdmin()) {
    $id = (int)$_GET['delete'];
    $conn->query("UPDATE products SET status='Inactive' WHERE id=$id");
    header("Location: index.php?msg=deleted"); exit;
}

$pageTitle    = "Products";
$pageSubtitle = "Manage your inventory items";
$headerAction = isAdmin() ? '<a href="add.php" class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold inline-flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Add Product</a>' : '';

$search   = trim($_GET['search'] ?? '');
$catFilter= (int)($_GET['category'] ?? 0);
$where    = ["1=1"];
if ($search)    $where[] = "(p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
if ($catFilter) $where[] = "p.category_id=$catFilter";
$whereSQL = implode(' AND ', $where);

$products   = $conn->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE $whereSQL ORDER BY p.created_at DESC");
$categories = $conn->query("SELECT id, name FROM categories WHERE status='Active' ORDER BY name");

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<?php if (isset($_GET['msg'])): ?>
<div class="mb-5 flash-<?= $_GET['msg']==='deleted'?'error':'success' ?>">
    <?= $_GET['msg']==='saved' ? '✓ Product saved.' : '✓ Product deactivated.' ?>
</div>
<?php endif; ?>

<div class="card p-6">
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="search" placeholder="Search products…" value="<?= htmlspecialchars($search) ?>" class="max-w-xs">
        <select name="category" style="width:auto; padding: 10px 14px;">
            <option value="">All Categories</option>
            <?php $categories->data_seek(0); while ($c = $categories->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>" <?= $catFilter==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold">Filter</button>
        <?php if ($search || $catFilter): ?><a href="index.php" class="px-4 py-2 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Clear</a><?php endif; ?>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b-2 border-slate-100">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">#</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Product</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Category</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Cost</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Price</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Stock</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                    <?php if (isAdmin()): ?><th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($products->num_rows === 0): ?>
                <tr><td colspan="8" class="py-12 text-center text-slate-400">No products found.</td></tr>
                <?php else: $i=1; while ($p = $products->fetch_assoc()): ?>
                <tr class="table-row border-b border-slate-50">
                    <td class="py-3 px-4 text-slate-400 font-mono text-xs"><?= $i++ ?></td>
                    <td class="py-3 px-4">
                        <div class="font-semibold text-slate-800"><?= htmlspecialchars($p['name']) ?></div>
                        <?php if ($p['description']): ?>
                        <div class="text-xs text-slate-400 truncate max-w-xs"><?= htmlspecialchars($p['description']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="py-3 px-4 text-slate-500"><?= htmlspecialchars($p['cat_name'] ?? '—') ?></td>
                    <td class="py-3 px-4 text-right text-slate-500">₱<?= number_format($p['cost_price'],2) ?></td>
                    <td class="py-3 px-4 text-right font-semibold text-slate-800">₱<?= number_format($p['selling_price'],2) ?></td>
                    <td class="py-3 px-4 text-right">
                        <span class="font-bold <?= $p['stock_quantity'] <= $p['reorder_level'] ? 'text-red-500' : 'text-slate-800' ?>">
                            <?= $p['stock_quantity'] ?>
                        </span>
                        <span class="text-xs text-slate-400"> / <?= $p['reorder_level'] ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="badge-<?= strtolower($p['status']) ?> text-xs font-semibold px-2.5 py-1 rounded-full"><?= $p['status'] ?></span>
                    </td>
                    <?php if (isAdmin()): ?>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="edit.php?id=<?= $p['id'] ?>" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">Edit</a>
                            <a href="index.php?delete=<?= $p['id'] ?>" onclick="return confirm('Deactivate this product?')" class="btn-danger text-xs font-semibold px-3 py-1.5 rounded-lg transition">Delete</a>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>