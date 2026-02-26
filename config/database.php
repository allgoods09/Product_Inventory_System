<?php
// Detect base URL automatically
if (!defined('BASE_URL')) {
    $scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
    $parts = explode('/', trim($scriptPath, '/'));
    // The first segment is the project folder (e.g. "Product_Inventory_System")
    define('BASE_URL', '/' . $parts[0]);
}

$host = "localhost";
$user = "root";
$password = "";
$database = "inventory_system";

$conn = new mysqli($host, $user, $password);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$conn->query("CREATE DATABASE IF NOT EXISTS `$database`");
$conn->select_db($database);

$queries = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        email VARCHAR(100) UNIQUE,
        password VARCHAR(255),
        role VARCHAR(50),
        status VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        description TEXT,
        status VARCHAR(20) DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT,
        name VARCHAR(150),
        description TEXT,
        cost_price DECIMAL(10,2),
        selling_price DECIMAL(10,2),
        stock_quantity INT,
        reorder_level INT,
        status VARCHAR(20) DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )",
    "CREATE TABLE IF NOT EXISTS sales (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT,
        quantity INT,
        price DECIMAL(10,2),
        total_amount DECIMAL(10,2),
        payment_method VARCHAR(50),
        sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id)
    )",
    // Admin seed handled separately below (needs PHP hash generation)
    "SELECT 1"
];

foreach ($queries as $sql) {
    if (!$conn->query($sql)) die("Setup error: " . $conn->error);
}

// Seed default admin with a properly generated hash
$adminCheck = $conn->query("SELECT id, password FROM users WHERE email = 'admin@gmail.com'");
if ($adminCheck->num_rows === 0) {
    $adminHash = password_hash('password', PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, status) VALUES ('Admin', 'admin@gmail.com', ?, 'Admin', 'Active')");
    $stmt->bind_param("s", $adminHash);
    $stmt->execute();
} else {
    // Repair hash if it was seeded with the old static hash (starts with known prefix)
    $adminRow = $adminCheck->fetch_assoc();
    if (!password_verify('password', $adminRow['password'])) {
        $adminHash = password_hash('password', PASSWORD_BCRYPT);
        $conn->query("UPDATE users SET password='$adminHash' WHERE email='admin@gmail.com'");
    }
}
?>