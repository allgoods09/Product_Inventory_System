<?php
require '../../includes/auth.php';
require '../../config/database.php';

if(isset($_POST['add'])){
    $stmt = $conn->prepare("INSERT INTO products 
        (category_id, name, description, cost_price, selling_price, stock_quantity, reorder_level, status)
        VALUES (?,?,?,?,?,?,?, 'Active')");
    $stmt->bind_param("issddii",
        $_POST['category_id'],
        $_POST['name'],
        $_POST['description'],
        $_POST['cost'],
        $_POST['sell'],
        $_POST['stock'],
        $_POST['reorder']
    );
    $stmt->execute();
    header("Location: index.php");
}

$cats = $conn->query("SELECT id,name FROM categories");
?>

<form method="POST">
Name: <input name="name" required><br>
Description: <input name="description"><br>
Cost: <input name="cost" type="number" step="0.01"><br>
Selling: <input name="sell" type="number" step="0.01"><br>
Stock: <input name="stock" type="number"><br>
Reorder Level: <input name="reorder" type="number"><br>
Category:
<select name="category_id">
<?php while($c=$cats->fetch_assoc()): ?>
<option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
<?php endwhile; ?>
</select><br>
<button name="add">Add</button>
</form>
