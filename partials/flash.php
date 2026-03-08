<?php
/*
|--------------------------------------------------------------------------
| Flash Messages Partial — LearnAI
|--------------------------------------------------------------------------
| Renders flash messages with auto-dismiss. Uses Alpine.js for animation.
*/
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/includes/helpers.php';

$flashes = getFlashes();
if (empty($flashes)) return;
?>

<div class="fixed top-4 right-4 z-[100] flex flex-col gap-3 max-w-sm w-full pointer-events-none" aria-live="polite">
    <?php foreach ($flashes as $flash): ?>
        <?php
        $type = $flash['type'];
        $message = $flash['message'];

        $styles = match ($type) {
            'success' => [
                'bg'   => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                'icon' => '<svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>',
            ],
            'error' => [
                'bg'   => 'bg-rose-50 border-rose-200 text-rose-800',
                'icon' => '<svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>',
            ],
            'warning' => [
                'bg'   => 'bg-amber-50 border-amber-200 text-amber-800',
                'icon' => '<svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>',
            ],
            default => [
                'bg'   => 'bg-sky-50 border-sky-200 text-sky-800',
                'icon' => '<svg class="w-5 h-5 text-sky-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>',
            ],
        };
        ?>
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 5000)"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-8"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-8"
            class="pointer-events-auto flex items-start gap-3 rounded-xl border px-4 py-3 shadow-lg backdrop-blur-sm <?= $styles['bg'] ?>"
            role="alert"
        >
            <?= $styles['icon'] ?>
            <p class="flex-1 text-sm font-medium"><?= e($message) ?></p>
            <button @click="show = false" class="shrink-0 p-0.5 rounded-lg hover:bg-black/5 transition" aria-label="Dismiss">
                <svg class="w-4 h-4 opacity-60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>
    <?php endforeach; ?>
</div>
