<?php
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: my-courses.php');
    exit;
}

$lesson_id = $_POST['lesson_id'] ?? null;
$course_id = $_POST['course_id'] ?? null;

if (!$lesson_id || !$course_id) {
    header('Location: my-courses.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Verify course ownership before deleting lesson
$teacher_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id FROM courses WHERE id = ? AND teacher_id = ?");
$stmt->execute([$course_id, $teacher_id]);

if (!$stmt->fetch()) {
    header('Location: my-courses.php');
    exit;
}

// Delete lesson
$stmt = $conn->prepare("DELETE FROM lessons WHERE id = ? AND course_id = ?");
$stmt->execute([$lesson_id, $course_id]);

header("Location: edit-course.php?id=$course_id");
exit;
