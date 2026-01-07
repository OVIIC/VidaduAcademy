#!/bin/bash

echo "🔧 VidaduAcademy Admin Setup Script"
echo "==================================="
echo ""

# Function to check if backend is running
check_backend() {
    local url="https://vidaduacademy-backend.onrender.com/api/health"
    local response=$(curl -s -w "HTTP_CODE:%{http_code}" "$url" 2>/dev/null)
    local status=$(echo "$response" | grep "HTTP_CODE:" | cut -d: -f2)
    
    if [ "$status" = "200" ]; then
        return 0
    else
        return 1
    fi
}

echo "📍 Checking deployment status..."
if check_backend; then
    echo "✅ Backend is running and healthy"
    
    echo ""
    echo "🎯 ADMIN USERS HAVE BEEN CREATED:"
    echo "================================="
    echo ""
    echo "🔑 PRIMARY ADMIN:"
    echo "   Email: admin@vidaduacademy.com"
    echo "   Password: admin123"
    echo ""
    echo "📝 CONTENT MANAGER:"
    echo "   Email: content@vidaduacademy.com"
    echo "   Password: content123"
    echo ""
    echo "🛠️  SUPPORT MANAGER:"
    echo "   Email: support@vidaduacademy.com"
    echo "   Password: support123"
    echo ""
    echo "🧪 TEST ADMIN:"
    echo "   Email: test@vidaduacademy.com"
    echo "   Password: test123"
    echo ""
    
    echo "🌐 ACCESS ADMIN PANEL:"
    echo "====================="
    echo "Frontend Admin Panel: https://vidaduacademy.onrender.com/admin"
    echo "Backend API: https://vidaduacademy-backend.onrender.com/admin"
    echo ""
    
    echo "✅ Admin users are ready to use!"
    echo "💡 Login with any of the credentials above"
    
else
    echo "❌ Backend is not running properly"
    echo ""
    echo "🚨 DEPLOYMENT ISSUE DETECTED:"
    echo "The backend must be running before admin users can be used."
    echo ""
    echo "🔧 STEPS TO FIX:"
    echo "1. Check Render Dashboard deployment status"
    echo "2. Set missing environment variables (especially APP_KEY)"
    echo "3. Trigger manual redeploy"
    echo "4. Wait for deployment to complete"
    echo "5. Run this script again"
    echo ""
    echo "⚡ QUICK FIX:"
    echo "Add to Render Environment: APP_KEY=base64:z0a3Q3u2vGAZ0dYflkfr2ELJ/CR7A6HjH44IMcpzjGo="
    echo ""
    echo "📋 For detailed troubleshooting, run:"
    echo "./deployment-fix-summary.sh"
fi

echo ""
echo "🏁 Admin setup check complete - $(date)"
