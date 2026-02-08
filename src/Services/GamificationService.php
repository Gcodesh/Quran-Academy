<?php
namespace App\Services;

use PDO;

class GamificationService {
    private $db;
    
    const RANKS = [
        ['label' => 'طالب مبتدئ', 'threshold' => 0],
        ['label' => 'مجتهد', 'threshold' => 501],
        ['label' => 'باحث', 'threshold' => 1501],
        ['label' => 'متقن', 'threshold' => 3001]
    ];

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Award points to a user and check for rank level updates.
     */
    public function awardPoints(int $userId, int $points, string $reason) {
        try {
            $this->db->beginTransaction();

            // 1. Add to history
            $stmt = $this->db->prepare("INSERT INTO user_points_history (user_id, points_added, reason) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $points, $reason]);

            // 2. Update user total
            $stmt = $this->db->prepare("UPDATE users SET points = points + ? WHERE id = ?");
            $stmt->execute([$points, $userId]);

            // 3. Check for Rank Update
            $this->updateRankLevel($userId);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Gamification Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Recalculate and update the rank level based on total points.
     */
    private function updateRankLevel(int $userId) {
        $stmt = $this->db->prepare("SELECT points FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $totalPoints = $stmt->fetchColumn();

        $newRank = 'طالب مبتدئ';
        foreach (array_reverse(self::RANKS) as $rank) {
            if ($totalPoints >= $rank['threshold']) {
                $newRank = $rank['label'];
                break;
            }
        }

        $stmt = $this->db->prepare("UPDATE users SET rank_level = ? WHERE id = ?");
        $stmt->execute([$newRank, $userId]);
    }

    /**
     * Get user's current gamification profile.
     */
    public function getUserStats(int $userId) {
        $stmt = $this->db->prepare("SELECT points, rank_level FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$stats) return null;

        // Calculate progress to next level
        $currentPoints = $stats['points'];
        $nextRank = null;
        $progress = 100;

        foreach (self::RANKS as $index => $rank) {
            if ($currentPoints < $rank['threshold']) {
                $nextRank = $rank;
                $prevThreshold = self::RANKS[$index - 1]['threshold'];
                $range = $rank['threshold'] - $prevThreshold;
                $currentInRange = $currentPoints - $prevThreshold;
                $progress = round(($currentInRange / $range) * 100);
                break;
            }
        }

        $stats['next_rank'] = $nextRank;
        $stats['progress_percent'] = $progress;
        
        return $stats;
    }
}
