# Environment Configuration Guide

This guide explains how to set up environment variables for the Popup Widget Admin Panel.

## Quick Setup

1. **Copy the example files:**
   ```bash
   # Main application
   cp .env.example .env
   
   # Next.js frontend
   cp .env.local.example .env.local
   
   # Mini services (if using)
   cp mini-services/.env.example mini-services/.env
   ```

2. **Update the values in each .env file with your actual configuration**

## Environment Files Overview

### 1. `.env` - Main Application
Contains configuration for the PHP backend and database.

**Required Variables:**
- `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` - Database connection
- `APP_URL` - Your application URL
- `JWT_SECRET` - Secret for JWT tokens

### 2. `.env.local` - Next.js Frontend
Contains configuration for the Next.js frontend application.

**Required Variables:**
- `DATABASE_URL` - Database connection string
- `NEXTAUTH_SECRET` - Authentication secret
- `NEXT_PUBLIC_APP_URL` - Frontend URL

### 3. `.env.production` - Production Environment
Production-specific configuration with enhanced security settings.

### 4. `mini-services/.env` - WebSocket Services
Configuration for additional WebSocket and API services.

## Security Best Practices

### Generate Secure Keys

**JWT Secret (256-bit):**
```bash
openssl rand -base64 32
```

**Encryption Key (256-bit):**
```bash
openssl rand -hex 32
```

**NextAuth Secret:**
```bash
openssl rand -base64 32
```

### Database Security

1. **Use strong passwords:**
   ```bash
   # Generate secure database password
   openssl rand -base64 24
   ```

2. **Limit database user permissions:**
   - Only grant necessary privileges
   - Use separate users for different environments

3. **Enable SSL for database connections** in production

### Environment-Specific Settings

#### Development (.env)
- `DEBUG=true` - Show detailed errors
- `SHOW_ERRORS=true` - Display errors in browser
- `SESSION_SECURE=false` - For local HTTP development

#### Production (.env.production)
- `DEBUG=false` - Hide sensitive information
- `SHOW_ERRORS=false` - Don't expose errors to users
- `SESSION_SECURE=true` - Require HTTPS
- `FORCE_HTTPS=true` - Redirect HTTP to HTTPS

## Configuration Examples

### Local Development Setup
```bash
# .env
DB_HOST=localhost
DB_NAME=popup_widget_dev
DB_USER=root
DB_PASSWORD=your_local_password
APP_URL=http://localhost:3002
JWT_SECRET=your_generated_secret_here
```

### Production Setup
```bash
# .env.production
DB_HOST=your-production-db-host.com
DB_NAME=popup_widget_prod
DB_USER=popup_widget_user
DB_PASSWORD=your_secure_production_password
APP_URL=https://yourdomain.com
JWT_SECRET=your_production_jwt_secret
```

## File Upload Configuration

### Development
```bash
MAX_FILE_SIZE=5242880  # 5MB
UPLOAD_PATH=uploads/
ALLOWED_FILE_TYPES=jpg,jpeg,png,gif,pdf
```

### Production
```bash
MAX_FILE_SIZE=2097152  # 2MB (more restrictive)
UPLOAD_PATH=/var/www/popup-widget/uploads/
ALLOWED_FILE_TYPES=jpg,jpeg,png,gif  # Only images
```

## Email Configuration

### Gmail (Development)
```bash
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your_email@gmail.com
SMTP_PASSWORD=your_app_password_here
```

### Production Email Service
```bash
SMTP_HOST=your-email-provider.com
SMTP_PORT=587
SMTP_USERNAME=noreply@yourdomain.com
SMTP_PASSWORD=your_production_email_password
```

## Cache Configuration

### Development (File Cache)
```bash
CACHE_DRIVER=file
CACHE_TTL=3600  # 1 hour
```

### Production (Redis)
```bash
CACHE_DRIVER=redis
CACHE_TTL=1800  # 30 minutes
REDIS_HOST=your-redis-host.com
REDIS_PORT=6379
REDIS_PASSWORD=your_redis_password
```

## Validation

After setting up your environment variables:

1. **Test database connection:**
   ```bash
   php -r "require_once 'config/database.php'; echo 'Database connection: ' . (DB ? 'Success' : 'Failed') . PHP_EOL;"
   ```

2. **Verify application loads:**
   ```bash
   # Start the development server
   npm run dev
   
   # Check if application loads without errors
   curl http://localhost:3002
   ```

3. **Test authentication:**
   - Try logging in with default credentials
   - Verify session management works

## Troubleshooting

### Common Issues

1. **Database connection failed:**
   - Check database credentials
   - Verify database server is running
   - Ensure database exists

2. **JWT errors:**
   - Verify JWT_SECRET is set
   - Check secret length (minimum 32 characters)

3. **File upload issues:**
   - Check upload directory permissions
   - Verify file size limits
   - Ensure allowed file types are correct

4. **Session issues:**
   - Check session path is writable
   - Verify session domain matches APP_URL
   - Check cookie settings

### Debug Mode

Enable debug mode to troubleshoot issues:
```bash
# In .env
DEBUG=true
SHOW_ERRORS=true
LOG_LEVEL=debug
```

**Remember to disable debug mode in production!**

## Environment Variables Reference

| Variable | Type | Required | Default | Description |
|----------|------|----------|---------|-------------|
| DB_HOST | string | Yes | localhost | Database host |
| DB_PORT | integer | Yes | 3306 | Database port |
| DB_NAME | string | Yes | - | Database name |
| DB_USER | string | Yes | - | Database username |
| DB_PASSWORD | string | Yes | - | Database password |
| APP_URL | string | Yes | - | Application URL |
| APP_ENV | string | No | development | Environment type |
| JWT_SECRET | string | Yes | - | JWT signing secret |
| DEBUG | boolean | No | false | Enable debug mode |
| MAX_FILE_SIZE | integer | No | 5242880 | Max upload size in bytes |

For a complete list, refer to the example files above.