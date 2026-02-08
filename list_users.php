<?php
require_once 'includes/config/database.php';
require_once 'includes/classes/Database.php';

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->query("SELECT email, role FROM users LIMIT 10");
    echo "Existing Users:\n";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
