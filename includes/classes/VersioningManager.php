<?php
namespace App\Classes;

use PDO;

class VersioningManager {
    private $db;

    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    /**
     * Create a new version of a lesson
     */
    public function createVersion($lessonId, $userId, $changeSummary = '') {
        // Fetch current lesson data
        $stmt = $this->db->prepare("SELECT * FROM lessons WHERE id = ?");
        $stmt->execute([$lessonId]);
        $lesson = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lesson) return false;

        // Get next version number
        $stmt = $this->db->prepare("SELECT MAX(version_number) FROM lesson_versions WHERE lesson_id = ?");
        $stmt->execute([$lessonId]);
        $nextVersion = ($stmt->fetchColumn() ?: 0) + 1;

        // Insert into versions
        $sql = "INSERT INTO lesson_versions (lesson_id, version_number, title, content, media_url, media_type, media_provider, created_by, change_summary) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $lessonId,
            $nextVersion,
            $lesson['title'],
            $lesson['content'],
            $lesson['media_url'],
            $lesson['media_type'],
            $lesson['media_provider'],
            $userId,
            $changeSummary
        ]);
    }

    /**
     * Get version history for a lesson
     */
    public function getHistory($lessonId) {
        $stmt = $this->db->prepare("
            SELECT v.*, u.full_name as author_name 
            FROM lesson_versions v 
            LEFT JOIN users u ON v.created_by = u.id 
            WHERE v.lesson_id = ? 
            ORDER BY v.version_number DESC
        ");
        $stmt->execute([$lessonId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Rollback a lesson to a specific version
     */
    public function rollback($lessonId, $versionNumber, $adminId) {
        // Fetch version data
        $stmt = $this->db->prepare("SELECT * FROM lesson_versions WHERE lesson_id = ? AND version_number = ?");
        $stmt->execute([$lessonId, $versionNumber]);
        $version = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$version) return false;

        // Create a backup of current state before rollback
        $this->createVersion($lessonId, $adminId, "Pre-rollback backup from version $versionNumber");

        // Update lesson with version data
        $sql = "UPDATE lessons SET title = ?, content = ?, media_url = ?, media_type = ?, media_provider = ?, status = 'draft' 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $version['title'],
            $version['content'],
            $version['media_url'],
            $version['media_type'],
            $version['media_provider'],
            $lessonId
        ]);
    }
}
