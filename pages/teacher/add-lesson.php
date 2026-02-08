<?php
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: my-courses.php');
    exit;
}

$course_id = $_POST['course_id'] ?? null;
$section_id = $_POST['section_id'] ?: null; // Optional section
$title = $_POST['title'] ?? '';
$lesson_type = $_POST['lesson_type'] ?? 'lecture';
$media_type = $_POST['media_type'] ?? 'text';
$media_url = $_POST['media_url'] ?? null;
$content = $_POST['content'] ?? '';
$teacher_id = $_SESSION['user_id'];

if (!$course_id || !$title) {
    header('Location: my-courses.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Verify course ownership
$stmt = $conn->prepare("SELECT id FROM courses WHERE id = ? AND teacher_id = ?");
$stmt->execute([$course_id, $teacher_id]);
if (!$stmt->fetch()) {
    header('Location: my-courses.php');
    exit;
}

// Handle File Upload if exists
$final_media_url = $media_url;
if (isset($_FILES['lesson_file']) && $_FILES['lesson_file']['error'] === 0) {
    $upload_dir = '../../uploads/lessons/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $ext = pathinfo($_FILES['lesson_file']['name'], PATHINFO_EXTENSION);
    $filename = 'lesson_' . time() . '_' . uniqid() . '.' . $ext;
    $target = $upload_dir . $filename;
    
    if (move_uploaded_file($_FILES['lesson_file']['tmp_name'], $target)) {
        $final_media_url = 'uploads/lessons/' . $filename;
    }
}

// Get next order number for this section/course
$order_stmt = $conn->prepare("SELECT MAX(order_number) FROM lessons WHERE course_id = ? AND (section_id = ? OR (section_id IS NULL AND ? IS NULL))");
$order_stmt->execute([$course_id, $section_id, $section_id]);
$next_order = ($order_stmt->fetchColumn() ?: 0) + 1;

// Insert Lesson with new fields
$sql = "INSERT INTO lessons (course_id, section_id, title, lesson_type, media_type, media_url, content, order_number, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'draft')";
$stmt = $conn->prepare($sql);
$stmt->execute([$course_id, $section_id, $title, $lesson_type, $media_type, $final_media_url, $content, $next_order]);

$new_lesson_id = $conn->lastInsertId();

// Create Initial Version
require_once '../../includes/classes/VersioningManager.php';
$versioning = new \App\Classes\VersioningManager($conn);
$versioning->createVersion($new_lesson_id, $teacher_id, "نسخة أولية عند الإنشاء");

header("Location: edit-course.php?id=$course_id");
exit;
