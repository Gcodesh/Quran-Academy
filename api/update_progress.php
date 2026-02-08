<?php
require_once '../includes/auth_middleware.php';
require_once '../includes/config/database.php';
require_once '../includes/classes/Database.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$lesson_id = $_POST['lesson_id'] ?? null;
$course_id = $_POST['course_id'] ?? null;
$status = $_POST['status'] ?? 'in_progress';

if (!$lesson_id || !$course_id) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

$db = (new Database())->getConnection();

try {
    // Check if progress record exists
    $stmt = $db->prepare("SELECT id FROM lesson_progress_detailed WHERE user_id = ? AND lesson_id = ?");
    $stmt->execute([$user_id, $lesson_id]);
    $exists = $stmt->fetch();

    if ($exists) {
        $stmt = $db->prepare("UPDATE lesson_progress_detailed SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ? AND lesson_id = ?");
        $stmt->execute([$status, $user_id, $lesson_id]);
    } else {
        $stmt = $db->prepare("INSERT INTO lesson_progress_detailed (user_id, lesson_id, status) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $lesson_id, $status]);
    }

    // Update global enrollment progress
    // Count total lessons
    $total_stmt = $db->prepare("SELECT COUNT(*) FROM lessons WHERE course_id = ?");
    $total_stmt->execute([$course_id]);
    $total = $total_stmt->fetchColumn();

    // Count completed lessons
    $comp_stmt = $db->prepare("SELECT COUNT(*) FROM lesson_progress_detailed pd JOIN lessons l ON pd.lesson_id = l.id WHERE pd.user_id = ? AND l.course_id = ? AND pd.status = 'completed'");
    $comp_stmt->execute([$user_id, $course_id]);
    $completed = $comp_stmt->fetchColumn();

    $percent = ($total > 0) ? round(($completed / $total) * 100) : 0;

    $stmt = $db->prepare("UPDATE enrollments SET progress_percentage = ? WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$percent, $user_id, $course_id]);

    // --- GAMIFICATION & CERTIFICATES ---
    if ($status === 'completed') {
        require_once '../src/Services/GamificationService.php';
        $gamification = new \App\Services\GamificationService($db);
        
        // Award points for lesson completion
        $gamification->awardPoints($user_id, 10, "إكمال درس في دورة علمية");

        // If course is 100% complete, issue certificate
        if ($percent == 100) {
            require_once '../src/Services/CertificateGenerator.php';
            $certService = new \App\Services\CertificateGenerator($db);
            $certHash = $certService->issueCertificate($user_id, $course_id);
            
            // Award bonus points for course completion
            $gamification->awardPoints($user_id, 100, "إكمال دورة تدريبية بنجاح");
        }
    }

    echo json_encode(['success' => true, 'progress' => $percent, 'issued_cert' => ($percent == 100)]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
