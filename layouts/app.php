<?php
/*
|--------------------------------------------------------------------------
| App Layout — LearnAI (Authenticated Pages)
|--------------------------------------------------------------------------
| Wraps $content with sidebar + top navigation, then delegates to base.php.
*/
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/includes/helpers.php';

// $content is already set by index.php — wrap it in the app chrome
$pageContent = $content ?? '';

ob_start();
?>
<div x-data="{ sidebarOpen: false }" class="min-h-screen flex">

    <!-- Sidebar -->
    <?php require BASE_PATH . '/partials/sidebar.php'; ?>

    <!-- Main column -->
    <div class="flex-1 flex flex-col min-w-0 lg:pl-64">

        <!-- Top nav -->
        <?php require BASE_PATH . '/partials/topnav.php'; ?>

        <!-- Page content -->
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8">
            <?= $pageContent ?>
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-200 bg-white px-6 py-4">
            <p class="text-xs text-slate-400 text-center">
                &copy; <?= date('Y') ?> <?= e(config('name')) ?>. All rights reserved. &middot; v<?= e(config('version')) ?>
            </p>
        </footer>
    </div>
</div>
<?php
$content = ob_get_clean();

require BASE_PATH . '/layouts/base.php';
