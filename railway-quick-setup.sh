#!/bin/bash

# Quick Railway Deployment Setup Script
# Automates the complete Railway deployment process for VidaduAcademy

set -e

echo "🚂 VidaduAcademy Railway Deployment Setup"
echo "========================================"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() { echo -e "${GREEN}✅ $1${NC}"; }
print_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
print_error() { echo -e "${RED}❌ $1${NC}"; }

# Check if Railway CLI is installed
check_railway_cli() {
    if ! command -v railway &> /dev/null; then
        print_error "Railway CLI not found"
        echo "Install with: npm install -g @railway/cli"
        echo "Then run: railway login"
        exit 1
    fi
    print_success "Railway CLI found"
}

# Check if user is logged in
check_railway_auth() {
    if ! railway whoami &> /dev/null; then
        print_error "Not logged into Railway"
        echo "Run: railway login"
        exit 1
    fi
    print_success "Railway authentication verified"
}

# Create Railway project and services
setup_railway_project() {
    echo ""
    echo "🏗️  Setting up Railway project..."
    
    read -p "Enter project name (default: vidadu-academy): " PROJECT_NAME
    PROJECT_NAME=${PROJECT_NAME:-vidadu-academy}
    
    # Create project
    railway project create $PROJECT_NAME
    print_success "Project '$PROJECT_NAME' created"
    
    # Create backend service
    railway service create --name vidadu-backend
    print_success "Backend service created"
    
    # Create frontend service
    railway service create --name vidadu-frontend
    print_success "Frontend service created"
    
    # Add MySQL database
    railway add --database mysql
    print_success "MySQL database added"
}

# Deploy backend
deploy_backend() {
    echo ""
    echo "🔧 Deploying backend..."
    
    # Switch to backend directory and deploy
    cd backend
    railway service select vidadu-backend
    
    # Connect the repo
    railway connect
    
    # Set environment variables
    echo "Setting essential environment variables..."
    railway variables set APP_NAME=VidaduAcademy
    railway variables set APP_ENV=production
    railway variables set APP_DEBUG=false
    railway variables set CACHE_DRIVER=file
    railway variables set SESSION_DRIVER=file
    railway variables set QUEUE_CONNECTION=sync
    railway variables set COMPOSER_PROCESS_TIMEOUT=600
    railway variables set COMPOSER_MEMORY_LIMIT=2G
    
    print_success "Backend environment variables set"
    
    # Deploy
    railway up
    print_success "Backend deployment initiated"
    
    cd ..
}

# Deploy frontend
deploy_frontend() {
    echo ""
    echo "🎨 Deploying frontend..."
    
    # Switch to frontend directory and deploy
    cd frontend
    railway service select vidadu-frontend
    
    # Connect the repo
    railway connect
    
    # Set environment variables
    railway variables set NODE_OPTIONS=--max-old-space-size=4096
    railway variables set NPM_CONFIG_NETWORK_TIMEOUT=300000
    railway variables set NPM_CONFIG_FETCH_RETRIES=5
    
    print_success "Frontend environment variables set"
    
    # Deploy
    railway up
    print_success "Frontend deployment initiated"
    
    cd ..
}

# Generate APP_KEY after backend is deployed
generate_app_key() {
    echo ""
    echo "🔑 Generating Laravel APP_KEY..."
    
    railway service select vidadu-backend
    
    # Wait for deployment to be ready
    print_warning "Waiting for backend to be ready..."
    sleep 30
    
    # Generate APP_KEY
    APP_KEY=$(railway run php artisan key:generate --show)
    if [ $? -eq 0 ]; then
        railway variables set APP_KEY="$APP_KEY"
        print_success "APP_KEY generated and set"
    else
        print_error "Failed to generate APP_KEY. You may need to do this manually."
    fi
}

# Run database migrations
run_migrations() {
    echo ""
    echo "🗄️  Running database migrations..."
    
    railway service select vidadu-backend
    
    # Run migrations
    if railway run php artisan migrate --force; then
        print_success "Database migrations completed"
    else
        print_error "Database migrations failed. Check logs."
    fi
    
    # Optional: run seeders
    read -p "Run database seeders? (y/N): " RUN_SEEDERS
    if [[ $RUN_SEEDERS =~ ^[Yy]$ ]]; then
        railway run php artisan db:seed --force
        print_success "Database seeders completed"
    fi
}

# Display final information
show_deployment_info() {
    echo ""
    echo "🎉 Deployment Complete!"
    echo "======================"
    
    echo ""
    echo "Backend URL: $(railway service select vidadu-backend && railway domain)"
    echo "Frontend URL: $(railway service select vidadu-frontend && railway domain)"
    
    echo ""
    echo "Next steps:"
    echo "1. Verify backend health: GET /api/health"
    echo "2. Test frontend application"
    echo "3. Configure custom domains if needed"
    echo "4. Set up monitoring and backups"
    
    echo ""
    echo "Useful commands:"
    echo "- View logs: railway logs"
    echo "- Check status: railway status"
    echo "- Run commands: railway run <command>"
    
    print_success "Setup completed successfully!"
}

# Main execution
main() {
    echo "Starting Railway deployment setup..."
    
    check_railway_cli
    check_railway_auth
    
    echo ""
    read -p "This will create a new Railway project. Continue? (y/N): " CONTINUE
    if [[ ! $CONTINUE =~ ^[Yy]$ ]]; then
        echo "Setup cancelled."
        exit 0
    fi
    
    setup_railway_project
    deploy_backend
    deploy_frontend
    generate_app_key
    run_migrations
    show_deployment_info
}

# Run main function
main "$@"
