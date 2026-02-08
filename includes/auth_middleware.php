<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkAuth($allowed_roles = []) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php?error=unauthorized');
        exit;
    }

    if (!empty($allowed_roles) && !in_array($_SESSION['user_role'], $allowed_roles)) {
        header('Location: ../home.php?error=forbidden');
        exit;
    }
}

// CSRF Protection
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed.');
    }
}
?>
