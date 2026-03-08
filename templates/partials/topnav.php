<?php
/**
 * EduWrite AI - Top Navigation Bar Partial
 */
$currentUser = $currentUser ?? null;
$breadcrumbs = $breadcrumbs ?? [];
$pageTitle = $pageTitle ?? 'Dashboard';
?>
<header class="fixed top-0 left-0 right-0 z-30 bg-white border-b border-gray-200 h-16">
    <div class="flex items-center justify-between h-full px-4 sm:px-6">
        <!-- Left: Logo & Mobile Toggle -->
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <a href="/" class="flex items-center gap-2 text-indigo-600 font-bold text-lg">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.083 12.083 0 0121 12.5c0 3-3.58 5.5-9 5.5s-9-2.5-9-5.5c0-.538.214-1.062.6-1.555L12 14z"/></svg>
                <span class="hidden sm:inline">EduWrite AI</span>
            </a>
        </div>

        <!-- Center: Search -->
        <div class="hidden md:flex flex-1 max-w-lg mx-8">
            <div class="relative w-full">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" placeholder="Search documents, classes, assignments…" class="w-full pl-10 pr-4 py-2 bg-gray-100 border border-transparent rounded-lg text-sm focus:bg-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition" />
            </div>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-2">
            <!-- Notifications -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span id="notification-badge" class="hidden absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-medium">0</span>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 top-12 w-80 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-900 text-sm">Notifications</h3>
                        <button onclick="markAllNotificationsRead()" class="text-xs text-indigo-600 hover:text-indigo-800">Mark all read</button>
                    </div>
                    <div id="notification-list" class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                        <div class="px-4 py-8 text-center text-gray-400 text-sm">No notifications yet</div>
                    </div>
                    <a href="/pages/notifications.php" class="block px-4 py-2.5 text-center text-sm text-indigo-600 hover:bg-gray-50 border-t border-gray-100">View all notifications</a>
                </div>
            </div>

            <!-- User Menu -->
            <?php if ($currentUser): ?>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-semibold">
                        <?= htmlspecialchars(strtoupper(substr($currentUser['first_name'] ?? 'U', 0, 1))) ?>
                    </div>
                    <span class="hidden sm:inline text-sm font-medium text-gray-700"><?= htmlspecialchars($currentUser['first_name'] ?? 'User') ?></span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 top-12 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50">
                    <div class="px-4 py-2.5 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars(($currentUser['first_name'] ?? '') . ' ' . ($currentUser['last_name'] ?? '')) ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($currentUser['email'] ?? '') ?></p>
                        <span class="inline-block mt-1 px-2 py-0.5 bg-indigo-50 text-indigo-700 text-xs font-medium rounded-full capitalize"><?= htmlspecialchars($currentUser['role'] ?? 'user') ?></span>
                    </div>
                    <a href="/pages/profile.php" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> Profile</a>
                    <a href="/pages/settings.php" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.573-1.066z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Settings</a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <a href="/pages/auth/logout.php" class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg> Log Out</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Breadcrumbs -->
    <?php if (!empty($breadcrumbs)): ?>
    <div class="hidden lg:block bg-gray-50 border-b border-gray-200 px-6 py-2">
        <nav class="flex items-center gap-1.5 text-sm text-gray-500">
            <a href="/pages/dashboard.php" class="hover:text-gray-700">Home</a>
            <?php foreach ($breadcrumbs as $crumb): ?>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <?php if (!empty($crumb['url'])): ?>
                    <a href="<?= htmlspecialchars($crumb['url']) ?>" class="hover:text-gray-700"><?= htmlspecialchars($crumb['label']) ?></a>
                <?php else: ?>
                    <span class="text-gray-900 font-medium"><?= htmlspecialchars($crumb['label']) ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </div>
    <?php endif; ?>
</header>
