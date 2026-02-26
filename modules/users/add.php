<?php
require_once '../../includes/auth.php';
requireAdmin();
require_once '../../config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = $_POST['role'] ?? 'Staff';
    $status   = $_POST['status'] ?? 'Active';

    if (!$name || !$email || !$password) {
        $error = "All fields are required.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "Email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $hash, $role, $status);
            $stmt->execute();
            header("Location: index.php?msg=saved"); exit;
        }
    }
}

$pageTitle   = "Add User";
$pageSubtitle= "Create a new system user";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>
<div class="max-w-xl">
    <div class="card p-8">
        <?php if ($error): ?><div class="flash-error mb-5"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" class="space-y-5">
            <div>
                <label>Full Name *</label>
                <input type="text" name="name" placeholder="John Doe" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>
            <div>
                <label>Email Address *</label>
                <input type="email" name="email" placeholder="user@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div>
                <label>Password *</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Role</label>
                    <select name="role">
                        <option value="Staff" <?= ($_POST['role']??'')==='Staff'?'selected':'' ?>>Staff</option>
                        <option value="Admin" <?= ($_POST['role']??'')==='Admin'?'selected':'' ?>>Admin</option>
                    </select>
                </div>
                <div>
                    <label>Status</label>
                    <select name="status">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold">Create User</button>
                <a href="index.php" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php require_once '../../includes/footer.php'; ?>