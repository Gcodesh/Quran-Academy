<?php

namespace App\Models;

class Admin extends User {
    
    public function approveCourse($courseId) {
        $sql = "UPDATE courses SET status = 'published' WHERE id = ?";
        return $this->db->prepare($sql)->execute([$courseId]);
    }

    public function banUser($userId) {
        $sql = "UPDATE users SET status = 'banned' WHERE id = ?";
        return $this->db->prepare($sql)->execute([$userId]);
    }
    
    public function getAllUsers() {
        return $this->findAll();
    }
}
