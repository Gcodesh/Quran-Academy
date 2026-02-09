<?php
// Vercel Serverless Function Entry Point
// This file handles all requests and routes them to the appropriate PHP files

// Get the request URI
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// Remove query string if present
$path = parse_url($requestUri, PHP_URL_PATH);

// Map root to home page
if ($path === '/' || $path === '/index.php') {
    $path = '/pages/home.php';
}

// Build the file path
$filePath = __DIR__ . '/..' . $path;

// Check if file exists
if (file_exists($filePath) && is_file($filePath)) {
    // Change to the file's directory
    chdir(dirname($filePath));
    
    // Include the requested file
    require $filePath;
} else {
    // 404 Not Found
    http_response_code(404);
    echo "404 - Page Not Found: " . htmlspecialchars($path);
}
