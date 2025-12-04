#!/bin/bash

# Quick Setup Script for Popup Widget Admin Panel
# This script helps you set up the environment quickly

echo "🚀 POPUP WIDGET ADMIN PANEL - QUICK SETUP"
echo "=========================================="

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Check if .env files exist
check_env_files() {
    print_info "Checking environment files..."
    
    if [ ! -f ".env" ]; then
        print_warning ".env file not found"
        print_info "Creating .env from example..."
        cp .env.example .env
        print_status ".env file created from example"
    else
        print_status ".env file already exists"
    fi
    
    if [ ! -f ".env.local" ]; then
        print_warning ".env.local file not found"
        print_info "Creating .env.local from example..."
        cp .env.local.example .env.local
        print_status ".env.local file created from example"
    else
        print_status ".env.local file already exists"
    fi
    
    if [ ! -f "mini-services/.env" ]; then
        print_warning "mini-services/.env file not found"
        print_info "Creating mini-services/.env from example..."
        cp mini-services/.env.example mini-services/.env
        print_status "mini-services/.env file created from example"
    else
        print_status "mini-services/.env file already exists"
    fi
}

# Generate secure keys
generate_secure_keys() {
    print_info "Generating secure keys..."
    
    # Generate JWT Secret
    JWT_SECRET=$(openssl rand -base64 32 2>/dev/null || date +%s | sha256sum | base64 | head -c 32)
    
    # Generate Encryption Key
    ENCRYPTION_KEY=$(openssl rand -hex 32 2>/dev/null || date +%s | sha256sum | head -c 64)
    
    # Generate NextAuth Secret
    NEXAUTH_SECRET=$(openssl rand -base64 32 2>/dev/null || date +%s | sha256sum | base64 | head -c 32)
    
    # Update .env file with generated keys
    sed -i.bak "s/your_jwt_secret_key_here/$JWT_SECRET/" .env
    sed -i "s/your_encryption_key_here/$ENCRYPTION_KEY/" .env
    sed -i.bak "s/your_nextauth_secret_here/$NEXAUTH_SECRET/" .env.local
    
    print_status "Secure keys generated and added to .env files"
}

# Prompt for database configuration
setup_database() {
    print_info "Setting up database configuration..."
    
    echo ""
    print_info "Please enter your database configuration:"
    read -p "Database Host (default: localhost): " DB_HOST
    read -p "Database Port (default: 3306): " DB_PORT
    read -p "Database Name (default: popup_widget_db): " DB_NAME
    read -p "Database User (default: root): " DB_USER
    read -s -p "Database Password: " DB_PASSWORD
    echo ""
    
    # Set defaults if empty
    DB_HOST=${DB_HOST:-localhost}
    DB_PORT=${DB_PORT:-3306}
    DB_NAME=${DB_NAME:-popup_widget_db}
    DB_USER=${DB_USER:-root}
    
    # Update .env file
    sed -i "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
    sed -i "s/DB_PORT=.*/DB_PORT=$DB_PORT/" .env
    sed -i "s/DB_NAME=.*/DB_NAME=$DB_NAME/" .env
    sed -i "s/DB_USER=.*/DB_USER=$DB_USER/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env
    
    print_status "Database configuration updated"
}

# Setup application URL
setup_app_url() {
    print_info "Setting up application URL..."
    
    read -p "Application URL (default: http://localhost:3002): " APP_URL
    APP_URL=${APP_URL:-http://localhost:3002}
    
    # Update .env file
    sed -i "s|APP_URL=.*|APP_URL=$APP_URL|" .env
    
    # Update .env.local
    sed -i "s|NEXT_PUBLIC_APP_URL=.*|NEXT_PUBLIC_APP_URL=$APP_URL|" .env.local
    sed -i "s|NEXT_PUBLIC_API_URL=.*|NEXT_PUBLIC_API_URL=$APP_URL|" .env.local
    
    print_status "Application URL configured"
}

# Create necessary directories
create_directories() {
    print_info "Creating necessary directories..."
    
    mkdir -p uploads
    mkdir -p logs
    mkdir -p db
    
    print_status "Directories created"
}

# Set permissions
set_permissions() {
    print_info "Setting file permissions..."
    
    chmod 755 uploads
    chmod 755 logs
    chmod 644 .env .env.local
    chmod 600 mini-services/.env
    
    print_status "File permissions set"
}

# Display next steps
show_next_steps() {
    echo ""
    print_status "🎉 SETUP COMPLETED!"
    echo ""
    echo "📋 Next Steps:"
    echo "   1. Review and update .env files if needed"
    echo "   2. Import the database schema:"
    echo "      mysql -u $DB_USER -p $DB_NAME < database.sql"
    echo "   3. Start the development server:"
    echo "      npm run dev"
    echo "   4. Access the application at: $APP_URL"
    echo ""
    echo "📚 Documentation:"
    echo "   • ENVIRONMENT-SETUP.md - Environment configuration guide"
    echo "   • INSTALL.md - Installation instructions"
    echo "   • README.md - Project overview"
    echo ""
    print_info "Thank you for using Popup Widget Admin Panel! 🎯"
}

# Main setup process
main() {
    echo ""
    print_info "Starting quick setup process..."
    echo ""
    
    check_env_files
    generate_secure_keys
    setup_database
    setup_app_url
    create_directories
    set_permissions
    show_next_steps
}

# Run main function
main