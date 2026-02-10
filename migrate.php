<?php
// Migration Entry Point for Vercel & Local use
// Access via URL: /migrate.php?key=YOUR_SECRET (optional security)

require_once __DIR__ . '/includes/config/root.php';
require_once path('includes/config/database.php');
require_once path('includes/classes/Database.php');
require_once path('src/Services/MigrationRunner.php');

use App\Services\MigrationRunner;

// Basic security check (prevent random public execution if needed)
// if (!isset($_GET['key']) || $_GET['key'] !== 'SECURE_KEY_123') {
//     die('Access Denied');
// }

$migrationsDir = __DIR__ . '/database/migrations';

if (!is_dir($migrationsDir)) {
    die("Error: Migrations directory not found at $migrationsDir");
}

echo "<h1>🚀 Database Migration Runner</h1>";
echo "<pre style='background:#f4f4f4; padding:15px; border-radius:5px;'>";

try {
    $runner = new MigrationRunner($migrationsDir);
    $results = $runner->run();
    
    foreach ($results as $line) {
        echo htmlspecialchars($line) . "\n";
    }
} catch (Exception $e) {
    echo "❌ Critical Error: " . htmlspecialchars($e->getMessage());
}

echo "</pre>";
echo "<p><a href='" . url('') . "'>Return to Home</a></p>";
?>
