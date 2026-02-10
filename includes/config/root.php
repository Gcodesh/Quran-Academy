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
        
        // Calculate base path relative to server root
        // Example: /islamic-education-platform/pages/home.php -> /islamic-education-platform/
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])); 
        
        // We need to find the root of the project from the current script.
        // Since we don't know the depth, let's rely on a fixed structure relative to PROJECT_ROOT?
        // No, file paths don't help with URLs directly without mapping.
        
        // ROBUST XAMPP/Vercel FIX:
        // Detect if we are in a subfolder structure known to be part of the app
        $base = $scriptDir;
        
        // Remove common subfolders from the path to find the "root" URL
        $base = str_replace(['/pages/dashboard', '/pages/admin', '/pages/teacher', '/pages/auth', '/pages', '/api'], '', $base);
        $base = rtrim($base, '/');
        
        $cleanPath = ltrim($path, '/');
        
        // Check if path is absolute URL
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // Return root-relative path (e.g., /assets/css/main.css)
        // This avoids mixed content issues (HTTP vs HTTPS)
        return $base . '/' . $cleanPath;
    }
}

// Helper function to safely start session
if (!function_exists('start_active_session')) {
    function start_active_session() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}

// Start session automatically
start_active_session();
