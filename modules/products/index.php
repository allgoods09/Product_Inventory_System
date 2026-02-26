<?php
require '../../includes/auth.php';
require '../../config/database.php';
$result=$conn->query("SELECT p.*,c.name as category FROM products p LEFT JOIN categories c ON p.category_id=c.id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Products</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<h1 class="text-2xl font-bold mb-4">Products</h1>
<a href="add.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded mb-4 inline-block">Add Product</a>
<table class="min-w-full divide-y divide-gray-200 bg-white rounded shadow">
<tr class="bg-gray-100"><th class="px-4 py-2">Name</th><th class="px-4 py-2">Category</th><th class="px-4 py-2">Price</th><th class="px-4 py-2">Stock</th><th class="px-4 py-2">Delete</th></tr>
<?php while($row=$result->fetch_assoc()): ?>
<tr class="hover:bg-gray-50">
<td class="px-4 py-2"><?php echo $row['name'];?></td>
<td class="px-4 py-2"><?php echo $row['category'];?></td>
<td class="px-4 py-2"><?php echo $row['selling_price'];?></td>
<td class="px-4 py-2"><?php echo $row['stock_quantity'];?></td>
<td class="px-4 py-2"><a href="delete.php?id=<?php echo $row['id'];?>" class="text-red-500 hover:text-red-700">Delete</a></td>
</tr>
<?php endwhile; ?>
</table>
<a href="../../dashboard.php" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Back</a>
</body>
</html>
