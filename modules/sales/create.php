<?php
require '../../includes/auth.php';
require '../../config/database.php';

if(isset($_POST['sell'])){
    $product_id=$_POST['product_id'];
    $qty=$_POST['qty'];
    $res=$conn->query("SELECT selling_price,stock_quantity FROM products WHERE id=$product_id");
    $p=$res->fetch_assoc();
    if($qty>$p['stock_quantity']){ echo "Not enough stock!"; exit(); }
    $total=$qty*$p['selling_price'];
    $conn->query("INSERT INTO sales(product_id,quantity,price,total_amount,payment_method) VALUES($product_id,$qty,{$p['selling_price']},$total,'Cash')");
    $conn->query("UPDATE products SET stock_quantity=stock_quantity-$qty WHERE id=$product_id");
    header("Location: index.php");
}

$products=$conn->query("SELECT id,name FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Sale</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<h1 class="text-2xl font-bold mb-4">Create Sale</h1>
<form method="POST" class="space-y-4 bg-white p-6 rounded shadow">
<select name="product_id" class="w-full px-4 py-2 border rounded">
<?php while($p=$products->fetch_assoc()): ?>
<option value="<?php echo $p['id'];?>"><?php echo $p['name'];?></option>
<?php endwhile; ?>
</select>
<input name="qty" type="number" placeholder="Quantity" class="w-full px-4 py-2 border rounded">
<button name="sell" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Sell</button>
</form>
<a href="index.php" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py
