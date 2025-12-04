# Popup Widget Admin Panel

A comprehensive PHP-based admin panel for creating and managing popup widgets on websites. This system allows you to create, customize, and track various types of popup widgets with detailed analytics.

## Features

### 🎯 Widget Management
- **Multiple Widget Types**: Modal, Slide-in, Notification, Exit Intent
- **Full Customization**: Colors, sizes, positions, timing
- **Advanced Targeting**: Page-specific targeting, date ranges, user limits
- **Rich Content**: HTML content support with custom buttons

### 🔗 Embed Code System
- **Multiple Access Points**: Get embed code from Dashboard, Widgets, Analytics, or Settings
- **Flexible Options**: Choose between all widgets or specific widget embedding
- **Loading Methods**: Asynchronous (recommended) or synchronous loading
- **One-Click Copy**: Copy generated code with single click
- **Code Customization**: Choose async/sync and widget-specific options

### 📊 Analytics & Tracking
- **Real-time Analytics**: Track views, clicks, and close rates
- **Performance Charts**: Visual representation of widget performance
- **User Insights**: Unique user tracking and session management
- **Export Data**: CSV export for detailed analysis

### 🔐 Security & Authentication
- **Role-based Access**: Super Admin, Admin, Editor roles
- **Secure Sessions**: Timeout-based session management
- **Password Protection**: Hashed passwords with secure authentication

### 🎨 Modern UI/UX
- **Responsive Design**: Works on all devices
- **Bootstrap 5**: Modern, clean interface
- **Interactive Charts**: Chart.js for data visualization
- **Dark Mode Ready**: Easy to implement dark theme

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for dependency management)

### Step 1: Database Setup
1. Create a new MySQL database:
   ```sql
   CREATE DATABASE popup_widget_admin;
   ```

2. Import the database schema:
   ```bash
   mysql -u username -p popup_widget_admin < database.sql
   ```

### Step 2: Configuration
1. Update database connection in `config/database.php`:
   ```php
   private $host = 'localhost';
   private $db_name = 'popup_widget_admin';
   private $username = 'your_db_username';
   private $password = 'your_db_password';
   ```

### Step 3: Web Server Setup
1. Place all files in your web directory (e.g., `/var/www/html/popup-admin/`)
2. Set proper permissions:
   ```bash
   chmod -R 755 /var/www/html/popup-admin/
   chmod -R 777 /var/www/html/popup-admin/uploads/
   ```

3. Configure Apache/Nginx to point to the project directory

### Step 4: Access the Admin Panel
1. Open your browser and navigate to `http://your-domain.com/admin/`
2. Login with default credentials:
   - Username: `admin`
   - Password: `admin123`

## Integration Guide

### Adding Widgets to Your Website

#### Method 1: Simple Integration (All Widgets)
Add this single line to your website before the closing `</body>` tag:
```html
<script src="https://your-domain.com/popup-widget.js"></script>
```

#### Method 2: Advanced Integration (From Admin Panel)

1. **From Dashboard:**
   - Click the embed code button (</>) on any widget in "Recent Widgets"
   - Choose embed options and copy the generated code

2. **From Widgets Page:**
   - Click the embed code button (</>) in the Actions column
   - Choose embed options and copy the generated code

3. **From Analytics Page:**
   - Click "Get Embed Code" button in the top header
   - Choose "All Widgets" or select specific widget

4. **From Settings Page:**
   - Go to Integration Code section
   - Use "Advanced Embed Options" for customization
   - Use "Choose Specific Widget" to browse all widgets

#### Embed Options

**Widget Selection:**
- **All Widgets**: Loads all active widgets automatically
- **Specific Widget**: Loads only the selected widget by ID

**Loading Method:**
- **Asynchronous** (Recommended): Loads script without blocking page rendering
- **Synchronous**: Loads script immediately, may slow page load

**Generated Code Examples:**

All Widgets (Async):
```html
<!-- All Popup Widgets -->
<script>
(function() {
    var script = document.createElement('script');
    script.src = 'https://your-domain.com/popup-widget.js';
    script.async = true;
    document.head.appendChild(script);
})();
</script>
```

Specific Widget (Async):
```html
<!-- Popup Widget: My Newsletter Signup -->
<script>
(function() {
    var script = document.createElement('script');
    script.src = 'https://your-domain.com/popup-widget.js';
    script.async = true;
    script.onload = function() {
        if (window.PopupWidget) {
            PopupWidget.show(123);
        }
    };
    document.head.appendChild(script);
})();
</script>
```

### Widget Types

#### Modal Popups
- Centered overlays with customizable content
- Ideal for announcements, promotions, and newsletters
- Support for auto-close and timing controls

#### Slide-in Widgets
- Slide in from the edges of the screen
- Perfect for chat widgets, notifications, and offers
- Can be triggered by scroll or time

#### Notification Bars
- Top or bottom positioned notification bars
- Great for alerts, updates, and quick messages
- Minimal intrusion design

#### Exit Intent Popups
- Triggered when user attempts to leave the page
- Excellent for lead capture and special offers
- Mouse movement detection

## API Endpoints

### Public Endpoints
- `GET /admin/api/widgets_public.php?action=active` - Get active widgets
- `POST /admin/api/analytics.php` - Track widget interactions

### Admin Endpoints (Authentication Required)
- `GET /admin/api/widgets.php` - Widget CRUD operations
- `GET /admin/api/analytics_dashboard.php` - Analytics data
- `POST /admin/api/settings.php` - System settings

## Configuration Options

### Widget Settings
- **Display Timing**: Show after X seconds, on scroll, or exit intent
- **Positioning**: Center, corners, or custom positions
- **Styling**: Custom colors, fonts, and dimensions
- **Targeting**: Specific pages, date ranges, user limits
- **Behavior**: Auto-close, close buttons, interaction tracking

### System Settings
- **Session Timeout**: Default 1 hour
- **File Upload Limits**: Default 5MB
- **Analytics Tracking**: Enable/disable tracking
- **User Roles**: Three-tier permission system

## Security Features

### Authentication
- Password hashing with PHP's `password_hash()`
- Session-based authentication with timeout
- CSRF protection on all forms
- SQL injection prevention with prepared statements

### Access Control
- Role-based permissions (Super Admin, Admin, Editor)
- IP-based tracking for analytics
- Secure file upload handling
- Input sanitization and validation

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `config/database.php`
   - Ensure MySQL server is running
   - Verify database exists and user has permissions

2. **Widgets Not Displaying**
   - Check browser console for JavaScript errors
   - Verify the integration script is correctly placed
   - Ensure widgets are set to "Active" status

3. **Analytics Not Tracking**
   - Check if analytics endpoint is accessible
   - Verify JavaScript console for errors
   - Ensure tracking is enabled in settings

4. **Permission Issues**
   - Check file permissions on uploads directory
   - Verify web server user has write access
   - Check PHP error logs for specific issues

### Debug Mode
To enable debug mode, add this to your PHP files:
```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## Support & Maintenance

### Regular Maintenance
- Clear analytics data periodically to optimize performance
- Update passwords regularly
- Monitor database size and optimize as needed
- Keep PHP and MySQL versions updated

### Backup Strategy
- Use the built-in backup feature in Settings
- Schedule regular database backups
- Test backup restoration process
- Keep backups in secure location

## License

This project is open-source and available under the MIT License.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## Version History

### v1.0.0
- Initial release
- Basic widget management
- Analytics tracking
- User authentication
- Responsive admin interface

---

For technical support or questions, please refer to the documentation or contact your system administrator.