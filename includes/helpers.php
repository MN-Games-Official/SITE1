<?php

/*
|--------------------------------------------------------------------------
| Helper Functions — LearnAI Education Platform
|--------------------------------------------------------------------------
|
| Pure utility functions used across every layout, partial and page.
| No side-effects on require — safe to include multiple times.
|
*/

// Guard against double-include
if (defined('HELPERS_LOADED')) {
    return;
}
define('HELPERS_LOADED', true);

/* ── configuration ────────────────────────────────────────────────────── */

function config(string $key = null, $default = null)
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require __DIR__ . '/../config/app.php';
    }
    if ($key === null) {
        return $cfg;
    }
    $segments = explode('.', $key);
    $value    = $cfg;
    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }
    return $value;
}

/* ── asset / URL helpers ──────────────────────────────────────────────── */

function asset(string $path): string
{
    $base = rtrim(config('base_url'), '/');
    return $base . '/' . ltrim($path, '/');
}

function css(string $file): string
{
    return asset('css/' . ltrim($file, '/'));
}

function js(string $file): string
{
    return asset('js/' . ltrim($file, '/'));
}

function img(string $file): string
{
    return asset('images/' . ltrim($file, '/'));
}

function url(string $path = '/'): string
{
    $base = rtrim(config('base_url'), '/');
    return $base . '/' . ltrim($path, '/');
}

function currentPath(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return '/' . trim($uri, '/');
}

function isActive(string $path, bool $exact = false): bool
{
    $current = currentPath();
    if ($exact) {
        return $current === '/' . ltrim($path, '/');
    }
    return str_starts_with($current, '/' . ltrim($path, '/'));
}

function activeClass(string $path, string $active = 'bg-indigo-50 text-indigo-700 font-semibold', string $inactive = 'text-slate-600 hover:bg-slate-50 hover:text-slate-900', bool $exact = false): string
{
    return isActive($path, $exact) ? $active : $inactive;
}

/* ── formatting helpers ───────────────────────────────────────────────── */

function formatDate(string $date, string $format = 'M j, Y'): string
{
    $ts = strtotime($date);
    return $ts ? date($format, $ts) : $date;
}

function formatDateTime(string $date): string
{
    return formatDate($date, 'M j, Y g:i A');
}

function timeAgo(string $datetime): string
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60)    return 'just now';
    if ($diff < 3600)  return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return formatDate($datetime);
}

function truncate(string $text, int $length = 100, string $suffix = '…'): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name));
    if (count($parts) >= 2) {
        return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1));
    }
    return mb_strtoupper(mb_substr($name, 0, 2));
}

function formatFileSize(int $bytes): string
{
    if ($bytes >= 1073741824) return round($bytes / 1073741824, 1) . ' GB';
    if ($bytes >= 1048576)    return round($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024)       return round($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
}

function formatNumber(int $number): string
{
    if ($number >= 1000000) return round($number / 1000000, 1) . 'M';
    if ($number >= 1000)    return round($number / 1000, 1) . 'K';
    return (string) $number;
}

/* ── role & status badges ─────────────────────────────────────────────── */

function roleColor(string $role): string
{
    return match ($role) {
        'admin'   => 'purple',
        'teacher' => 'emerald',
        'student' => 'blue',
        default   => 'slate',
    };
}

function roleBadge(string $role): string
{
    $color = roleColor($role);
    $label = ucfirst($role);
    return '<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-' . e($color) . '-100 text-' . e($color) . '-800">' . e($label) . '</span>';
}

function statusBadge(string $status): string
{
    $map = [
        'active'    => ['emerald', 'Active'],
        'inactive'  => ['slate',   'Inactive'],
        'pending'   => ['amber',   'Pending'],
        'submitted' => ['blue',    'Submitted'],
        'graded'    => ['indigo',  'Graded'],
        'late'      => ['rose',    'Late'],
        'flagged'   => ['red',     'Flagged'],
        'archived'  => ['gray',    'Archived'],
        'draft'     => ['slate',   'Draft'],
        'published' => ['emerald', 'Published'],
    ];
    [$color, $label] = $map[$status] ?? ['slate', ucfirst($status)];
    return '<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-' . e($color) . '-100 text-' . e($color) . '-800">' . e($label) . '</span>';
}

/* ── security helpers ─────────────────────────────────────────────────── */

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(string $token): bool
{
    return hash_equals(csrf_token(), $token);
}

/* ── session / auth helpers ───────────────────────────────────────────── */

function session_get(string $key, $default = null)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION[$key] ?? $default;
}

function session_set(string $key, $value): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION[$key] = $value;
}

function session_forget(string $key): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    unset($_SESSION[$key]);
}

function currentUser(): ?array
{
    return session_get('user');
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function userRole(): string
{
    return currentUser()['role'] ?? 'student';
}

function userName(): string
{
    return currentUser()['name'] ?? 'Guest';
}

function userEmail(): string
{
    return currentUser()['email'] ?? '';
}

function userAvatar(): string
{
    $user = currentUser();
    return $user['avatar'] ?? '';
}

function hasRole(string $role): bool
{
    return userRole() === $role;
}

function isAdmin(): bool
{
    return hasRole('admin');
}

function isTeacher(): bool
{
    return hasRole('teacher') || isAdmin();
}

function isStudent(): bool
{
    return hasRole('student');
}

/* ── flash messages ───────────────────────────────────────────────────── */

function flash(string $type, string $message): void
{
    $flashes   = session_get('_flash', []);
    $flashes[] = ['type' => $type, 'message' => $message];
    session_set('_flash', $flashes);
}

function getFlashes(): array
{
    $flashes = session_get('_flash', []);
    session_forget('_flash');
    return $flashes;
}

/* ── feature flags ────────────────────────────────────────────────────── */

function featureEnabled(string $feature): bool
{
    return (bool) config("features.{$feature}", false);
}

/* ── breadcrumbs ──────────────────────────────────────────────────────── */

function breadcrumbs(): array
{
    $path     = trim(currentPath(), '/');
    $segments = $path !== '' ? explode('/', $path) : [];
    $crumbs   = [['label' => 'Home', 'url' => url('/')]];
    $build    = '';
    foreach ($segments as $segment) {
        $build  .= '/' . $segment;
        $crumbs[] = [
            'label' => ucfirst(str_replace(['-', '_'], ' ', $segment)),
            'url'   => url($build),
        ];
    }
    return $crumbs;
}
