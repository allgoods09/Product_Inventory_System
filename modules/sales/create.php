<?php
require_once '../../includes/auth.php';
requireLogin();
require_once '../../config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $quantity   = (int)$_POST['quantity'];
    $payment    = trim($_POST['payment_method'] ?? '');

    $product = $conn->query("SELECT * FROM products WHERE id=$product_id AND status='Active'")->fetch_assoc();
    if (!$product) {
        $error = "Invalid product selected.";
    } elseif ($quantity < 1) {
        $error = "Quantity must be at least 1.";
    } elseif ($quantity > $product['stock_quantity']) {
        $error = "Insufficient stock. Available: " . $product['stock_quantity'];
    } else {
        $price = $product['selling_price'];
        $total = $price * $quantity;
        $stmt  = $conn->prepare("INSERT INTO sales (product_id, quantity, price, total_amount, payment_method) VALUES (?,?,?,?,?)");
        $stmt->bind_param("iidds", $product_id, $quantity, $price, $total, $payment);
        $stmt->execute();
        // Reduce stock
        $conn->query("UPDATE products SET stock_quantity = stock_quantity - $quantity WHERE id=$product_id");
        header("Location: index.php?msg=saved"); exit;
    }
}

$products    = $conn->query("SELECT id, name, selling_price, stock_quantity FROM products WHERE status='Active' AND stock_quantity > 0 ORDER BY name");
$pageTitle   = "New Sale";
$pageSubtitle= "Record a sales transaction";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>
<div class="max-w-xl">
    <div class="card p-8">
        <?php if ($error): ?><div class="flash-error mb-5"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" class="space-y-5" id="saleForm">
            <div>
                <label>Product *</label>
                <select name="product_id" id="productSelect" onchange="updatePrice()" required>
                    <option value="">— Select Product —</option>
                    <?php while ($p = $products->fetch_assoc()): ?>
                    <option value="<?= $p['id'] ?>" 
                        data-price="<?= $p['selling_price'] ?>" 
                        data-stock="<?= $p['stock_quantity'] ?>"
                        <?= ($_POST['product_id']??'')==$p['id']?'selected':'' ?>>
                        <?= htmlspecialchars($p['name']) ?> (Stock: <?= $p['stock_quantity'] ?>)
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div id="priceInfo" class="hidden p-4 rounded-xl" style="background:#f0fdf4; border:1px solid #86efac;">
                <div class="text-sm font-semibold text-slate-700">Unit Price: <span id="displayPrice" class="text-green-700"></span></div>
                <div class="text-xs text-slate-500 mt-0.5">Available Stock: <span id="displayStock"></span> units</div>
            </div>

            <div>
                <label>Quantity *</label>
                <input type="number" name="quantity" id="quantityInput" min="1" placeholder="1" value="<?= htmlspecialchars($_POST['quantity'] ?? '') ?>" oninput="updateTotal()" required>
            </div>

            <div>
                <label>Payment Method *</label>
                <select name="payment_method" required>
                    <option value="">— Select —</option>
                    <?php foreach (['Cash','GCash','PayMaya','Credit Card','Debit Card','Bank Transfer'] as $pm): ?>
                    <option value="<?= $pm ?>" <?= ($_POST['payment_method']??'')===$pm?'selected':'' ?>><?= $pm ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="totalBox" class="hidden p-4 rounded-xl" style="background:#0d1b2a;">
                <div class="text-xs text-slate-400 uppercase tracking-wide font-semibold mb-1">Total Amount</div>
                <div class="text-3xl font-bold text-white" id="displayTotal">₱0.00</div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold">Record Sale</button>
                <a href="index.php" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
function updatePrice() {
    const sel = document.getElementById('productSelect');
    const opt = sel.options[sel.selectedIndex];
    const price = parseFloat(opt.dataset.price || 0);
    const stock = parseInt(opt.dataset.stock || 0);
    const info = document.getElementById('priceInfo');
    if (price) {
        document.getElementById('displayPrice').textContent = '₱' + price.toFixed(2);
        document.getElementById('displayStock').textContent = stock;
        info.classList.remove('hidden');
        document.getElementById('quantityInput').max = stock;
    } else {
        info.classList.add('hidden');
    }
    updateTotal();
}
function updateTotal() {
    const sel   = document.getElementById('productSelect');
    const opt   = sel.options[sel.selectedIndex];
    const price = parseFloat(opt.dataset.price || 0);
    const qty   = parseInt(document.getElementById('quantityInput').value || 0);
    const box   = document.getElementById('totalBox');
    if (price && qty > 0) {
        document.getElementById('displayTotal').textContent = '₱' + (price * qty).toFixed(2);
        box.classList.remove('hidden');
    } else {
        box.classList.add('hidden');
    }
}
</script>
<?php require_once '../../includes/footer.php'; ?>