<?php

/*
|--------------------------------------------------------------------------
| LearnAI — Front Controller
|--------------------------------------------------------------------------
|
| Every web request is funnelled through this file by the .htaccess
| rewrite rules. It bootstraps helpers, resolves the requested page,
| and renders it inside the correct layout.
|
*/

// ── bootstrap ────────────────────────────────────────────────────────────
define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/includes/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Demo user (remove in production) ─────────────────────────────────────
// Seed a demo user so the UI is populated even without a real auth backend.
if (!isLoggedIn()) {
    $_SESSION['user'] = [
        'id'     => 1,
        'name'   => 'Alex Johnson',
        'email'  => 'alex@learnai.app',
        'role'   => 'student',
        'avatar' => '',
    ];
}

// ── resolve route ────────────────────────────────────────────────────────
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path       = '/' . trim($requestUri, '/');

// Static-file map — explicit page → file + layout bindings
$routes = [
    // Public / marketing
    '/'              => ['page' => 'home',               'layout' => 'landing'],
    '/login'         => ['page' => 'login',              'layout' => 'auth'],
    '/register'      => ['page' => 'register',           'layout' => 'auth'],
    '/forgot'        => ['page' => 'forgot',             'layout' => 'auth'],
    '/logout'        => ['page' => 'logout',             'layout' => 'auth'],

    // Student
    '/dashboard'     => ['page' => 'dashboard',          'layout' => 'app'],
    '/documents'     => ['page' => 'documents',          'layout' => 'app'],
    '/assignments'   => ['page' => 'assignments',        'layout' => 'app'],
    '/classes'       => ['page' => 'classes',            'layout' => 'app'],
    '/ai-assistant'  => ['page' => 'ai-assistant',       'layout' => 'app'],
    '/analytics'     => ['page' => 'analytics',          'layout' => 'app'],
    '/profile'       => ['page' => 'profile',            'layout' => 'app'],
    '/settings'      => ['page' => 'settings',           'layout' => 'app'],

    // Teacher
    '/teacher/dashboard'   => ['page' => 'teacher/dashboard',   'layout' => 'app'],
    '/teacher/classes'     => ['page' => 'teacher/classes',     'layout' => 'app'],
    '/teacher/assignments' => ['page' => 'teacher/assignments', 'layout' => 'app'],
    '/teacher/students'    => ['page' => 'teacher/students',    'layout' => 'app'],
    '/teacher/flags'       => ['page' => 'teacher/flags',       'layout' => 'app'],

    // Admin
    '/admin/dashboard' => ['page' => 'admin/dashboard', 'layout' => 'app'],
    '/admin/users'     => ['page' => 'admin/users',     'layout' => 'app'],
    '/admin/policies'  => ['page' => 'admin/policies',  'layout' => 'app'],
    '/admin/settings'  => ['page' => 'admin/settings',  'layout' => 'app'],
    '/admin/audit-log' => ['page' => 'admin/audit-log', 'layout' => 'app'],
];

// Match exact route or fall back to 404
$route  = $routes[$path] ?? null;
$layout = $route['layout'] ?? 'app';

// ── render page content ──────────────────────────────────────────────────
if ($route) {
    $pageFile = BASE_PATH . '/pages/' . $route['page'] . '.php';
    if (file_exists($pageFile)) {
        ob_start();
        require $pageFile;
        $content = ob_get_clean();
    } else {
        // Route is registered but the page file hasn't been created yet
        ob_start();
        $pageTitle = ucfirst(basename($route['page']));
        echo '<div class="flex flex-col items-center justify-center py-24 text-center">';
        echo '  <div class="rounded-full bg-indigo-100 p-6 mb-6"><svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg></div>';
        echo '  <h2 class="text-2xl font-bold text-slate-800 mb-2">' . e($pageTitle) . '</h2>';
        echo '  <p class="text-slate-500 max-w-md">This page is under construction. Check back soon!</p>';
        echo '</div>';
        $content = ob_get_clean();
    }
} else {
    // 404
    http_response_code(404);
    $layout = 'app';
    ob_start();
    echo '<div class="flex flex-col items-center justify-center py-24 text-center">';
    echo '  <p class="text-7xl font-extrabold text-indigo-600 mb-4">404</p>';
    echo '  <h2 class="text-2xl font-bold text-slate-800 mb-2">Page not found</h2>';
    echo '  <p class="text-slate-500 mb-8">The page you are looking for doesn\'t exist or has been moved.</p>';
    echo '  <a href="' . url('/dashboard') . '" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition">← Back to Dashboard</a>';
    echo '</div>';
    $content = ob_get_clean();
}

// ── render layout ────────────────────────────────────────────────────────
$layoutFile = BASE_PATH . '/layouts/' . $layout . '.php';
if (file_exists($layoutFile)) {
    require $layoutFile;
} else {
    // Absolute fallback — just output the content
    echo $content;
}
