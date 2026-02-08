<?php
include '../includes/config/database.php';
include '../includes/classes/Database.php';

session_start();
$student_id = $_SESSION['user_id'] ?? 1;

$data = json_decode(file_get_contents('php://input'), true);
$lesson_id = $data['lesson_id'];
$course_id = $data['course_id'];
$status = $data['status'];

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("INSERT INTO progress (student_id, course_id, lesson_id, status, completed_at) 
    VALUES (?, ?, ?, ?, NOW()) 
    ON DUPLICATE KEY UPDATE status=?, completed_at=NOW()");
if($stmt->execute([$student_id, $course_id, $lesson_id, $status, $status])){
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false]);
}
?>