#!/bin/bash

# Health Check Script for Popup Widget Admin

echo "🔍 Popup Widget Admin - Health Check"
echo "=================================="

# Check if production server is running
if pgrep -f "node .next/standalone/server.js" > /dev/null; then
    echo "✅ Production Server: Running"
    SERVER_PID=$(pgrep -f "node .next/standalone/server.js")
    echo "   PID: $SERVER_PID"
else
    echo "❌ Production Server: Not running"
    echo "   Starting production server..."
    NODE_ENV=production nohup node .next/standalone/server.js > server.log 2>&1 &
    sleep 3
fi

# Check port availability
if netstat -tuln | grep :3000 > /dev/null; then
    echo "✅ Port 3000: Available"
else
    echo "❌ Port 3000: Not available"
fi

# Check application response
if curl -s -f http://localhost:3000 > /dev/null; then
    echo "✅ Application: Responding"
    HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:3000)
    echo "   HTTP Status: $HTTP_STATUS"
else
    echo "❌ Application: Not responding"
fi

# Check database connection (if configured)
if [ -n "$DB_HOST" ]; then
    if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "SELECT 1" "$DB_NAME" > /dev/null 2>&1; then
        echo "✅ Database: Connected"
    else
        echo "❌ Database: Connection failed"
    fi
else
    echo "⚠️  Database: Not configured"
fi

# Check file permissions
if [ -w "logs" ]; then
    echo "✅ Logs Directory: Writable"
else
    echo "❌ Logs Directory: Not writable"
fi

if [ -w "uploads" ]; then
    echo "✅ Uploads Directory: Writable"
else
    echo "❌ Uploads Directory: Not writable"
fi

# Check disk space
DISK_USAGE=$(df . | tail -1 | awk '{print $5}' | sed 's/%//')
if [ "$DISK_USAGE" -lt 80 ]; then
    echo "✅ Disk Space: Available (${DISK_USAGE}% used)"
else
    echo "⚠️  Disk Space: Low (${DISK_USAGE}% used)"
fi

# Check memory usage
MEMORY_USAGE=$(free | grep Mem | awk '{printf "%.0f", $3/$2 * 100.0}')
if [ "$MEMORY_USAGE" -lt 80 ]; then
    echo "✅ Memory: Available (${MEMORY_USAGE}% used)"
else
    echo "⚠️  Memory: High usage (${MEMORY_USAGE}% used)"
fi

# Check recent logs
if [ -f "logs/server.log" ]; then
    RECENT_ERRORS=$(tail -100 logs/server.log | grep -i error | wc -l)
    if [ "$RECENT_ERRORS" -eq 0 ]; then
        echo "✅ Recent Logs: No errors"
    else
        echo "⚠️  Recent Logs: $RECENT_ERRORS errors found"
    fi
else
    echo "⚠️  Log file: Not found"
fi

echo ""
echo "🌐 Access Information:"
echo "   Local: http://localhost:3000"
echo "   Admin: http://localhost:3000/admin/"
echo ""
echo "📊 Management Commands:"
echo "   View logs: tail -f logs/server.log"
echo "   Stop server: pkill -f 'node .next/standalone/server.js'"
echo "   Restart: ./health-check.sh"
echo ""
echo "🔧 Default Login:"
echo "   Username: admin"
echo "   Password: admin123"
echo ""
echo "⚠️  Remember to change default password in production!"