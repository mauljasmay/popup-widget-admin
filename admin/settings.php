<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - <?php echo Config::get('app_name'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            transition: all 0.3s ease;
            border-radius: 0;
        }
        
        .sidebar-menu .nav-link:hover,
        .sidebar-menu .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .sidebar-menu .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }
        
        .top-header {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .settings-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e3e3e3;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: transform 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
        }
        
        .integration-code {
            background: #f8f9fa;
            border: 2px solid #e3e3e3;
            border-radius: 10px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            position: relative;
        }
        
        .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .copy-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }
        
        .embed-options {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .embed-option {
            margin-bottom: 10px;
        }
        
        .embed-option label {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
        }
        
        .widget-item {
            padding: 15px;
            border: 1px solid #e3e3e3;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .widget-item:hover {
            background-color: #f8f9fa;
            border-color: var(--primary-color);
        }
        
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 15px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
    <?php
    require_once '../includes/auth.php';
    $auth = new Auth();
    $auth->requireLogin();
    $user = $auth->getCurrentUser();
    ?>
    
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-window-restore fa-2x mb-2"></i>
            <h5 class="mb-0"><?php echo Config::get('app_name'); ?></h5>
            <small>Popup Widget Admin</small>
        </div>
        
        <div class="sidebar-menu">
            <nav class="nav flex-column">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link" href="widgets.php">
                    <i class="fas fa-window-restore"></i> Popup Widgets
                </a>
                <a class="nav-link" href="analytics.php">
                    <i class="fas fa-chart-line"></i> Analytics
                </a>
                <a class="nav-link active" href="settings.php">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <?php if ($auth->hasRole('super_admin')): ?>
                <a class="nav-link" href="users.php">
                    <i class="fas fa-users"></i> Users
                </a>
                <?php endif; ?>
                <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
                <a class="nav-link" href="#" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <div class="top-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="mb-0">Settings</h4>
                    <small class="text-muted">Configure your popup widget system</small>
                </div>
            </div>
        </div>
        
        <?php display_flash_message(); ?>
        
        <!-- Integration Settings -->
        <div class="settings-card">
            <h5 class="mb-4">
                <i class="fas fa-code"></i> Integration Code
            </h5>
            <p class="text-muted mb-3">Add this code to your website to enable popup widgets:</p>
            
            <div class="integration-code">
                <button class="copy-btn" onclick="copyCode('integrationCode')">
                    <i class="fas fa-copy"></i> Copy
                </button>
                <pre id="integrationCode">&lt;script src="<?php echo $_SERVER['HTTP_HOST']; ?>/popup-widget.js"&gt;&lt;/script&gt;</pre>
            </div>
            
            <div class="mt-3">
                <div class="d-flex gap-2 mb-3">
                    <button class="btn btn-outline-info" onclick="showAdvancedEmbed()">
                        <i class="fas fa-cog"></i> Advanced Embed Options
                    </button>
                    <button class="btn btn-outline-primary" onclick="showWidgetList()">
                        <i class="fas fa-list"></i> Choose Specific Widget
                    </button>
                </div>
                
                <h6>Instructions:</h6>
                <ol>
                    <li>Copy the code above</li>
                    <li>Paste it before the closing &lt;/body&gt; tag on your website</li>
                    <li>Save and refresh your website</li>
                    <li>Your active popup widgets will now appear</li>
                </ol>
            </div>
        </div>
        
        <!-- Account Settings -->
        <div class="settings-card">
            <h5 class="mb-4">
                <i class="fas fa-user-circle"></i> Account Settings
            </h5>
            
            <form id="accountForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" value="<?php echo htmlspecialchars($user['full_name']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control" value="<?php echo ucfirst($user['role']); ?>" readonly>
                    </div>
                </div>
                
                <hr>
                
                <h6>Change Password</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>
        
        <!-- System Settings (Super Admin Only) -->
        <?php if ($auth->hasRole('super_admin')): ?>
        <div class="settings-card">
            <h5 class="mb-4">
                <i class="fas fa-cogs"></i> System Settings
            </h5>
            
            <form id="systemForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Application Name</label>
                        <input type="text" class="form-control" id="appName" value="<?php echo Config::get('app_name'); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Session Timeout (seconds)</label>
                        <input type="number" class="form-control" id="sessionTimeout" value="<?php echo Config::get('session_timeout'); ?>">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Max File Size (bytes)</label>
                        <input type="number" class="form-control" id="maxFileSize" value="<?php echo Config::get('max_file_size'); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Default Popup Width</label>
                        <input type="number" class="form-control" id="defaultWidth" value="<?php echo Config::get('default_popup_width'); ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="analyticsEnabled" checked>
                        <label class="form-check-label" for="analyticsEnabled">
                            Enable Analytics Tracking
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save System Settings
                </button>
            </form>
        </div>
        
        <!-- Database Management -->
        <div class="settings-card">
            <h5 class="mb-4">
                <i class="fas fa-database"></i> Database Management
            </h5>
            
            <div class="row">
                <div class="col-md-6">
                    <button class="btn btn-outline-primary" onclick="backupDatabase()">
                        <i class="fas fa-download"></i> Backup Database
                    </button>
                    <button class="btn btn-outline-warning ms-2" onclick="clearAnalytics()">
                        <i class="fas fa-trash"></i> Clear Analytics Data
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Embed Code Modal -->
    <div class="modal fade" id="embedCodeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-code"></i> Embed Widget Code
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="embed-options">
                        <h6>Embed Options</h6>
                        <div class="embed-option">
                            <label>Widget:</label>
                            <span id="embedWidgetName" class="fw-bold"></span>
                        </div>
                        <div class="embed-option">
                            <label>Widget ID:</label>
                            <span id="embedWidgetId" class="font-monospace"></span>
                        </div>
                        <div class="embed-option">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="embedSpecificWidget" checked>
                                <label class="form-check-label" for="embedSpecificWidget">
                                    Embed this specific widget only
                                </label>
                            </div>
                        </div>
                        <div class="embed-option">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="embedAsync" checked>
                                <label class="form-check-label" for="embedAsync">
                                    Load asynchronously (recommended)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Embed Code:</label>
                        <div class="embed-code">
                            <button class="copy-btn" onclick="copyEmbedCode()">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                            <pre id="embedCodeContent"></pre>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Instructions:</strong>
                        <ol class="mb-0 mt-2">
                            <li>Copy the code above</li>
                            <li>Paste it before the closing <code>&lt;/body&gt;</code> tag on your website</li>
                            <li>Save and refresh your website</li>
                            <li>The widget will appear according to your settings</li>
                        </ol>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="copyEmbedCode()">
                        <i class="fas fa-copy"></i> Copy Code
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Widget List Modal -->
    <div class="modal fade" id="widgetListModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-list"></i> Choose Widget to Embed
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="widgetListContent">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Loading widgets...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }
        
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        }
        
        function copyCode(elementId) {
            const codeElement = document.getElementById(elementId);
            const textArea = document.createElement('textarea');
            textArea.value = codeElement.textContent;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            // Show feedback
            const copyBtn = event.target.closest('.copy-btn');
            const originalHTML = copyBtn.innerHTML;
            copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            setTimeout(() => {
                copyBtn.innerHTML = originalHTML;
            }, 2000);
        }
        
        // Account form submission
        document.getElementById('accountForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'update_account');
            formData.append('fullName', document.getElementById('fullName').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('username', document.getElementById('username').value);
            formData.append('currentPassword', document.getElementById('currentPassword').value);
            formData.append('newPassword', document.getElementById('newPassword').value);
            formData.append('confirmPassword', document.getElementById('confirmPassword').value);
            
            fetch('api/settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Account settings updated successfully!');
                    // Clear password fields
                    document.getElementById('currentPassword').value = '';
                    document.getElementById('newPassword').value = '';
                    document.getElementById('confirmPassword').value = '';
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating account settings');
            });
        });
        
        // System form submission
        document.getElementById('systemForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'update_system');
            formData.append('appName', document.getElementById('appName').value);
            formData.append('sessionTimeout', document.getElementById('sessionTimeout').value);
            formData.append('maxFileSize', document.getElementById('maxFileSize').value);
            formData.append('defaultWidth', document.getElementById('defaultWidth').value);
            formData.append('analyticsEnabled', document.getElementById('analyticsEnabled').checked);
            
            fetch('api/settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('System settings updated successfully!');
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating system settings');
            });
        });
        
        function backupDatabase() {
            if (confirm('This will create a backup of your database. Continue?')) {
                window.open('api/settings.php?action=backup', '_blank');
            }
        }
        
        // Embed Code Functions
        let currentEmbedWidget = null;
        
        function showAdvancedEmbed() {
            // Show embed code for all widgets
            document.getElementById('embedWidgetName').textContent = 'All Active Widgets';
            document.getElementById('embedWidgetId').textContent = 'All';
            document.getElementById('embedSpecificWidget').checked = false;
            
            generateEmbedCode();
            new bootstrap.Modal(document.getElementById('embedCodeModal')).show();
        }
        
        function showWidgetList() {
            // Load and show widget list
            fetch('api/widgets.php?action=list')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const container = document.getElementById('widgetListContent');
                        
                        if (data.widgets.length === 0) {
                            container.innerHTML = '<p class="text-muted">No widgets found.</p>';
                            return;
                        }
                        
                        container.innerHTML = data.widgets.map(widget => `
                            <div class="widget-item" onclick="selectWidget(${widget.id}, '${widget.name.replace(/'/g, "\\'")}')">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">${widget.name}</h6>
                                        <small class="text-muted">${widget.type} • ${widget.is_active ? 'Active' : 'Inactive'}</small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </div>
                        `).join('');
                        
                        new bootstrap.Modal(document.getElementById('widgetListModal')).show();
                    } else {
                        alert('Error loading widgets: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading widgets');
                });
        }
        
        function selectWidget(widgetId, widgetName) {
            currentEmbedWidget = widgetId;
            document.getElementById('embedWidgetName').textContent = widgetName;
            document.getElementById('embedWidgetId').textContent = widgetId;
            document.getElementById('embedSpecificWidget').checked = true;
            
            // Close widget list modal and show embed modal
            bootstrap.Modal.getInstance(document.getElementById('widgetListModal')).hide();
            
            generateEmbedCode();
            new bootstrap.Modal(document.getElementById('embedCodeModal')).show();
        }
        
        function generateEmbedCode() {
            const specificWidget = document.getElementById('embedSpecificWidget').checked;
            const async = document.getElementById('embedAsync').checked;
            const widgetId = currentEmbedWidget;
            const baseUrl = window.location.origin;
            
            let embedCode = '';
            
            if (specificWidget && widgetId) {
                // Embed specific widget
                if (async) {
                    embedCode = `<!-- Popup Widget: ${document.getElementById('embedWidgetName').textContent} -->
<script>
(function() {
    var script = document.createElement('script');
    script.src = '${baseUrl}/popup-widget.js';
    script.async = true;
    script.onload = function() {
        if (window.PopupWidget) {
            PopupWidget.show(${widgetId});
        }
    };
    document.head.appendChild(script);
})();
</script>`;
                } else {
                    embedCode = `<!-- Popup Widget: ${document.getElementById('embedWidgetName').textContent} -->
<script src="${baseUrl}/popup-widget.js"></script>
<script>
if (window.PopupWidget) {
    PopupWidget.show(${widgetId});
}
</script>`;
                }
            } else {
                // Embed all widgets
                if (async) {
                    embedCode = `<!-- All Popup Widgets -->
<script>
(function() {
    var script = document.createElement('script');
    script.src = '${baseUrl}/popup-widget.js';
    script.async = true;
    document.head.appendChild(script);
})();
</script>`;
                } else {
                    embedCode = `<!-- All Popup Widgets -->
<script src="${baseUrl}/popup-widget.js"></script>`;
                }
            }
            
            document.getElementById('embedCodeContent').textContent = embedCode;
        }
        
        function copyEmbedCode() {
            const codeElement = document.getElementById('embedCodeContent');
            const textArea = document.createElement('textarea');
            textArea.value = codeElement.textContent;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            // Show feedback
            const copyBtns = document.querySelectorAll('.copy-btn');
            copyBtns.forEach(btn => {
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                }, 2000);
            });
        }
        
        // Event listeners for embed options
        document.getElementById('embedSpecificWidget').addEventListener('change', generateEmbedCode);
        document.getElementById('embedAsync').addEventListener('change', generateEmbedCode);
        
        function clearAnalytics() {
            if (confirm('WARNING: This will permanently delete all analytics data. This action cannot be undone. Continue?')) {
                fetch('api/settings.php?action=clear_analytics', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Analytics data cleared successfully!');
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error clearing analytics data');
                });
            }
        }
    </script>
</body>
</html>