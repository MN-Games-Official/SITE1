<?php
/*
|--------------------------------------------------------------------------
| Landing Layout — LearnAI (Public / Marketing Pages)
|--------------------------------------------------------------------------
| Full-width layout with marketing header and footer. Extends base.php.
*/
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/includes/helpers.php';

$pageContent = $content ?? '';

ob_start();
?>

<!-- ─── Landing Header ──────────────────────────────────────────────── -->
<header class="sticky top-0 z-50 bg-white/80 backdrop-blur-lg border-b border-slate-200/60" x-data="{ mobileMenu: false }">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">
        <!-- Brand -->
        <a href="<?= url('/') ?>" class="flex items-center gap-2.5 group">
            <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-600 text-white text-base shadow group-hover:scale-105 transition">🎓</span>
            <span class="text-xl font-extrabold text-slate-800 tracking-tight"><?= e(config('name')) ?></span>
        </a>

        <!-- Desktop nav -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
            <a href="#features" class="hover:text-indigo-600 transition">Features</a>
            <a href="#pricing" class="hover:text-indigo-600 transition">Pricing</a>
            <a href="#testimonials" class="hover:text-indigo-600 transition">Testimonials</a>
            <a href="#faq" class="hover:text-indigo-600 transition">FAQ</a>
        </nav>

        <!-- CTA -->
        <div class="hidden md:flex items-center gap-3">
            <a href="<?= url('/login') ?>" class="text-sm font-semibold text-slate-700 hover:text-indigo-600 transition">Sign in</a>
            <a href="<?= url('/register') ?>" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition">
                Get Started Free
            </a>
        </div>

        <!-- Mobile toggle -->
        <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg hover:bg-slate-100 transition" aria-label="Toggle menu">
            <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                <path x-show="mobileMenu" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobileMenu" x-cloak x-transition class="md:hidden border-t border-slate-200 bg-white px-4 py-4 space-y-2">
        <a href="#features" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Features</a>
        <a href="#pricing" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Pricing</a>
        <a href="#testimonials" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Testimonials</a>
        <a href="#faq" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">FAQ</a>
        <hr class="my-2 border-slate-200">
        <a href="<?= url('/login') ?>" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Sign in</a>
        <a href="<?= url('/register') ?>" class="block rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white text-center hover:bg-indigo-700">Get Started Free</a>
    </div>
</header>

<!-- ─── Page Content ────────────────────────────────────────────────── -->
<main>
    <?= $pageContent ?>
</main>

<!-- ─── Footer ──────────────────────────────────────────────────────── -->
<footer class="bg-slate-900 text-slate-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-10">
            <!-- Brand column -->
            <div class="col-span-2 md:col-span-1">
                <a href="<?= url('/') ?>" class="flex items-center gap-2.5 mb-4">
                    <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-600 text-white text-base">🎓</span>
                    <span class="text-lg font-extrabold text-white tracking-tight"><?= e(config('name')) ?></span>
                </a>
                <p class="text-sm leading-relaxed">
                    AI-powered tools that help students learn smarter and educators teach better.
                </p>
            </div>

            <!-- Product -->
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-4">Product</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#features" class="hover:text-white transition">Features</a></li>
                    <li><a href="#pricing" class="hover:text-white transition">Pricing</a></li>
                    <li><a href="#" class="hover:text-white transition">Integrations</a></li>
                    <li><a href="#" class="hover:text-white transition">Changelog</a></li>
                </ul>
            </div>

            <!-- Company -->
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-4">Company</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white transition">About</a></li>
                    <li><a href="#" class="hover:text-white transition">Blog</a></li>
                    <li><a href="#" class="hover:text-white transition">Careers</a></li>
                    <li><a href="#" class="hover:text-white transition">Contact</a></li>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-4">Legal</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                    <li><a href="#" class="hover:text-white transition">Cookie Policy</a></li>
                    <li><a href="#" class="hover:text-white transition">FERPA Compliance</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-12 pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs">&copy; <?= date('Y') ?> <?= e(config('name')) ?>. All rights reserved.</p>
            <div class="flex items-center gap-5">
                <a href="#" class="hover:text-white transition" aria-label="Twitter">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.25c7.55 0 11.68-6.25 11.68-11.67 0-.18 0-.35-.01-.53A8.35 8.35 0 0 0 22 5.92a8.19 8.19 0 0 1-2.36.65 4.12 4.12 0 0 0 1.8-2.27 8.22 8.22 0 0 1-2.61 1 4.1 4.1 0 0 0-7 3.74A11.65 11.65 0 0 1 3.39 4.62a4.1 4.1 0 0 0 1.27 5.48A4.07 4.07 0 0 1 2.8 9.6v.05a4.1 4.1 0 0 0 3.29 4.02 4.1 4.1 0 0 1-1.85.07 4.11 4.11 0 0 0 3.83 2.85A8.23 8.23 0 0 1 2 18.41a11.62 11.62 0 0 0 6.29 1.84"/></svg>
                </a>
                <a href="#" class="hover:text-white transition" aria-label="GitHub">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12c0 4.42 2.87 8.17 6.84 9.5.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34-.46-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.87 1.52 2.34 1.07 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.93 0-1.09.39-1.98 1.03-2.68-.1-.25-.45-1.27.1-2.64 0 0 .84-.27 2.75 1.02A9.56 9.56 0 0 1 12 6.8c.85.004 1.71.115 2.51.34 1.91-1.29 2.75-1.02 2.75-1.02.55 1.37.2 2.39.1 2.64.64.7 1.03 1.59 1.03 2.68 0 3.84-2.34 4.68-4.57 4.93.36.31.68.92.68 1.85v2.74c0 .27.16.59.67.5A10.02 10.02 0 0 0 22 12c0-5.52-4.48-10-10-10z"/></svg>
                </a>
                <a href="#" class="hover:text-white transition" aria-label="LinkedIn">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                </a>
            </div>
        </div>
    </div>
</footer>

<?php
$content = ob_get_clean();

require BASE_PATH . '/layouts/base.php';
