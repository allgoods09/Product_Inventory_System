<?php
require '../../includes/auth.php';
require '../../config/database.php';

$result = $conn->query("SELECT s.*, p.name FROM sales s
                        JOIN products p ON s.product_id=p.id");
?>

<h2>Sales</h2>
<a href="create.php">New Sale</a>
<table border=1>
<tr><th>Product</th><th>Qty</th><th>Total</th><th>Date</th></tr>
<?php while($row=$result->fetch_assoc()): ?>
<tr>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['quantity']; ?></td>
<td><?php echo $row['total_amount']; ?></td>
<td><?php echo $row['sale_date']; ?></td>
</tr>
<?php endwhile; ?>
</table>
