#!/bin/bash

echo "=== VidaduAcademy Database Connection Check ==="
echo "Date: $(date)"
echo ""

echo "🔍 TESTING DATABASE CONNECTIONS:"
echo ""

echo "1️⃣ Testing local SQLite connection..."
curl -s https://vidaduacademy.onrender.com/api/health 2>/dev/null | head -5 || echo "❌ Health endpoint failed"
echo ""

echo "2️⃣ Testing backend /test endpoint..."
curl -s https://vidaduacademy.onrender.com/test 2>/dev/null | jq . || echo "❌ Test endpoint failed"
echo ""

echo "3️⃣ Checking if DATABASE_URL is set in Render..."
echo "Expected format: postgresql://username:password@host:port/database"
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
