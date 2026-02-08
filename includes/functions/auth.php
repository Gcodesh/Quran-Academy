<?php

// Ensure autoloader
if (!class_exists('App\Services\AuthService')) {
    require_once __DIR__ . '/../../src/bootstrap.php';
}
require_once __DIR__ . '/logger.php';

class Auth extends App\Services\AuthService {
    // Proxy class for backward compatibility
}
