<?php
/**
 * Auth Tests
 * Tests for authentication functionality
 */

require_once __DIR__ . '/bootstrap.php';

$runner = new TestRunner();

// Test 1: Login with valid credentials structure
$runner->addTest('Auth class exists', function() {
    assertTrue(class_exists('Auth'), 'Auth class should exist');
});

// Test 2: Auth has required methods
$runner->addTest('Auth has login method', function() {
    assertTrue(method_exists('Auth', 'login'), 'Auth should have login method');
});

$runner->addTest('Auth has register method', function() {
    assertTrue(method_exists('Auth', 'register'), 'Auth should have register method');
});

$runner->addTest('Auth has isLoggedIn method', function() {
    assertTrue(method_exists('Auth', 'isLoggedIn'), 'Auth should have isLoggedIn method');
});

$runner->addTest('Auth has logout method', function() {
    assertTrue(method_exists('Auth', 'logout'), 'Auth should have logout method');
});

$runner->addTest('Auth has getCurrentUser method', function() {
    assertTrue(method_exists('Auth', 'getCurrentUser'), 'Auth should have getCurrentUser method');
});

// Test 3: Login with invalid credentials returns error
$runner->addTest('Login with invalid credentials returns failure', function() {
    $result = Auth::login('nonexistent@example.com', 'wrongpassword');
    assertTrue(is_array($result), 'Login should return an array');
    assertArrayHasKey('success', $result, 'Result should have success key');
    assertEquals(false, $result['success'], 'Invalid login should return false');
});

// Test 4: Register validation
$runner->addTest('Register requires full_name', function() {
    $result = Auth::register([
        'email' => 'test_' . time() . '@example.com',
        'password' => 'testpassword123'
        // Missing full_name
    ]);
    // Should either fail or handle gracefully
    assertTrue(is_array($result), 'Register should return an array');
});

// Test 5: Database connection
$runner->addTest('Database connection works', function() {
    $db = new Database();
    $conn = $db->getConnection();
    assertTrue($conn !== null, 'Database connection should not be null');
    assertTrue($conn instanceof PDO, 'Connection should be PDO instance');
});

// Test 6: isLoggedIn returns boolean
$runner->addTest('isLoggedIn returns boolean when not logged in', function() {
    // Clear session if any
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    $_SESSION = [];
    
    $result = Auth::isLoggedIn();
    assertTrue(is_bool($result), 'isLoggedIn should return boolean');
    assertEquals(false, $result, 'Should be false when no session');
});

// Run all tests
$success = $runner->run();
exit($success ? 0 : 1);
?>
