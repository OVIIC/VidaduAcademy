#!/bin/bash

echo "=== VidaduAcademy Database Connection Check ==="
echo "Date: $(date)"
echo ""

echo "🔍 CURRENT DEPLOYMENT STATUS:"
echo ""

echo "1️⃣ Health Check:"
health_response=$(curl -s https://vidaduacademy.onrender.com/api/health 2>/dev/null)
if [ $? -eq 0 ] && [ ! -z "$health_response" ]; then
    echo "✅ Backend is responding"
    echo "Response: $health_response"
else
    echo "❌ Backend not responding or deployment failed"
fi
echo ""

echo "2️⃣ Database Debug Check:"
debug_response=$(curl -s https://vidaduacademy.onrender.com/debug-db 2>/dev/null)
if [ $? -eq 0 ] && [ ! -z "$debug_response" ]; then
    echo "✅ Debug endpoint accessible"
    echo "Database Status:"
    echo "$debug_response" | head -20
else
    echo "❌ Debug endpoint not accessible"
fi
echo ""

echo "3️⃣ Network Issues Analysis:"
echo "From your deployment logs, network checks are failing."
echo "This usually means:"
echo "   - PostgreSQL service not created/connected"
echo "   - DATABASE_URL not set in environment"
echo "   - Services in different regions"
echo ""

echo "4️⃣ Port Check:"
echo "Render needs to detect an open port. Checking if service is listening..."
if curl -s -I https://vidaduacademy.onrender.com/ >/dev/null 2>&1; then
    echo "✅ Service is responding on HTTPS"
else
    echo "❌ Service not responding - port binding issue"
    echo "   Check if Apache is configured for PORT environment variable"
fi
echo ""

echo "📋 RENDER ENVIRONMENT CHECKLIST:"
echo "✅ Check these variables are set in Render Dashboard:"
echo "   - DATABASE_URL (from PostgreSQL service)"
echo "   - DB_CONNECTION=pgsql"
echo "   - APP_KEY (generate with: php artisan key:generate --show)"
echo ""

echo "🎯 RENDER DASHBOARD STEPS:"
echo "1. Go to: https://dashboard.render.com/"
echo "2. Open your 'vidaduacademy' service"
echo "3. Go to 'Environment' tab"
echo "4. Check if DATABASE_URL exists and points to PostgreSQL"
echo "5. If missing, connect your PostgreSQL database"
echo ""

echo "🐘 POSTGRESQL DATABASE:"
echo "Service name in Render: VidaduAcademy-DB"
echo "Connection should be automatic if services are linked"
echo ""

echo "⚠️  CURRENT ISSUES:"
echo "- Backend returns 500 errors"
echo "- Database connection timing out (30/30 retries)"
echo "- Possible missing DATABASE_URL or wrong DB_CONNECTION"
