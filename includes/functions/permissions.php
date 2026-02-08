<?php
class Permissions {
    const ROLES = [
        'student' => ['view_courses', 'enroll_courses', 'access_lessons'],
        'teacher' => ['create_courses', 'manage_own_courses', 'view_own_students', 'withdraw_earnings'],
        'admin'   => ['manage_users', 'manage_all_courses', 'manage_payments', 'view_analytics', 'system_settings']
    ];

    public static function can($action, $userRole = null) {
        $role = $userRole ?? $_SESSION['role'] ?? 'guest';
        return in_array($action, self::ROLES[$role] ?? []);
    }

    public static function requirePermission($action) {
        if (!self::can($action)) {
            http_response_code(403);
            include __DIR__ . '/../../pages/errors/403.php';
            exit();
        }
    }
}
?>
