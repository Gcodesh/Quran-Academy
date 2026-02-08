-- Enterprise Lessons Schema Update
-- Introduces Sections and expands Lesson metadata

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Create Sections Table
CREATE TABLE IF NOT EXISTS course_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    order_number INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- 2. Enhance Lessons Table
-- We add section_id and detailed media fields
ALTER TABLE lessons ADD COLUMN section_id INT NULL AFTER course_id;
ALTER TABLE lessons ADD COLUMN lesson_type ENUM('lecture', 'quiz', 'assignment', 'reading', 'exam') DEFAULT 'lecture' AFTER title;
ALTER TABLE lessons ADD COLUMN media_provider ENUM('youtube', 'vimeo', 'local', 's3', 'drive') DEFAULT 'local' AFTER media_type;
ALTER TABLE lessons ADD COLUMN duration INT DEFAULT 0; -- in seconds
ALTER TABLE lessons ADD CONSTRAINT fk_lesson_section FOREIGN KEY (section_id) REFERENCES course_sections(id) ON DELETE SET NULL;

-- 3. Create Detailed Progress Table
CREATE TABLE IF NOT EXISTS lesson_progress_detailed (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lesson_id INT NOT NULL,
    status ENUM('not_started', 'in_progress', 'completed') DEFAULT 'not_started',
    last_position INT DEFAULT 0, -- in seconds for video/audio
    time_spent INT DEFAULT 0, -- total seconds spent
    completion_percent INT DEFAULT 0,
    completed_at TIMESTAMP NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, lesson_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
);

SET FOREIGN_KEY_CHECKS = 1;
