<?php
require '../../includes/auth.php';
require '../../config/database.php';

if(isset($_POST['sell'])){
    $product_id = $_POST['product_id'];
    $qty = $_POST['qty'];

    $res = $conn->query("SELECT selling_price, stock_quantity FROM products WHERE id=$product_id");
    $p = $res->fetch_assoc();

    if($qty > $p['stock_quantity']){
        echo "Not enough stock!";
        exit();
    }

    $total = $qty * $p['selling_price'];

    $conn->query("INSERT INTO sales 
        (product_id, quantity, price, total_amount, payment_method)
        VALUES ($product_id,$qty,{$p['selling_price']},$total,'Cash')");

    $conn->query("UPDATE products 
        SET stock_quantity = stock_quantity - $qty 
        WHERE id=$product_id");

    header("Location: index.php");
}

$products = $conn->query("SELECT id,name FROM products");
?>

<form method="POST">
Product:
<select name="product_id">
<?php while($p=$products->fetch_assoc()): ?>
<option value="<?php echo $p['id']; ?>"><?php echo $p['name']; ?></option>
<?php endwhile; ?>
</select><br>
Quantity: <input name="qty" type="number"><br>
<button name="sell">Sell</button>
</form>
