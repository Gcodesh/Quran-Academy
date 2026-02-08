<?php
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';
require_once '../../includes/classes/VersioningManager.php';
session_start();

$lesson_id = $_GET['id'] ?? null;
$course_id = $_GET['course_id'] ?? null;
$version_number = $_GET['v'] ?? null;
$teacher_id = $_SESSION['user_id'];

if (!$lesson_id || !$course_id || !$version_number) {
    die("Data missing");
}

$db = new Database();
$conn = $db->getConnection();

// Verify ownership via course
$stmt = $conn->prepare("SELECT c.teacher_id FROM lessons l JOIN courses c ON l.course_id = c.id WHERE l.id = ? AND c.id = ? AND c.teacher_id = ?");
$stmt->execute([$lesson_id, $course_id, $teacher_id]);
if (!$stmt->fetch()) {
    die("Unauthorized");
}

$versioning = new \App\Classes\VersioningManager($conn);
if ($versioning->rollback($lesson_id, $version_number, $teacher_id)) {
    header("Location: edit-lesson.php?id=$lesson_id&course_id=$course_id&success=rollback");
} else {
    die("Rollback failed");
}
exit;
