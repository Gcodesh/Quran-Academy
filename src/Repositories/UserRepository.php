<?php
namespace App\Repositories;

require_once __DIR__ . '/BaseRepository.php';

use PDO;

class UserRepository extends BaseRepository {
    protected $table = 'users';

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePoints($userId, $points) {
        // Increment points safely
        $stmt = $this->db->prepare("UPDATE {$this->table} SET points = points + :points, total_points = total_points + :points WHERE id = :id");
        return $stmt->execute(['points' => $points, 'id' => $userId]);
    }

    public function getGamificationStats($userId) {
        // Safe selection of gamification fields
        // 'rank' is a reserved keyword in MySQL 8.0+/TiDB, must be backticked
        $stmt = $this->db->prepare("SELECT id, full_name, points, `rank`, total_points FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateRank($userId, $rank) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET `rank` = :rank WHERE id = :id");
        return $stmt->execute(['rank' => $rank, 'id' => $userId]);
    }
}
