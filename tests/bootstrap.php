<?php
/**
 * Bootstrap file for tests
 * Sets up autoloading and test environment
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load environment if available
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

if (file_exists(BASE_PATH . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
}

// Include required files
require_once BASE_PATH . '/includes/config/database.php';
require_once BASE_PATH . '/includes/classes/Database.php';
require_once BASE_PATH . '/includes/functions/auth.php';
require_once BASE_PATH . '/includes/functions/logger.php';

/**
 * Simple Test Runner
 */
class TestRunner {
    private $passed = 0;
    private $failed = 0;
    private $tests = [];
    
    public function addTest($name, $callback) {
        $this->tests[$name] = $callback;
    }
    
    public function run() {
        echo "\n=== Running Tests ===\n\n";
        
        foreach ($this->tests as $name => $callback) {
            try {
                $callback();
                $this->passed++;
                echo "✓ PASS: $name\n";
            } catch (Exception $e) {
                $this->failed++;
                echo "✗ FAIL: $name - " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n=== Results ===\n";
        echo "Passed: {$this->passed}\n";
        echo "Failed: {$this->failed}\n";
        echo "Total: " . count($this->tests) . "\n";
        
        return $this->failed === 0;
    }
}

/**
 * Assertion helper
 */
function assertTrue($condition, $message = 'Assertion failed') {
    if (!$condition) {
        throw new Exception($message);
    }
}

function assertEquals($expected, $actual, $message = '') {
    if ($expected !== $actual) {
        throw new Exception($message ?: "Expected '$expected' but got '$actual'");
    }
}

function assertNotEmpty($value, $message = 'Value should not be empty') {
    if (empty($value)) {
        throw new Exception($message);
    }
}

function assertArrayHasKey($key, $array, $message = '') {
    if (!isset($array[$key])) {
        throw new Exception($message ?: "Array does not have key '$key'");
    }
}
?>
