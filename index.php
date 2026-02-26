<?php
require 'config/database.php';
session_start();

// LOGIN
if(isset($_POST['login'])){
    $email = $_POST['login_email'];
    $password = $_POST['login_password'];

    $stmt = $conn->prepare("SELECT id,password FROM users WHERE email=? AND status='Active'");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows>0){
        $stmt->bind_result($id,$hashed);
        $stmt->fetch();
        if(password_verify($password,$hashed)){
            $_SESSION['user_id']=$id;
            header("Location: dashboard.php");
        }else{
            $login_error="Invalid password.";
        }
    }else{
        $login_error="User not found or inactive.";
    }
}

// REGISTER
if(isset($_POST['register'])){
    $name=$_POST['reg_name'];
    $email=$_POST['reg_email'];
    $password=$_POST['reg_password'];
    $role=$_POST['reg_role']??'Staff';
    $hashed=password_hash($password,PASSWORD_DEFAULT);

    $stmt=$conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows>0){
        $reg_error="Email already registered!";
    }else{
        $stmt=$conn->prepare("INSERT INTO users(name,email,password,role,status) VALUES(?,?,?,?,'Active')");
        $stmt->bind_param("ssss",$name,$email,$hashed,$role);
        if($stmt->execute()){
            $reg_success="Registration successful! You can login now.";
        }else{
            $reg_error="Error: ".$stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Inventory System</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="w-full max-w-6xl mx-auto flex shadow-lg rounded-lg overflow-hidden bg-white">

    <!-- LOGIN -->
    <div class="w-1/2 p-8 bg-gray-50">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Login</h2>
        <?php if(isset($login_error)) echo "<p class='text-red-500 mb-4'>$login_error</p>"; ?>
        <form method="POST" class="space-y-4">
            <input type="email" name="login_email" placeholder="Email" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            <input type="password" name="login_password" placeholder="Password" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            <button name="login" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">Login</button>
        </form>
    </div>

    <!-- REGISTER -->
    <div class="w-1/2 p-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Register</h2>
        <?php if(isset($reg_error)) echo "<p class='text-red-500 mb-4'>$reg_error</p>"; ?>
        <?php if(isset($reg_success)) echo "<p class='text-green-500 mb-4'>$reg_success</p>"; ?>
        <form method="POST" class="space-y-4">
            <input type="text" name="reg_name" placeholder="Full Name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
            <input type="email" name="reg_email" placeholder="Email" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
            <input type="password" name="reg_password" placeholder="Password" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
            <select name="reg_role" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                <option value="Admin">Admin</option>
                <option value="Staff" selected>Staff</option>
            </select>
            <button name="register" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">Register</button>
        </form>
    </div>

</div>
</body>
</html>
