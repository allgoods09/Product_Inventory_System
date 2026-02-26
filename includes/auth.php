<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /inventory_system/login.php");
    exit();
}
?>
