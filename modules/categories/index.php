<?php
require_once '../../includes/auth.php';
requireLogin();
require_once '../../config/database.php';

// Handle delete
if (isset($_GET['delete']) && isAdmin()) {
    $id = (int)$_GET['delete'];
    $conn->query("UPDATE categories SET status='Inactive' WHERE id=$id");
    header("Location: index.php?msg=deleted"); exit;
}

$pageTitle    = "Categories";
$pageSubtitle = "Manage product categories";
$headerAction = isAdmin() ? '<a href="add.php" class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold inline-flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Add Category</a>' : '';

$search = trim($_GET['search'] ?? '');
$where  = $search ? "WHERE name LIKE '%$search%'" : "";
$categories = $conn->query("SELECT * FROM categories $where ORDER BY created_at DESC");

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<?php if (isset($_GET['msg'])): ?>
<div class="mb-5 flash-<?= $_GET['msg'] === 'deleted' ? 'error' : 'success' ?>">
    <?= $_GET['msg'] === 'saved' ? '✓ Category saved successfully.' : '✓ Category deactivated.' ?>
</div>
<?php endif; ?>

<div class="card p-6">
    <div class="flex items-center gap-3 mb-6">
        <form method="GET" class="flex-1 max-w-xs">
            <input type="text" name="search" placeholder="Search categories…" value="<?= htmlspecialchars($search) ?>">
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b-2 border-slate-100">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">#</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Name</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Description</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Created</th>
                    <?php if (isAdmin()): ?><th class="text-right py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wide">Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($categories->num_rows === 0): ?>
                <tr><td colspan="6" class="py-12 text-center text-slate-400">No categories found.</td></tr>
                <?php else: $i = 1; while ($cat = $categories->fetch_assoc()): ?>
                <tr class="table-row border-b border-slate-50">
                    <td class="py-3 px-4 text-slate-400 font-mono text-xs"><?= $i++ ?></td>
                    <td class="py-3 px-4 font-semibold text-slate-800"><?= htmlspecialchars($cat['name']) ?></td>
                    <td class="py-3 px-4 text-slate-500 max-w-xs truncate"><?= htmlspecialchars($cat['description'] ?? '—') ?></td>
                    <td class="py-3 px-4">
                        <span class="badge-<?= strtolower($cat['status']) ?> text-xs font-semibold px-2.5 py-1 rounded-full">
                            <?= $cat['status'] ?>
                        </span>
                    </td>
                    <td class="py-3 px-4 text-slate-400 text-xs"><?= date('M d, Y', strtotime($cat['created_at'])) ?></td>
                    <?php if (isAdmin()): ?>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="edit.php?id=<?= $cat['id'] ?>" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">Edit</a>
                            <a href="index.php?delete=<?= $cat['id'] ?>" onclick="return confirm('Deactivate this category?')" class="btn-danger text-xs font-semibold px-3 py-1.5 rounded-lg transition">Delete</a>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>