<?php
namespace App\Services;

use PDO;

class CertificateGenerator {
    private $db;
    
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Generate a new verifiable certificate record.
     */
    public function issueCertificate(int $userId, int $course_id, float $score = 100.00) {
        // 1. Check if already issued
        $stmt = $this->db->prepare("SELECT cert_hash FROM certificates WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$userId, $course_id]);
        $existing = $stmt->fetchColumn();
        
        if ($existing) return $existing;

        // 2. Generate Unique Hash
        $certHash = bin2hex(random_bytes(16)); // Secure random hex string
        $isGold = ($score >= 95.00);

        // 3. Save to database
        $stmt = $this->db->prepare("INSERT INTO certificates (user_id, course_id, cert_hash, score_percent, is_gold) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $course_id, $certHash, $score, $isGold ? 1 : 0]);

        return $certHash;
    }

    /**
     * Get certificate details for verification.
     */
    public function verifyCertificate(string $hash) {
        $sql = "SELECT c.*, u.full_name as student_name, crs.title as course_title, u.rank_level 
                FROM certificates c
                JOIN users u ON c.user_id = u.id
                JOIN courses crs ON c.course_id = crs.id
                WHERE c.cert_hash = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hash]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
