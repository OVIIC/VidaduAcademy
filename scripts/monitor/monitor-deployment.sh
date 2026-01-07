#!/bin/bash

echo "🔍 RENDER DEPLOYMENT MONITORING"
echo "==============================="
echo "Monitoring deployment progress and testing fixes..."
echo ""

BACKEND_URL="https://vidaduacademy.onrender.com"
MAX_ATTEMPTS=10
ATTEMPT=1

while [ $ATTEMPT -le $MAX_ATTEMPTS ]; do
    echo "🔄 Attempt $ATTEMPT/$MAX_ATTEMPTS - $(date)"
    
    # Test health endpoint
    health_response=$(curl -s -w "HTTP_CODE:%{http_code}" "$BACKEND_URL/api/health" 2>/dev/null)
    HEALTH_STATUS=$(echo "$health_response" | grep "HTTP_CODE:" | cut -d: -f2 | tr -d '"' | cut -d',' -f1)
    
    # Test debug endpoint  
    debug_response=$(curl -s -w "HTTP_CODE:%{http_code}" "$BACKEND_URL/debug-db" 2>/dev/null)
    DEBUG_STATUS=$(echo "$debug_response" | grep "HTTP_CODE:" | cut -d: -f2 | tr -d '"' | cut -d',' -f1)
    
    # Test standalone debug
    standalone_response=$(curl -s -w "HTTP_CODE:%{http_code}" "$BACKEND_URL/debug-standalone" 2>/dev/null)
    STANDALONE_STATUS=$(echo "$standalone_response" | grep "HTTP_CODE:" | cut -d: -f2 | tr -d '"' | cut -d',' -f1)
    
    echo "Health: $HEALTH_STATUS | Debug: $DEBUG_STATUS | Standalone: $STANDALONE_STATUS"
    
    if [ "$DEBUG_STATUS" = "200" ]; then
        echo ""
        echo "✅ DEBUG ENDPOINT WORKING - Checking database connection..."
        
        # Check if PostgreSQL driver error is fixed
        if curl -s "$BACKEND_URL/debug-standalone" | grep -q "could not find driver"; then
            echo "❌ PostgreSQL driver still missing"
        else
            echo "✅ PostgreSQL driver issue resolved"
        fi
        
        # Check if .env file is created
        if curl -s "$BACKEND_URL/debug-standalone" | grep -q ".env file does not exist"; then
            echo "❌ .env file still missing"
        else
            echo "✅ .env file created successfully"
        fi
        
        # Check database connection
        if curl -s "$BACKEND_URL/debug-standalone" | grep -q "PostgreSQL connection failed"; then
            echo "❌ Database connection still failing"
        else
            echo "✅ Database connection working"
        fi
        
        echo ""
        echo "🎯 DEPLOYMENT STATUS:"
        if curl -s "$BACKEND_URL/debug-standalone" | grep -q "✅.*connection.*success"; then
            echo "✅ DEPLOYMENT SUCCESSFUL!"
            echo "✅ All systems operational"
            echo ""
            echo "🚀 You can now test login functionality"
            echo "🔗 Backend URL: $BACKEND_URL"
            break
        else
            echo "⚠️  Deployment partially working, some issues remain"
        fi
    else
        echo "⏳ Deployment still in progress..."
    fi
    
    if [ $ATTEMPT -eq $MAX_ATTEMPTS ]; then
        echo ""
        echo "🔍 FINAL DIAGNOSTIC REPORT:"
        echo "=========================="
        curl -s "$BACKEND_URL/debug-standalone" | head -30
        echo ""
        echo "❌ Deployment monitoring timeout"
        echo "📋 Manual investigation required"
        echo "🔗 Check Render Dashboard: https://dashboard.render.com/"
        break
    fi
    
    echo "⏳ Waiting 30 seconds before next check..."
    echo ""
    sleep 30
    ATTEMPT=$((ATTEMPT + 1))
done

echo ""
echo "🏁 Monitoring complete - $(date)"
