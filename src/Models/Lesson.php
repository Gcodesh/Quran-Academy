<?php

namespace App\Models;

class Lesson extends Model {
    protected $table = 'lessons';

    public function findByCourseId($courseId) {
        $stmt = $this->db->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY order_number ASC");
        $stmt->execute([$courseId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create(array $data) {
        if (!$this->validate($data)) return false;

        $sql = "INSERT INTO lessons (course_id, title, content, video_url, order_number) VALUES (?, ?, ?, ?, ?)";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt->execute([
                $data['course_id'],
                $data['title'],
                $data['content'] ?? '',
                $data['video_url'] ?? null,
                $data['order_number'] ?? 0
            ])) {
                return $this->db->lastInsertId();
            }
        } catch (\PDOException $e) {
            $this->errors[] = $e->getMessage();
        }
        return false;
    }

    public function validate(array $data) {
        if (empty($data['course_id'])) $this->errors[] = "Course ID is required";
        if (empty($data['title'])) $this->errors[] = "Title is required";
        return empty($this->errors);
    }
}
