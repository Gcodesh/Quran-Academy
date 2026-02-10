<?php
namespace App\Repositories;

require_once __DIR__ . '/BaseRepository.php';

use PDO;

class MigrationRepository extends BaseRepository {
    protected $table = 'migrations';

    public function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);
    }

    public function getExecutedMigrations() {
        $this->createTableIfNotExists();
        $stmt = $this->db->query("SELECT migration FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function logMigration($migration, $batch = 1) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (migration, batch) VALUES (:migration, :batch)");
        $stmt->execute(['migration' => $migration, 'batch' => $batch]);
    }
    
    public function getLastBatchNumber() {
        $this->createTableIfNotExists();
        $stmt = $this->db->query("SELECT MAX(batch) FROM {$this->table}");
        return (int) $stmt->fetchColumn() ?: 0;
    }
}
