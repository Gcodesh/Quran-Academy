<?php
/**
 * Notifications API
 * Handles notification-related actions
 */

require_once '../includes/config/database.php';
require_once '../includes/classes/Database.php';

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

// Handle JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'mark_all_read':
        $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        $stmt->execute([$user_id]);
        echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);
        break;
        
    case 'mark_read':
        $notification_id = $input['notification_id'] ?? $_POST['notification_id'] ?? 0;
        $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        $stmt->execute([$notification_id, $user_id]);
        echo json_encode(['success' => true]);
        break;
        
    case 'get_unread_count':
        $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$user_id]);
        $count = $stmt->fetchColumn();
        echo json_encode(['success' => true, 'count' => (int)$count]);
        break;
        
    case 'get_notifications':
        $limit = $input['limit'] ?? 10;
        $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$user_id, $limit]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'notifications' => $notifications]);
        break;
        
    case 'delete':
        $notification_id = $input['notification_id'] ?? $_POST['notification_id'] ?? 0;
        $stmt = $db->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
        $stmt->execute([$notification_id, $user_id]);
        echo json_encode(['success' => true]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
