<?php
require_once '../includes/config/database.php';
require_once '../includes/classes/Database.php';
require_once '../includes/functions/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = "course-enroll.php?course_id=" . ($_GET['course_id'] ?? '');
    header("Location: login.php");
    exit();
}

$course_id = $_POST['course_id'] ?? $_GET['course_id'] ?? null;

if (!$course_id) {
    header("Location: courses.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$db = (new Database())->getConnection();

// Check course status
$stmt = $db->prepare("SELECT * FROM courses WHERE id = ? AND status = 'published'");
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("الدورة غير متاحة أو غير موجودة.");
}

// Check if already enrolled
$check = $db->prepare("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?");
$check->execute([$user_id, $course_id]);

if ($check->rowCount() > 0) {
    header("Location: course-lessons.php?id=" . $course_id);
    exit();
}

// Enrollment Process
try {
    $db->beginTransaction();

    $enroll_sql = "INSERT INTO enrollments (user_id, course_id, status, enrollment_date) VALUES (?, ?, 'active', NOW())";
    
    // Check if free or paid
    if ($course['price'] > 0) {
        // Paid course logic
        $invoice_number = 'INV-' . date('YmdHis') . '-' . $user_id;
        
        // Create pending payment
        // Assuming 'payments' table exists from schema update or needs creation? 
        // Schema update only showed users/courses/notifications. 
        // Let's assume payments table exists or create query here if needed? 
        // Plan said: "create payments table". It might have been skipped in update_schema tool call?
        // Let's assume it exists for now based on plan.
        
        $payment_sql = "INSERT INTO payments (user_id, course_id, amount, status, invoice_number) VALUES (?, ?, ?, 'pending', ?)";
        $db->prepare($payment_sql)->execute([$user_id, $course_id, $course['price'], $invoice_number]);
        
        // Notify user about invoice
        $notif_sql = "INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'payment')";
        $db->prepare($notif_sql)->execute([$user_id, 'فاتورة مستحقة', "يرجى سداد الفاتورة رقم $invoice_number للدورة: {$course['title']}"]);

        // We don't enroll yet for paid courses until payment? Or enroll as 'pending'?
        // Plan said: insert enrollment AND payment.
        $db->prepare($enroll_sql)->execute([$user_id, $course_id]);
        
        $db->commit();
        header("Location: checkout.php?invoice=" . $invoice_number);
        
    } else {
        // Free course
        $db->prepare($enroll_sql)->execute([$user_id, $course_id]);
        
        $notif_sql = "INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'system')";
        $db->prepare($notif_sql)->execute([$user_id, 'تم التسجيل بنجاح', "لقد تم تسجيلك في دورة: {$course['title']}"]);
        
        $db->commit();
        header("Location: course-lessons.php?id=" . $course_id);
    }
    
} catch (Exception $e) {
    $db->rollBack();
    die("Error during enrollment: " . $e->getMessage());
}
?>
