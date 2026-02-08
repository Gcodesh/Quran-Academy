<?php
require_once 'includes/config/database.php';
require_once 'includes/classes/Database.php';

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->query("DESCRIBE lessons");
    echo "Lessons Table Schema:\n";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    
    $stmt = $conn->query("SHOW TABLES");
    echo "\nTables in Database:\n";
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
