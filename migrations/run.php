<?php

require_once __DIR__ . '/../src/bootstrap.php';

use App\Database\Database;

class MigrationRunner {
    private $pdo;
    private $migrationsDir;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
        $this->migrationsDir = __DIR__;
    }

    public function init() {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function migrate() {
        $this->init();

        $files = glob($this->migrationsDir . '/*.sql');
        sort($files);

        $executed = $this->getExecutedMigrations();

        foreach ($files as $file) {
            $filename = basename($file);
            if (!in_array($filename, $executed)) {
                echo "Running migration: $filename\n";
                $this->runMigration($file);
                $this->recordMigration($filename);
                echo "Completed: $filename\n";
            }
        }
        
        echo "All migrations are up to date.\n";
    }

    private function getExecutedMigrations() {
        $stmt = $this->pdo->query("SELECT migration FROM migrations");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function runMigration($file) {
        $sql = file_get_contents($file);
        // Split by semicolon? No, standard PDO exec can run multiple statements usually unless emulated prepares are weird?
        // Better to split if statements are complex. But for init schema, it's safer to use raw exec.
        // However, PDO sometimes fails on multiple statements. 
        // We will try to execute it as a block.
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            die("Migration failed: " . basename($file) . " Error: " . $e->getMessage());
        }
    }

    private function recordMigration($filename) {
        $stmt = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES (?)");
        $stmt->execute([$filename]);
    }
}

// Run if called directly
if (php_sapi_name() == 'cli') {
    $runner = new MigrationRunner();
    $runner->migrate();
}
