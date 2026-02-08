<?php

namespace App\Models;

class Teacher extends User {
    
    public function getMyCourses() {
        $sql = "SELECT * FROM courses WHERE teacher_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createCourse(array $data) {
        $course = new Course();
        $data['teacher_id'] = $this->id;
        return $course->create($data);
    }
}
