#!/bin/bash

# Railway Deployment Monitor Script
# Monitors Railway build and deployment status

echo "🚂 Railway Deployment Monitor"
echo "============================"

# Function to check if Railway CLI is installed
check_railway_cli() {
    if ! command -v railway &> /dev/null; then
        echo "❌ Railway CLI not found. Install with: npm install -g @railway/cli"
        exit 1
    fi
    echo "✅ Railway CLI found"
}

# Function to get deployment status
get_deployment_status() {
    echo "📊 Getting deployment status..."
    railway status || echo "❌ Failed to get status - are you logged in? (railway login)"
}

# Function to get recent logs
get_recent_logs() {
    echo "📝 Getting recent deployment logs..."
    railway logs --tail 50 || echo "❌ Failed to get logs"
}

# Function to get build information
get_build_info() {
    echo "🔨 Getting build information..."
    railway ps || echo "❌ Failed to get build info"
}

# Main execution
echo "Starting Railway monitoring..."

# Check prerequisites
check_railway_cli

# Get deployment information
get_deployment_status
echo ""
get_build_info
echo ""
get_recent_logs

echo ""
echo "✅ Monitoring complete"
echo "💡 Pro tip: Use 'railway logs --follow' to watch live logs"
