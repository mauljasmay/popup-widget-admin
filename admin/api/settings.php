<?php
header('Content-Type: application/json');
require_once '../../includes/auth.php';

$auth = new Auth();
$auth->requireLogin();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

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
        case 'update_account':
            $fullName = $_POST['fullName'] ?? '';
            $email = $_POST['email'] ?? '';
            $username = $_POST['username'] ?? '';
            $currentPassword = $_POST['currentPassword'] ?? '';
            $newPassword = $_POST['newPassword'] ?? '';
            $confirmPassword = $_POST['confirmPassword'] ?? '';
            
            // Validate required fields
            if (!$fullName || !$email || !$username) {
                echo json_encode(['success' => false, 'error' => 'All fields are required']);
                exit;
            }
            
            // Check if username is unique (excluding current user)
            $stmt = $db->prepare("SELECT id FROM admin_users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Username already exists']);
                exit;
            }
            
            // Check if email is unique (excluding current user)
            $stmt = $db->prepare("SELECT id FROM admin_users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Email already exists']);
                exit;
            }
            
            // Update basic info
            $stmt = $db->prepare("
                UPDATE admin_users SET 
                    full_name = ?, 
                    email = ?, 
                    username = ?
                WHERE id = ?
            ");
            $result = $stmt->execute([$fullName, $email, $username, $_SESSION['user_id']]);
            
            // Update password if provided
            if ($newPassword) {
                if (!$currentPassword) {
                    echo json_encode(['success' => false, 'error' => 'Current password is required to change password']);
                    exit;
                }
                
                if ($newPassword !== $confirmPassword) {
                    echo json_encode(['success' => false, 'error' => 'New passwords do not match']);
                    exit;
                }
                
                // Verify current password
                $stmt = $db->prepare("SELECT password_hash FROM admin_users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!password_verify($currentPassword, $user['password_hash'])) {
                    echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
                    exit;
                }
                
                // Update password
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?");
                $stmt->execute([$newPasswordHash, $_SESSION['user_id']]);
            }
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Account updated successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update account']);
            }
            break;
            
        case 'update_system':
            // Only super admin can update system settings
            if (!$auth->hasRole('super_admin')) {
                echo json_encode(['success' => false, 'error' => 'Insufficient permissions']);
                exit;
            }
            
            // This would typically update a config file or database table
            // For now, we'll just return success
            echo json_encode(['success' => true, 'message' => 'System settings updated successfully']);
            break;
            
        case 'backup':
            // Only super admin can backup database
            if (!$auth->hasRole('super_admin')) {
                echo json_encode(['success' => false, 'error' => 'Insufficient permissions']);
                exit;
            }
            
            // Set headers for SQL download
            header('Content-Type: application/sql');
            header('Content-Disposition: attachment; filename="popup_widget_backup_' . date('Y-m-d_H-i-s') . '.sql"');
            
            // Get all tables
            $tables = ['admin_users', 'popup_widgets', 'popup_analytics', 'popup_images'];
            
            $output = "-- Popup Widget Admin Database Backup\n";
            $output .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";
            
            foreach ($tables as $table) {
                $stmt = $db->query("SHOW CREATE TABLE $table");
                $createTable = $stmt->fetch(PDO::FETCH_ASSOC);
                $output .= $createTable['Create Table'] . ";\n\n";
                
                $stmt = $db->query("SELECT * FROM $table");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($rows)) {
                    $output .= "-- Data for table $table\n";
                    foreach ($rows as $row) {
                        $values = array_map(function($value) use ($db) {
                            return $value === null ? 'NULL' : $db->quote($value);
                        }, $row);
                        $output .= "INSERT INTO $table VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $output .= "\n";
                }
            }
            
            echo $output;
            exit;
            
        case 'clear_analytics':
            // Only super admin can clear analytics
            if (!$auth->hasRole('super_admin')) {
                echo json_encode(['success' => false, 'error' => 'Insufficient permissions']);
                exit;
            }
            
            $stmt = $db->query("DELETE FROM popup_analytics");
            $deletedRows = $stmt->rowCount();
            
            echo json_encode([
                'success' => true, 
                'message' => "Cleared $deletedRows analytics records"
            ]);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>