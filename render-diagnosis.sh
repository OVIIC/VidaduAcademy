#!/bin/bash
set -e

echo "🔧 VidaduAcademy Render Deployment Analysis"
echo "=========================================="
echo ""

# Function to check endpoint with detailed response
check_endpoint() {
    local url=$1
    local name=$2
    echo "🌐 Testing $name: $url"
    
    # Check with curl and get detailed response
    response=$(curl -s -w "\nHTTP_CODE:%{http_code}\nTIME_TOTAL:%{time_total}" "$url" 2>/dev/null || echo "CONNECTION_FAILED")
    
    if [[ $response == "CONNECTION_FAILED" ]]; then
        echo "❌ Connection failed - service may be down"
    else
        http_code=$(echo "$response" | grep "HTTP_CODE:" | cut -d: -f2)
        time_total=$(echo "$response" | grep "TIME_TOTAL:" | cut -d: -f2)
        content=$(echo "$response" | sed '/HTTP_CODE:/d' | sed '/TIME_TOTAL:/d')
        
        echo "   Status: $http_code (${time_total}s)"
        if [[ $http_code == "200" ]]; then
            echo "   ✅ Success"
            echo "   Response: ${content:0:100}$([ ${#content} -gt 100 ] && echo "...")"
        elif [[ $http_code == "404" ]]; then
            echo "   ❌ Not Found - route may not exist or app not started"
        elif [[ $http_code == "500" ]]; then
            echo "   ❌ Server Error"
            echo "   Response: $content"
        else
            echo "   ⚠️  Unexpected status"
            echo "   Response: $content"
        fi
    fi
    echo ""
}

# Main backend URL
BACKEND_URL="https://vidaduacademy-backend.onrender.com"

echo "📍 Testing core endpoints..."
check_endpoint "$BACKEND_URL/" "Root"
check_endpoint "$BACKEND_URL/test" "Test endpoint"
check_endpoint "$BACKEND_URL/api/health" "Health check"

echo "🔍 Testing debug endpoints..."
check_endpoint "$BACKEND_URL/debug-db" "Laravel debug"
check_endpoint "$BACKEND_URL/debug-standalone" "Standalone debug"

echo "📊 Testing API endpoints..."
check_endpoint "$BACKEND_URL/api/courses" "Courses API"
check_endpoint "$BACKEND_URL/api/test-db" "Test DB API"

# Check if we can determine the deployment status
echo "🚀 Render Service Analysis:"
echo "   If all endpoints return 404 or connection failed:"
echo "   1. Deployment may still be in progress"
echo "   2. Build may have failed"
echo "   3. Service may be stopped or crashed"
echo ""
echo "   If health endpoint works but others don't:"
echo "   1. Laravel routing issues"
echo "   2. Database connection problems"
echo "   3. Environment configuration issues"
echo ""

# Additional diagnostic information
echo "🔧 Local Environment Check:"
echo "   Current directory: $(pwd)"
echo "   Git branch: $(git branch --show-current 2>/dev/null || echo 'unknown')"
echo "   Latest commit: $(git log -1 --format='%h %s' 2>/dev/null || echo 'unknown')"
echo ""

# Environment variable template
echo "📋 Required Render Environment Variables:"
echo "   DATABASE_URL=postgresql://user:password@host:port/database"
echo "   APP_KEY=base64:your-app-key-here"
echo "   APP_ENV=production"
echo "   STRIPE_KEY=pk_test_..."
echo "   STRIPE_SECRET=sk_test_..."
echo "   STRIPE_WEBHOOK_SECRET=whsec_..."
echo ""

echo "💡 Next Steps:"
echo "   1. Check Render Dashboard for deployment logs"
echo "   2. Verify all environment variables are set"
echo "   3. Ensure PostgreSQL add-on is connected"
echo "   4. Check if build completed successfully"
echo "   5. Review application logs for errors"
echo ""

echo "🏁 Analysis complete - $(date)"
