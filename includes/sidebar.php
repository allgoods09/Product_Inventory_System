<?php
$currentPath = $_SERVER['PHP_SELF'];
function navActive($path) {
    global $currentPath;
    return str_contains($currentPath, $path) ? 'active' : '';
}
$user = currentUser();
?>
<!-- Sidebar -->
<aside class="w-64 min-h-screen flex flex-col" style="background:#0d1b2a;">
    <!-- Logo -->
    <div class="px-6 py-6 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#16b36e;">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <div class="text-white font-bold text-lg leading-none">StockFlow</div>
                <div class="text-xs mt-0.5" style="color:#16b36e;">Inventory System</div>
            </div>
        </div>
    </div>

    <!-- User Info -->
    <div class="px-4 py-4 border-b border-white/10">
        <div class="flex items-center gap-3 px-2 py-2 rounded-xl" style="background:rgba(255,255,255,0.05);">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0" style="background:#16b36e;">
                <?= strtoupper(substr($user['name'], 0, 1)) ?>
            </div>
            <div class="overflow-hidden">
                <div class="text-white text-sm font-medium truncate"><?= htmlspecialchars($user['name']) ?></div>
                <div class="text-xs" style="color:rgba(255,255,255,0.4);"><?= $user['role'] ?></div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-1">
        <div class="text-xs font-semibold uppercase tracking-widest mb-3 px-3" style="color:rgba(255,255,255,0.3);">Main</div>

        <a href="<?= BASE_URL ?>/dashboard.php" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?= navActive('dashboard') ? 'active' : '' ?>" style="color:rgba(255,255,255,0.7);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>

        <div class="text-xs font-semibold uppercase tracking-widest mb-2 mt-5 px-3" style="color:rgba(255,255,255,0.3);">Inventory</div>

        <a href="<?= BASE_URL ?>/modules/products/index.php" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?= navActive('products') ? 'active' : '' ?>" style="color:rgba(255,255,255,0.7);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Products
        </a>

        <a href="<?= BASE_URL ?>/modules/categories/index.php" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?= navActive('categories') ? 'active' : '' ?>" style="color:rgba(255,255,255,0.7);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            Categories
        </a>

        <div class="text-xs font-semibold uppercase tracking-widest mb-2 mt-5 px-3" style="color:rgba(255,255,255,0.3);">Transactions</div>

        <a href="<?= BASE_URL ?>/modules/sales/index.php" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?= navActive('sales') ? 'active' : '' ?>" style="color:rgba(255,255,255,0.7);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
            Sales
        </a>

        <?php if (isAdmin()): ?>
        <div class="text-xs font-semibold uppercase tracking-widest mb-2 mt-5 px-3" style="color:rgba(255,255,255,0.3);">Admin</div>
        <a href="<?= BASE_URL ?>/modules/users/index.php" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium <?= navActive('users') ? 'active' : '' ?>" style="color:rgba(255,255,255,0.7);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Users
        </a>
        <?php endif; ?>
    </nav>

    <!-- Logout -->
    <div class="px-3 py-4 border-t border-white/10">
        <a href="<?= BASE_URL ?>/logout.php" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium" style="color:rgba(255,255,255,0.5);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Sign Out
        </a>
    </div>
</aside>

<!-- Main Content Wrapper -->
<main class="flex-1 flex flex-col overflow-auto">
    <!-- Top Bar -->
    <header class="bg-white border-b border-slate-100 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
        <div>
            <h1 class="text-xl font-bold text-slate-800"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
            <?php if (!empty($pageSubtitle)): ?>
            <p class="text-sm text-slate-400 mt-0.5"><?= htmlspecialchars($pageSubtitle) ?></p>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-slate-400"><?= date('l, F j, Y') ?></span>
            <?php if (!empty($headerAction)): ?>
                <?= $headerAction ?>
            <?php endif; ?>
        </div>
    </header>
    <div class="flex-1 p-8">