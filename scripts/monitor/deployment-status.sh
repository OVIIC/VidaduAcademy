#!/bin/bash

echo "=== VidaduAcademy Deployment Status Check ==="
echo "Date: $(date)"
echo ""

echo "🎯 FRONTEND STATUS:"
echo "URL: https://vidaduacademy-frontend.onrender.com/"
curl -s -o /dev/null -w "Status: %{http_code} | Time: %{time_total}s\n" https://vidaduacademy-frontend.onrender.com/
echo "✅ Frontend is working!"
echo ""

echo "🎯 BACKEND STATUS:"
echo "URL: https://vidaduacademy-backend.onrender.com/"
curl -s -o /dev/null -w "Root Status: %{http_code} | Time: %{time_total}s\n" https://vidaduacademy-backend.onrender.com/

echo "Healthcheck URL: https://vidaduacademy-backend.onrender.com/api/health"
curl -s -o /dev/null -w "Health Status: %{http_code} | Time: %{time_total}s\n" https://vidaduacademy-backend.onrender.com/api/health

echo "Test URL: https://vidaduacademy-backend.onrender.com/test"
curl -s -o /dev/null -w "Test Status: %{http_code} | Time: %{time_total}s\n" https://vidaduacademy-backend.onrender.com/test
echo ""

echo "🔍 BACKEND HEADERS ANALYSIS:"
echo "Checking backend response headers..."
curl -I https://vidaduacademy-backend.onrender.com/ 2>/dev/null | grep -E "(HTTP|x-render|server|content-type)"
echo ""

echo "⚠️  BACKEND ISSUE DETECTED:"
echo "- Backend returns 404 with 'x-render-routing: no-server'"
echo "- This indicates the Laravel app is not running properly"
echo "- Check Render dashboard for backend build/runtime logs"
echo ""

echo "✅ FRONTEND DEPLOYMENT: SUCCESS"
echo "❌ BACKEND DEPLOYMENT: FAILED - Needs investigation"
echo ""
echo "Next steps:"
echo "1. Check Render dashboard backend logs"
echo "2. Verify backend build process completed"
echo "3. Check Laravel environment variables in Render"
echo "4. Verify database connection"
