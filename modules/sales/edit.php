<?php
require_once '../../includes/auth.php';
requireAdmin();
require_once '../../config/database.php';

$id   = (int)($_GET['id'] ?? 0);
$sale = $conn->query("SELECT s.*, p.stock_quantity, p.selling_price FROM sales s JOIN products p ON s.product_id=p.id WHERE s.id=$id")->fetch_assoc();
if (!$sale) { header("Location: index.php"); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_qty     = (int)$_POST['quantity'];
    $payment     = trim($_POST['payment_method'] ?? '');
    $old_qty     = (int)$sale['quantity'];
    $product_id  = (int)$sale['product_id'];
    $price       = (float)$sale['price'];

    // Available stock = current stock + what was originally sold (reversing the old sale)
    $available = $sale['stock_quantity'] + $old_qty;

    if ($new_qty < 1) {
        $error = "Quantity must be at least 1.";
    } elseif ($new_qty > $available) {
        $error = "Not enough stock. Max available: $available units.";
    } else {
        $total      = $price * $new_qty;
        $stock_diff = $old_qty - $new_qty; // positive = restoring stock, negative = taking more

        // Update sale
        $stmt = $conn->prepare("UPDATE sales SET quantity=?, total_amount=?, payment_method=? WHERE id=?");
        $stmt->bind_param("idsi", $new_qty, $total, $payment, $id);
        $stmt->execute();

        // Adjust product stock
        $conn->query("UPDATE products SET stock_quantity = stock_quantity + $stock_diff WHERE id=$product_id");

        header("Location: index.php?msg=updated"); exit;
    }
}

$pageTitle    = "Edit Sale";
$pageSubtitle = "Modify transaction #$id";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

// Refresh sale for display
$sale = $conn->query("SELECT s.*, p.name as product_name, p.stock_quantity, p.selling_price FROM sales s JOIN products p ON s.product_id=p.id WHERE s.id=$id")->fetch_assoc();
$maxQty = $sale['stock_quantity'] + $sale['quantity']; // current stock + originally sold
?>

<div class="max-w-xl">
    <div class="card p-8">

        <!-- Sale info banner -->
        <div class="mb-6 p-4 rounded-xl" style="background:#f8fafc; border:1px solid #e2e8f0;">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Editing Sale</div>
            <div class="font-bold text-slate-800 text-lg"><?= htmlspecialchars($sale['product_name']) ?></div>
            <div class="text-sm text-slate-500 mt-0.5">
                Unit price: <strong>₱<?= number_format($sale['price'], 2) ?></strong> &nbsp;·&nbsp;
                Max qty available: <strong><?= $maxQty ?></strong>
            </div>
        </div>

        <?php if ($error): ?>
        <div class="flash-error mb-5"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label>Quantity *</label>
                <input type="number" name="quantity" min="1" max="<?= $maxQty ?>"
                    value="<?= htmlspecialchars($_POST['quantity'] ?? $sale['quantity']) ?>"
                    id="qtyInput" oninput="updateTotal()" required>
                <p class="text-xs text-slate-400 mt-1">Originally: <?= $sale['quantity'] ?> units</p>
            </div>

            <div>
                <label>Payment Method *</label>
                <select name="payment_method" required>
                    <?php foreach (['Cash','GCash','PayMaya','Credit Card','Debit Card','Bank Transfer'] as $pm): ?>
                    <option value="<?= $pm ?>" <?= ($sale['payment_method'] === $pm ? 'selected' : '') ?>><?= $pm ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Live total preview -->
            <div class="p-4 rounded-xl" style="background:#0d1b2a;">
                <div class="text-xs text-slate-400 uppercase tracking-wide font-semibold mb-1">New Total</div>
                <div class="text-3xl font-bold text-white" id="totalDisplay">
                    ₱<?= number_format($sale['price'] * $sale['quantity'], 2) ?>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold">Update Sale</button>
                <a href="index.php" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
const unitPrice = <?= (float)$sale['price'] ?>;
function updateTotal() {
    const qty = parseInt(document.getElementById('qtyInput').value) || 0;
    document.getElementById('totalDisplay').textContent = '₱' + (unitPrice * qty).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}
</script>

<?php require_once '../../includes/footer.php'; ?>