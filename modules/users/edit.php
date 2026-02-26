<?php
require_once '../../includes/auth.php';
requireAdmin();
require_once '../../config/database.php';

$id   = (int)($_GET['id'] ?? 0);
$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
if (!$user) { header("Location: index.php"); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $role    = $_POST['role'] ?? 'Staff';
    $status  = $_POST['status'] ?? 'Active';
    $newpass = trim($_POST['password'] ?? '');

    if (!$name || !$email) {
        $error = "Name and email are required.";
    } else {
        if ($newpass) {
            $hash = password_hash($newpass, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=?, role=?, status=? WHERE id=?");
            $stmt->bind_param("sssssi", $name, $email, $hash, $role, $status, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=?, status=? WHERE id=?");
            $stmt->bind_param("ssssi", $name, $email, $role, $status, $id);
        }
        $stmt->execute();
        header("Location: index.php?msg=saved"); exit;
    }

    // On validation error, keep what the user typed by updating the $user array manually
    $user['name']   = $name;
    $user['email']  = $email;
    $user['role']   = $role;
    $user['status'] = $status;
}

$pageTitle    = "Edit User";
$pageSubtitle = "Update user details";
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>
<div class="max-w-xl">
    <div class="card p-8">
        <?php if ($error): ?>
        <div class="flash-error mb-5"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" class="space-y-5">
            <div>
                <label>Full Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div>
                <label>Email Address *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div>
                <label>New Password <span class="text-slate-400 font-normal">(leave blank to keep current)</span></label>
                <input type="password" name="password" placeholder="••••••••">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Role</label>
                    <select name="role">
                        <option value="Staff"  <?= $user['role'] === 'Staff'  ? 'selected' : '' ?>>Staff</option>
                        <option value="Admin"  <?= $user['role'] === 'Admin'  ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div>
                    <label>Status</label>
                    <select name="status">
                        <option value="Active"   <?= $user['status'] === 'Active'   ? 'selected' : '' ?>>Active</option>
                        <option value="Inactive" <?= $user['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold">Update User</button>
                <a href="index.php" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php require_once '../../includes/footer.php'; ?>