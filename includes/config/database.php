<?php
// Load .env manually to avoid Composer dependency for now
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}
require_once __DIR__ . '/root.php';

// Check for environment variables (Vercel uses getenv, local uses $_ENV or defaults)
$db_host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost');
$db_port = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '3306');
$db_user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root');
$db_pass = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? '');
$db_name = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'islamic_education');

define('DB_HOST', $db_host);
define('DB_PORT', $db_port);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);
define('DB_NAME', $db_name);
?>