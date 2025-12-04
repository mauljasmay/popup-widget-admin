<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'popup_widget_admin';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function __construct($host = null, $db_name = null, $username = null, $password = null) {
        if ($host) $this->host = $host;
        if ($db_name) $this->db_name = $db_name;
        if ($username) $this->username = $username;
        if ($password) $this->password = $password;
    }

    public function connect() {
        $this->conn = null;

        try {
            // Try MySQL first
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->exec("SET sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_IN_DATE,NO_ENGINE_SUBSTITUTION'");
            
            // Test connection
            $this->conn->query("SELECT 1");
            
        } catch(PDOException $e) {
            // Log error instead of echoing
            error_log("Database connection failed: " . $e->getMessage());
            
            // Return null on failure
            return null;
        }

        return $this->conn;
    }
    
    public function isConnected() {
        return $this->conn !== null;
    }
    
    public function getError() {
        if ($this->conn) {
            $errorInfo = $this->conn->errorInfo();
            return $errorInfo[2] ?? null;
        }
        return null;
    }
    
    public function testConnection() {
        try {
            $conn = $this->connect();
            if ($conn) {
                $result = $conn->query("SELECT VERSION() as version");
                $version = $result->fetch();
                return [
                    'success' => true,
                    'version' => $version['version'],
                    'database' => $this->db_name
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function createTables() {
        try {
            $conn = $this->connect();
            if (!$conn) {
                return false;
            }

            // Read and execute SQL schema
            $sql = file_get_contents(__DIR__ . '/../database.sql');
            $conn->exec($sql);
            
            return true;
        } catch (Exception $e) {
            error_log("Failed to create tables: " . $e->getMessage());
            return false;
        }
    }
}

class Config {
    public static function get($key) {
        $config = [
            'app_name' => 'Popup Widget Admin',
            'app_version' => '1.0.0',
            'session_timeout' => 3600, // 1 hour
            'max_file_size' => 5242880, // 5MB
            'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'popup_js_url' => '/popup-widget.js',
            'default_popup_width' => 400,
            'default_popup_height' => 300,
        ];
        
        return isset($config[$key]) ? $config[$key] : null;
    }
    
    public static function getAll() {
        return [
            'app_name' => 'Popup Widget Admin',
            'app_version' => '1.0.0',
            'session_timeout' => 3600,
            'max_file_size' => 5242880,
            'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'popup_js_url' => '/popup-widget.js',
            'default_popup_width' => 400,
            'default_popup_height' => 300,
        ];
    }
}
?>