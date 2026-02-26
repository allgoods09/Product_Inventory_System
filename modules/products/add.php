<?php
require '../../includes/auth.php';
require '../../config/database.php';

if(isset($_POST['add'])){
    $stmt=$conn->prepare("INSERT INTO products(category_id,name,description,cost_price,selling_price,stock_quantity,reorder_level,status) VALUES(?,?,?,?,?,?,?,'Active')");
    $stmt->bind_param("issddii",$_POST['category_id'],$_POST['name'],$_POST['description'],$_POST['cost'],$_POST['sell'],$_POST['stock'],$_POST['reorder']);
    $stmt->execute();
    header("Location: index.php");
}

$cats=$conn->query("SELECT id,name FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Product</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<h1 class="text-2xl font-bold mb-4">Add Product</h1>
<form method="POST" class="space-y-4 bg-white p-6 rounded shadow">
<input name="name" placeholder="Product Name" class="w-full px-4 py-2 border rounded" required>
<input name="description" placeholder="Description" class="w-full px-4 py-2 border rounded">
<input name="cost" type="number" step="0.01" placeholder="Cost Price" class="w-full px-4 py-2 border rounded">
<input name="sell" type="number" step="0.01" placeholder="Selling Price" class="w-full px-4 py-2 border rounded">
<input name="stock" type="number" placeholder="Stock Quantity" class="w-full px-4 py-2 border rounded">
<input name="reorder" type="number" placeholder="Reorder Level" class="w-full px-4 py-2 border rounded">
<select name="category_id" class="w-full px-4 py-2 border rounded">
<?php while($c=$cats->fetch_assoc()): ?>
<option value="<?php echo $c['id'];?>"><?php echo $c['name'];?></option>
<?php endwhile; ?>
</select>
<button name="add" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Add Product</button>
</form>
<a href="index.php" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Back</a>
</body>
</html>
