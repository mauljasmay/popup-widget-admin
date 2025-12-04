# Production Deployment Guide

## 🚀 Production Deployment

### Prerequisites
- Node.js 14+ 
- MySQL 5.7+ or MariaDB 10.3+
- Nginx or Apache web server
- SSL certificate (recommended)
- Domain name

### Quick Deployment

1. **Clone repository to production server**
   ```bash
   git clone <repository-url> /var/www/popup-widget
   cd /var/www/popup-widget
   ```

2. **Run deployment script**
   ```bash
   ./deploy.sh
   ```

### Manual Deployment Steps

1. **Install dependencies**
   ```bash
   npm ci --production
   ```

2. **Build application**
   ```bash
   npm run build
   ```

3. **Configure environment**
   ```bash
   cp .env.production .env
   # Edit .env with your production settings
   ```

4. **Setup database**
   ```bash
   mysql -u root -p
   CREATE DATABASE popup_widget_admin;
   CREATE USER 'popup_user'@'localhost' IDENTIFIED BY 'secure_password';
   GRANT ALL PRIVILEGES ON popup_widget_admin.* TO 'popup_user'@'localhost';
   FLUSH PRIVILEGES;
   
   mysql -u popup_user -p popup_widget_admin < database.sql
   ```

5. **Start production server**
   ```bash
   NODE_ENV=production node .next/standalone/server.js
   ```

### Nginx Configuration

Create `/etc/nginx/sites-available/popup-widget`:
```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    ssl_certificate /etc/ssl/certs/yourdomain.crt;
    ssl_certificate_key /etc/ssl/private/yourdomain.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    root /var/www/popup-widget/.next/standalone;
    index index.html index.htm;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self';" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # Static files caching
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # API routes
    location /api/ {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
    }

    # Main application
    location / {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
    }

    # Error pages
    error_page 404 /404.html;
    error_page 500 502 503 504 /50x.html;
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/popup-widget /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Apache Configuration

Create `/etc/apache2/sites-available/popup-widget.conf`:
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    Redirect permanent / https://yourdomain.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /var/www/popup-widget/.next/standalone
    
    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/yourdomain.crt
    SSLCertificateKeyFile /etc/ssl/private/yourdomain.key
    
    # Security headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set X-Content-Type-Options "nosniff"
    Header always set Referrer-Policy "no-referrer-when-downgrade"
    Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self';"
    
    # Compression
    LoadModule deflate_module modules/mod_deflate.so
    <Location />
        SetOutputFilter DEFLATE
        SetEnvIfNoCase Request_URI \
            \.(?:gif|jpe?g|png)$ no-gzip dont-vary
        SetEnvIfNoCase Request_URI \
            \.(?:exe|t?gz|zip|bz2|sit|rar)$ no-gzip dont-vary
    </Location>
    
    # Proxy to Node.js app
    ProxyPreserveHost On
    ProxyRequests Off
    ProxyPass / http://localhost:3000/
    ProxyPassReverse / http://localhost:3000/
</VirtualHost>
```

Enable site:
```bash
sudo a2ensite popup-widget
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo apache2ctl configtest
sudo systemctl reload apache2
```

### Systemd Service

Create `/etc/systemd/system/popup-widget.service`:
```ini
[Unit]
Description=Popup Widget Admin
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/popup-widget
Environment=NODE_ENV=production
ExecStart=/usr/bin/node .next/standalone/server.js
Restart=always
RestartSec=10
StandardOutput=syslog
StandardError=syslog
SyslogIdentifier=popup-widget

[Install]
WantedBy=multi-user.target
```

Enable and start service:
```bash
sudo systemctl daemon-reload
sudo systemctl enable popup-widget
sudo systemctl start popup-widget
sudo systemctl status popup-widget
```

### SSL Certificate Setup

#### Let's Encrypt (Free)
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

#### Commercial SSL
1. Purchase SSL certificate
2. Upload certificate files to `/etc/ssl/certs/`
3. Upload private key to `/etc/ssl/private/`
4. Set proper permissions:
   ```bash
   sudo chmod 600 /etc/ssl/private/yourdomain.key
   sudo chmod 644 /etc/ssl/certs/yourdomain.crt
   ```

### Monitoring and Logs

#### Log Files
- Application: `/var/www/popup-widget/logs/server.log`
- Error: `/var/www/popup-widget/logs/error.log`
- Nginx: `/var/log/nginx/access.log`
- System: `journalctl -u popup-widget -f`

#### Monitoring Commands
```bash
# Check service status
sudo systemctl status popup-widget

# View real-time logs
tail -f /var/www/popup-widget/logs/server.log

# Monitor system resources
htop
df -h
free -h

# Check network connections
netstat -tulpn | grep :3000
```

### Backup Strategy

#### Automated Backup Script
```bash
#!/bin/bash
# backup.sh
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/popup-widget"
APP_DIR="/var/www/popup-widget"

# Create backup
mysqldump -u popup_user -p popup_widget_admin > $BACKUP_DIR/db_backup_$DATE.sql
tar -czf $BACKUP_DIR/app_backup_$DATE.tar.gz -C $APP_DIR .

# Keep only last 30 days
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
```

#### Cron Job
```bash
# Edit crontab
crontab -e

# Add daily backup at 2 AM
0 2 * * * /path/to/backup.sh
```

### Performance Optimization

#### Node.js Performance
```bash
# Increase memory limit
export NODE_OPTIONS="--max-old-space-size=4096"

# Enable clustering
export NODE_CLUSTER_WORKERS=4
```

#### Database Optimization
```sql
-- MySQL configuration
SET GLOBAL innodb_buffer_pool_size = 2G;
SET GLOBAL innodb_log_file_size = 256M;
SET GLOBAL innodb_flush_log_at_trx_commit = 2;
```

### Security Hardening

#### Firewall Configuration
```bash
# UFW (Ubuntu)
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# iptables
sudo iptables -A INPUT -p tcp --dport 22 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 80 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 443 -j ACCEPT
sudo iptables -A INPUT -j DROP
```

#### Fail2Ban
```bash
sudo apt install fail2ban
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
# Edit jail.local to protect your application
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### Domain Configuration

#### DNS Records
```
A Record: yourdomain.com → YOUR_SERVER_IP
A Record: www.yourdomain.com → YOUR_SERVER_IP
CNAME: admin.yourdomain.com → yourdomain.com
MX Record: yourdomain.com → mail.yourdomain.com
```

#### Testing
```bash
# Test domain resolution
nslookup yourdomain.com
ping yourdomain.com

# Test SSL certificate
curl -I https://yourdomain.com
openssl s_client -connect yourdomain.com:443

# Test application
curl -I http://localhost:3000
```

### Troubleshooting

#### Common Issues
1. **Port 3000 in use**: Kill existing processes
   ```bash
   sudo lsof -i :3000
   sudo kill -9 PID
   ```

2. **Database connection**: Check credentials and permissions
3. **File permissions**: Ensure www-data owns application files
4. **SSL issues**: Verify certificate paths and permissions

#### Health Check Script
```bash
#!/bin/bash
# health-check.sh
if curl -f http://localhost:3000/health; then
    echo "✅ Application is healthy"
else
    echo "❌ Application is down"
    # Send alert
    systemctl restart popup-widget
fi
```

This production setup ensures high availability, security, and performance for your Popup Widget Admin Panel.