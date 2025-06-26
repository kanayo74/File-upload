<?php
// config.php

class Config {
    const DB_HOST = 'localhost';
    const DB_NAME = 'nsia_dms';
    const DB_USER = 'root';
    const DB_PASS = '';
    const BASE_URL = 'http://localhost/nsia-dms/';
    
    // File upload settings
    const UPLOAD_DIR = 'uploads/documents/';
    const MAX_FILE_SIZE = 10485760; // 10MB
    const ALLOWED_TYPES = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
    
    // Security settings
    const SALT = 'your-random-salt-here';
}

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Timezone
date_default_timezone_set('Africa/Lagos');

// Create database connection
function getDB() {
    static $db = null;
    
    if ($db === null) {
        try {
            $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8mb4';
            $db = new PDO($dsn, Config::DB_USER, Config::DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            error_log('Connection failed: ' . $e->getMessage());
            die('Database connection failed. Please try again later.');
        }
    }
    
    return $db;
}

// Helper function to redirect
function redirect($url) {
    header("Location: " . Config::BASE_URL . $url);
    exit();
}

// CSRF protection
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}