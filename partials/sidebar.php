<?php
/*
|--------------------------------------------------------------------------
| Sidebar Partial — LearnAI
|--------------------------------------------------------------------------
| Role-aware sidebar with collapsible sections and Alpine.js interactivity.
*/
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/includes/helpers.php';

$role = userRole();
?>

<!-- ─── Mobile overlay ──────────────────────────────────────────────── -->
<div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden">
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

    <aside x-show="sidebarOpen"
           x-transition:enter="transition transform duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
           x-transition:leave="transition transform duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
           class="relative flex flex-col w-64 h-full bg-white border-r border-slate-200 shadow-xl">
        <?php require __DIR__ . '/_sidebar_content.php'; ?>
    </aside>
</div>

<!-- ─── Desktop sidebar ─────────────────────────────────────────────── -->
<aside class="hidden lg:flex lg:flex-col lg:fixed lg:inset-y-0 lg:w-64 bg-white border-r border-slate-200 z-30">
    <?php require __DIR__ . '/_sidebar_content.php'; ?>
</aside>
