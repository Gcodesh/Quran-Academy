<?php
namespace App\Services;

require_once __DIR__ . '/../Repositories/MigrationRepository.php';

use App\Repositories\MigrationRepository;
use PDOException;

class MigrationRunner {
    private $repo;
    private $migrationsDir;

    public function __construct($migrationsDir) {
        $this->repo = new MigrationRepository();
        $this->migrationsDir = $migrationsDir;
    }

    public function run() {
        $executed = $this->repo->getExecutedMigrations();
        $files = glob($this->migrationsDir . '/*.sql');
        
        $newBatch = $this->repo->getLastBatchNumber() + 1;
        $count = 0;
        $output = [];

        foreach ($files as $file) {
            $filename = basename($file);
            
            if (in_array($filename, $executed)) {
                continue;
            }

            try {
                $sql = file_get_contents($file);
                // Execute SQL (handling multiple statements if needed)
                // PDO exec handles multiple statements in some drivers, but separation is safer
                // For now, assuming standard SQL dumps supported by PDO exec
                // Ensure getDb exists (Deployment sync check)
                if (!method_exists($this->repo, 'getDb')) {
                    throw new \Exception("System update in progress. Please refresh in 30 seconds. (BaseRepository outdated)");
                }
                
                $this->repo->getDb()->exec($sql);
                
                $this->repo->logMigration($filename, $newBatch);
                $output[] = "✅ Migrated: $filename";
                $count++;
            } catch (PDOException $e) {
                // If column already exists (1054/1060), distinct logic could go here
                // For now, creating a resilient check is better in SQL itself (IF NOT EXISTS)
                $output[] = "❌ Failed: $filename - " . $e->getMessage();
                return $output; // Stop on error
            }
        }

        if ($count === 0) {
            $output[] = "No new migrations found.";
        }

        return $output;
    }
    
    // Getter for DB connection to run raw queries if publicly needed
    public function getDb() {
         return $this->repo->getDb(); // Hacky but BaseRepository doesn't expose it public by default.
         // Actually BaseRepository properties are protected. 
         // Let's rely on standard repo usage or make a getter there if needed.
    }
}
