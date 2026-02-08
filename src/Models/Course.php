<?php

namespace App\Models;

class Course extends Model {
    protected $table = 'courses';

    public function create(array $data) {
        if (!$this->validate($data)) {
            return false;
        }

        $sql = "INSERT INTO courses (title, description, teacher_id, category_id, price, thumbnail, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $status = $data['status'] ?? 'draft';
        $price = $data['price'] ?? 0.00;
        $thumbnail = $data['thumbnail'] ?? null;

        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt->execute([
                $data['title'], 
                $data['description'], 
                $data['teacher_id'], 
                $data['category_id'], 
                $price, 
                $thumbnail,
                $status
            ])) {
                return $this->db->lastInsertId();
            }
        } catch (\PDOException $e) {
            $this->errors[] = $e->getMessage();
        }
        return false;
    }

    public function validate(array $data) {
        if (empty($data['title'])) $this->errors[] = "Title is required";
        if (empty($data['description'])) $this->errors[] = "Description is required";
        if (empty($data['teacher_id'])) $this->errors[] = "Teacher ID is required";
        if (empty($data['category_id'])) $this->errors[] = "Category is required";
        return empty($this->errors);
    }
    
    public function getLessons($courseId) {
        $lesson = new Lesson();
        return $lesson->findByCourseId($courseId);
    }
}
