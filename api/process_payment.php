<?php
require_once '../includes/config/database.php';
require_once '../includes/classes/Database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$invoice = $_POST['invoice'] ?? '';
$method = $_POST['method'] ?? 'card';

if (!$invoice) {
    echo json_encode(['success' => false, 'message' => 'Invoice number required']);
    exit;
}

$db = (new Database())->getConnection();

try {
    $db->beginTransaction();

    // 1. Verify Payment Record
    $stmt = $db->prepare("SELECT * FROM payments WHERE invoice_number = ? AND user_id = ?");
    $stmt->execute([$invoice, $_SESSION['user_id']]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$payment) {
        throw new Exception("Invoice not found");
    }

    if ($payment['status'] == 'completed') {
        echo json_encode([
            'success' => true, 
            'message' => 'Already paid',
            'redirect_url' => "../pages/course-lessons.php?id=" . $payment['course_id']
        ]);
        $db->rollBack(); // No changes needed
        exit;
    }

    // 2. Simulate Payment Gateway Success
    // In a real app, we would verify with Stripe/PayPal API here
    $transaction_id = 'TXN-' . strtoupper(uniqid());

    // 3. Update Payment Status
    $update_payment = $db->prepare("UPDATE payments SET status = 'completed', transaction_id = ?, payment_method = ? WHERE id = ?");
    $update_payment->execute([$transaction_id, $method, $payment['id']]);

    // 4. Activate Enrollment
    // Assuming enrollment was created as 'pending' or checking logic in course-enroll.php
    // In course-enroll.php we inserted as 'active' but maybe we should rely on this check?
    // Let's ensure enrollment is active.
    $check_enroll = $db->prepare("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?");
    $check_enroll->execute([$payment['user_id'], $payment['course_id']]);
    
    if ($check_enroll->rowCount() == 0) {
        // Create if missing (failsafe)
        $enroll_sql = "INSERT INTO enrollments (user_id, course_id, status, enrollment_date) VALUES (?, ?, 'active', NOW())";
        $db->prepare($enroll_sql)->execute([$payment['user_id'], $payment['course_id']]);
    } else {
        // Ensure status is active
        $update_enroll = $db->prepare("UPDATE enrollments SET status = 'active' WHERE user_id = ? AND course_id = ?");
        $update_enroll->execute([$payment['user_id'], $payment['course_id']]);
    }

    // 5. Create Notification
    $notif_sql = "INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'payment')";
    $db->prepare($notif_sql)->execute([
        $payment['user_id'], 
        'تم الدفع بنجاح', 
        "تم استلام دفعتك بنجاح للفاتورة رقم $invoice. نتمنى لك رحلة تعلم ممتعة!"
    ]);

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Payment successful',
        'redirect_url' => "../pages/course-lessons.php?id=" . $payment['course_id']
    ]);

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
