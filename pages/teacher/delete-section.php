<?php
require_once '../../includes/config/database.php';
require_once '../../includes/classes/Database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: my-courses.php');
    exit;
}

$course_id = $_POST['course_id'] ?? null;
$section_id = $_POST['section_id'] ?? null;
$teacher_id = $_SESSION['user_id'];

if (!$course_id || !$section_id) {
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

// Delete section (Lessons will have section_id set to NULL due to FK constraint)
$stmt = $conn->prepare("DELETE FROM course_sections WHERE id = ? AND course_id = ?");
$stmt->execute([$section_id, $course_id]);

header("Location: edit-course.php?id=$course_id");
exit;
