<?php
require_once 'includes/config/database.php';
require_once 'includes/classes/Database.php';

$db = new Database();
$conn = $db->getConnection();

$sql = file_get_contents('migrations/004_enterprise_core_enhancements.sql');

try {
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $conn->exec($stmt);
        }
    }
    echo "Enterprise Core Enhancements (Phase 1) applied successfully! ✅";
} catch (PDOException $e) {
    echo "Error applying migration: " . $e->getMessage();
}
?>
