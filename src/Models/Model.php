<?php

namespace App\Models;

use App\Database\Database;
use PDO;

abstract class Model {
    protected $db;
    protected $table;
    protected $fillable = [];
    protected $errors = [];

    public function __construct($db = null) {
        $this->db = $db ?? Database::getInstance()->getConnection();
    }

    public function getErrors() {
        return $this->errors;
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM " . $this->table);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // Abstract validate method to enforce implementation
    abstract public function validate(array $data);
}
