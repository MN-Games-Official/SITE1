<?php
/**
 * EduWrite AI - Authentication Helper Functions
 * Session management, login/logout, registration, role checks, and password utilities.
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/database.php';

/**
 * Configure and start a secure PHP session.
 */
function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $isSecure = SESSION_SECURE && isSecureConnection();

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    ini_set('session.cookie_lifetime', '0');
    ini_set('session.gc_maxlifetime', (string)SESSION_LIFETIME);

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => SESSION_PATH,
        'domain'   => SESSION_DOMAIN,
        'secure'   => $isSecure,
        'httponly'  => SESSION_HTTPONLY,
        'samesite' => SESSION_SAMESITE,
    ]);

    session_name(SESSION_NAME);
    session_start();

    // Regenerate session ID periodically to prevent fixation
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > SESSION_REGENERATE_INTERVAL) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

/**
 * Authenticate a user by email and password.
 *
 * @param string $email
 * @param string $password
 * @return array|false User data on success, false on failure
 */
function login(string $email, string $password): array|false
{
    $email = strtolower(trim($email));

    if (!checkLoginAttempts($email)) {
        return false;
    }

    $user = fetch(
        'SELECT id, school_id, email, username, first_name, last_name, password_hash, role, status
         FROM users WHERE email = :email LIMIT 1',
        [':email' => $email]
    );

    if (!$user || $user['status'] !== 'active') {
        recordLoginAttempt($email, false);
        return false;
    }

    if (!verifyPassword($password, $user['password_hash'])) {
        recordLoginAttempt($email, false);
        return false;
    }

    recordLoginAttempt($email, true);

    // Create session
    startSecureSession();
    session_regenerate_id(true);

    $sessionToken = generateToken(64);
    $csrfToken    = generateToken(64);
    $expiresAt    = date('Y-m-d H:i:s', time() + SESSION_LIFETIME);

    // Store session in database
    query(
        'INSERT INTO sessions (user_id, token, ip_address, user_agent, csrf_token, expires_at)
         VALUES (:user_id, :token, :ip, :ua, :csrf, :expires)',
        [
            ':user_id' => $user['id'],
            ':token'   => hash('sha256', $sessionToken),
            ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            ':ua'      => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 512),
            ':csrf'    => hash('sha256', $csrfToken),
            ':expires' => $expiresAt,
        ]
    );

    unset($user['password_hash']);

    $_SESSION['user_id']       = $user['id'];
    $_SESSION['school_id']     = $user['school_id'];
    $_SESSION['role']          = $user['role'];
    $_SESSION['session_token'] = $sessionToken;
    $_SESSION['csrf_token']    = $csrfToken;
    $_SESSION['_created']      = time();
    $_SESSION['_last_active']  = time();

    updateLastLogin($user['id']);

    return $user;
}

/**
 * Log the current user out and destroy their session.
 */
function logout(): void
{
    startSecureSession();

    if (isset($_SESSION['session_token'])) {
        query(
            'DELETE FROM sessions WHERE token = :token',
            [':token' => hash('sha256', $_SESSION['session_token'])]
        );
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

/**
 * Register a new user account.
 *
 * @param array $data Expected keys: email, password, first_name, last_name, username, role, school_id
 * @return array{success: bool, user_id?: string, errors?: array}
 */
function register(array $data): array
{
    $errors = [];

    // Required field validation
    $required = ['email', 'password', 'first_name', 'last_name', 'username', 'school_id'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    $email = strtolower(trim($data['email']));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email address.';
    }

    $passwordErrors = validatePasswordStrength($data['password']);
    if (!empty($passwordErrors)) {
        $errors['password'] = $passwordErrors;
    }

    if (strlen($data['username']) < 3 || strlen($data['username']) > 50) {
        $errors['username'] = 'Username must be between 3 and 50 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
        $errors['username'] = 'Username may only contain letters, numbers, and underscores.';
    }

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    // Check uniqueness
    if (exists('users', 'email = :email', [':email' => $email])) {
        $errors['email'] = 'This email is already registered.';
    }
    if (exists('users', 'username = :username', [':username' => $data['username']])) {
        $errors['username'] = 'This username is already taken.';
    }

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    $allowedRoles = ['student', 'teacher', 'admin'];
    $role = in_array($data['role'] ?? 'student', $allowedRoles, true) ? $data['role'] : 'student';

    $userId = insert('users', [
        'school_id'     => (int)$data['school_id'],
        'email'         => $email,
        'username'      => $data['username'],
        'password_hash' => hashPassword($data['password']),
        'first_name'    => trim($data['first_name']),
        'last_name'     => trim($data['last_name']),
        'role'          => $role,
        'status'        => 'active',
        'created_at'    => date('Y-m-d H:i:s'),
        'updated_at'    => date('Y-m-d H:i:s'),
    ]);

    return ['success' => true, 'user_id' => $userId];
}

/**
 * Get the currently authenticated user or null.
 *
 * @return array|null
 */
function getCurrentUser(): ?array
{
    startSecureSession();

    if (!isLoggedIn()) {
        return null;
    }

    return getUserById($_SESSION['user_id']);
}

/**
 * Check whether a user is currently logged in with a valid session.
 */
function isLoggedIn(): bool
{
    startSecureSession();

    if (empty($_SESSION['user_id']) || empty($_SESSION['session_token'])) {
        return false;
    }

    return isSessionValid();
}

/**
 * Require authentication; redirect to login page if not authenticated.
 */
function requireAuth(): void
{
    if (!isLoggedIn()) {
        if (php_sapi_name() !== 'cli') {
            header('Location: ' . APP_URL . '/pages/auth/login.php');
            exit;
        }
        throw new \RuntimeException('Authentication required.');
    }
}

/**
 * Require a specific role. Sends a 403 response if the user does not have the role.
 *
 * @param string|array $role Allowed role(s)
 */
function requireRole(string|array $role): void
{
    requireAuth();

    $roles = is_array($role) ? $role : [$role];

    if (!in_array($_SESSION['role'] ?? '', $roles, true)) {
        http_response_code(403);
        if (php_sapi_name() !== 'cli') {
            include APP_ROOT . '/templates/errors/403.php';
            exit;
        }
        throw new \RuntimeException('Forbidden: insufficient role.');
    }
}

/**
 * Require that the current user owns the resource identified by $userId.
 * Admins and super_admins bypass this check.
 *
 * @param int $userId The owner user ID of the resource
 */
function requireOwnership(int $userId): void
{
    requireAuth();

    $currentRole = $_SESSION['role'] ?? '';
    if (in_array($currentRole, ['admin', 'super_admin'], true)) {
        return;
    }

    if ((int)$_SESSION['user_id'] !== $userId) {
        http_response_code(403);
        if (php_sapi_name() !== 'cli') {
            include APP_ROOT . '/templates/errors/403.php';
            exit;
        }
        throw new \RuntimeException('Forbidden: resource ownership mismatch.');
    }
}

/**
 * Hash a password using bcrypt.
 */
function hashPassword(string $password): string
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => PASSWORD_BCRYPT_COST]);
}

/**
 * Verify a password against a bcrypt hash.
 */
function verifyPassword(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

/**
 * Generate a cryptographically secure random token.
 *
 * @param int $length Length in bytes (output is hex, so string length = $length * 2)
 * @return string Hexadecimal token
 */
function generateToken(int $length = 32): string
{
    return bin2hex(random_bytes($length));
}

/**
 * Validate password strength requirements.
 *
 * @return string[] List of validation error messages (empty if valid)
 */
function validatePasswordStrength(string $password): array
{
    $errors = [];

    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least one lowercase letter.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number.';
    }

    return $errors;
}

/**
 * Check whether the given email has exceeded login attempt limits.
 *
 * @return bool True if login is allowed, false if locked out
 */
function checkLoginAttempts(string $email): bool
{
    $cutoff = date('Y-m-d H:i:s', time() - LOGIN_LOCKOUT_DURATION);

    $row = fetch(
        'SELECT COUNT(*) AS attempts FROM activity_logs
         WHERE action = :action AND details LIKE :email AND created_at > :cutoff',
        [
            ':action' => 'login_failed',
            ':email'  => '%' . $email . '%',
            ':cutoff' => $cutoff,
        ]
    );

    return ($row['attempts'] ?? 0) < MAX_LOGIN_ATTEMPTS;
}

/**
 * Record a login attempt in the activity log.
 */
function recordLoginAttempt(string $email, bool $success): void
{
    $user = fetch('SELECT id FROM users WHERE email = :email LIMIT 1', [':email' => $email]);

    $details = json_encode([
        'email'   => $email,
        'success' => $success,
        'ip'      => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
    ]);

    query(
        'INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, ip_address, user_agent, created_at)
         VALUES (:uid, :action, :etype, :eid, :details, :ip, :ua, NOW())',
        [
            ':uid'     => $user['id'] ?? null,
            ':action'  => $success ? 'login_success' : 'login_failed',
            ':etype'   => 'user',
            ':eid'     => $user['id'] ?? null,
            ':details' => $details,
            ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            ':ua'      => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 512),
        ]
    );
}

/**
 * Check whether the current session is valid (not expired, token matches DB).
 */
function isSessionValid(): bool
{
    if (empty($_SESSION['session_token'])) {
        return false;
    }

    // Check session expiry by last activity
    if (isset($_SESSION['_last_active'])) {
        if (time() - $_SESSION['_last_active'] > SESSION_LIFETIME) {
            logout();
            return false;
        }
    }

    $hashedToken = hash('sha256', $_SESSION['session_token']);

    $session = fetch(
        'SELECT id, expires_at FROM sessions WHERE token = :token AND user_id = :uid LIMIT 1',
        [
            ':token' => $hashedToken,
            ':uid'   => $_SESSION['user_id'],
        ]
    );

    if (!$session) {
        return false;
    }

    if (strtotime($session['expires_at']) < time()) {
        query('DELETE FROM sessions WHERE id = :id', [':id' => $session['id']]);
        return false;
    }

    $_SESSION['_last_active'] = time();
    return true;
}

/**
 * Extend the current session's lifetime.
 */
function refreshSession(): void
{
    if (empty($_SESSION['session_token'])) {
        return;
    }

    $newExpiry   = date('Y-m-d H:i:s', time() + SESSION_LIFETIME);
    $hashedToken = hash('sha256', $_SESSION['session_token']);

    query(
        'UPDATE sessions SET expires_at = :expires WHERE token = :token',
        [':expires' => $newExpiry, ':token' => $hashedToken]
    );

    $_SESSION['_last_active'] = time();
}

/**
 * Fetch a user by their ID. Excludes the password hash.
 *
 * @param int $id
 * @return array|null
 */
function getUserById(int $id): ?array
{
    $user = fetch(
        'SELECT id, school_id, email, username, first_name, last_name, role, status,
                avatar_url, bio, preferences, last_login_at, created_at, updated_at
         FROM users WHERE id = :id LIMIT 1',
        [':id' => $id]
    );

    return $user ?: null;
}

/**
 * Update the last login timestamp for a user.
 */
function updateLastLogin(int $userId): void
{
    query(
        'UPDATE users SET last_login_at = NOW(), updated_at = NOW() WHERE id = :id',
        [':id' => $userId]
    );
}

/**
 * Helper: check if current connection is HTTPS.
 * Defined here to avoid circular dependency when auth.php is loaded without security.php.
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
