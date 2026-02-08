<?php
// Run migration script
require_once 'includes/config/database.php';
require_once 'includes/classes/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $sql = file_get_contents('database/migrations/gamification_system.sql');
    
    /**
     * PDO exec() doesn't always handle multiple queries well with some drivers.
     * Splitting by semicolon as a safety measure for this migration.
     */
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($queries as $query) {
        if (!empty($query)) {
            $conn->exec($query);
        }
    }
    
    echo "Migration successful! ✅\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
