<?php
require '../../includes/auth.php';
require '../../config/database.php';

$result = $conn->query("SELECT p.*, c.name as category 
                        FROM products p
                        LEFT JOIN categories c ON p.category_id=c.id");
?>

<h2>Products</h2>
<a href="add.php">Add Product</a>
<table border=1>
<tr><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Delete</th></tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['category']; ?></td>
<td><?php echo $row['selling_price']; ?></td>
<td><?php echo $row['stock_quantity']; ?></td>
<td><a href="delete.php?id=<?php echo $row['id']; ?>">Delete</a></td>
</tr>
<?php endwhile; ?>
</table>
