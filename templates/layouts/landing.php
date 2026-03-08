<?php
/**
 * Landing / Public Page Layout - EduWrite AI
 * Variables: $pageTitle, $landingContent
 */
$landingContent = $landingContent ?? '';
ob_start();
?>

<div x-data="{ scrolled: false, mobileNav: false }" @scroll.window="scrolled = (window.scrollY > 20)">
    <!-- Public Navigation -->
    <nav class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
         :class="scrolled ? 'bg-white/95 backdrop-blur-sm shadow-sm' : 'bg-transparent'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-9 h-9 bg-primary-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold" :class="scrolled ? 'text-gray-900' : 'text-white'">EduWrite AI</span>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-sm font-medium transition-colors" :class="scrolled ? 'text-gray-600 hover:text-primary-600' : 'text-white/90 hover:text-white'">Features</a>
                    <a href="#pricing" class="text-sm font-medium transition-colors" :class="scrolled ? 'text-gray-600 hover:text-primary-600' : 'text-white/90 hover:text-white'">Pricing</a>
                    <a href="#about" class="text-sm font-medium transition-colors" :class="scrolled ? 'text-gray-600 hover:text-primary-600' : 'text-white/90 hover:text-white'">About</a>
                    <a href="/login" class="text-sm font-medium transition-colors" :class="scrolled ? 'text-gray-700 hover:text-primary-600' : 'text-white hover:text-white/80'">Log In</a>
                    <a href="/register" class="px-5 py-2 bg-white text-primary-600 text-sm font-semibold rounded-lg shadow-sm hover:bg-gray-50 transition-all duration-200">Sign Up Free</a>
                </div>

                <!-- Mobile menu button -->
                <button @click="mobileNav = !mobileNav" class="md:hidden p-2 rounded-lg" :class="scrolled ? 'text-gray-600' : 'text-white'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileNav" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileNav" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Nav Panel -->
            <div x-show="mobileNav" x-transition class="md:hidden bg-white rounded-b-xl shadow-lg p-4 space-y-3">
                <a href="#features" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">Features</a>
                <a href="#pricing" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">Pricing</a>
                <a href="#about" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">About</a>
                <hr class="my-2">
                <a href="/login" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">Log In</a>
                <a href="/register" class="block px-4 py-2 bg-primary-600 text-white text-center rounded-lg font-medium">Sign Up Free</a>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <?= $landingContent ?>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="col-span-2 md:col-span-1">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v7.5"/>
                            </svg>
                        </div>
                        <span class="text-white font-bold">EduWrite AI</span>
                    </div>
                    <p class="text-sm leading-relaxed">AI-powered writing assistance for education.</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold text-sm mb-3">Product</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#features" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="#pricing" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="/changelog" class="hover:text-white transition-colors">Changelog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold text-sm mb-3">Resources</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/docs" class="hover:text-white transition-colors">Documentation</a></li>
                        <li><a href="/help" class="hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="/blog" class="hover:text-white transition-colors">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold text-sm mb-3">Legal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/privacy" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="/terms" class="hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="/security" class="hover:text-white transition-colors">Security</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-10 pt-6 text-sm text-center">
                &copy; <?= date('Y') ?> EduWrite AI. All rights reserved.
            </div>
        </div>
    </footer>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/base.php';
?>
