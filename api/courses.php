<?php
require_once '../includes/config/database.php';
require_once '../includes/classes/Database.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/media.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/teacher/index.php');
    exit;
}

if (!Auth::isLoggedIn()) {
    die('Unauthorized');
}

// Allow Teacher or Admin
if ($_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'admin') {
    die('Unauthorized: Role not allowed');
}

// ... CSRF check ...

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    try {
        $db = (new Database())->getConnection();
        
        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'] ?? 0;
        $category_slug = $_POST['category'];
        
        // Map slug to ID or fetch
        $catStmt = $db->prepare("SELECT id FROM categories WHERE name LIKE ? LIMIT 1");
        $catStmt->execute(["%$category_slug%"]);
        $catId = $catStmt->fetchColumn(); 
        
        if (!$catId) $catId = 1; // Fallback

        $creator_id = $_SESSION['user_id'];
        
        // Logic: If Admin, default to 'published'. If Teacher, 'pending'.
        // Unless specific status was sent (e.g. draft)
        if ($_SESSION['role'] === 'admin') {
            $status = $_POST['status'] ?? 'published';
        } else {
            $status = $_POST['status'] ?? 'pending';
        }
        
        // Handle Image
        $thumbnail = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $thumbnail = MediaHandler::upload($_FILES['image'], 'course_thumbnails');
        }

        $stmt = $db->prepare("INSERT INTO courses (teacher_id, category_id, title, description, price, thumbnail, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$creator_id, $catId, $title, $description, $price, $thumbnail, $status]);
        
        $course_id = $db->lastInsertId();
        
        // Notify admin (only if teacher created)
        if ($_SESSION['role'] === 'teacher') {
            $db->exec("INSERT INTO notifications (user_id, title, message, type) SELECT id, 'New Course Pending', 'Teacher {$_SESSION['user_name']} submitted course: $title', 'system' FROM users WHERE role = 'admin'");
        }

        // Role-based redirect
        if ($_SESSION['role'] === 'admin') {
            header('Location: ../pages/admin/courses.php?success=course_created');
        } else {
            header('Location: ../pages/teacher/index.php?success=course_created');
        }
        exit;

    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
