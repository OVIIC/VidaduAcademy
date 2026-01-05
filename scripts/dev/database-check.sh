#!/bin/bash

echo "=== VidaduAcademy Database Connection Check ==="
echo "Date: $(date)"
echo ""

# Updated backend URL - WORKING URL FOUND!
BACKEND_URL="https://vidaduacademy.onrender.com"

echo "🔍 CURRENT DEPLOYMENT STATUS:"
echo "Backend URL: $BACKEND_URL"
echo ""

echo "1️⃣ Health Check:"
health_response=$(curl -s -w "HTTP_CODE:%{http_code}" "$BACKEND_URL/api/health" 2>/dev/null)
HEALTH_STATUS=$(echo "$health_response" | grep "HTTP_CODE:" | cut -d: -f2)
health_content=$(echo "$health_response" | sed '/HTTP_CODE:/d')

# Clean the status code (remove quotes and extra content)
HEALTH_STATUS=$(echo "$HEALTH_STATUS" | tr -d '"' | cut -d',' -f1)

if [ "$HEALTH_STATUS" = "200" ]; then
    echo "✅ Backend is responding"
    echo "Response: $health_content"
elif [ "$HEALTH_STATUS" = "404" ]; then
    echo "❌ Backend returning 404 - Laravel not running properly"
    echo "This indicates deployment failure or missing routes"
elif [ "$HEALTH_STATUS" = "500" ]; then
    echo "⚠️  Backend returning 500 - application error"
    echo "Laravel is running but has internal errors"
else
    echo "❌ Backend not responding or deployment failed (HTTP: $HEALTH_STATUS)"
fi
echo ""

echo "2️⃣ Database Debug Check:"
debug_response=$(curl -s -w "HTTP_CODE:%{http_code}" "$BACKEND_URL/debug-db" 2>/dev/null)
DEBUG_STATUS=$(echo "$debug_response" | grep "HTTP_CODE:" | cut -d: -f2)
debug_content=$(echo "$debug_response" | sed '/HTTP_CODE:/d')

# Clean the status code
DEBUG_STATUS=$(echo "$DEBUG_STATUS" | tr -d '"' | cut -d',' -f1)

if [ "$DEBUG_STATUS" = "200" ]; then
    echo "✅ Debug endpoint accessible"
    echo "Database Status:"
    echo "$debug_content" | head -20
elif [ "$DEBUG_STATUS" = "404" ]; then
    echo "❌ Debug endpoint not found - Laravel not running"
elif [ "$DEBUG_STATUS" = "500" ]; then
    echo "⚠️  Debug endpoint error - database connection issues"
    echo "Error: $debug_content"
else
    echo "❌ Debug endpoint not accessible (HTTP: $DEBUG_STATUS)"
fi
echo ""

echo "3️⃣ Enhanced Diagnostic Check:"
standalone_response=$(curl -s -w "HTTP_CODE:%{http_code}" "$BACKEND_URL/debug-standalone" 2>/dev/null)
STANDALONE_STATUS=$(echo "$standalone_response" | grep "HTTP_CODE:" | cut -d: -f2)
if [ "$STANDALONE_STATUS" = "200" ]; then
    echo "✅ Standalone debug available"
    echo "Detailed diagnosis:"
    echo "$standalone_response" | sed '/HTTP_CODE:/d' | head -30
else
    echo "❌ Standalone debug not available"
fi
echo ""

echo "🎯 CRITICAL DIAGNOSIS:"
echo "====================="
if [ "$HEALTH_STATUS" = "404" ] && [ "$DEBUG_STATUS" = "404" ]; then
    echo "❌ DEPLOYMENT FAILURE: Laravel application not running"
    echo ""
    echo "🚨 IMMEDIATE ACTION REQUIRED:"
    echo "1. Check Render Dashboard for deployment errors"
    echo "2. Verify APP_KEY is set in environment variables"
    echo "3. Ensure all required env vars are configured"
    echo "4. Trigger manual redeploy if needed"
    echo ""
    echo "💡 Quick Fix - Add this to Render Environment:"
    echo "   APP_KEY=base64:z0a3Q3u2vGAZ0dYflkfr2ELJ/CR7A6HjH44IMcpzjGo="
    echo ""
    echo "📋 Run './deployment-fix-summary.sh' for complete fix guide"
elif [ "$HEALTH_STATUS" = "200" ] && [ "$DEBUG_STATUS" = "500" ]; then
    echo "⚠️  PARTIAL SUCCESS: App running but database issues"
    echo "✅ Laravel is responding"
    echo "❌ Database connection failing"
    echo ""
    echo "🔧 Database troubleshooting needed:"
    echo "- Check DATABASE_URL format"
    echo "- Verify PostgreSQL service is connected"
    echo "- Review database connection logs"
elif [ "$HEALTH_STATUS" = "200" ] && [ "$DEBUG_STATUS" = "200" ]; then
    echo "✅ DEPLOYMENT SUCCESS: All systems operational"
    echo "✅ Laravel application: Running"
    echo "✅ Database connection: Working"
    echo "✅ Debug endpoints: Accessible"
else
    echo "🔍 MIXED RESULTS: Partial functionality detected"
    echo "Health Status: $HEALTH_STATUS | Debug Status: $DEBUG_STATUS"
    echo "Manual investigation required"
fi
echo ""

echo "📋 RENDER ENVIRONMENT CHECKLIST:"
echo "✅ REQUIRED variables in Render Dashboard:"
echo "   - APP_KEY=base64:z0a3Q3u2vGAZ0dYflkfr2ELJ/CR7A6HjH44IMcpzjGo="
echo "   - DATABASE_URL (from PostgreSQL service)"
echo "   - APP_ENV=production"
echo "   - APP_DEBUG=false"
echo "   - STRIPE_KEY, STRIPE_SECRET, STRIPE_WEBHOOK_SECRET"
echo ""

echo "🎯 RENDER DASHBOARD STEPS:"
echo "1. Go to: https://dashboard.render.com/"
echo "2. Open your 'vidaduacademy-backend' service"
echo "3. Go to 'Environment' tab"
echo "4. Add missing APP_KEY if not present"
echo "5. Check if DATABASE_URL exists and points to PostgreSQL"
echo "6. If missing, connect your PostgreSQL database"
echo "7. Click 'Manual Deploy' after making changes"
echo ""

echo "🐘 POSTGRESQL DATABASE:"
echo "Service should be linked to web service"
echo "DATABASE_URL should be automatically populated"
echo ""

echo "🔧 NEXT STEPS:"
if [ "$HEALTH_STATUS" = "404" ]; then
    echo "PRIORITY 1: Fix deployment failure"
    echo "- Set APP_KEY in Render environment"
    echo "- Trigger manual redeploy"
    echo "- Wait 2-3 minutes and rerun this script"
elif [ "$HEALTH_STATUS" = "200" ] && [ "$DEBUG_STATUS" = "500" ]; then
    echo "PRIORITY 2: Fix database connection"
    echo "- Verify PostgreSQL service is running"
    echo "- Check DATABASE_URL format"
    echo "- Review application logs for DB errors"
else
    echo "Run detailed diagnosis: './render-diagnosis.sh'"
fi

echo ""
echo "📞 SUPPORT: If issues persist, check Render logs and run:"
echo "   ./deployment-fix-summary.sh"
echo ""
echo "🏁 Check complete - $(date)"
