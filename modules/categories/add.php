<?php
require '../../includes/auth.php';
require '../../config/database.php';
if(isset($_POST['add'])){
    $stmt=$conn->prepare("INSERT INTO categories(name,description,status) VALUES(?,?, 'Active')");
    $stmt->bind_param("ss",$_POST['name'],$_POST['description']);
    $stmt->execute();
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Category</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<h1 class="text-2xl font-bold mb-4">Add Category</h1>
<form method="POST" class="space-y-4 bg-white p-6 rounded shadow">
<input type="text" name="name" placeholder="Category Name" class="w-full px-4 py-2 border rounded" required>
<input type="text" name="description" placeholder="Description" class="w-full px-4 py-2 border rounded">
<button name="add" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Add Category</button>
</form>
<a href="index.php" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Back</a>
</body>
</html>
