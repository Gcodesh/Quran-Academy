<?php
// Vercel Serverless Function Entry Point
// This file handles all requests and routes them to the appropriate PHP files

// Define project root for all includes
define('PROJECT_ROOT', dirname(__DIR__));

// Get the request URI
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// Remove query string if present
$path = parse_url($requestUri, PHP_URL_PATH);

// Map root to home page
if ($path === '/' || $path === '/index.php') {
    $path = '/pages/home.php';
}

// Build the file path
$filePath = PROJECT_ROOT . $path;

// Check if file exists
if (file_exists($filePath) && is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
    // Change working directory to the file's directory (for relative paths like ../includes/)
    chdir(dirname($filePath));
    
    // Include the requested file
    require $filePath;
    exit;
} else {
    // 404 Not Found
    http_response_code(404);
    echo "<!DOCTYPE html><html><head><title>404 Not Found</title></head><body>";
    echo "<h1>404 - Page Not Found</h1>";
    echo "<p>The requested page could not be found: " . htmlspecialchars($path) . "</p>";
    echo "</body></html>";
    exit;
}
