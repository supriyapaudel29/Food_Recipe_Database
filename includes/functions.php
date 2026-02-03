<?php
// START SESSION (IMPORTANT)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Escape output
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Generate CSRF token
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Require login for protected pages
function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit;
    }
}
?>
