<?php
/**
 * Logout Page — LearnAI
 *
 * Destroys the active session and redirects to the login page
 * with a flash confirmation message.
 *
 * This page is mapped to /logout in the router and uses the 'auth'
 * layout — although the layout is never rendered because we redirect
 * before any output is produced.
 */

// Ensure a session is active before trying to destroy it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Wipe all session variables
$_SESSION = [];

// Remove the session cookie from the browser
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

// Destroy the server-side session data
session_destroy();

// Start a fresh session so the flash helper can store a message
session_start();

// Re-include helpers (session was destroyed, so HELPERS_LOADED constant
// is still defined but the flash function needs a live session)
flash('success', 'You have been signed out successfully.');

// Redirect to the login page
$loginUrl = url('/login');
header('Location: ' . $loginUrl);
exit;
