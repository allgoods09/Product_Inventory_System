<?php
require_once '../../includes/auth.php';
requireAdmin();
require_once '../../config/database.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id !== (int)currentUser()['id']) {
        $conn->query("UPDATE users SET status='Inactive' WHERE id=$id");
    }
    header("Location: index.php?msg=deleted"); exit;
}

$pageTitle    = "User Management";
$pageSubtitle = "Manage system users and roles";
$headerAction = '<a href="add.php" class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold inline-flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Add User</a>';

$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<?php if (isset($_GET['msg'])): ?>
<div class="mb-5 flash-<?= $_GET['msg']==='deleted'?'error':'success' ?>">
    <?= $_GET['msg']==='saved' ? '✓ User saved successfully.' : '✓ User deactivated.' ?>
</div>
<?php endif; ?>

<div class="card p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b-2 border-slate-100">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">#</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Name</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Email</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Role</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Joined</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $i=1; while ($u = $users->fetch_assoc()): $isSelf = $u['id'] === currentUser()['id']; ?>
                <tr class="table-row border-b border-slate-50">
                    <td class="py-3 px-4 text-slate-400 font-mono text-xs"><?= $i++ ?></td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background:#16b36e;">
                                <?= strtoupper(substr($u['name'],0,1)) ?>
                            </div>
                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($u['name']) ?></span>
                            <?php if ($isSelf): ?><span class="text-xs px-2 py-0.5 rounded-full" style="background:#eff6ff; color:#3b82f6;">You</span><?php endif; ?>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-slate-500"><?= htmlspecialchars($u['email']) ?></td>
                    <td class="py-3 px-4">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?= $u['role']==='Admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-50 text-blue-600' ?>">
                            <?= $u['role'] ?>
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="badge-<?= strtolower($u['status']) ?> text-xs font-semibold px-2.5 py-1 rounded-full"><?= $u['status'] ?></span>
                    </td>
                    <td class="py-3 px-4 text-slate-400 text-xs"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="edit.php?id=<?= $u['id'] ?>" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">Edit</a>
                            <?php if (!$isSelf): ?>
                            <a href="index.php?delete=<?= $u['id'] ?>" onclick="return confirm('Deactivate this user?')" class="btn-danger text-xs font-semibold px-3 py-1.5 rounded-lg transition">Deactivate</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>