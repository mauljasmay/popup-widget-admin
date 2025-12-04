<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once '../../includes/auth.php';

$auth = new Auth();
$auth->requireLogin();

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

try {
    $database = new Database();
    $db = $database->connect();
    
    // Check database connection
    if (!$db) {
        echo json_encode([
            'success' => false, 
            'error' => 'Database connection failed',
            'debug' => $database->getError()
        ]);
        exit;
    }
    
    switch ($action) {
        case 'list':
            $search = $_GET['search'] ?? '';
            $status = $_GET['status'] ?? '';
            
            // Build query with proper joins
            $sql = "SELECT w.*, 
                           (SELECT COUNT(*) FROM popup_analytics WHERE widget_id = w.id AND action = 'show') as views,
                           (SELECT COUNT(*) FROM popup_analytics WHERE widget_id = w.id AND action = 'click') as clicks
                    FROM popup_widgets w WHERE 1=1";
            $params = [];
            
            if ($search) {
                $sql .= " AND (w.name LIKE ? OR w.title LIKE ?)";
                $searchParam = "%$search%";
                $params[] = $searchParam;
                $params[] = $searchParam;
            }
            
            if ($status !== '') {
                $sql .= " AND w.is_active = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY w.created_at DESC";
            
            $stmt = $db->prepare($sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement");
            }
            
            $stmt->execute($params);
            $widgets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'widgets' => $widgets]);
            break;
            
        case 'get':
            if (empty($id)) {
                echo json_encode(['success' => false, 'error' => 'Widget ID is required']);
                break;
            }
            
            $stmt = $db->prepare("SELECT * FROM popup_widgets WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Failed to prepare statement");
            }
            
            $stmt->execute([$id]);
            $widget = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($widget) {
                echo json_encode(['success' => true, 'widget' => $widget]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Widget not found']);
            }
            break;
            
        case 'create':
            // Validate required fields
            $requiredFields = ['name', 'title', 'content', 'type'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['success' => false, 'error' => "Field '$field' is required"]);
                    break;
                }
            }
            
            $stmt = $db->prepare("
                INSERT INTO popup_widgets (
                    name, title, content, type, position, width, height, 
                    background_color, text_color, button_text, button_color,
                    show_close_button, auto_close, show_after, show_on_scroll,
                    scroll_percentage, show_on_exit, is_active, target_pages,
                    exclude_pages, start_date, end_date, max_shows_per_user, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            if (!$stmt) {
                throw new Exception("Failed to prepare insert statement");
            }
            
            $result = $stmt->execute([
                $_POST['name'],
                $_POST['title'],
                $_POST['content'],
                $_POST['type'],
                $_POST['position'],
                $_POST['width'],
                $_POST['height'],
                $_POST['background_color'],
                $_POST['text_color'],
                $_POST['button_text'],
                $_POST['button_color'],
                $_POST['show_close_button'],
                $_POST['auto_close'],
                $_POST['show_after'],
                $_POST['show_on_scroll'],
                $_POST['scroll_percentage'],
                $_POST['show_on_exit'],
                $_POST['is_active'],
                null, // target_pages
                null, // exclude_pages
                !empty($_POST['start_date']) ? $_POST['start_date'] : null,
                !empty($_POST['end_date']) ? $_POST['end_date'] : null,
                $_POST['max_shows_per_user'],
                $_SESSION['user_id']
            ]);
            
            if ($result) {
                $widgetId = $db->lastInsertId();
                echo json_encode([
                    'success' => true, 
                    'message' => 'Widget created successfully',
                    'widget_id' => $widgetId
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to create widget']);
            }
            break;
            
        case 'update':
            if (empty($_POST['id'])) {
                echo json_encode(['success' => false, 'error' => 'Widget ID is required']);
                break;
            }
            
            // Validate required fields
            $requiredFields = ['name', 'title', 'content', 'type'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['success' => false, 'error' => "Field '$field' is required"]);
                    break;
                }
            }
            
            $stmt = $db->prepare("
                UPDATE popup_widgets SET
                    name = ?, title = ?, content = ?, type = ?, position = ?, 
                    width = ?, height = ?, background_color = ?, text_color = ?, 
                    button_text = ?, button_color = ?, show_close_button = ?, 
                    auto_close = ?, show_after = ?, show_on_scroll = ?, 
                    scroll_percentage = ?, show_on_exit = ?, is_active = ?, 
                    start_date = ?, end_date = ?, max_shows_per_user = ?
                WHERE id = ?
            ");
            
            if (!$stmt) {
                throw new Exception("Failed to prepare update statement");
            }
            
            $result = $stmt->execute([
                $_POST['name'],
                $_POST['title'],
                $_POST['content'],
                $_POST['type'],
                $_POST['position'],
                $_POST['width'],
                $_POST['height'],
                $_POST['background_color'],
                $_POST['text_color'],
                $_POST['button_text'],
                $_POST['button_color'],
                $_POST['show_close_button'],
                $_POST['auto_close'],
                $_POST['show_after'],
                $_POST['show_on_scroll'],
                $_POST['scroll_percentage'],
                $_POST['show_on_exit'],
                $_POST['is_active'],
                !empty($_POST['start_date']) ? $_POST['start_date'] : null,
                !empty($_POST['end_date']) ? $_POST['end_date'] : null,
                $_POST['max_shows_per_user'],
                $_POST['id']
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Widget updated successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update widget']);
            }
            break;
            
        case 'delete':
            if (empty($id)) {
                echo json_encode(['success' => false, 'error' => 'Widget ID is required']);
                break;
            }
            
            $stmt = $db->prepare("DELETE FROM popup_widgets WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Failed to prepare delete statement");
            }
            
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Widget deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to delete widget']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'debug' => [
            'file' => __FILE__,
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}
?>