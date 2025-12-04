<?php
session_start();
require_once '../config/database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        
        // Check if database connection failed
        if (!$this->db) {
            error_log("Database connection failed in Auth constructor");
        }
    }
    
    public function login($username, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE username = :username AND is_active = 1");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Update last login
                $update_stmt = $this->db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = :id");
                $update_stmt->bindParam(':id', $user['id']);
                $update_stmt->execute();
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['login_time'] = time();
                
                return true;
            }
            return false;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function logout() {
        session_destroy();
        header('Location: login.php');
        exit();
    }
    
    public function isLoggedIn() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['login_time'])) {
            return false;
        }
        
        // Check session timeout
        $timeout = Config::get('session_timeout');
        if (time() - $_SESSION['login_time'] > $timeout) {
            $this->logout();
            return false;
        }
        
        return true;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE id = :id");
            $stmt->bindParam(':id', $_SESSION['user_id']);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return null;
        }
    }
    
    public function hasRole($required_role) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $role_hierarchy = [
            'editor' => 1,
            'admin' => 2,
            'super_admin' => 3
        ];
        
        $user_level = $role_hierarchy[$_SESSION['role']] ?? 0;
        $required_level = $role_hierarchy[$required_role] ?? 0;
        
        return $user_level >= $required_level;
    }
    
    public function requireRole($required_role) {
        if (!$this->hasRole($required_role)) {
            $_SESSION['error'] = 'Insufficient permissions';
            header('Location: dashboard.php');
            exit();
        }
    }
}

// Helper functions
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function display_flash_message() {
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
}
?>