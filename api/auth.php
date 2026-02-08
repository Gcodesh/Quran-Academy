<?php
require_once '../includes/functions/auth.php';
require_once '../includes/functions/logger.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['name'])) {
        // Register
        $data = [
            'full_name' => $_POST['name'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'role' => $_POST['role']
        ];

        $result = Auth::register($data);
        echo json_encode($result);
    } else {
        // Login
        $email = $_POST['email'];
        $password = $_POST['password'];

        $result = Auth::login($email, $password);
        echo json_encode($result);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>