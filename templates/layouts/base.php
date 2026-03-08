<?php
/**
 * Base HTML Layout - EduWrite AI
 * All other layouts extend this base template.
 * Variables: $pageTitle, $content, $bodyClass
 */
if (!function_exists('escapeOutput')) {
    function escapeOutput($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}
$pageTitle = $pageTitle ?? 'EduWrite AI';
$bodyClass = $bodyClass ?? '';
$csrfToken = $csrfToken ?? '';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= escapeOutput($csrfToken) ?>">
    <meta name="description" content="EduWrite AI - AI-powered educational writing platform">
    <title><?= escapeOutput($pageTitle) ?> | EduWrite AI</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                            950: '#1e1b4b',
                        },
                        brand: {
                            light: '#e0e7ff',
                            DEFAULT: '#4f46e5',
                            dark: '#3730a3',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Smooth transitions */
        .transition-smooth { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }

        /* Slide-in animation */
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .animate-slide-in { animation: slideIn 0.3s ease-out; }

        /* Pulse skeleton */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .animate-pulse-slow { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }

        /* Focus ring */
        .focus-ring:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
        }

        /* Backdrop blur fallback */
        .backdrop-blur-sm { backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); }

        [x-cloak] { display: none !important; }
    </style>

    <?php if (!empty($headExtra)): ?>
        <?= $headExtra ?>
    <?php endif; ?>
</head>
<body class="bg-gray-50 antialiased text-gray-900 <?= escapeOutput($bodyClass) ?>">

    <?php include __DIR__ . '/../partials/flash-messages.php'; ?>

    <?php if (!empty($content)): ?>
        <?= $content ?>
    <?php endif; ?>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        // CSRF token helper
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        // Auto-dismiss flash messages
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-auto-dismiss]').forEach(el => {
                const delay = parseInt(el.dataset.autoDismiss) || 5000;
                setTimeout(() => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateX(100%)';
                    setTimeout(() => el.remove(), 300);
                }, delay);
            });
        });
    </script>

    <?php if (!empty($footerExtra)): ?>
        <?= $footerExtra ?>
    <?php endif; ?>
</body>
</html>
