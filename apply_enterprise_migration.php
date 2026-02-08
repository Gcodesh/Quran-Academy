<?php
require_once 'includes/config/database.php';
require_once 'includes/classes/Database.php';

$db = new Database();
$conn = $db->getConnection();

$sql = file_get_contents('migrations/003_enterprise_lessons_schema.sql');

try {
    // Split by semicolon and run each statement
    // Using simple regex to handle potential semicolons in strings is risky but okay here
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $stmt) {
        $conn->exec($stmt);
    }
    echo "Enterprise Migration applied successfully! ✅";
} catch (PDOException $e) {
    echo "Error applying migration: " . $e->getMessage();
}
?>
