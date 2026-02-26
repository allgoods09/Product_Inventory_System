<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "inventory_system";

// Connect without selecting a database first
$conn = new mysqli($host, $user, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS `$database`");
$conn->select_db($database);

// Create tables and seed data if they don't exist
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
        status VARCHAR(20),
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
        status VARCHAR(20),
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

    // Insert default admin only if no users exist yet
    "INSERT INTO users (name, email, password, role, status)
     SELECT 'Admin', 'admin@gmail.com',
            '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9eB1C4M6NnR9R6H7e8gI5G',
            'Admin', 'Active'
     FROM DUAL
     WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@gmail.com')"
];

foreach ($queries as $sql) {
    if (!$conn->query($sql)) {
        die("Setup error: " . $conn->error . "<br>Query: $sql");
    }
}
?>
