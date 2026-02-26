<?php
require '../../includes/auth.php';
require '../../config/database.php';

$result = $conn->query("SELECT * FROM categories");
?>

<h2>Categories</h2>
<a href="add.php">Add Category</a>
<table border=1>
<tr><th>ID</th><th>Name</th><th>Description</th></tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['description']; ?></td>
</tr>
<?php endwhile; ?>
</table>
