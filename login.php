<?php
session_start();
require 'config/database.php';

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        $stmt->bind_result($id, $hashed);
        $stmt->fetch();

        if(password_verify($password, $hashed)){
            $_SESSION['user_id'] = $id;
            header("Location: dashboard.php");
        } else {
            echo "Invalid Password";
        }
    } else {
        echo "User not found";
    }
}
?>

<form method="POST">
Email: <input type="email" name="email" required><br>
Password: <input type="password" name="password" required><br>
<button name="login">Login</button>
</form>
