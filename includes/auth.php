<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!defined('BASE_URL')) {
    $scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
    $parts = explode('/', trim($scriptPath, '/'));
    define('BASE_URL', '/' . $parts[0]);
}

function isLoggedIn() {
    // Check if session is properly started and user_id exists
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return false;
    }
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    // Use case-insensitive comparison for role check
    $role = isset($_SESSION['user_role']) ? strtolower($_SESSION['user_role']) : '';
    if ($role !== 'admin') {
        header("Location: " . BASE_URL . "/dashboard.php?error=unauthorized");
        exit;
    }
}

function isAdmin() {
    // Use case-insensitive comparison for role check
    if (!isset($_SESSION['user_role']) || empty($_SESSION['user_role'])) {
        return false;
    }
    return strtolower($_SESSION['user_role']) === 'admin';
}

function currentUser() {
    return [
        'id'   => $_SESSION['user_id']   ?? null,
        'name' => $_SESSION['user_name'] ?? '',
        'role' => $_SESSION['user_role'] ?? '',
    ];
}
?>
