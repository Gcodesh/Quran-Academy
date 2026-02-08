<?php

namespace App\Services;

use App\Models\User;
use App\Database\Database;

class AuthService {
    
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Secure session params
            $cookieParams = session_get_cookie_params();
            session_set_cookie_params([
                'lifetime' => $cookieParams['lifetime'],
                'path' => '/',
                'domain' => $cookieParams['domain'],
                'secure' => true, // HttpOnly
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            session_start();
        }
    }

    public static function login($email, $password) {
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return [
                'status' => 'error',
                'success' => false,
                'message' => 'Invalid email or password', 
                'data' => null,
                'errors' => ['credentials' => 'Invalid email or password']
            ];
        }

        if ($user['status'] !== 'active') {
             return [
                'status' => 'error',
                'success' => false,
                'message' => 'User account is ' . $user['status'],
                'data' => null,
                'errors' => ['status' => 'User account is ' . $user['status']]
            ];
        }

        self::startSession();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['role'] = $user['role']; // Legacy support
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        return [
            'status' => 'success',
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user['id'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ],
                'csrf_token' => $_SESSION['csrf_token']
            ],
            'errors' => null
        ];
    }

    public static function register(array $data) {
        $userModel = new User();
        
        // Check if exists
        if ($userModel->findByEmail($data['email'])) {
             return [
                'status' => 'error',
                'success' => false,
                'message' => 'Email already registered',
                'data' => null,
                'errors' => ['email' => 'Email already registered']
            ];
        }

        $userId = $userModel->create($data);
        
        if ($userId) {
            return [
                'status' => 'success',
                'success' => true,
                'message' => 'Registration successful',
                'data' => ['user_id' => $userId],
                'errors' => null
            ];
        }

        return [
            'status' => 'error',
            'success' => false,
            'message' => 'Registration failed',
            'data' => null,
            'errors' => $userModel->getErrors()
        ];
    }

    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    public static function logout() {
        self::startSession();
        session_destroy();
        return true;
    }

    public static function getCurrentUser() {
        if (!self::isLoggedIn()) return null;
        $userModel = new User();
        return $userModel->find($_SESSION['user_id']);
    }
}
