<?php
require 'config/database.php';

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'Staff'; // default role

    // Hash the password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        echo "Email already registered!";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name,email,password,role,status) VALUES (?,?,?,?, 'Active')");
        $stmt->bind_param("ssss", $name,$email,$hashed,$role);
        if($stmt->execute()){
            echo "Registration successful! <a href='login.php'>Login here</a>";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>

<h2>Register</h2>
<form method="POST">
Name: <input type="text" name="name" required><br>
Email: <input type="email" name="email" required><br>
Password: <input type="password" name="password" required><br>
Role:
<select name="role">
<option value="Admin">Admin</option>
<option value="Staff" selected>Staff</option>
</select><br>
<button name="register">Register</button>
</form>
