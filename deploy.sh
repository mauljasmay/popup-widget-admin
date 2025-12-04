#!/bin/bash

# Production Deployment Script for Popup Widget Admin

echo "🚀 Starting Production Deployment..."

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js first."
    exit 1
fi

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "❌ npm is not installed. Please install npm first."
    exit 1
fi

# Create necessary directories
echo "📁 Creating directories..."
mkdir -p logs
mkdir -p backups
mkdir -p uploads

# Set proper permissions
echo "🔒 Setting permissions..."
chmod 755 logs
chmod 755 backups
chmod 755 uploads

# Install dependencies
echo "📦 Installing dependencies..."
npm ci --production

# Build the application
echo "🔨 Building application..."
npm run build

# Check if build was successful
if [ $? -eq 0 ]; then
    echo "✅ Build successful!"
else
    echo "❌ Build failed!"
    exit 1
fi

# Create production database if needed
echo "🗄️ Setting up database..."
if [ -f "database.sql" ]; then
    echo "Database schema found. Please import it manually:"
    echo "mysql -u username -p popup_widget_admin < database.sql"
fi

# Start production server
echo "🌟 Starting production server..."
NODE_ENV=production node .next/standalone/server.js &

# Wait for server to start
sleep 3

# Check if server is running
if curl -s http://localhost:3000 > /dev/null; then
    echo "✅ Production server is running successfully!"
    echo "🌐 Access your application at: http://localhost:3000"
    echo "📊 Admin panel: http://localhost:3000/admin/"
    echo ""
    echo "🔧 Default login credentials:"
    echo "   Username: admin"
    echo "   Password: admin123"
    echo ""
    echo "📝 Don't forget to:"
    echo "   1. Change default password"
    echo "   2. Configure database settings"
    echo "   3. Set up SSL certificate"
    echo "   4. Configure domain name"
    echo ""
    echo "📋 Server logs: tail -f logs/server.log"
    echo "🔄 To stop server: pkill -f 'node .next/standalone/server.js'"
else
    echo "❌ Failed to start production server!"
    exit 1
fi