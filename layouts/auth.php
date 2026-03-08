<?php
/*
|--------------------------------------------------------------------------
| Auth Layout — LearnAI (Login / Register)
|--------------------------------------------------------------------------
| Centred card layout with brand mark. Extends base.php.
*/
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/includes/helpers.php';

$pageContent = $content ?? '';
$bodyClass   = 'bg-gradient-to-br from-indigo-50 via-white to-sky-50';

ob_start();
?>
<div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">

    <!-- Brand -->
    <a href="<?= url('/') ?>" class="flex items-center gap-3 mb-10 group">
        <span class="flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-600 text-white text-xl shadow-lg shadow-indigo-200 group-hover:scale-105 transition">
            🎓
        </span>
        <span class="text-2xl font-extrabold text-slate-800 tracking-tight"><?= e(config('name')) ?></span>
    </a>

    <!-- Card -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl shadow-slate-200/60 ring-1 ring-slate-900/5 p-8 sm:p-10">
        <?= $pageContent ?>
    </div>

    <!-- Footer -->
    <p class="mt-10 text-xs text-slate-400">
        &copy; <?= date('Y') ?> <?= e(config('name')) ?>. All rights reserved.
    </p>
</div>
<?php
$content = ob_get_clean();

require BASE_PATH . '/layouts/base.php';
