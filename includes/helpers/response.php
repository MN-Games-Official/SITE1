<?php
/**
 * EduWrite AI - HTTP Response Helper Functions
 * JSON responses, redirects, flash messages, page rendering, and cache headers.
 */

require_once __DIR__ . '/../../config/app.php';

/**
 * Send a JSON response with the given status code.
 *
 * @param mixed $data       Data to encode as JSON
 * @param int   $statusCode HTTP status code
 */
function jsonResponse(mixed $data, int $statusCode = 200): void
{
    if (!headers_sent()) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    exit;
}

/**
 * Send an error JSON response.
 *
 * @param string     $message    Error message
 * @param int        $statusCode HTTP status code
 * @param array|null $errors     Detailed field-level errors
 */
function jsonError(string $message, int $statusCode = 400, ?array $errors = null): void
{
    $payload = [
        'success' => false,
        'message' => $message,
    ];

    if ($errors !== null) {
        $payload['errors'] = $errors;
    }

    jsonResponse($payload, $statusCode);
}

/**
 * Send a success JSON response.
 *
 * @param mixed       $data    Response data
 * @param string|null $message Optional success message
 */
function jsonSuccess(mixed $data = null, ?string $message = null): void
{
    $payload = ['success' => true];

    if ($message !== null) {
        $payload['message'] = $message;
    }

    if ($data !== null) {
        $payload['data'] = $data;
    }

    jsonResponse($payload, 200);
}

/**
 * Redirect the client to a different URL.
 *
 * @param string $url        Target URL
 * @param int    $statusCode HTTP redirect status (302 default)
 */
function redirect(string $url, int $statusCode = 302): void
{
    if (!headers_sent()) {
        header('Location: ' . $url, true, $statusCode);
    }
    exit;
}

/**
 * Redirect the client back to the referring page.
 * Falls back to the application root if no referrer is set.
 */
function redirectBack(): void
{
    $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL;
    redirect($referer);
}

/**
 * Redirect to a URL with a flash message stored in the session.
 *
 * @param string $url     Target URL
 * @param string $message Flash message content
 * @param string $type    Message type: success, error, warning, info
 */
function redirectWithMessage(string $url, string $message, string $type = 'info'): void
{
    setFlashMessage($message, $type);
    redirect($url);
}

/**
 * Store a flash message in the session.
 *
 * @param string $message Message content
 * @param string $type    Message type: success, error, warning, info
 */
function setFlashMessage(string $message, string $type = 'info'): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        if (php_sapi_name() !== 'cli') {
            session_start();
        } else {
            return;
        }
    }

    $_SESSION['_flash_message'] = [
        'message' => $message,
        'type'    => $type,
        'time'    => time(),
    ];
}

/**
 * Retrieve and clear the current flash message from the session.
 *
 * @return array|null Array with 'message' and 'type' keys, or null
 */
function getFlashMessage(): ?array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        if (php_sapi_name() !== 'cli') {
            session_start();
        } else {
            return null;
        }
    }

    if (!isset($_SESSION['_flash_message'])) {
        return null;
    }

    $flash = $_SESSION['_flash_message'];
    unset($_SESSION['_flash_message']);

    // Discard stale flash messages (older than 5 minutes)
    if (isset($flash['time']) && time() - $flash['time'] > 300) {
        return null;
    }

    return $flash;
}

/**
 * Render a page template with the given data.
 * Variables in the $data array are extracted into the template's scope.
 *
 * @param string $template Relative path to the template file (from APP_ROOT/templates/)
 * @param array  $data     Variables to pass to the template
 */
function renderPage(string $template, array $data = []): void
{
    $templatePath = APP_ROOT . '/templates/' . ltrim($template, '/');

    if (!file_exists($templatePath)) {
        render500('Template not found: ' . $template);
        return;
    }

    // Make common data available to all templates
    $data['app_name']      = APP_NAME;
    $data['app_url']       = APP_URL;
    $data['app_version']   = APP_VERSION;
    $data['flash_message'] = getFlashMessage();

    if (session_status() === PHP_SESSION_ACTIVE) {
        $data['csrf_token'] = $_SESSION['csrf_token'] ?? '';
        $data['current_user_id']   = $_SESSION['user_id'] ?? null;
        $data['current_user_role'] = $_SESSION['role'] ?? null;
    }

    extract($data, EXTR_SKIP);

    include $templatePath;
}

/**
 * Render a 404 Not Found page.
 *
 * @param string $message Optional custom message
 */
function render404(string $message = 'The page you are looking for could not be found.'): void
{
    if (!headers_sent()) {
        http_response_code(404);
    }

    $errorFile = APP_ROOT . '/templates/errors/404.php';

    if (file_exists($errorFile)) {
        $error_message = $message;
        include $errorFile;
    } else {
        echo renderErrorFallback(404, 'Not Found', $message);
    }

    exit;
}

/**
 * Render a 403 Forbidden page.
 *
 * @param string $message Optional custom message
 */
function render403(string $message = 'You do not have permission to access this resource.'): void
{
    if (!headers_sent()) {
        http_response_code(403);
    }

    $errorFile = APP_ROOT . '/templates/errors/403.php';

    if (file_exists($errorFile)) {
        $error_message = $message;
        include $errorFile;
    } else {
        echo renderErrorFallback(403, 'Forbidden', $message);
    }

    exit;
}

/**
 * Render a 500 Internal Server Error page.
 *
 * @param string $message Optional custom message
 */
function render500(string $message = 'An internal server error occurred. Please try again later.'): void
{
    if (!headers_sent()) {
        http_response_code(500);
    }

    // In debug mode, show the real message; otherwise, generic
    $displayMessage = APP_DEBUG ? $message : 'An internal server error occurred. Please try again later.';

    $errorFile = APP_ROOT . '/templates/errors/500.php';

    if (file_exists($errorFile)) {
        $error_message = $displayMessage;
        include $errorFile;
    } else {
        echo renderErrorFallback(500, 'Server Error', $displayMessage);
    }

    exit;
}

/**
 * Render a maintenance mode page.
 */
function renderMaintenancePage(): void
{
    if (!headers_sent()) {
        http_response_code(503);
        header('Retry-After: 3600');
    }

    $maintenanceFile = APP_ROOT . '/templates/errors/maintenance.php';

    if (file_exists($maintenanceFile)) {
        include $maintenanceFile;
    } else {
        echo renderErrorFallback(503, 'Maintenance', APP_NAME . ' is currently undergoing maintenance. Please check back shortly.');
    }

    exit;
}

/**
 * Send a file as a downloadable response.
 *
 * @param string      $path        Absolute file path
 * @param string|null $filename    Download filename (defaults to basename of path)
 * @param string|null $contentType MIME type (auto-detected if null)
 */
function sendFile(string $path, ?string $filename = null, ?string $contentType = null): void
{
    if (!file_exists($path) || !is_readable($path)) {
        render404('The requested file could not be found.');
        return;
    }

    // Prevent directory traversal
    $realPath = realpath($path);
    $storageBase = realpath(APP_ROOT . '/storage');
    if ($realPath === false || ($storageBase !== false && !str_starts_with($realPath, $storageBase))) {
        render403('Access denied.');
        return;
    }

    $filename    = $filename ?? basename($path);
    $contentType = $contentType ?? mime_content_type($path) ?: 'application/octet-stream';
    $fileSize    = filesize($path);

    if (!headers_sent()) {
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . addslashes($filename) . '"');
        header('Content-Length: ' . $fileSize);
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: no-store');
        header('Pragma: no-cache');
    }

    readfile($path);
    exit;
}

/**
 * Set cache control headers.
 *
 * @param int $seconds Cache duration in seconds
 */
function setCacheHeaders(int $seconds): void
{
    if (headers_sent()) {
        return;
    }

    header('Cache-Control: public, max-age=' . $seconds);
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $seconds) . ' GMT');
    header('Pragma: cache');
}

/**
 * Set headers to prevent caching.
 */
function setNoCacheHeaders(): void
{
    if (headers_sent()) {
        return;
    }

    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
}

/**
 * Render a minimal HTML error page when no template is available.
 *
 * @param int    $code    HTTP status code
 * @param string $title   Error title
 * @param string $message Error message
 * @return string HTML content
 */
function renderErrorFallback(int $code, string $title, string $message): string
{
    $escapedTitle   = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $escapedMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $escapedAppName = htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8');

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$code} - {$escapedTitle} | {$escapedAppName}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center px-6">
        <h1 class="text-7xl font-bold text-indigo-600 mb-4">{$code}</h1>
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">{$escapedTitle}</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">{$escapedMessage}</p>
        <a href="/" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors font-medium">
            Go Home
        </a>
    </div>
</body>
</html>
HTML;
}
