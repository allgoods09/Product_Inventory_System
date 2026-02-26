<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    $base = '/' . explode('/', trim($_SERVER['SCRIPT_NAME'], '/'))[0];
    header("Location: $base/dashboard.php"); exit;
}

$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm'] ?? '');

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'Staff', 'Inactive')");
            $stmt->bind_param("sss", $name, $email, $hash);
            if ($stmt->execute()) {
                $base = '/' . explode('/', trim($_SERVER['SCRIPT_NAME'], '/'))[0];
                header("Location: $base/login.php?registered=1"); exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — StockFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        input { border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; width: 100%; font-family: 'DM Sans'; font-size: 14px; outline: none; transition: border-color 0.15s; }
        input:focus { border-color: #16b36e; box-shadow: 0 0 0 3px rgba(22,179,110,0.1); }
        .btn { background: #16b36e; color: white; border-radius: 10px; padding: 13px; width: 100%; font-weight: 600; font-size: 15px; transition: all 0.15s; cursor: pointer; }
        .btn:hover { background: #0d9258; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center" style="background:#f0f4f8;">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-10">
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-6">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#16b36e;">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <span class="font-bold text-slate-800">StockFlow</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Request Access</h1>
            <p class="text-slate-400 mt-1 text-sm">Your account will be activated by an admin.</p>
        </div>

        <?php if ($error): ?>
        <div class="mb-5 p-3 rounded-xl text-sm" style="background:#fee2e2; color:#991b1b;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Full Name</label>
                <input type="text" name="name" placeholder="John Doe" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                <input type="email" name="email" placeholder="you@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Confirm Password</label>
                <input type="password" name="confirm" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn mt-2">Create Account</button>
        </form>
        <p class="text-center text-sm text-slate-400 mt-5">Already have an account? <a href="login.php" class="font-semibold" style="color:#16b36e;">Sign in</a></p>
    </div>
</body>
</html>