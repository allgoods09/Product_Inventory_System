<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    $base = '/' . explode('/', trim($_SERVER['SCRIPT_NAME'], '/'))[0];
    header("Location: $base/dashboard.php"); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND status = 'Active'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $base = '/' . explode('/', trim($_SERVER['SCRIPT_NAME'], '/'))[0];
        header("Location: $base/dashboard.php"); exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — StockFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        input { border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; width: 100%; font-family: 'DM Sans'; font-size: 14px; outline: none; transition: border-color 0.15s; }
        input:focus { border-color: #16b36e; box-shadow: 0 0 0 3px rgba(22,179,110,0.1); }
        .btn { background: #16b36e; color: white; border-radius: 10px; padding: 13px; width: 100%; font-weight: 600; font-size: 15px; transition: all 0.15s; cursor: pointer; }
        .btn:hover { background: #0d9258; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(22,179,110,0.35); }
    </style>
</head>
<body class="min-h-screen flex" style="background: #f0f4f8;">
    <!-- Left Panel -->
    <div class="hidden lg:flex w-1/2 flex-col justify-between p-16" style="background: #0d1b2a;">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#16b36e;">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <span class="text-white text-xl font-bold">StockFlow</span>
        </div>
        <div>
            <h2 class="text-4xl font-bold text-white leading-tight mb-4">Manage your inventory<br><span style="color:#16b36e;">smarter & faster.</span></h2>
            <p class="text-slate-400 text-lg">Track products, sales, and categories all in one place.</p>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <?php foreach ([['Products','Track every SKU'],['Sales','Real-time records'],['Categories','Organized catalog'],['Reports','Data at a glance']] as [$t,$s]): ?>
            <div class="p-4 rounded-xl" style="background:rgba(255,255,255,0.05);">
                <div class="text-white font-semibold text-sm"><?= $t ?></div>
                <div class="text-slate-500 text-xs mt-1"><?= $s ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="flex-1 flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            <div class="mb-10">
                <h1 class="text-3xl font-bold text-slate-800">Welcome back</h1>
                <p class="text-slate-400 mt-2">Sign in to your account to continue</p>
            </div>

            <?php if ($error): ?>
            <div class="mb-6 p-4 rounded-xl text-sm font-medium" style="background:#fee2e2; color:#991b1b; border:1px solid #fca5a5;">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
            <div class="mb-6 p-4 rounded-xl text-sm font-medium" style="background:#dcfce7; color:#166534; border:1px solid #86efac;">
                Account created! You can now sign in once an admin activates your account.
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email Address</label>
                    <input type="email" name="email" placeholder="admin@gmail.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn mt-2">Sign In</button>
            </form>

            <p class="text-center text-sm text-slate-400 mt-6">
                Don't have an account? <a href="register.php" class="font-semibold" style="color:#16b36e;">Request access</a>
            </p>
            <p class="text-center text-xs text-slate-300 mt-4">Default: admin@gmail.com / password</p>
        </div>
    </div>
</body>
</html>