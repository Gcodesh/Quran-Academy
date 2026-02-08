<?php
/**
 * Admin Actions API
 * Handles administrative actions like approving courses, banning users, etc.
 */

require_once '../includes/config/database.php';
require_once '../includes/classes/Database.php';

session_start();

// Ensure only admin access
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    die('Unauthorized');
}

$db = (new Database())->getConnection();
$admin_id = $_SESSION['user_id'];

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Validate CSRF (Optional but recommended, assume token passed)
    // $csrf_token = $_POST['csrf_token'] ?? '';
    // if (!verifyCsrf($csrf_token)) die('CSRF Failed');

    if ($action === 'approve_course') {
        $course_id = $_POST['course_id'] ?? 0;
        
        try {
            // Update course status
            $stmt = $db->prepare("UPDATE courses SET status = 'published' WHERE id = ?");
            $stmt->execute([$course_id]);
            
            // Log action
            $log = $db->prepare("INSERT INTO audit_logs (user_id, action, details, severity) VALUES (?, ?, ?, ?)");
            $log->execute([$admin_id, 'approve_course', "Approved course ID: $course_id", 'info']);
            
            // Notify Teacher
            $teacher_id = $db->query("SELECT teacher_id FROM courses WHERE id = $course_id")->fetchColumn();
            if ($teacher_id) {
                $notif = $db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'course')");
                $notif->execute([$teacher_id, 'تم قبول الدورة', 'تمت الموافقة على نشر دورتك بنجاح!', 'course']);
            }
            
            header('Location: ../pages/admin/approvals.php?success=approved');
            exit;
            
        } catch (Exception $e) {
            header('Location: ../pages/admin/approvals.php?error=' . urlencode($e->getMessage()));
            exit;
        }
    }
    
    if ($action === 'reject_course') {
        $course_id = $_POST['course_id'] ?? 0;
        $reason = $_POST['reason'] ?? 'No reason provided';
        
        try {
            // Update course status
            $stmt = $db->prepare("UPDATE courses SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$course_id]);
            
            $log = $db->prepare("INSERT INTO audit_logs (user_id, action, details, severity) VALUES (?, ?, ?, ?)");
            $log->execute([$admin_id, 'reject_course', "Rejected course ID: $course_id. Reason: $reason", 'warning']);
            
            // Notify Teacher
            $teacher_id = $db->query("SELECT teacher_id FROM courses WHERE id = $course_id")->fetchColumn();
            if ($teacher_id) {
                $notif = $db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'course')");
                $notif->execute([$teacher_id, 'تم رفض الدورة', "نأسف، تم رفض دورتك. السبب: $reason", 'course']);
            }
            
            header('Location: ../pages/admin/approvals.php?success=rejected');
            exit;
            
        } catch (Exception $e) {
            header('Location: ../pages/admin/approvals.php?error=' . urlencode($e->getMessage()));
            exit;
        }
    }
}

// Helper: Handle AJAX JSON responses if needed
// ...

header('Location: ../pages/admin/index.php');
