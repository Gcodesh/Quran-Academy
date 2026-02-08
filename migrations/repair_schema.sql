-- Repair Lessons Table Schema
-- Adds missing core and enterprise columns

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Add missing core columns if they don't exist
ALTER TABLE lessons ADD COLUMN IF NOT EXISTS media_url VARCHAR(255) AFTER pdf_url;
ALTER TABLE lessons ADD COLUMN IF NOT EXISTS media_type ENUM('video', 'audio', 'pdf', 'text') DEFAULT 'text' AFTER media_url;
ALTER TABLE lessons ADD COLUMN IF NOT EXISTS order_number INT DEFAULT 0 AFTER media_type;

-- 2. Add enterprise columns if they don't exist
ALTER TABLE lessons ADD COLUMN IF NOT EXISTS media_provider ENUM('youtube', 'vimeo', 'local', 's3', 'drive') DEFAULT 'local' AFTER media_type;
ALTER TABLE lessons ADD COLUMN IF NOT EXISTS duration INT DEFAULT 0 AFTER media_provider;
ALTER TABLE lessons ADD COLUMN IF NOT EXISTS status ENUM('draft', 'review', 'published', 'rejected') DEFAULT 'draft' AFTER order_number;
ALTER TABLE lessons ADD COLUMN IF NOT EXISTS moderation_notes TEXT AFTER status;

-- 3. Ensure lesson_progress_detailed exists
CREATE TABLE IF NOT EXISTS lesson_progress_detailed (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lesson_id INT NOT NULL,
    status ENUM('not_started', 'in_progress', 'completed') DEFAULT 'not_started',
    last_position INT DEFAULT 0,
    time_spent INT DEFAULT 0,
    completion_percent INT DEFAULT 0,
    completed_at TIMESTAMP NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, lesson_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
);

SET FOREIGN_KEY_CHECKS = 1;
