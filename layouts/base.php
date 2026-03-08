<?php
/*
|--------------------------------------------------------------------------
| Base Layout — LearnAI
|--------------------------------------------------------------------------
| Root HTML shell shared by every other layout. Layouts set $content
| before requiring this file.
*/
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/includes/helpers.php';

$pageTitle   = isset($pageTitle) ? e($pageTitle) . ' — ' . e(config('name')) : e(config('name')) . ' — ' . e(config('tagline'));
$bodyClass   = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e(config('tagline')) ?>">
    <meta name="theme-color" content="#4f46e5">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">

    <title><?= $pageTitle ?></title>

    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>

    <!-- Google Fonts — Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= css('app.css') ?>">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        /* custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased <?= e($bodyClass) ?>">

    <?php require BASE_PATH . '/partials/flash.php'; ?>

    <?= $content ?? '' ?>

    <!-- Custom JS -->
    <script src="<?= js('app.js') ?>"></script>
</body>
</html>
