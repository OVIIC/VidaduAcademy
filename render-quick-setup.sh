#!/bin/bash

echo "🚀 VidaduAcademy Render Quick Setup"
echo "=================================="
echo ""

echo "📋 PRE-DEPLOYMENT CHECKLIST:"
echo ""

echo "1️⃣ RENDER DASHBOARD SETUP:"
echo "✅ Create PostgreSQL database service:"
echo "   - Name: vidaduacademy-db"
echo "   - Plan: Free (for testing)"
echo "   - Region: Same as web service"
echo ""

echo "2️⃣ WEB SERVICE SETUP:"
echo "✅ Create Web Service:"
echo "   - Name: vidaduacademy"
echo "   - Repository: Your GitHub repo"
echo "   - Branch: main"
echo "   - Root Directory: backend"
echo "   - Build Command: ./render-build.sh"
echo "   - Start Command: ./start-render.sh"
echo ""

echo "3️⃣ CONNECT SERVICES:"
echo "✅ Link PostgreSQL to Web Service:"
echo "   - Go to Web Service > Environment"
echo "   - DATABASE_URL should appear automatically"
echo "   - If not, manually connect the services"
echo ""

echo "4️⃣ CRITICAL ENVIRONMENT VARIABLES:"
echo "✅ Set these in Web Service > Environment:"
echo ""

cat << 'EOF'
APP_NAME=VidaduAcademy
APP_ENV=production
APP_DEBUG=false
APP_URL=https://vidaduacademy.onrender.com
APP_KEY=base64:wUAdCvxjj0YV4Kcs9ML+Qc3vKU69u9qZcuQlvfyB9aM=

DB_CONNECTION=pgsql
# DATABASE_URL=postgresql://... (auto-set when PostgreSQL connected)

LOG_CHANNEL=stderr
LOG_LEVEL=error

SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=database
EOF

echo ""
echo "5️⃣ DEPLOYMENT STATUS:"
echo "✅ After setting environment variables:"
echo "   - Render will auto-deploy"
echo "   - Check logs for any errors"
echo "   - Wait for 'Live' status"
echo ""

echo "6️⃣ TESTING:"
echo "✅ Test these endpoints after deployment:"
echo "   - https://vidaduacademy.onrender.com/api/health"
echo "   - https://vidaduacademy.onrender.com/debug-db"
echo "   - https://vidaduacademy.onrender.com/admin/login"
echo ""

echo "🔧 TROUBLESHOOTING:"
echo "❌ If database connection fails:"
echo "   1. Check if PostgreSQL service is running"
echo "   2. Verify DATABASE_URL is set correctly"
echo "   3. Check if services are in same region"
echo "   4. Look at deployment logs for specific errors"
echo ""

echo "❌ If 500 errors persist:"
echo "   1. Check debug endpoint: /debug-db"
echo "   2. Verify APP_KEY is set"
echo "   3. Check storage permissions"
echo "   4. Verify all migrations ran"
echo ""

echo "📞 SUPPORT:"
echo "   - Render Docs: https://render.com/docs"
echo "   - Laravel Docs: https://laravel.com/docs"
echo ""

echo "🎯 Current Status: Ready for Render deployment!"
echo "Next: Set environment variables in Render Dashboard"
