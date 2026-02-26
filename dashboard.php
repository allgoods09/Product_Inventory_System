<?php
require 'includes/auth.php';
require 'config/database.php';

$c_count=$conn->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc()['total'];
$p_count=$conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$s_count=$conn->query("SELECT COUNT(*) as total FROM sales")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto p-6">
<h1 class="text-3xl font-bold mb-6">Dashboard</h1>
<div class="grid grid-cols-3 gap-6">
    <div class="bg-white shadow rounded p-4"><h2 class="font-bold">Categories</h2><p class="text-2xl"><?php echo $c_count;?></p></div>
    <div class="bg-white shadow rounded p-4"><h2 class="font-bold">Products</h2><p class="text-2xl"><?php echo $p_count;?></p></div>
    <div class="bg-white shadow rounded p-4"><h2 class="font-bold">Sales</h2><p class="text-2xl"><?php echo $s_count;?></p></div>
</div>
<div class="mt-6 space-x-4">
<a href="modules/categories/index.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Categories</a>
<a href="modules/products/index.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Products</a>
<a href="modules/sales/index.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Sales</a>
<a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Logout</a>
</div>
</div>
</body>
</html>
