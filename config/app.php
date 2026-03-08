<?php
/**
 * EduWrite AI - Application Configuration
 * Central configuration constants and settings for the platform.
 */

// Prevent direct access
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// ---------------------------------------------------------------------------
// Application Settings
// ---------------------------------------------------------------------------
define('APP_NAME', 'EduWrite AI');
define('APP_VERSION', '1.0.0');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost');
define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('APP_DEBUG', filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN));
define('APP_TIMEZONE', getenv('APP_TIMEZONE') ?: 'UTC');
define('APP_LOCALE', 'en_US');
define('APP_SECRET', getenv('APP_SECRET') ?: 'CHANGE_THIS_TO_A_RANDOM_SECRET_KEY');

// ---------------------------------------------------------------------------
// Database Configuration
// ---------------------------------------------------------------------------
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'eduwrite');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8mb4_unicode_ci');
define('DB_PREFIX', '');
define('DB_CONNECT_TIMEOUT', 5);
define('DB_READ_TIMEOUT', 30);

// ---------------------------------------------------------------------------
// Session Configuration
// ---------------------------------------------------------------------------
define('SESSION_LIFETIME', (int)(getenv('SESSION_LIFETIME') ?: 7200)); // 2 hours
define('SESSION_NAME', 'eduwrite_session');
define('SESSION_SECURE', filter_var(getenv('SESSION_SECURE') ?: true, FILTER_VALIDATE_BOOLEAN));
define('SESSION_HTTPONLY', true);
define('SESSION_SAMESITE', 'Lax');
define('SESSION_PATH', '/');
define('SESSION_DOMAIN', getenv('SESSION_DOMAIN') ?: '');
define('SESSION_REGENERATE_INTERVAL', 900); // 15 minutes

// ---------------------------------------------------------------------------
// AI Provider Configuration
// ---------------------------------------------------------------------------
define('AI_DEFAULT_PROVIDER', getenv('AI_DEFAULT_PROVIDER') ?: 'abacus');
define('AI_API_ENDPOINT', getenv('AI_API_ENDPOINT') ?: 'https://api.abacus.ai/v1/chat/completions');
define('AI_API_KEY', getenv('AI_API_KEY') ?: '');
define('AI_ALLOWED_MODELS', [
    'abacus-gpt4' => 'GPT-4 (Abacus)',
    'abacus-gpt35' => 'GPT-3.5 Turbo (Abacus)',
    'abacus-claude' => 'Claude (Abacus)',
    'abacus-llama' => 'LLaMA (Abacus)',
]);
define('AI_DEFAULT_MODEL', getenv('AI_DEFAULT_MODEL') ?: 'abacus-gpt35');
define('AI_MAX_TOKENS', (int)(getenv('AI_MAX_TOKENS') ?: 2048));
define('AI_DEFAULT_TEMPERATURE', 0.7);
define('AI_RATE_LIMIT_PER_MINUTE', (int)(getenv('AI_RATE_LIMIT_PER_MINUTE') ?: 10));
define('AI_RATE_LIMIT_PER_HOUR', (int)(getenv('AI_RATE_LIMIT_PER_HOUR') ?: 100));
define('AI_RATE_LIMIT_PER_DAY', (int)(getenv('AI_RATE_LIMIT_PER_DAY') ?: 500));
define('AI_REQUEST_TIMEOUT', 30);

// ---------------------------------------------------------------------------
// Upload Configuration
// ---------------------------------------------------------------------------
define('UPLOAD_MAX_SIZE', (int)(getenv('UPLOAD_MAX_SIZE') ?: 10485760)); // 10 MB
define('UPLOAD_ALLOWED_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/webp',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'text/plain',
    'text/csv',
]);
define('UPLOAD_ALLOWED_EXTENSIONS', [
    'jpg', 'jpeg', 'png', 'gif', 'webp',
    'pdf', 'doc', 'docx', 'txt', 'csv',
]);
define('UPLOAD_STORAGE_PATH', APP_ROOT . '/storage/uploads');
define('UPLOAD_TEMP_PATH', APP_ROOT . '/storage/uploads/tmp');

// ---------------------------------------------------------------------------
// Mail Configuration
// ---------------------------------------------------------------------------
define('MAIL_DRIVER', getenv('MAIL_DRIVER') ?: 'smtp');
define('MAIL_HOST', getenv('MAIL_HOST') ?: 'smtp.mailtrap.io');
define('MAIL_PORT', (int)(getenv('MAIL_PORT') ?: 587));
define('MAIL_USERNAME', getenv('MAIL_USERNAME') ?: '');
define('MAIL_PASSWORD', getenv('MAIL_PASSWORD') ?: '');
define('MAIL_ENCRYPTION', getenv('MAIL_ENCRYPTION') ?: 'tls');
define('MAIL_FROM_ADDRESS', getenv('MAIL_FROM_ADDRESS') ?: 'noreply@eduwrite.ai');
define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: APP_NAME);

// ---------------------------------------------------------------------------
// Security Configuration
// ---------------------------------------------------------------------------
define('CSRF_TOKEN_LIFETIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_DURATION', 900); // 15 minutes
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_BCRYPT_COST', 12);
define('ENCRYPTION_METHOD', 'aes-256-cbc');
define('ENCRYPTION_KEY', getenv('ENCRYPTION_KEY') ?: APP_SECRET);
define('ALLOWED_ORIGINS', array_filter(explode(',', getenv('ALLOWED_ORIGINS') ?: APP_URL)));
define('TRUSTED_PROXIES', array_filter(explode(',', getenv('TRUSTED_PROXIES') ?: '')));

// ---------------------------------------------------------------------------
// Feature Flags
// ---------------------------------------------------------------------------
define('FEATURE_AI_ASSISTANT', filter_var(getenv('FEATURE_AI_ASSISTANT') ?: true, FILTER_VALIDATE_BOOLEAN));
define('FEATURE_DOCUMENT_SHARING', filter_var(getenv('FEATURE_DOCUMENT_SHARING') ?: true, FILTER_VALIDATE_BOOLEAN));
define('FEATURE_PLAGIARISM_CHECK', filter_var(getenv('FEATURE_PLAGIARISM_CHECK') ?: true, FILTER_VALIDATE_BOOLEAN));
define('FEATURE_EXPORT_PDF', filter_var(getenv('FEATURE_EXPORT_PDF') ?: true, FILTER_VALIDATE_BOOLEAN));
define('FEATURE_STUDENT_ANALYTICS', filter_var(getenv('FEATURE_STUDENT_ANALYTICS') ?: true, FILTER_VALIDATE_BOOLEAN));
define('FEATURE_REAL_TIME_COLLAB', filter_var(getenv('FEATURE_REAL_TIME_COLLAB') ?: false, FILTER_VALIDATE_BOOLEAN));
define('FEATURE_MAINTENANCE_MODE', filter_var(getenv('FEATURE_MAINTENANCE_MODE') ?: false, FILTER_VALIDATE_BOOLEAN));
define('FEATURE_REGISTRATION', filter_var(getenv('FEATURE_REGISTRATION') ?: true, FILTER_VALIDATE_BOOLEAN));

// ---------------------------------------------------------------------------
// Pagination Defaults
// ---------------------------------------------------------------------------
define('DEFAULT_PAGE_SIZE', 20);
define('MAX_PAGE_SIZE', 100);

// ---------------------------------------------------------------------------
// Logging
// ---------------------------------------------------------------------------
define('LOG_PATH', APP_ROOT . '/storage/logs');
define('LOG_LEVEL', getenv('LOG_LEVEL') ?: (APP_DEBUG ? 'debug' : 'warning'));
define('LOG_MAX_FILES', 30);

// ---------------------------------------------------------------------------
// Cache
// ---------------------------------------------------------------------------
define('CACHE_DRIVER', getenv('CACHE_DRIVER') ?: 'file');
define('CACHE_PATH', APP_ROOT . '/storage/cache');
define('CACHE_TTL', 3600);

// ---------------------------------------------------------------------------
// Export
// ---------------------------------------------------------------------------
define('EXPORT_PATH', APP_ROOT . '/storage/exports');
define('EXPORT_MAX_AGE', 86400); // 24 hours

// ---------------------------------------------------------------------------
// Runtime Configuration
// ---------------------------------------------------------------------------
date_default_timezone_set(APP_TIMEZONE);
mb_internal_encoding('UTF-8');

if (APP_ENV === 'production' && APP_SECRET === 'CHANGE_THIS_TO_A_RANDOM_SECRET_KEY') {
    error_log('[CRITICAL] APP_SECRET has not been configured. Set the APP_SECRET environment variable.');
}

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', '0');
}

ini_set('log_errors', '1');
ini_set('error_log', LOG_PATH . '/php_errors.log');
