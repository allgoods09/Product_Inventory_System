<?php
require_once '../../includes/auth.php';
requireAdmin();
require_once '../../config/database.php';
$id = (int)($_GET['id'] ?? 0);
$conn->query("UPDATE products SET status='Inactive' WHERE id=$id");
header("Location: index.php?msg=deleted"); exit;
?>