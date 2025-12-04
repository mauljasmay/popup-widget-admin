<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 1 for debugging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Timezone
date_default_timezone_set('UTC');

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Security headers
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: "1; mode=block"');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// CORS for API endpoints
if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit(0);
    }
}

// Constants
define('ROOT_DIR', __DIR__);
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('LOG_DIR', __DIR__ . '/logs/');

// Create directories if they don't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

if (!file_exists(LOG_DIR)) {
    mkdir(LOG_DIR, 0755, true);
}

// Autoloader
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/includes/',
        __DIR__ . '/admin/api/',
        __DIR__ . '/config/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Utility functions
function sanitize_filename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    return $filename;
}

function is_valid_image($file) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file);
    finfo_close($finfo);
    
    return in_array($mime_type, $allowed_types);
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

function log_message($message, $level = 'INFO') {
    $log_file = LOG_DIR . 'app.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message" . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// Handle file uploads
function handle_upload($file, $max_size = 5242880) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload error: ' . $file['error']);
    }
    
    if ($file['size'] > $max_size) {
        throw new Exception('File too large');
    }
    
    if (!is_valid_image($file['tmp_name'])) {
        throw new Exception('Invalid file type');
    }
    
    $filename = sanitize_filename($file['name']);
    $filename = uniqid() . '_' . $filename;
    $upload_path = UPLOAD_DIR . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception('Failed to move uploaded file');
    }
    
    return $filename;
}

// Clean old analytics data (optional)
function cleanup_old_analytics($days = 90) {
    try {
        $database = new Database();
        $db = $database->connect();
        
        $stmt = $db->prepare("DELETE FROM popup_analytics WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)");
        $stmt->execute([$days]);
        
        return $stmt->rowCount();
    } catch (Exception $e) {
        log_message('Cleanup error: ' . $e->getMessage(), 'ERROR');
        return 0;
    }
}

// Rate limiting (optional)
function check_rate_limit($key, $limit = 100, $window = 3600) {
    $cache_file = sys_get_temp_dir() . '/rate_limit_' . md5($key);
    $current_time = time();
    
    if (file_exists($cache_file)) {
        $data = json_decode(file_get_contents($cache_file), true);
        
        if ($data['reset_time'] < $current_time) {
            // Reset window
            $data = ['count' => 1, 'reset_time' => $current_time + $window];
        } else {
            $data['count']++;
            
            if ($data['count'] > $limit) {
                return false;
            }
        }
    } else {
        $data = ['count' => 1, 'reset_time' => $current_time + $window];
    }
    
    file_put_contents($cache_file, json_encode($data));
    return true;
}

// Initialize error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $message = "[$errno] $errstr in $errfile on line $errline";
    log_message($message, 'ERROR');
    
    if (ini_get('display_errors')) {
        echo "<div class='error'>$message</div>";
    }
});

// Initialize exception handler
set_exception_handler(function($exception) {
    $message = "Uncaught exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
    log_message($message, 'ERROR');
    
    if (ini_get('display_errors')) {
        echo "<div class='error'>$message</div>";
    }
});

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>