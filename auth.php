<?php
// auth.php

require_once 'config.php';

class Auth {
    public static function register($firstName, $lastName, $email, $password) {
        $db = getDB();
        
        // Validate input
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        if (strlen($password) < 8) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters'];
        }
        
        // Check if email exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Hash password
        $hashedPassword = password_hash($password . Config::SALT, PASSWORD_BCRYPT);
        
        // Insert user
        $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $email, $hashedPassword]);
        
        // Log the user in automatically
        $userId = $db->lastInsertId();
        self::loginUser($userId);
        
        return ['success' => true, 'message' => 'Registration successful'];
    }
    
    public static function login($email, $password) {
        $db = getDB();
        
        $stmt = $db->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password . Config::SALT, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        self::loginUser($user['id']);
        
        return ['success' => true, 'message' => 'Login successful'];
    }
    
    private static function loginUser($userId) {
        $db = getDB();
        
        // Update last login
        $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$userId]);
        
        // Get user data
        $stmt = $db->prepare("SELECT id, first_name, last_name, email, role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        $_SESSION['user'] = $user;
        $_SESSION['logged_in'] = true;
        
        // Log the login
        self::logAction($userId, 'login', 'users', $userId);
    }
    
    public static function logout() {
        if (isset($_SESSION['user'])) {
            self::logAction($_SESSION['user']['id'], 'logout', 'users', $_SESSION['user']['id']);
        }
        
        session_unset();
        session_destroy();
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public static function getUser() {
        return $_SESSION['user'] ?? null;
    }
    
    public static function isAdmin() {
        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
    }
    
    public static function logAction($userId, $action, $table, $recordId, $oldValues = null, $newValues = null) {
        $db = getDB();
        
        $stmt = $db->prepare("INSERT INTO audit_log (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            $action,
            $table,
            $recordId,
            json_encode($oldValues),
            json_encode($newValues),
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
}