<?php
require_once 'includes/config/database.php';
require_once 'includes/classes/Database.php';

$db = new Database();
$conn = $db->getConnection();

$sql = file_get_contents('migrations/repair_schema.sql');

try {
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $conn->exec($stmt);
        }
    }
    echo "Schema repaired successfully! ✅";
} catch (PDOException $e) {
    echo "Error repairing schema: " . $e->getMessage();
}
?>
