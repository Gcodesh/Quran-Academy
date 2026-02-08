-- Migration for Advanced Gamification & Certification System

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Update users table
ALTER TABLE users ADD COLUMN IF NOT EXISTS points INT DEFAULT 0;
ALTER TABLE users ADD COLUMN IF NOT EXISTS rank_level VARCHAR(50) DEFAULT 'طالب مبتدئ';

-- 2. Create user_points_history table
CREATE TABLE IF NOT EXISTS user_points_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    points_added INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. Create certificates table
CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    cert_hash VARCHAR(100) UNIQUE NOT NULL,
    issue_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    score_percent DECIMAL(5,2) DEFAULT 100.00,
    is_gold BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

SET FOREIGN_KEY_CHECKS = 1;
