<?php
// Define project root constant for all includes
// If PROJECT_ROOT is already defined (e.g. by index.php), use it, otherwise define it
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__, 2)); // Go up 2 levels config -> includes -> root
}

// Helper function for file paths (Serverside includes)
if (!function_exists('path')) {
    function path($path) {
        $cleanPath = ltrim($path, '/\\');
        return PROJECT_ROOT . DIRECTORY_SEPARATOR . $cleanPath;
    }
}

// Helper function for URLs (Client-side links)
if (!function_exists('url')) {
    function url($path = '') {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        
        // Handle Vercel or Root deployment
        $baseUrl = $protocol . '://' . $host;
        
        $cleanPath = ltrim($path, '/');
        return $baseUrl . '/' . $cleanPath;
    }
}

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
