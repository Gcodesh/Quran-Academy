<?php
require_once __DIR__ . '/includes/config/root.php';
require_once path('includes/config/database.php');
require_once path('includes/classes/Database.php');

$db = (new Database())->getConnection();

echo "<!DOCTYPE html><html dir='ltr'><head><style>body{font-family:monospace;background:#f4f4f4;padding:20px} table{width:100%;border-collapse:collapse;margin-bottom:20px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,0.1)} th,td{padding:10px;border:1px solid #ddd;text-align:left} th{background:#2A2A2A;color:#fff} h2{color:#0E5F4B;border-bottom:2px solid #5FB3A2;padding-bottom:5px}</style></head><body>";

echo "<h1>🔍 Database Schema Diagnosis</h1>";

// Get all tables
$stmt = $db->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    echo "<h2>Table: {$table}</h2>";
    
    // Get columns
    try {
        $stmt = $db->query("DESCRIBE {$table}");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table>";
        echo "<thead><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr></thead>";
        echo "<tbody>";
        foreach ($columns as $col) {
            echo "<tr>";
            foreach ($col as $key => $val) {
                echo "<td>" . ($val === null ? 'NULL' : htmlspecialchars($val)) . "</td>";
            }
            echo "</tr>";
        }
        echo "</tbody></table>";
    } catch (PDOException $e) {
        echo "<p style='color:red'>Error describing table: " . $e->getMessage() . "</p>";
    }
}

echo "</body></html>";
?>
