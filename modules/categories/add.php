<?php
require '../../includes/auth.php';
require '../../config/database.php';

if(isset($_POST['add'])){
    $stmt = $conn->prepare("INSERT INTO categories (name, description, status) VALUES (?, ?, 'Active')");
    $stmt->bind_param("ss", $_POST['name'], $_POST['description']);
    $stmt->execute();
    header("Location: index.php");
}
?>

<form method="POST">
Name: <input name="name" required><br>
Description: <input name="description"><br>
<button name="add">Add</button>
</form>
