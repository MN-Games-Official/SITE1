<?php
/**
 * EduWrite AI - Authentication API Endpoint
 *
 * Handles login, registration, logout, password management, and profile updates.
 */

require_once __DIR__ . '/router.php';

$method = getRequestMethod();
$action = getAction();

// Public actions that don't require authentication
$publicActions = ['login', 'register', 'forgot-password', 'reset-password'];

if (!in_array($action, $publicActions)) {
    requireAuth();
}

routeAction([
    'login'           => 'handleLogin',
    'register'        => 'handleRegister',
    'logout'          => 'handleLogout',
    'me'              => 'handleMe',
    'forgot-password' => 'handleForgotPassword',
    'reset-password'  => 'handleResetPassword',
    'change-password' => 'handleChangePassword',
    'profile'         => 'handleUpdateProfile',
]);

/** POST - Authenticate user with email and password */
function handleLogin(): void {
    requireMethod('POST');

    $data = getRequestBody();
    $errors = validate($data, [
        'email'    => 'required|email',
        'password' => 'required|string|min:1',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $email = sanitize($data['email'], 'email');

    if (!checkLoginAttempts($email)) {
        jsonError('Too many login attempts. Please try again later.', 429);
    }

    $user = login($email, $data['password']);

    if (!$user) {
        recordLoginAttempt($email, false);
        jsonError('Invalid email or password', 401);
    }

    recordLoginAttempt($email, true);

    jsonSuccess([
        'user'       => sanitizeUserData($user),
        'csrf_token' => generateCSRFToken(),
    ], 'Login successful');
}

/** POST - Register a new user account */
function handleRegister(): void {
    requireMethod('POST');

    if (!defined('FEATURE_REGISTRATION') || !FEATURE_REGISTRATION) {
        jsonError('Registration is currently disabled', 403);
    }

    $data = getRequestBody();
    $errors = validate($data, [
        'email'      => 'required|email|unique:users,email',
        'password'   => 'required|string|min:8',
        'first_name' => 'required|string|min:1|max:100',
        'last_name'  => 'required|string|min:1|max:100',
        'role'       => 'required|in:student,teacher',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $passwordErrors = validatePasswordStrength($data['password']);
    if (!empty($passwordErrors)) {
        jsonError('Password does not meet requirements', 422, ['password' => implode('. ', $passwordErrors)]);
    }

    $user = register([
        'email'      => sanitize($data['email'], 'email'),
        'password'   => $data['password'],
        'first_name' => sanitize($data['first_name'], 'string'),
        'last_name'  => sanitize($data['last_name'], 'string'),
        'role'       => $data['role'],
        'username'   => $data['username'] ?? null,
    ]);

    if (isset($user['errors'])) {
        jsonError('Registration failed', 422, $user['errors']);
    }

    jsonSuccess([
        'user'       => sanitizeUserData($user),
        'csrf_token' => generateCSRFToken(),
    ], 'Registration successful');
}

/** POST - Destroy the current session */
function handleLogout(): void {
    requireMethod('POST');
    validateWriteCSRF();
    logout();
    jsonSuccess(null, 'Logged out successfully');
}

/** GET - Return current authenticated user data */
function handleMe(): void {
    requireMethod('GET');

    $user = getCurrentUser();
    if (!$user) {
        jsonError('Not authenticated', 401);
    }

    jsonSuccess([
        'user'       => sanitizeUserData($user),
        'csrf_token' => generateCSRFToken(),
    ]);
}

/** POST - Initiate password reset (sends token placeholder) */
function handleForgotPassword(): void {
    requireMethod('POST');

    $data = getRequestBody();
    $errors = validate($data, [
        'email' => 'required|email',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $email = sanitize($data['email'], 'email');

    // Generate reset token
    $token = generateToken(32);
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Check if user exists (don't reveal whether they do)
    $user = fetch("SELECT id FROM users WHERE email = ?", [$email]);

    if ($user) {
        update('users', [
            'reset_token'   => hashPassword($token),
            'reset_expires' => $expiry,
        ], 'id = ?', [$user['id']]);

        // TODO: Send email with reset link containing $token
        logSecurityEvent('password_reset_requested', ['email' => $email]);
    }

    // Always return success to prevent email enumeration
    jsonSuccess(null, 'If an account with that email exists, a password reset link has been sent.');
}

/** POST - Reset password using a valid token */
function handleResetPassword(): void {
    requireMethod('POST');

    $data = getRequestBody();
    $errors = validate($data, [
        'email'    => 'required|email',
        'token'    => 'required|string',
        'password' => 'required|string|min:8',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $passwordErrors = validatePasswordStrength($data['password']);
    if (!empty($passwordErrors)) {
        jsonError('Password does not meet requirements', 422, ['password' => implode('. ', $passwordErrors)]);
    }

    $user = fetch(
        "SELECT id, reset_token, reset_expires FROM users WHERE email = ?",
        [sanitize($data['email'], 'email')]
    );

    if (!$user || !$user['reset_token']) {
        jsonError('Invalid or expired reset token', 400);
    }

    if (strtotime($user['reset_expires']) < time()) {
        jsonError('Reset token has expired', 400);
    }

    if (!verifyPassword($data['token'], $user['reset_token'])) {
        jsonError('Invalid or expired reset token', 400);
    }

    update('users', [
        'password_hash' => hashPassword($data['password']),
        'reset_token'   => null,
        'reset_expires' => null,
    ], 'id = ?', [$user['id']]);

    logSecurityEvent('password_reset_completed', ['user_id' => $user['id']]);
    jsonSuccess(null, 'Password has been reset successfully');
}

/** POST - Change password for the logged-in user */
function handleChangePassword(): void {
    requireMethod('POST');
    validateWriteCSRF();

    $user = getCurrentUser();
    $data = getRequestBody();

    $errors = validate($data, [
        'current_password' => 'required|string',
        'new_password'     => 'required|string|min:8',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $fullUser = fetch("SELECT password_hash FROM users WHERE id = ?", [$user['id']]);

    if (!verifyPassword($data['current_password'], $fullUser['password_hash'])) {
        jsonError('Current password is incorrect', 400);
    }

    $passwordErrors = validatePasswordStrength($data['new_password']);
    if (!empty($passwordErrors)) {
        jsonError('New password does not meet requirements', 422, ['new_password' => implode('. ', $passwordErrors)]);
    }

    update('users', [
        'password_hash' => hashPassword($data['new_password']),
    ], 'id = ?', [$user['id']]);

    logSecurityEvent('password_changed', ['user_id' => $user['id']]);
    jsonSuccess(null, 'Password changed successfully');
}

/** PUT - Update user profile (first_name, last_name, avatar) */
function handleUpdateProfile(): void {
    requireMethod('PUT');
    validateWriteCSRF();

    $user = getCurrentUser();
    $data = getRequestBody();

    $errors = validate($data, [
        'first_name' => 'string|min:1|max:100',
        'last_name'  => 'string|min:1|max:100',
        'avatar'     => 'string|url',
    ]);

    if (!empty($errors)) {
        jsonError('Validation failed', 422, $errors);
    }

    $updateData = [];
    if (isset($data['first_name'])) $updateData['first_name'] = sanitize($data['first_name'], 'string');
    if (isset($data['last_name']))  $updateData['last_name']  = sanitize($data['last_name'], 'string');
    if (isset($data['avatar']))     $updateData['avatar']     = sanitize($data['avatar'], 'url');

    if (empty($updateData)) {
        jsonError('No fields to update', 400);
    }

    update('users', $updateData, 'id = ?', [$user['id']]);

    $updatedUser = getUserById($user['id']);
    jsonSuccess(['user' => sanitizeUserData($updatedUser)], 'Profile updated successfully');
}

/**
 * Strip sensitive fields from user data before returning.
 */
function sanitizeUserData(array $user): array {
    unset(
        $user['password_hash'],
        $user['reset_token'],
        $user['reset_expires'],
        $user['session_token']
    );
    return $user;
}
