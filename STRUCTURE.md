# Popup Widget Admin - Project Structure

```
popup-widget-admin/
├── admin/                          # Admin panel files
│   ├── api/                        # API endpoints
│   │   ├── analytics.php           # Analytics tracking
│   │   ├── analytics_dashboard.php # Analytics dashboard data
│   │   ├── dashboard_stats.php     # Dashboard statistics
│   │   ├── settings.php            # Settings management
│   │   ├── widgets.php             # Widget CRUD operations
│   │   └── widgets_public.php      # Public widget data
│   ├── analytics.php               # Analytics dashboard page
│   ├── dashboard.php               # Main dashboard page
│   ├── login.php                   # Login page
│   ├── logout.php                  # Logout handler
│   ├── settings.php                # Settings page
│   └── widgets.php                 # Widget management page
├── config/                         # Configuration files
│   ├── database.php                # Database connection and config
│   └── init.php                    # Application initialization
├── includes/                       # Shared includes
│   └── auth.php                    # Authentication system
├── uploads/                        # File upload directory
├── logs/                           # Log files directory
├── database.sql                    # Database schema
├── popup-widget.js                 # Frontend widget script
├── .htaccess                       # Apache configuration
├── README.md                       # Main documentation
├── INSTALL.md                      # Installation guide
└── index.php                       # Optional landing page
```

## Key Files Description

### Core System Files
- **config/database.php**: Database connection and configuration
- **config/init.php**: Application initialization and utilities
- **includes/auth.php**: Authentication and session management

### Admin Panel
- **admin/dashboard.php**: Main dashboard with statistics
- **admin/widgets.php**: Widget creation and management
- **admin/analytics.php**: Analytics and reporting
- **admin/settings.php**: System and user settings

### API Endpoints
- **admin/api/widgets.php**: Widget CRUD operations
- **admin/api/analytics.php**: Analytics data collection
- **admin/api/widgets_public.php**: Public widget data (no auth required)

### Frontend Integration
- **popup-widget.js**: JavaScript file for displaying widgets on websites

### Database
- **database.sql**: Complete database schema with sample data

## Security Features
- Password hashing with PHP's built-in functions
- Session-based authentication with timeout
- CSRF protection on forms
- SQL injection prevention with prepared statements
- File upload validation and sanitization
- Security headers via .htaccess

## Integration Method
Simply add one line to any website:
```html
<script src="https://your-domain.com/popup-widget.js"></script>
```

The script automatically:
- Loads active widgets from your admin panel
- Displays them according to your settings
- Tracks user interactions
- Respects targeting rules and limits