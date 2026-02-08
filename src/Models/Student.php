<?php

namespace App\Models;

class Student extends User {
    
    public function enroll($courseId) {
        // Enforce role check if needed, but assuming instantiated as Student
        
        // check if already enrolled
        $sql = "SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->id, $courseId]);
        if ($stmt->fetch()) {
            return false; // Already enrolled
        }

        $sql = "INSERT INTO enrollments (user_id, course_id, status) VALUES (?, ?, 'active')";
        return $this->db->prepare($sql)->execute([$this->id, $courseId]);
    }

    public function getEnrolledCourses() {
        $sql = "SELECT c.*, e.progress_percentage, e.status as enrollment_status 
                FROM courses c 
                JOIN enrollments e ON c.id = e.course_id 
                WHERE e.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
