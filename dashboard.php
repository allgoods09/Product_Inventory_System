<?php
require_once 'includes/auth.php';
requireLogin();
require_once 'config/database.php';

$pageTitle = "Dashboard";
$pageSubtitle = "Welcome back, " . currentUser()['name'];

// Stats
$totalProducts  = $conn->query("SELECT COUNT(*) c FROM products WHERE status='Active'")->fetch_assoc()['c'];
$totalCategories= $conn->query("SELECT COUNT(*) c FROM categories WHERE status='Active'")->fetch_assoc()['c'];
$totalSales     = $conn->query("SELECT COUNT(*) c FROM sales")->fetch_assoc()['c'];
$totalRevenue   = $conn->query("SELECT COALESCE(SUM(total_amount),0) r FROM sales")->fetch_assoc()['r'];
$lowStock       = $conn->query("SELECT COUNT(*) c FROM products WHERE stock_quantity <= reorder_level AND status='Active'")->fetch_assoc()['c'];
$totalUsers     = $conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];

// Recent sales
$recentSales = $conn->query("
    SELECT s.*, p.name as product_name 
    FROM sales s 
    JOIN products p ON s.product_id = p.id 
    ORDER BY s.sale_date DESC LIMIT 8
");

// Low stock products
$lowStockProducts = $conn->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.stock_quantity <= p.reorder_level AND p.status='Active'
    LIMIT 5
");

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<!-- Stats Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <?php
    $stats = [
        ['Revenue',    '₱' . number_format($totalRevenue, 2), 'Total earnings', '#16b36e', 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
        ['Products',   $totalProducts,   'Active products',  '#3b82f6', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['Sales',      $totalSales,      'Total transactions','#8b5cf6', 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
        ['Low Stock',  $lowStock,        'Need reorder',     '#f59e0b', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
    ];
    foreach ($stats as [$label, $value, $sub, $color, $icon]): ?>
    <div class="card p-6">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:<?= $color ?>20;">
                <svg class="w-5 h-5" style="color:<?= $color ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $icon ?>"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-slate-800"><?= $value ?></div>
        <div class="text-sm font-semibold text-slate-700 mt-1"><?= $label ?></div>
        <div class="text-xs text-slate-400 mt-0.5"><?= $sub ?></div>
    </div>
    <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Recent Sales -->
    <div class="card p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-slate-800">Recent Sales</h2>
            <a href="/modules/sales/index.php" class="text-sm font-medium" style="color:#16b36e;">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left py-2 pb-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Product</th>
                        <th class="text-right py-2 pb-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Qty</th>
                        <th class="text-right py-2 pb-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Total</th>
                        <th class="text-right py-2 pb-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recentSales->num_rows === 0): ?>
                    <tr><td colspan="4" class="py-8 text-center text-slate-400">No sales recorded yet.</td></tr>
                    <?php else: while ($s = $recentSales->fetch_assoc()): ?>
                    <tr class="table-row border-b border-slate-50">
                        <td class="py-3 font-medium text-slate-700"><?= htmlspecialchars($s['product_name']) ?></td>
                        <td class="py-3 text-right text-slate-500"><?= $s['quantity'] ?></td>
                        <td class="py-3 text-right font-semibold text-slate-800">₱<?= number_format($s['total_amount'], 2) ?></td>
                        <td class="py-3 text-right text-slate-400 text-xs"><?= date('M d, Y', strtotime($s['sale_date'])) ?></td>
                    </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="card p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-slate-800">Low Stock Alert</h2>
            <?php if ($lowStock > 0): ?>
            <span class="text-xs font-bold px-2 py-1 rounded-full" style="background:#fef3c7; color:#d97706;"><?= $lowStock ?> items</span>
            <?php endif; ?>
        </div>
        <?php if ($lowStockProducts->num_rows === 0): ?>
        <div class="text-center py-8 text-slate-400 text-sm">All products are sufficiently stocked. ✓</div>
        <?php else: while ($p = $lowStockProducts->fetch_assoc()): ?>
        <div class="flex items-center justify-between py-3 border-b border-slate-50">
            <div>
                <div class="text-sm font-semibold text-slate-700"><?= htmlspecialchars($p['name']) ?></div>
                <div class="text-xs text-slate-400"><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></div>
            </div>
            <div class="text-right">
                <div class="text-sm font-bold" style="color:#dc2626;"><?= $p['stock_quantity'] ?></div>
                <div class="text-xs text-slate-400">/ <?= $p['reorder_level'] ?> min</div>
            </div>
        </div>
        <?php endwhile; endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>