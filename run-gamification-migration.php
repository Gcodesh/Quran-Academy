<?php
require_once __DIR__ . '/includes/config/root.php';
require_once path('includes/config/database.php');
require_once path('includes/classes/Database.php');

$db = (new Database())->getConnection();

echo "<h1>Starting Gamification Migration...</h1>";

$queries = [
    // 1. Add columns to users table if not exists
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS points INT DEFAULT 0",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS `rank` VARCHAR(50) DEFAULT 'mubtadi'",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS total_points INT DEFAULT 0",
    
    // 2. Create courses table if not exists (just in case)
    "CREATE TABLE IF NOT EXISTS courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        thumbnail VARCHAR(255),
        price DECIMAL(10,2) DEFAULT 0.00,
        level VARCHAR(50) DEFAULT 'beginner',
        status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
        teacher_id INT,
        category_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    // 3. Create modules (sections) table
    "CREATE TABLE IF NOT EXISTS course_modules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    )",

    // 4. Create lessons table
    "CREATE TABLE IF NOT EXISTS lessons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        module_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        content_type ENUM('video', 'text', 'quiz') NOT NULL,
        content_url VARCHAR(255),
        duration_minutes INT DEFAULT 0,
        sort_order INT DEFAULT 0,
        is_free TINYINT(1) DEFAULT 0,
        points_reward INT DEFAULT 10,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (module_id) REFERENCES course_modules(id) ON DELETE CASCADE
    )",
    
    // 5. Create user_progress table
    "CREATE TABLE IF NOT EXISTS user_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        lesson_id INT NOT NULL,
        completed TINYINT(1) DEFAULT 0,
        completed_at TIMESTAMP NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
    )",
    
    // 6. Create certificates table
    "CREATE TABLE IF NOT EXISTS certificates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        course_id INT NOT NULL,
        certificate_code VARCHAR(100) UNIQUE NOT NULL,
        issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    )"
];

foreach ($queries as $sql) {
    try {
        $db->exec($sql);
        echo "<p style='color: green;'>Executed: " . htmlspecialchars(substr($sql, 0, 50)) . "...</p>";
    } catch (PDOException $e) {
        // Ignore "Duplicate column" error (1060) safely
        if ($e->getCode() == '42S21' || strpos($e->getMessage(), 'Duplicate column') !== false) {
             echo "<p style='color: orange;'>Skipped (Already exists): " . htmlspecialchars(substr($sql, 0, 50)) . "...</p>";
        } else {
             echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
}

echo "<h2>Migration Completed!</h2>";
?>
