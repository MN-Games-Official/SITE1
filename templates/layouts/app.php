<?php
/**
 * Authenticated App Layout - EduWrite AI
 * Extends base layout. Includes sidebar + topnav.
 * Variables: $pageTitle, $currentPage, $userRole, $user, $breadcrumbs, $notifications
 */
$currentPage = $currentPage ?? 'dashboard';
$userRole = $userRole ?? 'student';
$user = $user ?? ['name' => 'User', 'email' => '', 'avatar' => ''];
$notifications = $notifications ?? [];
$breadcrumbs = $breadcrumbs ?? [];
$unreadCount = $unreadCount ?? 0;

ob_start();
?>

<div x-data="{ sidebarOpen: true, mobileMenuOpen: false }" class="min-h-screen bg-gray-50">

    <!-- Top Navigation -->
    <?php include __DIR__ . '/../partials/topnav.php'; ?>

    <!-- Sidebar -->
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="transition-all duration-200"
          :class="sidebarOpen ? 'ml-64' : 'ml-0 lg:ml-64'"
          style="padding-top: 4rem;">

        <div class="p-6 max-w-7xl mx-auto">
            <!-- Breadcrumbs -->
            <?php if (!empty($breadcrumbs)): ?>
            <nav class="mb-4 text-sm" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <?php foreach ($breadcrumbs as $i => $crumb): ?>
                        <?php if ($i > 0): ?>
                            <li>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </li>
                        <?php endif; ?>
                        <li>
                            <?php if (!empty($crumb['url'])): ?>
                                <a href="<?= escapeOutput($crumb['url']) ?>" class="text-gray-500 hover:text-primary-600 transition-colors duration-200"><?= escapeOutput($crumb['label']) ?></a>
                            <?php else: ?>
                                <span class="text-gray-700 font-medium"><?= escapeOutput($crumb['label']) ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </nav>
            <?php endif; ?>

            <!-- Page Content -->
            <?php if (!empty($pageContent)): ?>
                <?= $pageContent ?>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <footer class="mt-auto border-t border-gray-200 bg-white px-6 py-4">
            <div class="max-w-7xl mx-auto flex items-center justify-between text-sm text-gray-500">
                <p>&copy; <?= date('Y') ?> EduWrite AI. All rights reserved.</p>
                <div class="flex items-center space-x-4">
                    <a href="/help" class="hover:text-primary-600 transition-colors">Help</a>
                    <a href="/privacy" class="hover:text-primary-600 transition-colors">Privacy</a>
                    <a href="/terms" class="hover:text-primary-600 transition-colors">Terms</a>
                </div>
            </div>
        </footer>
    </main>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/base.php';
?>
