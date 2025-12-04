<?php
header('Content-Type: application/json');
require_once '../includes/auth.php';

// This endpoint is public - no authentication required
// It only returns active widgets for display on websites

try {
    $database = new Database();
    $db = $database->connect();
    
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'active':
            // Get all active widgets
            $stmt = $db->query("
                SELECT * FROM popup_widgets 
                WHERE is_active = 1 
                AND (start_date IS NULL OR start_date <= CURDATE())
                AND (end_date IS NULL OR end_date >= CURDATE())
                ORDER BY created_at DESC
            ");
            $widgets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'widgets' => $widgets]);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>