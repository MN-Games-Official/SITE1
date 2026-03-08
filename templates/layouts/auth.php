<?php
/**
 * Auth Page Layout - EduWrite AI
 * Split design: gradient left panel + form right panel.
 * Variables: $pageTitle, $authContent
 */
$authContent = $authContent ?? '';
ob_start();
?>

<div class="min-h-screen flex">
    <!-- Left Panel: Brand / Illustration -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-600 via-primary-700 to-purple-800 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg">
                <circle cx="400" cy="400" r="300" fill="none" stroke="white" stroke-width="1"/>
                <circle cx="400" cy="400" r="200" fill="none" stroke="white" stroke-width="0.5"/>
                <circle cx="400" cy="400" r="100" fill="none" stroke="white" stroke-width="0.5"/>
            </svg>
        </div>

        <div class="relative z-10 flex flex-col items-center justify-center w-full px-12 text-white">
            <!-- Logo -->
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold tracking-tight">EduWrite AI</span>
            </div>

            <h1 class="text-4xl font-bold mb-4 text-center leading-tight">Empower Learning<br>Through Writing</h1>
            <p class="text-lg text-white/80 text-center max-w-md leading-relaxed">
                AI-powered writing assistance designed for education. Help students grow as writers while maintaining academic integrity.
            </p>

            <div class="mt-12 grid grid-cols-3 gap-6 text-center">
                <div class="p-4">
                    <div class="text-3xl font-bold">10K+</div>
                    <div class="text-sm text-white/70 mt-1">Students</div>
                </div>
                <div class="p-4">
                    <div class="text-3xl font-bold">500+</div>
                    <div class="text-sm text-white/70 mt-1">Schools</div>
                </div>
                <div class="p-4">
                    <div class="text-3xl font-bold">1M+</div>
                    <div class="text-sm text-white/70 mt-1">Documents</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel: Form -->
    <div class="flex-1 flex items-center justify-center p-8 bg-gray-50">
        <div class="w-full max-w-md">
            <!-- Mobile Logo -->
            <div class="lg:hidden flex items-center justify-center space-x-2 mb-8">
                <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-900">EduWrite AI</span>
            </div>

            <div class="bg-white shadow-xl rounded-2xl p-8">
                <?= $authContent ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/base.php';
?>
