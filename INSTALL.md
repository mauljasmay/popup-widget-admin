# Installation Guide

## Quick Start

### 1. System Requirements
- PHP 7.4+ 
- MySQL 5.7+
- Apache/Nginx
- 50MB+ disk space

### 2. Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE popup_widget_admin;

# Import schema
mysql -u root -p popup_widget_admin < database.sql
```

### 3. Configuration
Edit `config/database.php`:
```php
private $host = 'localhost';
private $db_name = 'popup_widget_admin';
private $username = 'your_username';
private $password = 'your_password';
```

### 4. File Permissions
```bash
chmod -R 755 /path/to/project/
chmod -R 777 /path/to/project/uploads/
```

### 5. Access Admin Panel
Navigate to `http://your-domain.com/admin/`
- Username: `admin`
- Password: `admin123`

### 6. Integration
Add this to your website:
```html
<script src="http://your-domain.com/popup-widget.js"></script>
```

## Detailed Setup

### Apache Configuration
Create `.htaccess` in project root:
```apache
RewriteEngine On
RewriteRule ^admin/(.*)$ admin/$1 [L]
RewriteRule ^popup-widget.js$ popup-widget.js [L]
```

### Nginx Configuration
```nginx
location /popup-admin/ {
    root /var/www/html;
    index index.php;
    try_files $uri $uri/ /popup-admin/index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
```

### Virtual Host Setup
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/html/popup-admin
    
    <Directory /var/www/html/popup-admin>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## Post-Installation

### Change Default Password
1. Login to admin panel
2. Go to Settings → Account Settings
3. Update password and personal information

### Create Additional Users
1. Go to Settings → Users (Super Admin only)
2. Add new users with appropriate roles

### Configure Widgets
1. Go to Widgets → Create New Widget
2. Customize appearance and behavior
3. Set targeting rules
4. Activate widget

### Test Integration
1. Add integration script to test page
2. Verify widgets appear correctly
3. Check analytics tracking

## Troubleshooting

### Common Issues
- **404 Errors**: Check web server configuration
- **Database Errors**: Verify credentials and permissions
- **Permission Issues**: Check file/directory permissions
- **Widget Not Showing**: Check browser console for errors

### Debug Mode
Add to PHP files for debugging:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Security Recommendations

1. Change default admin password immediately
2. Use HTTPS in production
3. Restrict database user permissions
4. Regular backups
5. Keep PHP/MySQL updated
6. Monitor access logs

## Support

For additional support:
- Check README.md for detailed documentation
- Review error logs
- Test with different browsers
- Verify server requirements