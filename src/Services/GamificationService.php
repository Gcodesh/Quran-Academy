<?php
namespace App\Services;

use App\Repositories\UserRepository;
use PDO;

class GamificationService {
    private $userRepo;
    
    const RANKS = [
        ['label' => 'طالب مبتدئ', 'threshold' => 0],
        ['label' => 'مجتهد', 'threshold' => 501],
        ['label' => 'باحث', 'threshold' => 1501],
        ['label' => 'متقن', 'threshold' => 3001]
    ];

    public function __construct() {
        $this->userRepo = new UserRepository();
    }

    /**
     * Award points to a user and check for rank level updates.
     */
    public function awardPoints(int $userId, int $points, string $reason) {
        try {
            // 1. Add to history (Table missing in production, disabled for stability)
            // TODO: Create user_points_history table or use audit_logs
            // error_log("Points awarded: User $userId, Points $points, Reason: $reason");

            // 2. Update user total
            $this->userRepo->updatePoints($userId, $points);

            // 3. Check for Rank Update
            $this->updateRankLevel($userId);

            return true;
        } catch (\Exception $e) {
            error_log("Gamification Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Recalculate and update the rank level based on total points.
     */
    private function updateRankLevel(int $userId) {
        $stats = $this->userRepo->getGamificationStats($userId);
        if (!$stats) return;

        $totalPoints = $stats['total_points'] ?? 0;

        $newRank = 'طالب مبتدئ';
        foreach (array_reverse(self::RANKS) as $rank) {
            if ($totalPoints >= $rank['threshold']) {
                $newRank = $rank['label'];
                break;
            }
        }

        // Only update if rank changed
        if (($stats['rank'] ?? '') !== $newRank) {
            $this->userRepo->updateRank($userId, $newRank);
        }
    }

    /**
     * Get user's current gamification profile.
     */
    public function getUserStats(int $userId) {
        $stats = $this->userRepo->getGamificationStats($userId);

        if (!$stats) return null;

        // Calculate progress to next level
        $currentPoints = $stats['points'];
        $nextRank = null;
        $progress = 100;

        foreach (self::RANKS as $index => $rank) {
            if ($currentPoints < $rank['threshold']) {
                $nextRank = $rank;
                $prevThreshold = isset(self::RANKS[$index - 1]) ? self::RANKS[$index - 1]['threshold'] : 0;
                $range = $rank['threshold'] - $prevThreshold;
                $currentInRange = $currentPoints - $prevThreshold;
                $progress = ($range > 0) ? round(($currentInRange / $range) * 100) : 0;
                break;
            }
        }

        $stats['next_rank'] = $nextRank;
        $stats['progress_percent'] = $progress;
        
        return $stats;
    }
}
