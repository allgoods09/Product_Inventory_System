<?php
require 'includes/auth.php';
require 'config/database.php';

$result = $conn->query("SELECT COUNT(*) as total FROM products");
$row = $result->fetch_assoc();
?>

<h2>Dashboard</h2>
<p>Total Products: <?php echo $row['total']; ?></p>
<a href="modules/products/index.php">Products</a> |
<a href="modules/categories/index.php">Categories</a> |
<a href="modules/sales/index.php">Sales</a> |
<a href="logout.php">Logout</a>
