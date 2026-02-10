<?php
namespace App\Repositories;

require_once __DIR__ . '/BaseRepository.php';

use PDO;

class CourseRepository extends BaseRepository {
    protected $table = 'courses';

    public function findWithDetails($id) {
        $sql = "SELECT c.*, 
                       u.full_name as teacher_name, 
                       cat.name as category_name, 
                       cat.slug as category_slug
                FROM {$this->table} c
                LEFT JOIN users u ON c.teacher_id = u.id
                LEFT JOIN categories cat ON c.category_id = cat.id
                WHERE c.id = :id 
                LIMIT 1";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAllPublished($limit = 10, $offset = 0) {
        $sql = "SELECT c.*, 
                       u.full_name as teacher_name, 
                       cat.name as category_name, 
                       cat.slug as category_slug
                FROM {$this->table} c
                LEFT JOIN users u ON c.teacher_id = u.id
                LEFT JOIN categories cat ON c.category_id = cat.id
                WHERE c.status = 'published'
                ORDER BY c.created_at DESC
                LIMIT :limit OFFSET :offset";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByCategory($categorySlug) {
        $sql = "SELECT c.*, 
                       u.full_name as teacher_name, 
                       cat.name as category_name, 
                       cat.slug as category_slug
                FROM {$this->table} c
                LEFT JOIN users u ON c.teacher_id = u.id
                JOIN categories cat ON c.category_id = cat.id
                WHERE c.status = 'published' AND cat.slug = :slug
                ORDER BY c.created_at DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $categorySlug]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
