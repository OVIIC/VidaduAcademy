#!/bin/bash

echo "🔧 VidaduAcademy Environment Setup Helper"
echo "========================================"
echo ""

# Function to display environment variables by category
show_env_category() {
    local category="$1"
    local file="$2"
    
    echo "📋 $category Environment Variables:"
    echo "--------------------------------"
    
    if [ -f "$file" ]; then
        grep -E "^[A-Z_]+=.*" "$file" | head -10
        echo "..."
        echo "✅ Full list available in: $file"
    else
        echo "❌ Template file not found: $file"
    fi
    echo ""
}

echo "🎯 RENDER.COM PRODUCTION SETUP:"
echo "1. Go to https://dashboard.render.com/"
echo "2. Select your 'vidaduacademy' service"
echo "3. Navigate to 'Environment' tab"
echo "4. Copy variables from ENVIRONMENT_VARIABLES_COMPLETE.md"
echo ""

show_env_category "PRODUCTION (Render Backend)" "ENVIRONMENT_VARIABLES_COMPLETE.md"

echo "🏠 LOCAL DEVELOPMENT SETUP:"
echo "1. Copy backend/.env.local to backend/.env"
echo "2. Copy frontend/.env.local.template to frontend/.env.local"
echo "3. Update keys and URLs for your local setup"
echo ""

show_env_category "LOCAL BACKEND" "backend/.env.local"
show_env_category "LOCAL FRONTEND" "frontend/.env.local.template"

echo "🔑 IMPORTANT KEYS TO UPDATE:"
echo "- APP_KEY: Use the provided key or generate new one"
echo "- DATABASE_URL: Will be auto-set by Render PostgreSQL"
echo "- STRIPE_KEY/SECRET: Your Stripe dashboard keys"
echo "- MAIL_* settings: Your email provider settings"
echo ""

echo "⚠️  SECURITY CHECKLIST:"
echo "✅ Never commit real .env files to git"
echo "✅ Use test keys for development"
echo "✅ Use live keys only in production"
echo "✅ Enable HTTPS in production"
echo "✅ Set secure session cookies"
echo ""

echo "🧪 TEST AFTER SETUP:"
echo "./test-production-endpoints.sh"
