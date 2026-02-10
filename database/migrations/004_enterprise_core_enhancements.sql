-- Enterprise Enhancements - Phase 1: Institutional Core
-- Introduces Lesson Versioning and Workflow Engine

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Lesson Versions Table
-- Stores snapshots of lesson content to allow rollbacks and audit history
CREATE TABLE IF NOT EXISTS lesson_versions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lesson_id INT NOT NULL,
    version_number INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    media_url VARCHAR(255),
    media_type VARCHAR(50),
    media_provider VARCHAR(50),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    change_summary TEXT, -- Brief description of what changed in this version
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY (lesson_id, version_number)
);

-- 2. Update Lessons Table with Workflow Status
-- status: draft (visible only to teacher/admin), 
--         review (submitted for approval, locked for teacher), 
--         published (visible to students),
--         rejected (returned to teacher with notes)
ALTER TABLE lessons ADD COLUMN IF NOT EXISTS status ENUM('draft', 'review', 'published', 'rejected') DEFAULT 'draft' AFTER order_number;
ALTER TABLE lessons ADD COLUMN IF NOT EXISTS moderation_notes TEXT AFTER status;

-- 3. Enhanced Audit Logs System
-- We'll use the existing audit_logs table but ensure we log workflow transitions
-- This is more of a policy/logic change in the PHP layer.

SET FOREIGN_KEY_CHECKS = 1;
