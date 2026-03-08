<?php
/*
|--------------------------------------------------------------------------
| Top Navigation Partial — LearnAI
|--------------------------------------------------------------------------
| Search bar, breadcrumbs, notifications dropdown, user menu.
*/
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/includes/helpers.php';

$crumbs = breadcrumbs();
?>

<header class="sticky top-0 z-20 bg-white/80 backdrop-blur-lg border-b border-slate-200/60">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8 gap-4">

        <!-- Left: mobile toggle + breadcrumbs -->
        <div class="flex items-center gap-3 min-w-0">
            <!-- Mobile sidebar toggle -->
            <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 rounded-lg hover:bg-slate-100 text-slate-500 transition" aria-label="Open sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </button>

            <!-- Breadcrumbs (hidden on very small screens) -->
            <nav class="hidden sm:flex items-center gap-1.5 text-sm text-slate-500 truncate" aria-label="Breadcrumb">
                <?php foreach ($crumbs as $i => $crumb): ?>
                    <?php if ($i > 0): ?>
                        <svg class="w-3.5 h-3.5 text-slate-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    <?php endif; ?>
                    <?php if ($i === count($crumbs) - 1): ?>
                        <span class="font-medium text-slate-800 truncate"><?= e($crumb['label']) ?></span>
                    <?php else: ?>
                        <a href="<?= e($crumb['url']) ?>" class="hover:text-indigo-600 transition truncate"><?= e($crumb['label']) ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
        </div>

        <!-- Center: search -->
        <div class="hidden md:flex flex-1 max-w-md mx-auto" x-data="{ focused: false }">
            <div class="relative w-full">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                <input
                    type="search"
                    placeholder="Search courses, documents…"
                    class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-slate-200 bg-slate-50 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 focus:bg-white transition"
                    @focus="focused = true" @blur="focused = false"
                >
                <kbd class="absolute right-3 top-1/2 -translate-y-1/2 hidden lg:inline-flex items-center rounded border border-slate-200 bg-white px-1.5 py-0.5 text-[10px] font-medium text-slate-400">⌘K</kbd>
            </div>
        </div>

        <!-- Right: actions -->
        <div class="flex items-center gap-2">

            <!-- Mobile search toggle -->
            <button class="md:hidden p-2 rounded-lg hover:bg-slate-100 text-slate-500 transition" aria-label="Search">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            </button>

            <!-- Notifications -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="relative p-2 rounded-lg hover:bg-slate-100 text-slate-500 transition" aria-label="Notifications">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                    <!-- Badge -->
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
                </button>

                <!-- Notifications dropdown -->
                <div x-show="open" x-cloak @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"
                     class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl shadow-slate-200/60 ring-1 ring-slate-900/5 overflow-hidden">

                    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-800">Notifications</h3>
                        <button class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">Mark all read</button>
                    </div>

                    <ul class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                        <li class="px-4 py-3 hover:bg-slate-50 transition cursor-pointer bg-indigo-50/40">
                            <div class="flex gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 text-sm shrink-0">📝</span>
                                <div class="min-w-0">
                                    <p class="text-sm text-slate-800 font-medium">New assignment posted</p>
                                    <p class="text-xs text-slate-500 mt-0.5">CS 101 — Due in 3 days</p>
                                    <p class="text-[11px] text-slate-400 mt-1">2 hours ago</p>
                                </div>
                            </div>
                        </li>
                        <li class="px-4 py-3 hover:bg-slate-50 transition cursor-pointer bg-indigo-50/40">
                            <div class="flex gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 text-sm shrink-0">✅</span>
                                <div class="min-w-0">
                                    <p class="text-sm text-slate-800 font-medium">Essay graded: A-</p>
                                    <p class="text-xs text-slate-500 mt-0.5">English Literature</p>
                                    <p class="text-[11px] text-slate-400 mt-1">5 hours ago</p>
                                </div>
                            </div>
                        </li>
                        <li class="px-4 py-3 hover:bg-slate-50 transition cursor-pointer">
                            <div class="flex gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-600 text-sm shrink-0">⚠️</span>
                                <div class="min-w-0">
                                    <p class="text-sm text-slate-800 font-medium">AI usage policy updated</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Please review the changes</p>
                                    <p class="text-[11px] text-slate-400 mt-1">Yesterday</p>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <div class="border-t border-slate-100 px-4 py-2.5">
                        <a href="#" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition">View all notifications →</a>
                    </div>
                </div>
            </div>

            <!-- User menu -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 transition" aria-label="User menu">
                    <?php if (userAvatar()): ?>
                        <img src="<?= e(userAvatar()) ?>" alt="" class="w-8 h-8 rounded-full object-cover ring-2 ring-white">
                    <?php else: ?>
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold ring-2 ring-white"><?= e(initials(userName())) ?></span>
                    <?php endif; ?>
                    <span class="hidden sm:block text-sm font-medium text-slate-700 max-w-[120px] truncate"><?= e(userName()) ?></span>
                    <svg class="w-4 h-4 text-slate-400 hidden sm:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </button>

                <!-- User dropdown -->
                <div x-show="open" x-cloak @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"
                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl shadow-slate-200/60 ring-1 ring-slate-900/5 overflow-hidden">

                    <div class="px-4 py-3 border-b border-slate-100">
                        <p class="text-sm font-semibold text-slate-800"><?= e(userName()) ?></p>
                        <p class="text-xs text-slate-500 mt-0.5"><?= e(userEmail()) ?></p>
                        <div class="mt-1.5"><?= roleBadge(userRole()) ?></div>
                    </div>

                    <ul class="py-1">
                        <li>
                            <a href="<?= url('/profile') ?>" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                My Profile
                            </a>
                        </li>
                        <li>
                            <a href="<?= url('/settings') ?>" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                Settings
                            </a>
                        </li>
                        <li>
                            <a href="<?= url('/analytics') ?>" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75Z"/></svg>
                                Analytics
                            </a>
                        </li>
                    </ul>

                    <div class="border-t border-slate-100 py-1">
                        <a href="<?= url('/login') ?>" class="flex items-center gap-2.5 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                            Sign out
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>
