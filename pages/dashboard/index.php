<?php
session_start();

require_once '../../includes/auth_middleware.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$role = $_SESSION['user_role'] ?? 'student';

switch ($role) {
    case 'admin':
        header('Location: ../admin/index.php');
        break;
    case 'teacher':
        header('Location: ../teacher/index.php');
        break;
    case 'student':
    default:
        // For now, redirect student to home or student specific page
        // Assuming student.php is still in pages/dashboard/ or moved.
        // Based on sidebar update, student links to ../dashboard/student.php
        header('Location: student.php');
        break;
}
exit;
?>
