<?php
header('Content-Type: application/json');
require_once '../../includes/auth.php';

$auth = new Auth();
$auth->requireLogin();

try {
    $database = new Database();
    $db = $database->connect();
    
    // Get total widgets
    $stmt = $db->query("SELECT COUNT(*) as total FROM popup_widgets");
    $totalWidgets = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get active widgets
    $stmt = $db->query("SELECT COUNT(*) as total FROM popup_widgets WHERE is_active = 1");
    $activeWidgets = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get total views
    $stmt = $db->query("SELECT COUNT(*) as total FROM popup_analytics WHERE action = 'show'");
    $totalViews = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get total clicks
    $stmt = $db->query("SELECT COUNT(*) as total FROM popup_analytics WHERE action = 'click'");
    $totalClicks = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get recent widgets
    $stmt = $db->query("
        SELECT w.*, 
               (SELECT COUNT(*) FROM popup_analytics WHERE widget_id = w.id AND action = 'show') as views
        FROM popup_widgets w 
        ORDER BY w.created_at DESC 
        LIMIT 5
    ");
    $recentWidgets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get chart data for last 7 days
    $chartData = [
        'labels' => [],
        'views' => [],
        'clicks' => []
    ];
    
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $chartData['labels'][] = date('M j', strtotime($date));
        
        // Views for this date
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM popup_analytics WHERE DATE(created_at) = ? AND action = 'show'");
        $stmt->execute([$date]);
        $chartData['views'][] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Clicks for this date
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM popup_analytics WHERE DATE(created_at) = ? AND action = 'click'");
        $stmt->execute([$date]);
        $chartData['clicks'][] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    echo json_encode([
        'success' => true,
        'totalWidgets' => $totalWidgets,
        'activeWidgets' => $activeWidgets,
        'totalViews' => $totalViews,
        'totalClicks' => $totalClicks,
        'recentWidgets' => $recentWidgets,
        'chartData' => $chartData
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>