<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popup Widgets - <?php echo Config::get('app_name'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
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
        
        .widget-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        
        .widget-card:hover {
            transform: translateY(-2px);
        }
        
        .widget-preview {
            border: 2px dashed #e3e3e3;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background: #fafafa;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
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
        
        .widget-type-card {
            border: 2px solid #e3e3e3;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .widget-type-card:hover,
        .widget-type-card.selected {
            border-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.05);
        }
        
        .widget-type-card i {
            font-size: 32px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .color-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .color-input {
            width: 50px;
            height: 40px;
            border: 2px solid #e3e3e3;
            border-radius: 10px 0 0 10px;
            cursor: pointer;
        }
        
        .color-text {
            flex: 1;
            border-radius: 0 10px 10px 0;
            border-left: none;
        }
        
        .embed-code-modal {
            max-width: 600px;
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
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link active" href="widgets.php">
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
                    <h4 class="mb-0">Popup Widgets</h4>
                    <small class="text-muted">Create and manage your popup widgets</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-primary" onclick="showCreateWidget()">
                        <i class="fas fa-plus"></i> Create New Widget
                    </button>
                </div>
            </div>
        </div>
        
        <?php display_flash_message(); ?>
        
        <!-- Widget List -->
        <div id="widgetList" class="row">
            <div class="col-12">
                <div class="widget-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Your Widgets</h5>
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control" id="searchWidget" placeholder="Search widgets..." style="width: 200px;">
                            <select class="form-select" id="filterStatus" style="width: 150px;">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Views</th>
                                    <th>Clicks</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="widgetTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <i class="fas fa-spinner fa-spin"></i> Loading widgets...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Create/Edit Widget Modal -->
        <div class="modal fade" id="widgetModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="widgetModalTitle">Create New Widget</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="widgetForm">
                            <input type="hidden" id="widgetId">
                            
                            <!-- Basic Information -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Widget Name</label>
                                    <input type="text" class="form-control" id="widgetName" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Widget Title</label>
                                    <input type="text" class="form-control" id="widgetTitle" required>
                                </div>
                            </div>
                            
                            <!-- Widget Type Selection -->
                            <div class="mb-3">
                                <label class="form-label">Widget Type</label>
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <div class="widget-type-card" data-type="modal">
                                            <i class="fas fa-square"></i>
                                            <p class="mb-0">Modal</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="widget-type-card" data-type="slide_in">
                                            <i class="fas fa-arrow-right"></i>
                                            <p class="mb-0">Slide In</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="widget-type-card" data-type="notification">
                                            <i class="fas fa-bell"></i>
                                            <p class="mb-0">Notification</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="widget-type-card" data-type="exit_intent">
                                            <i class="fas fa-sign-out-alt"></i>
                                            <p class="mb-0">Exit Intent</p>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="widgetType" value="modal" required>
                            </div>
                            
                            <!-- Content -->
                            <div class="mb-3">
                                <label class="form-label">Content</label>
                                <textarea class="form-control" id="widgetContent" rows="4" required></textarea>
                            </div>
                            
                            <!-- Appearance Settings -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Width (px)</label>
                                    <input type="number" class="form-control" id="widgetWidth" value="400" min="200" max="800">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Height (px)</label>
                                    <input type="number" class="form-control" id="widgetHeight" value="300" min="150" max="600">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Background Color</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" class="color-input" id="widgetBgColor" value="#ffffff">
                                        <input type="text" class="form-control color-text" id="widgetBgColorText" value="#ffffff">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Text Color</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" class="color-input" id="widgetTextColor" value="#333333">
                                        <input type="text" class="form-control color-text" id="widgetTextColorText" value="#333333">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Button Settings -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Button Text</label>
                                    <input type="text" class="form-control" id="widgetButtonText" value="Close">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Button Color</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" class="color-input" id="widgetButtonColor" value="#007bff">
                                        <input type="text" class="form-control color-text" id="widgetButtonColorText" value="#007bff">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Position</label>
                                    <select class="form-select" id="widgetPosition">
                                        <option value="center">Center</option>
                                        <option value="top_left">Top Left</option>
                                        <option value="top_right">Top Right</option>
                                        <option value="bottom_left">Bottom Left</option>
                                        <option value="bottom_right">Bottom Right</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Display Settings -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Show After (seconds)</label>
                                    <input type="number" class="form-control" id="widgetShowAfter" value="0" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Auto Close (seconds)</label>
                                    <input type="number" class="form-control" id="widgetAutoClose" value="0" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Scroll Trigger (%)</label>
                                    <input type="number" class="form-control" id="widgetScrollPercentage" value="50" min="0" max="100">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Max Shows/User</label>
                                    <input type="number" class="form-control" id="widgetMaxShows" value="0" min="0">
                                </div>
                            </div>
                            
                            <!-- Display Options -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Display Options</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="widgetShowClose">
                                                <label class="form-check-label" for="widgetShowClose">
                                                    Show Close Button
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="widgetShowOnScroll">
                                                <label class="form-check-label" for="widgetShowOnScroll">
                                                    Show on Scroll
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="widgetShowOnExit">
                                                <label class="form-check-label" for="widgetShowOnExit">
                                                    Show on Exit
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="widgetIsActive" checked>
                                                <label class="form-check-label" for="widgetIsActive">
                                                    Active
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Date Range -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="text" class="form-control" id="widgetStartDate" placeholder="Optional">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input type="text" class="form-control" id="widgetEndDate" placeholder="Optional">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="saveWidget()">Save Widget</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Embed Code Modal -->
    <div class="modal fade" id="embedCodeModal" tabindex="-1">
        <div class="modal-dialog modal-lg embed-code-modal">
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let currentEditingWidget = null;
        
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }
        
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        }
        
        function showCreateWidget() {
            currentEditingWidget = null;
            document.getElementById('widgetModalTitle').textContent = 'Create New Widget';
            document.getElementById('widgetForm').reset();
            document.getElementById('widgetType').value = 'modal';
            document.querySelector('.widget-type-card[data-type="modal"]').classList.add('selected');
            new bootstrap.Modal(document.getElementById('widgetModal')).show();
        }
        
        function editWidget(id) {
            currentEditingWidget = id;
            loadWidgetForEdit(id);
        }
        
        function loadWidgetForEdit(id) {
            fetch(`api/widgets.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const widget = data.widget;
                        document.getElementById('widgetModalTitle').textContent = 'Edit Widget';
                        
                        // Fill form fields
                        document.getElementById('widgetId').value = widget.id;
                        document.getElementById('widgetName').value = widget.name;
                        document.getElementById('widgetTitle').value = widget.title;
                        document.getElementById('widgetContent').value = widget.content;
                        document.getElementById('widgetType').value = widget.type;
                        document.getElementById('widgetWidth').value = widget.width;
                        document.getElementById('widgetHeight').value = widget.height;
                        document.getElementById('widgetBgColor').value = widget.background_color;
                        document.getElementById('widgetBgColorText').value = widget.background_color;
                        document.getElementById('widgetTextColor').value = widget.text_color;
                        document.getElementById('widgetTextColorText').value = widget.text_color;
                        document.getElementById('widgetButtonText').value = widget.button_text;
                        document.getElementById('widgetButtonColor').value = widget.button_color;
                        document.getElementById('widgetButtonColorText').value = widget.button_color;
                        document.getElementById('widgetPosition').value = widget.position;
                        document.getElementById('widgetShowAfter').value = widget.show_after;
                        document.getElementById('widgetAutoClose').value = widget.auto_close;
                        document.getElementById('widgetScrollPercentage').value = widget.scroll_percentage;
                        document.getElementById('widgetMaxShows').value = widget.max_shows_per_user;
                        document.getElementById('widgetStartDate').value = widget.start_date || '';
                        document.getElementById('widgetEndDate').value = widget.end_date || '';
                        
                        // Checkboxes
                        document.getElementById('widgetShowClose').checked = widget.show_close_button;
                        document.getElementById('widgetShowOnScroll').checked = widget.show_on_scroll;
                        document.getElementById('widgetShowOnExit').checked = widget.show_on_exit;
                        document.getElementById('widgetIsActive').checked = widget.is_active;
                        
                        // Select widget type card
                        document.querySelectorAll('.widget-type-card').forEach(card => {
                            card.classList.remove('selected');
                        });
                        document.querySelector(`.widget-type-card[data-type="${widget.type}"]`).classList.add('selected');
                        
                        new bootstrap.Modal(document.getElementById('widgetModal')).show();
                    } else {
                        alert('Error loading widget: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading widget');
                });
        }
        
        function saveWidget() {
            const formData = new FormData();
            const widgetId = document.getElementById('widgetId').value;
            
            formData.append('action', widgetId ? 'update' : 'create');
            if (widgetId) formData.append('id', widgetId);
            
            formData.append('name', document.getElementById('widgetName').value);
            formData.append('title', document.getElementById('widgetTitle').value);
            formData.append('content', document.getElementById('widgetContent').value);
            formData.append('type', document.getElementById('widgetType').value);
            formData.append('width', document.getElementById('widgetWidth').value);
            formData.append('height', document.getElementById('widgetHeight').value);
            formData.append('background_color', document.getElementById('widgetBgColor').value);
            formData.append('text_color', document.getElementById('widgetTextColor').value);
            formData.append('button_text', document.getElementById('widgetButtonText').value);
            formData.append('button_color', document.getElementById('widgetButtonColor').value);
            formData.append('position', document.getElementById('widgetPosition').value);
            formData.append('show_after', document.getElementById('widgetShowAfter').value);
            formData.append('auto_close', document.getElementById('widgetAutoClose').value);
            formData.append('scroll_percentage', document.getElementById('widgetScrollPercentage').value);
            formData.append('max_shows_per_user', document.getElementById('widgetMaxShows').value);
            formData.append('start_date', document.getElementById('widgetStartDate').value);
            formData.append('end_date', document.getElementById('widgetEndDate').value);
            formData.append('show_close_button', document.getElementById('widgetShowClose').checked);
            formData.append('show_on_scroll', document.getElementById('widgetShowOnScroll').checked);
            formData.append('show_on_exit', document.getElementById('widgetShowOnExit').checked);
            formData.append('is_active', document.getElementById('widgetIsActive').checked);
            
            fetch('api/widgets.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('widgetModal')).hide();
                    loadWidgets();
                } else {
                    alert('Error saving widget: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving widget');
            });
        }
        
        function deleteWidget(id) {
            if (confirm('Are you sure you want to delete this widget? This action cannot be undone.')) {
                fetch(`api/widgets.php?action=delete&id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadWidgets();
                        } else {
                            alert('Error deleting widget: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting widget');
                    });
            }
        }
        
        function loadWidgets() {
            const searchTerm = document.getElementById('searchWidget').value;
            const statusFilter = document.getElementById('filterStatus').value;
            
            let url = 'api/widgets.php?action=list';
            if (searchTerm) url += `&search=${encodeURIComponent(searchTerm)}`;
            if (statusFilter !== '') url += `&status=${statusFilter}`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('widgetTableBody');
                        
                        if (data.widgets.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="7" class="text-center">No widgets found.</td></tr>';
                            return;
                        }
                        
                        tbody.innerHTML = data.widgets.map(widget => `
                            <tr>
                                <td>
                                    <strong>${widget.name}</strong><br>
                                    <small class="text-muted">${widget.title}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">${widget.type}</span>
                                </td>
                                <td>
                                    <span class="badge ${widget.is_active ? 'bg-success' : 'bg-secondary'}">
                                        ${widget.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </td>
                                <td>${widget.views || 0}</td>
                                <td>${widget.clicks || 0}</td>
                                <td><small>${new Date(widget.created_at).toLocaleDateString()}</small></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info" onclick="showEmbedCode(${widget.id})" title="Get Embed Code">
                                            <i class="fas fa-code"></i>
                                        </button>
                                        <button class="btn btn-outline-primary" onclick="editWidget(${widget.id})" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="deleteWidget(${widget.id})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        console.error('Error loading widgets:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
        
        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date pickers
            flatpickr("#widgetStartDate", {
                dateFormat: "Y-m-d",
                allowInput: true
            });
            
            flatpickr("#widgetEndDate", {
                dateFormat: "Y-m-d",
                allowInput: true
            });
            
            // Widget type selection
            document.querySelectorAll('.widget-type-card').forEach(card => {
                card.addEventListener('click', function() {
                    document.querySelectorAll('.widget-type-card').forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                    document.getElementById('widgetType').value = this.dataset.type;
                });
            });
            
            // Color input synchronization
            document.getElementById('widgetBgColor').addEventListener('input', function() {
                document.getElementById('widgetBgColorText').value = this.value;
            });
            
            document.getElementById('widgetBgColorText').addEventListener('input', function() {
                document.getElementById('widgetBgColor').value = this.value;
            });
            
            document.getElementById('widgetTextColor').addEventListener('input', function() {
                document.getElementById('widgetTextColorText').value = this.value;
            });
            
            document.getElementById('widgetTextColorText').addEventListener('input', function() {
                document.getElementById('widgetTextColor').value = this.value;
            });
            
            document.getElementById('widgetButtonColor').addEventListener('input', function() {
                document.getElementById('widgetButtonColorText').value = this.value;
            });
            
            document.getElementById('widgetButtonColorText').addEventListener('input', function() {
                document.getElementById('widgetButtonColor').value = this.value;
            });
            
            // Search and filter
            document.getElementById('searchWidget').addEventListener('input', loadWidgets);
            document.getElementById('filterStatus').addEventListener('change', loadWidgets);
            
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
            
            // Load initial widgets
            loadWidgets();
        });
    </script>
</body>
</html>