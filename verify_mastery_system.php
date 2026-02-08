<?php
require_once 'includes/config/database.php';
require_once 'includes/classes/Database.php';
require_once 'src/Services/GamificationService.php';
require_once 'src/Services/CertificateGenerator.php';

function log_result($msg, $success = true) {
    echo ($success ? "✅ " : "❌ ") . $msg . "\n";
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // 1. Setup Test User
    $test_email = 'mastery_test@example.com';
    $conn->prepare("DELETE FROM users WHERE email = ?")->execute([$test_email]);
    $conn->prepare("INSERT INTO users (full_name, email, password_hash, role, status, points, rank_level) VALUES (?, ?, 'hash', 'student', 'active', 0, 'طالب مبتدئ')")
         ->execute(['طالب تجربة الإتقان', $test_email]);
    $userId = $conn->lastInsertId();
    
    $gamification = new \App\Services\GamificationService($conn);
    $certService = new \App\Services\CertificateGenerator($conn);
    
    log_result("Test User Created: ID $userId");

    // 2. Test Awarding Points
    $gamification->awardPoints($userId, 100, "اختبار النظام");
    $stats = $gamification->getUserStats($userId);
    if ($stats['points'] == 100) {
        log_result("Points awarding works.");
    } else {
        log_result("Points awarding failed!", false);
    }

    // 3. Test Rank Level Up
    $gamification->awardPoints($userId, 500, "الوصول لرتبة مجتهد");
    $stats = $gamification->getUserStats($userId);
    if ($stats['rank_level'] === 'مجتهد') {
        log_result("Rank Level Up works (Novice -> Diligent).");
    } else {
        log_result("Rank Level Up failed! Current: " . $stats['rank_level'], false);
    }

    // 4. Test Certificate Issuance
    $courseId = 1; // Assuming course ID 1 exists
    $certHash = $certService->issueCertificate($userId, $courseId, 98.5);
    if ($certHash) {
        log_result("Certificate issued successfully: $certHash");
        
        // 5. Test Verification Page Logic
        $verif = $certService->verifyCertificate($certHash);
        if ($verif && $verif['student_name'] === 'طالب تجربة الإتقان' && $verif['is_gold'] == 1) {
            log_result("Certificate verification logic is accurate (including Gold status).");
        } else {
            log_result("Certificate verification logic failed!", false);
        }
    } else {
        log_result("Certificate issuance failed!", false);
    }

    // Clean up
    $conn->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
    log_result("Test completed and cleaned up.");

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
