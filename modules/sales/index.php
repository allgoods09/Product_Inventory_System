<?php
require_once '../../includes/auth.php';
requireLogin();
require_once '../../config/database.php';

$pageTitle    = "Sales";
$pageSubtitle = "Transaction history";
$headerAction = '<a href="create.php" class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold inline-flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>New Sale</a>';

// Handle delete
if (isset($_GET['delete']) && isAdmin()) {
    $id  = (int)$_GET['delete'];
    $row = $conn->query("SELECT product_id, quantity FROM sales WHERE id=$id")->fetch_assoc();
    if ($row) {
        // Restore stock
        $conn->query("UPDATE products SET stock_quantity = stock_quantity + {$row['quantity']} WHERE id={$row['product_id']}");
        $conn->query("DELETE FROM sales WHERE id=$id");
    }
    header("Location: index.php?msg=deleted"); exit;
}

$search = trim($_GET['search'] ?? '');
$where  = $search ? "WHERE p.name LIKE '%$search%'" : "";

$sales = $conn->query("
    SELECT s.*, p.name as product_name 
    FROM sales s 
    JOIN products p ON s.product_id=p.id 
    $where
    ORDER BY s.sale_date DESC
");

$totalRevenue = $conn->query("SELECT COALESCE(SUM(total_amount),0) r FROM sales")->fetch_assoc()['r'];
$totalSales   = $conn->query("SELECT COUNT(*) c FROM sales")->fetch_assoc()['c'];

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<?php if (isset($_GET['msg'])): ?>
<div class="mb-5 flash-<?= $_GET['msg'] === 'deleted' ? 'error' : 'success' ?>">
    <?= $_GET['msg'] === 'saved' ? '✓ Sale recorded successfully.' : ($_GET['msg'] === 'updated' ? '✓ Sale updated.' : '✓ Sale deleted and stock restored.') ?>
</div>
<?php endif; ?>

<div class="grid grid-cols-3 gap-5 mb-6">
    <div class="card p-5">
        <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">Total Revenue</div>
        <div class="text-2xl font-bold text-slate-800">₱<?= number_format($totalRevenue, 2) ?></div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">Total Transactions</div>
        <div class="text-2xl font-bold text-slate-800"><?= $totalSales ?></div>
    </div>
    <div class="card p-5">
        <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">Avg. Sale Value</div>
        <div class="text-2xl font-bold text-slate-800">₱<?= $totalSales > 0 ? number_format($totalRevenue / $totalSales, 2) : '0.00' ?></div>
    </div>
</div>

<div class="card p-6">
    <form method="GET" class="flex gap-3 mb-6">
        <input type="text" name="search" placeholder="Search by product…" value="<?= htmlspecialchars($search) ?>" class="max-w-xs">
        <button type="submit" class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold">Search</button>
        <?php if ($search): ?><a href="index.php" class="px-4 py-2 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Clear</a><?php endif; ?>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b-2 border-slate-100">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">#</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Product</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Qty</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Unit Price</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Total</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Payment</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Date</th>
                    <?php if (isAdmin()): ?>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($sales->num_rows === 0): ?>
                <tr><td colspan="7" class="py-12 text-center text-slate-400">No sales recorded yet.</td></tr>
                <?php else: $i=1; while ($s = $sales->fetch_assoc()): ?>
                <tr class="table-row border-b border-slate-50">
                    <td class="py-3 px-4 text-slate-400 font-mono text-xs"><?= $i++ ?></td>
                    <td class="py-3 px-4 font-semibold text-slate-800"><?= htmlspecialchars($s['product_name']) ?></td>
                    <td class="py-3 px-4 text-right text-slate-600"><?= $s['quantity'] ?></td>
                    <td class="py-3 px-4 text-right text-slate-500">₱<?= number_format($s['price'],2) ?></td>
                    <td class="py-3 px-4 text-right font-bold text-slate-800">₱<?= number_format($s['total_amount'],2) ?></td>
                    <td class="py-3 px-4">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background:#eff6ff; color:#1d4ed8;"><?= htmlspecialchars($s['payment_method']) ?></span>
                    </td>
                    <td class="py-3 px-4 text-slate-400 text-xs"><?= date('M d, Y H:i', strtotime($s['sale_date'])) ?></td>
                    <?php if (isAdmin()): ?>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="edit.php?id=<?= $s['id'] ?>" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">Edit</a>
                            <a href="index.php?delete=<?= $s['id'] ?>" onclick="return confirm('Delete this sale? Stock will be restored.')" class="btn-danger text-xs font-semibold px-3 py-1.5 rounded-lg transition">Delete</a>
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