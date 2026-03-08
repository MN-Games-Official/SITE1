<?php
/**
 * EduWrite AI - API v1 Router / Bootstrap
 * 
 * Common setup for all API v1 endpoints.
 * Include this file at the top of each endpoint file.
 */

// Prevent direct access
if (basename($_SERVER['SCRIPT_FILENAME']) === 'router.php') {
    http_response_code(403);
    echo json_encode(['error' => 'Direct access not allowed']);
    exit;
}

// Base path
define('BASE_PATH', dirname(__DIR__, 2));

// Load configuration
require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';

// Load helpers
require_once BASE_PATH . '/includes/helpers/auth.php';
require_once BASE_PATH . '/includes/helpers/database.php';
require_once BASE_PATH . '/includes/helpers/validation.php';
require_once BASE_PATH . '/includes/helpers/security.php';
require_once BASE_PATH . '/includes/helpers/response.php';
require_once BASE_PATH . '/includes/helpers/formatting.php';

// Initialize secure session and security headers
startSecureSession();
setSecurityHeaders();

// Set JSON content type for all API responses
header('Content-Type: application/json; charset=utf-8');

/**
 * Get the request method, supporting method override via _method parameter.
 */
function getRequestMethod(): string {
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'POST' && isset($_POST['_method'])) {
        $override = strtoupper($_POST['_method']);
        if (in_array($override, ['PUT', 'DELETE', 'PATCH'])) {
            return $override;
        }
    }
    return $method;
}

/**
 * Get the action parameter from the request.
 */
function getAction(): string {
    return $_GET['action'] ?? $_POST['action'] ?? '';
}

/**
 * Parse JSON request body for PUT/DELETE/PATCH requests.
 */
function getRequestBody(): array {
    $body = file_get_contents('php://input');
    if (empty($body)) {
        return [];
    }
    $data = json_decode($body, true);
    return is_array($data) ? $data : [];
}

/**
 * Require a specific HTTP method or return 405.
 */
function requireMethod(string ...$methods): void {
    $current = getRequestMethod();
    if (!in_array($current, $methods)) {
        jsonError('Method not allowed', 405);
    }
}

/**
 * Validate CSRF token on write operations (POST, PUT, DELETE, PATCH).
 */
function validateWriteCSRF(): void {
    $method = getRequestMethod();
    if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
        $token = $_POST['csrf_token']
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? getRequestBody()['csrf_token']
            ?? '';
        if (!verifyCSRFToken($token)) {
            jsonError('Invalid or expired CSRF token', 403);
        }
    }
}

/**
 * Get pagination parameters from query string.
 */
function getPaginationParams(int $defaultPerPage = 20, int $maxPerPage = 100): array {
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = min($maxPerPage, max(1, (int)($_GET['per_page'] ?? $defaultPerPage)));
    return [$page, $perPage];
}

/**
 * Get period parameter (for analytics).
 */
function getPeriodParam(string $default = '30d'): string {
    $period = $_GET['period'] ?? $default;
    $allowed = ['1d', '7d', '14d', '30d', '90d', '365d', 'all'];
    return in_array($period, $allowed) ? $period : $default;
}

/**
 * Route an action to a handler function with error handling.
 */
function routeAction(array $routes): void {
    $action = getAction();

    if (empty($action)) {
        jsonError('Missing required parameter: action', 400);
    }

    if (!isset($routes[$action])) {
        jsonError("Unknown action: {$action}", 400);
    }

    try {
        $routes[$action]();
    } catch (InvalidArgumentException $e) {
        jsonError($e->getMessage(), 422);
    } catch (RuntimeException $e) {
        $message = $e->getMessage();
        $code = str_contains($message, 'Unauthorized') || str_contains($message, 'Permission') ? 403 : 400;
        jsonError($message, $code);
    } catch (Exception $e) {
        error_log("API Error [{$action}]: " . $e->getMessage());
        jsonError('An internal error occurred', 500);
    }
}
