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
$title = $_POST['title'] ?? '';
$teacher_id = $_SESSION['user_id'];

if (!$course_id || !$title) {
    header('Location: my-courses.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Verify ownership
$stmt = $conn->prepare("SELECT id FROM courses WHERE id = ? AND teacher_id = ?");
$stmt->execute([$course_id, $teacher_id]);
if (!$stmt->fetch()) {
    header('Location: my-courses.php');
    exit;
}

if ($section_id) {
    // Update existing
    $stmt = $conn->prepare("UPDATE course_sections SET title = ? WHERE id = ? AND course_id = ?");
    $stmt->execute([$title, $section_id, $course_id]);
} else {
    // Add new
    // Get max order
    $max = $conn->prepare("SELECT MAX(order_number) FROM course_sections WHERE course_id = ?");
    $max->execute([$course_id]);
    $next = ($max->fetchColumn() ?: 0) + 1;

    $stmt = $conn->prepare("INSERT INTO course_sections (course_id, title, order_number) VALUES (?, ?, ?)");
    $stmt->execute([$course_id, $title, $next]);
}

header("Location: edit-course.php?id=$course_id");
exit;
