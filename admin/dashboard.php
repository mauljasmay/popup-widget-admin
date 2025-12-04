<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo Config::get('app_name'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css" rel="stylesheet">
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
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
            border: none;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .stat-card.primary .stat-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .stat-card.success .stat-icon {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        
        .stat-card.warning .stat-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .stat-card.info .stat-icon {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .recent-widgets {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .widget-item {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.3s ease;
        }
        
        .widget-item:hover {
            background-color: #f8f9fa;
        }
        
        .widget-item:last-child {
            border-bottom: none;
        }
        
        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .embed-code {
            background: #f8f9fa;
            border: 2px solid #e3e3e3;
            border-radius: 10px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            position: relative;
            max-height: 300px;
            overflow-y: auto;
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
                <a class="nav-link active" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link" href="widgets.php">
                    <i class="fas fa-window-restore"></i> Popup Widgets
                </a>
                <a class="nav-link" href="analytics.php">
                    <i class="fas fa-chart-line"></i> Analytics
                </a>
                <a class="nav-link" href="settings.php">
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
                    <h4 class="mb-0">Dashboard</h4>
                    <small class="text-muted">Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-primary">
                        <i class="fas fa-user-circle"></i> <?php echo ucfirst($user['role']); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <?php display_flash_message(); ?>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="fas fa-window-restore"></i>
                    </div>
                    <h3 class="mb-1" id="totalWidgets">0</h3>
                    <p class="text-muted mb-0">Total Widgets</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 class="mb-1" id="activeWidgets">0</h3>
                    <p class="text-muted mb-0">Active Widgets</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 class="mb-1" id="totalViews">0</h3>
                    <p class="text-muted mb-0">Total Views</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-mouse-pointer"></i>
                    </div>
                    <h3 class="mb-1" id="totalClicks">0</h3>
                    <p class="text-muted mb-0">Total Clicks</p>
                </div>
            </div>
        </div>
        
        <!-- Charts and Recent Widgets -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="chart-container">
                    <h5 class="mb-4">Widget Performance (Last 7 Days)</h5>
                    <canvas id="performanceChart" height="100"></canvas>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="recent-widgets">
                    <h5 class="mb-4">Recent Widgets</h5>
                    <div id="recentWidgetsList">
                        <div class="text-center text-muted">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }
        
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        }
        
        // Load dashboard statistics
        async function loadDashboardStats() {
            try {
                const response = await fetch('api/dashboard_stats.php');
                const data = await response.json();
                
                document.getElementById('totalWidgets').textContent = data.totalWidgets || 0;
                document.getElementById('activeWidgets').textContent = data.activeWidgets || 0;
                document.getElementById('totalViews').textContent = data.totalViews || 0;
                document.getElementById('totalClicks').textContent = data.totalClicks || 0;
                
                // Load recent widgets
                loadRecentWidgets(data.recentWidgets || []);
                
                // Load performance chart
                loadPerformanceChart(data.chartData || {});
            } catch (error) {
                console.error('Error loading dashboard stats:', error);
            }
        }
        
        function loadRecentWidgets(widgets) {
            const container = document.getElementById('recentWidgetsList');
            
            if (widgets.length === 0) {
                container.innerHTML = '<p class="text-muted">No widgets found.</p>';
                return;
            }
            
            container.innerHTML = widgets.map(widget => `
                <div class="widget-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${widget.name}</h6>
                            <small class="text-muted">${widget.type} • ${widget.views} views</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-sm btn-outline-info" onclick="showEmbedCode(${widget.id})" title="Get Embed Code">
                                <i class="fas fa-code"></i>
                            </button>
                            <span class="badge-status ${widget.is_active ? 'bg-success' : 'bg-secondary'}">
                                ${widget.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        function loadPerformanceChart(chartData) {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels || [],
                    datasets: [{
                        label: 'Views',
                        data: chartData.views || [],
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Clicks',
                        data: chartData.clicks || [],
                        borderColor: '#764ba2',
                        backgroundColor: 'rgba(118, 75, 162, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        // Embed Code Functions
        let currentEmbedWidget = null;
        
        function showEmbedCode(widgetId) {
            currentEmbedWidget = widgetId;
            
            // Load widget details
            fetch(`api/widgets.php?action=get&id=${widgetId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const widget = data.widget;
                        document.getElementById('embedWidgetName').textContent = widget.name;
                        document.getElementById('embedWidgetId').textContent = widget.id;
                        
                        generateEmbedCode();
                        new bootstrap.Modal(document.getElementById('embedCodeModal')).show();
                    } else {
                        alert('Error loading widget: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading widget');
                });
        }
        
        function generateEmbedCode() {
            const specificWidget = document.getElementById('embedSpecificWidget').checked;
            const async = document.getElementById('embedAsync').checked;
            const widgetId = currentEmbedWidget;
            const baseUrl = window.location.origin;
            
            let embedCode = '';
            
            if (specificWidget) {
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
        
        // Load stats when page loads
        document.addEventListener('DOMContentLoaded', loadDashboardStats);
    </script>
</body>
</html>