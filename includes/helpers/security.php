<?php
/**
 * EduWrite AI - Security Utility Functions
 * CSRF protection, rate limiting, output escaping, encryption, headers, and event logging.
 */

require_once __DIR__ . '/../../config/app.php';

/**
 * Generate a CSRF token, store it in the session, and return it.
 */
function generateCSRFToken(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        if (php_sapi_name() !== 'cli') {
            session_start();
        }
    }

    $token = bin2hex(random_bytes(32));
    $_SESSION['_csrf_token']      = $token;
    $_SESSION['_csrf_token_time'] = time();

    return $token;
}

/**
 * Verify a CSRF token against the one stored in the session.
 * Also checks token expiry based on CSRF_TOKEN_LIFETIME.
 */
function verifyCSRFToken(string $token): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return false;
    }

    if (empty($_SESSION['_csrf_token'])) {
        return false;
    }

    // Check expiry
    $tokenTime = $_SESSION['_csrf_token_time'] ?? 0;
    if (time() - $tokenTime > CSRF_TOKEN_LIFETIME) {
        unset($_SESSION['_csrf_token'], $_SESSION['_csrf_token_time']);
        return false;
    }

    return hash_equals($_SESSION['_csrf_token'], $token);
}

/**
 * Return an HTML <meta> tag containing the CSRF token (useful for AJAX).
 */
function csrfMetaTag(): string
{
    $token = $_SESSION['_csrf_token'] ?? generateCSRFToken();
    return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Return a hidden form field containing the CSRF token.
 */
function csrfHiddenField(): string
{
    $token = $_SESSION['_csrf_token'] ?? generateCSRFToken();
    return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Check whether a key has exceeded its rate limit.
 *
 * @param string $key          Unique identifier (e.g. "login:user@example.com")
 * @param int    $maxAttempts  Maximum attempts allowed
 * @param int    $decayMinutes Time window in minutes
 * @return bool True if within limit, false if limit exceeded
 */
function rateLimiter(string $key, int $maxAttempts = 5, int $decayMinutes = 15): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        if (php_sapi_name() !== 'cli') {
            session_start();
        } else {
            return true;
        }
    }

    $storeKey = '_rate_limit_' . md5($key);
    $now      = time();
    $window   = $decayMinutes * 60;

    if (!isset($_SESSION[$storeKey])) {
        $_SESSION[$storeKey] = [];
    }

    // Remove expired entries
    $_SESSION[$storeKey] = array_filter(
        $_SESSION[$storeKey],
        fn(int $timestamp): bool => ($now - $timestamp) < $window
    );

    return count($_SESSION[$storeKey]) < $maxAttempts;
}

/**
 * Record a rate-limit hit for the given key.
 */
function recordRateLimitHit(string $key): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        if (php_sapi_name() !== 'cli') {
            session_start();
        } else {
            return;
        }
    }

    $storeKey = '_rate_limit_' . md5($key);

    if (!isset($_SESSION[$storeKey])) {
        $_SESSION[$storeKey] = [];
    }

    $_SESSION[$storeKey][] = time();
}

/**
 * Clear the rate limit for a given key.
 */
function clearRateLimit(string $key): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }

    $storeKey = '_rate_limit_' . md5($key);
    unset($_SESSION[$storeKey]);
}

/**
 * Escape a string for safe HTML output.
 */
function escapeOutput(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Escape a string for safe inclusion in a JavaScript context.
 */
function escapeJS(string $string): string
{
    return json_encode($string, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
}

/**
 * Escape a string for safe inclusion in a URL.
 */
function escapeUrl(string $string): string
{
    return urlencode($string);
}

/**
 * Strip all HTML tags except the explicitly allowed ones.
 *
 * @param string   $html        Raw HTML
 * @param string[] $allowedTags Tags to keep (e.g. ['p', 'b', 'i', 'a'])
 */
function sanitizeHtml(string $html, array $allowedTags = []): string
{
    if (empty($allowedTags)) {
        return strip_tags($html);
    }

    $tagString = implode('', array_map(fn(string $t): string => '<' . $t . '>', $allowedTags));
    return strip_tags($html, $tagString);
}

/**
 * Create an HMAC hash of data using the application secret.
 */
function hashData(string $data): string
{
    return hash_hmac('sha256', $data, APP_SECRET);
}

/**
 * Verify an HMAC hash of data.
 */
function verifyHash(string $data, string $hash): bool
{
    return hash_equals(hashData($data), $hash);
}

/**
 * Encrypt data using AES-256-CBC.
 *
 * @return string Base64-encoded ciphertext (IV prepended)
 * @throws \RuntimeException on encryption failure
 */
function encryptData(string $data): string
{
    $key    = hash('sha256', ENCRYPTION_KEY, true);
    $iv     = random_bytes(openssl_cipher_iv_length(ENCRYPTION_METHOD));
    $cipher = openssl_encrypt($data, ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);

    if ($cipher === false) {
        throw new \RuntimeException('Encryption failed.');
    }

    $mac = hash_hmac('sha256', $iv . $cipher, $key, true);

    return base64_encode($mac . $iv . $cipher);
}

/**
 * Decrypt data that was encrypted with encryptData().
 *
 * @return string Decrypted plaintext
 * @throws \RuntimeException on decryption failure or tampered data
 */
function decryptData(string $data): string
{
    $raw = base64_decode($data, true);
    if ($raw === false) {
        throw new \RuntimeException('Decryption failed: invalid base64.');
    }

    $key     = hash('sha256', ENCRYPTION_KEY, true);
    $macLen  = 32; // SHA-256 HMAC length
    $ivLen   = openssl_cipher_iv_length(ENCRYPTION_METHOD);

    if (strlen($raw) < $macLen + $ivLen) {
        throw new \RuntimeException('Decryption failed: data too short.');
    }

    $mac    = substr($raw, 0, $macLen);
    $iv     = substr($raw, $macLen, $ivLen);
    $cipher = substr($raw, $macLen + $ivLen);

    $expectedMac = hash_hmac('sha256', $iv . $cipher, $key, true);
    if (!hash_equals($expectedMac, $mac)) {
        throw new \RuntimeException('Decryption failed: data has been tampered with.');
    }

    $plain = openssl_decrypt($cipher, ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);
    if ($plain === false) {
        throw new \RuntimeException('Decryption failed.');
    }

    return $plain;
}

/**
 * Generate a cryptographically secure random token.
 *
 * @param int $length Byte length (output hex string is twice this)
 */
function generateSecureToken(int $length = 32): string
{
    return bin2hex(random_bytes($length));
}

/**
 * Validate an IP address (both IPv4 and IPv6).
 */
function validateIPAddress(string $ip): bool
{
    return filter_var($ip, FILTER_VALIDATE_IP) !== false;
}

/**
 * Get the real client IP address, handling common proxy headers.
 */
function getClientIP(): string
{
    $trustedProxies = defined('TRUSTED_PROXIES') ? TRUSTED_PROXIES : [];
    $remoteAddr     = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    // Only trust proxy headers if the immediate connection is from a trusted proxy
    if (!empty($trustedProxies) && in_array($remoteAddr, $trustedProxies, true)) {
        $headers = [
            'HTTP_CF_CONNECTING_IP',   // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = array_map('trim', explode(',', $_SERVER[$header]));
                // Use the first (leftmost) non-private IP
                foreach ($ips as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        return $ip;
                    }
                }
                // If all are private, use the first one
                $first = trim($ips[0]);
                if (filter_var($first, FILTER_VALIDATE_IP)) {
                    return $first;
                }
            }
        }
    }

    return $remoteAddr;
}

/**
 * Check whether the current request is over HTTPS.
 */
if (!function_exists('isSecureConnection')) {
    function isSecureConnection(): bool
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        }
        if (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) {
            return true;
        }
        return false;
    }
}

/**
 * Set recommended security response headers.
 */
function setSecurityHeaders(): void
{
    if (headers_sent()) {
        return;
    }

    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

    $csp = implode('; ', [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com",
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.tailwindcss.com",
        "font-src 'self' https://fonts.gstatic.com",
        "img-src 'self' data: https:",
        "connect-src 'self'",
        "frame-ancestors 'self'",
        "base-uri 'self'",
        "form-action 'self'",
    ]);
    header('Content-Security-Policy: ' . $csp);

    if (isSecureConnection()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

/**
 * Log a security-related event to the database.
 *
 * @param string $type    Event type (e.g. "csrf_failure", "brute_force", "unauthorized_access")
 * @param array  $details Additional context
 */
function logSecurityEvent(string $type, array $details = []): void
{
    $details['ip']         = getClientIP();
    $details['user_agent'] = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 512);
    $details['timestamp']  = date('Y-m-d H:i:s');
    $details['uri']        = $_SERVER['REQUEST_URI'] ?? '';

    // Attempt database logging
    try {
        require_once __DIR__ . '/database.php';

        $userId = $_SESSION['user_id'] ?? null;

        query(
            'INSERT INTO activity_logs (user_id, action, entity_type, details, ip_address, user_agent, created_at)
             VALUES (:uid, :action, :etype, :details, :ip, :ua, NOW())',
            [
                ':uid'     => $userId,
                ':action'  => 'security_' . $type,
                ':etype'   => 'security',
                ':details' => json_encode($details, JSON_UNESCAPED_UNICODE),
                ':ip'      => $details['ip'],
                ':ua'      => $details['user_agent'],
            ]
        );
    } catch (\Throwable $e) {
        // Fall back to file logging if DB is unavailable
        $logDir = defined('LOG_PATH') ? LOG_PATH : dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $message = sprintf(
            "[%s] SECURITY [%s]: %s\n",
            date('Y-m-d H:i:s'),
            $type,
            json_encode($details, JSON_UNESCAPED_UNICODE)
        );

        error_log($message, 3, $logDir . '/security.log');
    }
}
