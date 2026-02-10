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
                // Ensure getDb exists (Deployment sync check)
                if (!method_exists($this->repo, 'getDb')) {
                    throw new \Exception("System update in progress. Please refresh in 30 seconds. (BaseRepository outdated)");
                }
                
                // Split SQL into statements to handle them individually
                // valid for these schema files which likely don't contain complex stored procs with inner semicolons
                $statements = array_filter(array_map('trim', explode(';', $sql)));

                foreach ($statements as $stmt) {
                    if (empty($stmt)) continue;
                    
                    try {
                        $this->repo->getDb()->exec($stmt);
                    } catch (PDOException $e) {
                        // Check for "Already Exists" errors to make migrations idempotent
                        // 1050: Table already exists
                        // 1060: Duplicate column name
                        // 1061: Duplicate key name
                        // 1826: Duplicate foreign key constraint
                        $code = $e->errorInfo[1] ?? 0;
                        if (in_array($code, [1050, 1060, 1061, 1826])) {
                            // Log warning but continue
                            error_log("Migration Warning [$filename]: " . $e->getMessage());
                            continue;
                        }
                        throw $e; // Re-throw critical errors
                    }
                }
                
                $this->repo->logMigration($filename, $newBatch);
                $output[] = "✅ Migrated: $filename";
                $count++;
            } catch (PDOException $e) {
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
