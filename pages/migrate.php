<?php
// Simple migration runner for production
// Access this file ONCE via browser to run migrations, then delete it

require_once __DIR__ . '/../includes/config/database.php';
require_once __DIR__ . '/../includes/classes/Database.php';

echo "<!DOCTYPE html><html><head><title>Database Migration</title></head><body>";
echo "<h1>Running Database Migrations...</h1>";
echo "<pre>";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "✅ Connected to database\n\n";
    
    // Run migration files
    $migrationFiles = [
        __DIR__ . '/../migrations/001_initial_schema.sql',
        __DIR__ . '/../migrations/002_add_gamification.sql',
        __DIR__ . '/../migrations/003_enterprise_lessons_schema.sql',
        __DIR__ . '/../migrations/004_enterprise_core_enhancements.sql',
    ];
    
    foreach ($migrationFiles as $file) {
        if (!file_exists($file)) {
            echo "⚠️  Skipping missing file: " . basename($file) . "\n";
            continue;
        }
        
        echo "📄 Running: " . basename($file) . "\n";
        
        $sql = file_get_contents($file);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $stmt) {
            if (empty($stmt)) continue;
            
            try {
                $conn->exec($stmt);
            } catch (PDOException $e) {
                // Ignore duplicate errors
                $code = $e->errorInfo[1] ?? 0;
                if (!in_array($code, [1050, 1051, 1060, 1061, 1091, 1826])) {
                    echo "   ❌ Error: " . $e->getMessage() . "\n";
                }
            }
        }
        
        echo "   ✅ Completed\n\n";
    }
    
    echo "\n✅ All migrations completed successfully!\n";
    echo "\n⚠️  IMPORTANT: Delete this file (migrate.php) for security!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "</pre></body></html>";
