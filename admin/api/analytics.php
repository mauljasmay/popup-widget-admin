<?php
header('Content-Type: application/json');
require_once '../includes/auth.php';

// This endpoint is public - no authentication required
// It receives analytics data from popup widget displays

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }
    
    $database = new Database();
    $db = $database->connect();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['widget_id']) || !isset($data['action'])) {
        echo json_encode(['success' => false, 'error' => 'Missing required data']);
        exit;
    }
    
    // Validate action
    $validActions = ['show', 'close', 'click'];
    if (!in_array($data['action'], $validActions)) {
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        exit;
    }
    
    // Get user IP
    $userIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    
    // Insert analytics record
    $stmt = $db->prepare("
        INSERT INTO popup_analytics (
            widget_id, page_url, user_ip, user_agent, action, session_id
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $data['widget_id'],
        $data['page_url'] ?? '',
        $userIp,
        $data['user_agent'] ?? '',
        $data['action'],
        $data['session_id'] ?? ''
    ]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Analytics recorded']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to record analytics']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>