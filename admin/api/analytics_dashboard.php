<?php
header('Content-Type: application/json');
require_once '../../includes/auth.php';

$auth = new Auth();
$auth->requireLogin();

$action = $_GET['action'] ?? '';

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
        case 'overview':
            $dateRange = $_GET['dateRange'] ?? '30';
            $startDate = $_GET['startDate'] ?? '';
            $endDate = $_GET['endDate'] ?? '';
            $widgetId = $_GET['widgetId'] ?? '';
            
            // Calculate date range
            if ($startDate && $endDate) {
                $dateCondition = "AND DATE(a.created_at) BETWEEN '$startDate' AND '$endDate'";
            } else {
                $days = intval($dateRange);
                $dateCondition = "AND a.created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)";
            }
            
            $widgetCondition = $widgetId ? "AND w.id = $widgetId" : '';
            
            // Get overall statistics
            $stmt = $db->query("
                SELECT 
                    COUNT(CASE WHEN a.action = 'show' THEN 1 END) as totalViews,
                    COUNT(CASE WHEN a.action = 'click' THEN 1 END) as totalClicks,
                    COUNT(DISTINCT a.session_id) as uniqueUsers
                FROM popup_analytics a
                JOIN popup_widgets w ON a.widget_id = w.id
                WHERE 1=1 $dateCondition $widgetCondition
            ");
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Calculate click rate
            $clickRate = $stats['totalViews'] > 0 ? 
                round(($stats['totalClicks'] / $stats['totalViews']) * 100, 2) : 0;
            
            // Get chart data
            $chartData = [
                'labels' => [],
                'views' => [],
                'clicks' => []
            ];
            
            $days = intval($dateRange);
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $chartData['labels'][] = date('M j', strtotime($date));
                
                $stmt = $db->prepare("
                    SELECT 
                        COUNT(CASE WHEN action = 'show' THEN 1 END) as views,
                        COUNT(CASE WHEN action = 'click' THEN 1 END) as clicks
                    FROM popup_analytics a
                    WHERE DATE(a.created_at) = ? $widgetCondition
                ");
                $stmt->execute([$date]);
                $dayData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $chartData['views'][] = (int)$dayData['views'];
                $chartData['clicks'][] = (int)$dayData['clicks'];
            }
            
            // Get widget performance data
            $stmt = $db->query("
                SELECT 
                    w.name,
                    COUNT(CASE WHEN a.action = 'show' THEN 1 END) as views
                FROM popup_widgets w
                LEFT JOIN popup_analytics a ON w.id = a.widget_id
                WHERE 1=1 $dateCondition $widgetCondition
                GROUP BY w.id, w.name
                ORDER BY views DESC
                LIMIT 6
            ");
            $widgetPerformance = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $widgetData = [
                'labels' => [],
                'data' => []
            ];
            
            foreach ($widgetPerformance as $widget) {
                $widgetData['labels'][] = $widget['name'];
                $widgetData['data'][] = (int)$widget['views'];
            }
            
            // Get detailed table data
            $stmt = $db->query("
                SELECT 
                    w.name,
                    w.type,
                    COUNT(CASE WHEN a.action = 'show' THEN 1 END) as views,
                    COUNT(CASE WHEN a.action = 'click' THEN 1 END) as clicks,
                    COUNT(CASE WHEN a.action = 'close' THEN 1 END) as closes,
                    COUNT(DISTINCT a.session_id) as uniqueUsers,
                    GROUP_CONCAT(DISTINCT SUBSTRING_INDEX(a.page_url, '/', 3) ORDER BY COUNT(*) DESC LIMIT 3) as topPages
                FROM popup_widgets w
                LEFT JOIN popup_analytics a ON w.id = a.widget_id
                WHERE 1=1 $dateCondition $widgetCondition
                GROUP BY w.id, w.name, w.type
                ORDER BY views DESC
            ");
            $tableData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate click rates for table
            foreach ($tableData as &$row) {
                $row['clickRate'] = $row['views'] > 0 ? 
                    round(($row['clicks'] / $row['views']) * 100, 2) . '%' : '0%';
            }
            
            echo json_encode([
                'success' => true,
                'stats' => [
                    'totalViews' => $stats['totalViews'],
                    'totalClicks' => $stats['totalClicks'],
                    'clickRate' => $clickRate . '%',
                    'uniqueUsers' => $stats['uniqueUsers']
                ],
                'chartData' => $chartData,
                'widgetData' => $widgetData,
                'tableData' => $tableData
            ]);
            break;
            
        case 'export':
            // Similar to overview but returns CSV
            $dateRange = $_GET['dateRange'] ?? '30';
            $startDate = $_GET['startDate'] ?? '';
            $endDate = $_GET['endDate'] ?? '';
            $widgetId = $_GET['widgetId'] ?? '';
            
            // Calculate date range
            if ($startDate && $endDate) {
                $dateCondition = "AND DATE(a.created_at) BETWEEN '$startDate' AND '$endDate'";
            } else {
                $days = intval($dateRange);
                $dateCondition = "AND a.created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)";
            }
            
            $widgetCondition = $widgetId ? "AND w.id = $widgetId" : '';
            
            $stmt = $db->query("
                SELECT 
                    w.name as widget_name,
                    w.type as widget_type,
                    a.action,
                    a.page_url,
                    a.user_ip,
                    a.session_id,
                    a.created_at
                FROM popup_analytics a
                JOIN popup_widgets w ON a.widget_id = w.id
                WHERE 1=1 $dateCondition $widgetCondition
                ORDER BY a.created_at DESC
            ");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Set headers for CSV download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="popup_analytics_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            
            // Header row
            fputcsv($output, ['Widget Name', 'Widget Type', 'Action', 'Page URL', 'User IP', 'Session ID', 'Date']);
            
            // Data rows
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['widget_name'],
                    $row['widget_type'],
                    $row['action'],
                    $row['page_url'],
                    $row['user_ip'],
                    $row['session_id'],
                    $row['created_at']
                ]);
            }
            
            fclose($output);
            exit;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>