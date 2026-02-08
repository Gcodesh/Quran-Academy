<?php

namespace App\Models;

use PDO;

class User extends Model {
    protected $table = 'users'; // Explicitly defined
    
    public $id;
    public $full_name;
    public $email;
    public $pass_hash;
    public $role;
    public $status;
    public $created_at;

    public function create(array $data) {
        if (!$this->validate($data)) {
            return false;
        }

        $sql = "INSERT INTO users (full_name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = $data['role'] ?? 'student';
        $status = $data['status'] ?? 'active';

        try {
            if ($stmt->execute([$data['full_name'], $data['email'], $passwordHash, $role, $status])) {
                return $this->db->lastInsertId();
            }
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
        }
        return false;
    }

    public function update($id, array $data) {
        // Assume partial update allowed
        // Simplified Logic for now
        $allowed = ['full_name', 'email', 'role', 'status'];
        $sets = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $sets[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($sets)) return true; // Nothing to update
        
        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $sets) . " WHERE id = ?";
        return $this->db->prepare($sql)->execute($values);
    }
    
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function validate(array $data) {
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid email address.";
        }
        if (empty($data['full_name'])) {
            $this->errors[] = "Full name is required.";
        }
        if (isset($data['password']) && strlen($data['password']) < 6) {
            $this->errors[] = "Password must be at least 6 characters.";
        }
        return empty($this->errors);
    }
}
