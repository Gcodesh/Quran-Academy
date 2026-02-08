<?php
require_once 'includes/config/database.php';
require_once 'includes/classes/Database.php';

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->prepare("UPDATE lessons SET status = 'published' WHERE status IS NULL OR status = 'draft'");
    $stmt->execute();
    echo "Fixed status for " . $stmt->rowCount() . " lessons. ✅";
    
    // Also fix courses
    $stmt = $conn->prepare("UPDATE courses SET status = 'published' WHERE status = 'pending' OR status = 'draft'");
    $stmt->execute();
    echo "\nFixed status for " . $stmt->rowCount() . " courses. ✅";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
