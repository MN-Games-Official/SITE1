/*
|--------------------------------------------------------------------------
| LearnAI — Client-side JavaScript
|--------------------------------------------------------------------------
*/

document.addEventListener('DOMContentLoaded', function () {

    /* ── Keyboard shortcut: ⌘K / Ctrl+K to focus search ────────────── */
    document.addEventListener('keydown', function (e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            var search = document.querySelector('input[type="search"]');
            if (search) search.focus();
        }
    });

    /* ── Auto-hide flash messages after 6s (fallback if Alpine fails) ─ */
    document.querySelectorAll('[role="alert"]').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity 0.3s ease';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 300);
        }, 6000);
    });

});
