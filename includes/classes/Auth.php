<?php

if (!class_exists('App\Services\AuthService')) {
    require_once __DIR__ . '/../../src/bootstrap.php';
}

class Auth extends App\Services\AuthService {
    // Proxy class
}