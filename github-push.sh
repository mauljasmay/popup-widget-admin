#!/bin/bash

echo "🚀 PUSH TO GITHUB - POPUP WIDGET ADMIN"
echo "===================================="
echo ""

# Check if we're in a git repository
if [ ! -d ".git" ]; then
    echo "❌ Not a git repository. Initializing..."
    git init
    git add .
    git commit -m "Initial commit: Popup Widget Admin Panel"
fi

# Check for uncommitted changes
if [ -n "$(git status --porcelain)" ]; then
    echo "📝 Committing changes..."
    git add .
    git commit -m "Update: Production-ready Popup Widget Admin Panel with embed code generation

🎯 Features Added:
- Complete widget management system with CRUD operations
- 4 widget types: Modal, Slide-in, Notification, Exit Intent
- Advanced customization options (colors, sizes, positions, timing)
- Targeting rules (pages, dates, user limits)

📊 Analytics & Tracking:
- Real-time performance tracking with Chart.js
- Interactive charts and detailed analytics dashboard
- CSV export functionality for data analysis
- User behavior tracking and session management

🔗 Embed Code System:
- Multi-access embed code generation from all admin pages
- Flexible options (specific/all widgets, async/sync loading)
- One-click copy functionality with visual feedback
- Real-time code generation based on user selections
- Advanced embed options with customization

🔐 Security & Authentication:
- Role-based access control (Super Admin, Admin, Editor)
- Secure session management with configurable timeout
- Password hashing and CSRF protection
- Input validation and SQL injection prevention
- Security headers and XSS protection

🎨 Modern UI/UX:
- Responsive Bootstrap 5 design
- Mobile-friendly interface
- Interactive components with smooth animations
- Dark mode ready CSS structure
- Accessible design patterns

🚀 Production Ready:
- Optimized production build with Next.js 15
- Comprehensive deployment scripts and documentation
- Health monitoring and logging systems
- SSL/HTTPS configuration support
- Database backup and maintenance tools

📋 Complete Documentation:
- README.md with full feature documentation and integration guide
- INSTALL.md with step-by-step installation instructions
- DEPLOYMENT.md with production deployment guide
- GITHUB-SETUP.md with repository setup instructions
- Multiple utility scripts for maintenance and monitoring

🔧 Technical Stack:
- Backend: PHP with MySQL database and prepared statements
- Frontend: Next.js 15 with TypeScript and App Router
- Styling: Tailwind CSS + shadcn/ui component library
- Charts: Chart.js for interactive data visualization
- Security: Modern security practices and validation

This is a production-ready, feature-complete popup widget admin panel
with advanced embed code generation capabilities."
fi

# Check for remote
if ! git remote get-url origin > /dev/null 2>&1; then
    echo "📡 No remote repository found."
    echo ""
    echo "🔗 SETUP GITHUB REPOSITORY:"
    echo "1. Buka https://github.com/new"
    echo "2. Repository name: popup-widget-admin"
    echo "3. Description: Complete PHP popup widget admin panel with embed code generation"
    echo "4. Pilih Public atau Private"
    echo "5. Jangan initialize dengan README (sudah ada)"
    echo ""
    echo "📝 TAMBAHKAN REMOTE:"
    echo "git remote add origin https://github.com/YOUR_USERNAME/popup-widget-admin.git"
    echo ""
    echo "🚀 PUSH KE GITHUB:"
    echo "git push -u origin master"
    echo ""
    echo "🔄 GANTI YOUR_USERNAME dengan username GitHub Anda!"
else
    echo "📡 Remote repository found: $(git remote get-url origin)"
    echo ""
    echo "🚀 PUSHING TO GITHUB..."
    git push -u origin master
    
    if [ $? -eq 0 ]; then
        echo ""
        echo "✅ SUCCESS! Project berhasil di-push ke GitHub!"
        echo ""
        echo "🌐 REPOSITORY URL:"
        echo "$(git remote get-url origin | sed 's/https:\/\/github.com\//https:\/\/github.com\//')"
        echo ""
        echo "📋 FITUR YANG DI-PUSH:"
        echo "✅ Widget Management System (CRUD + 4 types)"
        echo "✅ Analytics & Tracking (Chart.js + Export)"
        echo "✅ Embed Code Generation (Multi-access + Options)"
        echo "✅ Authentication & Security (Role-based + Protection)"
        echo "✅ Modern UI/UX (Responsive + Bootstrap 5)"
        echo "✅ Production Ready (Scripts + Documentation)"
        echo ""
        echo "📚 DOKUMENTASI LENGKAP:"
        echo "📄 README.md - Feature documentation + integration guide"
        echo "📄 INSTALL.md - Step-by-step installation"
        echo "📄 DEPLOYMENT.md - Production deployment guide"
        echo "📄 GITHUB-SETUP.md - Repository setup instructions"
        echo ""
        echo "🎉 POPUP WIDGET ADMIN PANEL SIAP DI GITHUB!"
    else
        echo ""
        echo "❌ GAGAL! Push ke GitHub gagal."
        echo "🔧 PERIKSA:"
        echo "1. GitHub credentials Anda"
        echo "2. Koneksi internet"
        echo "3. Repository permissions"
        echo "4. Branch yang benar (master)"
    fi
fi

echo ""
echo "===================================="
echo "📊 PROJECT STATISTICS:"
echo "📁 Total Files: $(find . -type f | wc -l)"
echo "📁 Project Size: $(du -sh . | cut -f1)"
echo "📁 Git Status: $(git status --porcelain | wc -l) files modified"
echo "===================================="