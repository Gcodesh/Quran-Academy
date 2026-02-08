<?php
/**
 * API Tests
 * Tests for API endpoints structure and responses
 */

require_once __DIR__ . '/bootstrap.php';

$runner = new TestRunner();

// Test 1: Auth API file exists
$runner->addTest('Auth API file exists', function() {
    assertTrue(file_exists(BASE_PATH . '/api/auth.php'), 'auth.php should exist');
});

// Test 2: Courses API file exists
$runner->addTest('Courses API file exists', function() {
    assertTrue(file_exists(BASE_PATH . '/api/courses.php'), 'courses.php should exist');
});

// Test 3: Progress API file exists
$runner->addTest('Progress API file exists', function() {
    assertTrue(file_exists(BASE_PATH . '/api/progress.php'), 'progress.php should exist');
});

// Test 4: Notifications API file exists
$runner->addTest('Notifications API file exists', function() {
    assertTrue(file_exists(BASE_PATH . '/api/notifications.php'), 'notifications.php should exist');
});

// Test 5: Lessons directory exists
$runner->addTest('Lessons API directory exists', function() {
    assertTrue(is_dir(BASE_PATH . '/api/lessons'), 'lessons directory should exist');
    assertTrue(file_exists(BASE_PATH . '/api/lessons/index.php'), 'lessons/index.php should exist');
    assertTrue(file_exists(BASE_PATH . '/api/lessons/single.php'), 'lessons/single.php should exist');
});

// Test 6: Messages directory exists
$runner->addTest('Messages API directory exists', function() {
    assertTrue(is_dir(BASE_PATH . '/api/messages'), 'messages directory should exist');
    assertTrue(file_exists(BASE_PATH . '/api/messages/index.php'), 'messages/index.php should exist');
    assertTrue(file_exists(BASE_PATH . '/api/messages/single.php'), 'messages/single.php should exist');
});

// Test 7: Database tables exist
$runner->addTest('Required database tables exist', function() {
    $db = new Database();
    $conn = $db->getConnection();
    
    $tables = ['users', 'courses', 'lessons', 'messages', 'progress', 'enrollments'];
    
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        assertTrue($stmt->rowCount() > 0, "Table '$table' should exist");
    }
});

// Test 8: Users table structure
$runner->addTest('Users table has required columns', function() {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $required = ['id', 'email', 'role'];
    foreach ($required as $col) {
        assertTrue(in_array($col, $columns), "Users table should have '$col' column");
    }
});

// Test 9: Courses table structure
$runner->addTest('Courses table has required columns', function() {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->query("DESCRIBE courses");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $required = ['id', 'title', 'status'];
    foreach ($required as $col) {
        assertTrue(in_array($col, $columns), "Courses table should have '$col' column");
    }
});

// Test 10: Config files exist
$runner->addTest('Config files exist', function() {
    assertTrue(file_exists(BASE_PATH . '/includes/config/database.php'), 'database.php should exist');
    assertTrue(file_exists(BASE_PATH . '/.env') || file_exists(BASE_PATH . '/.env.example'), '.env file should exist');
});

// Run all tests
$success = $runner->run();
exit($success ? 0 : 1);
?>
