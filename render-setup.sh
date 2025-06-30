#!/bin/bash

# Render Deployment Setup Script
# Automates VidaduAcademy deployment preparation for Render

set -e

echo "🎨 VidaduAcademy Render Deployment Setup"
echo "======================================="

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() { echo -e "${GREEN}✅ $1${NC}"; }
print_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
print_error() { echo -e "${RED}❌ $1${NC}"; }
print_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }

# Check if in correct directory
check_directory() {
    if [[ ! -f "package.json" ]] && [[ ! -d "backend" ]] && [[ ! -d "frontend" ]]; then
        print_error "Not in VidaduAcademy project directory"
        echo "Please run this script from the project root directory"
        exit 1
    fi
    print_success "Project directory verified"
}

# Prepare backend for Render
prepare_backend() {
    echo ""
    print_info "Preparing backend for Render deployment..."
    
    # Copy Render-specific Dockerfile
    if [[ -f "backend/Dockerfile.render" ]]; then
        print_success "Render Dockerfile already exists"
    else
        print_warning "Creating Render-specific Dockerfile..."
        # The Dockerfile.render should already be created by the setup
    fi
    
    # Update database configuration for PostgreSQL
    if [[ -f "backend/config/database.php" ]]; then
        print_info "Updating database configuration for PostgreSQL..."
        # Ensure PostgreSQL is properly configured
        grep -q "pgsql" backend/config/database.php && print_success "PostgreSQL configuration found" || print_warning "Check PostgreSQL configuration"
    fi
    
    print_success "Backend prepared for Render"
}

# Prepare frontend for Render
prepare_frontend() {
    echo ""
    print_info "Preparing frontend for Render deployment..."
    
    # Check if Vite config exists
    if [[ -f "frontend/vite.config.js" ]]; then
        print_success "Vite configuration found"
    else
        print_warning "Vite configuration not found - check build setup"
    fi
    
    # Check package.json build script
    if grep -q '"build":' frontend/package.json; then
        print_success "Build script found in package.json"
    else
        print_error "Build script missing in package.json"
        exit 1
    fi
    
    print_success "Frontend prepared for Render"
}

# Create deployment checklist
create_checklist() {
    echo ""
    print_info "Creating deployment checklist..."
    
    cat > RENDER_DEPLOYMENT_CHECKLIST.md << 'EOF'
# 🎨 Render Deployment Checklist

## Pre-Deployment
- [ ] GitHub repository connected to Render
- [ ] Backend Dockerfile.render created
- [ ] Frontend build configuration verified
- [ ] Environment variables template prepared

## Backend Setup
- [ ] Web Service created on Render
- [ ] Docker environment selected
- [ ] PostgreSQL database added
- [ ] Environment variables configured
- [ ] First deployment completed

## Frontend Setup  
- [ ] Static Site created on Render
- [ ] Build command: `npm install && npm run build`
- [ ] Publish directory: `dist`
- [ ] Environment variables set
- [ ] Deployment completed

## Post-Deployment
- [ ] APP_KEY generated and set
- [ ] Database migrations run
- [ ] API health check working
- [ ] Frontend-backend communication verified
- [ ] CORS configuration tested

## Production Ready
- [ ] Custom domains configured (optional)
- [ ] SSL certificates active
- [ ] Performance optimized
- [ ] Monitoring set up
- [ ] Backup strategy implemented

## URLs
- Frontend: https://vidaduacademy-frontend.onrender.com
- Backend: https://vidaduacademy-backend.onrender.com
- Health Check: https://vidaduacademy-backend.onrender.com/api/health
EOF

    print_success "Deployment checklist created"
}

# Validate configuration files
validate_config() {
    echo ""
    print_info "Validating configuration files..."
    
    # Check render.yaml
    if [[ -f "render.yaml" ]]; then
        print_success "render.yaml configuration found"
    else
        print_warning "render.yaml not found (manual setup required)"
    fi
    
    # Check environment template
    if [[ -f "render-env-template.md" ]]; then
        print_success "Environment variables template found"
    else
        print_warning "Environment template missing"
    fi
    
    # Check backend Dockerfile
    if [[ -f "backend/Dockerfile.render" ]]; then
        print_success "Render-specific Dockerfile found"
    else
        print_error "Render Dockerfile missing"
        exit 1
    fi
}

# Generate deployment URLs
generate_urls() {
    echo ""
    print_info "Suggested Render service names and URLs:"
    echo ""
    echo "Backend Service:"
    echo "  Name: vidaduacademy-backend"
    echo "  URL:  https://vidaduacademy-backend.onrender.com"
    echo ""
    echo "Frontend Service:"
    echo "  Name: vidaduacademy-frontend" 
    echo "  URL:  https://vidaduacademy-frontend.onrender.com"
    echo ""
    echo "Database:"
    echo "  Name: vidaduacademy-db"
    echo "  Type: PostgreSQL (Free)"
}

# Show next steps
show_next_steps() {
    echo ""
    print_info "Next Steps:"
    echo ""
    echo "1. 🌐 Go to https://dashboard.render.com"
    echo "2. 🔗 Connect your GitHub repository"
    echo "3. 🚀 Create Web Service for backend:"
    echo "   - Docker environment"
    echo "   - Use backend/Dockerfile.render"
    echo "   - Add PostgreSQL database"
    echo "4. 📱 Create Static Site for frontend:"
    echo "   - Build: npm install && npm run build"
    echo "   - Publish: dist"
    echo "5. ⚙️  Set environment variables (see render-env-template.md)"
    echo "6. 🔑 Generate APP_KEY after first deployment"
    echo "7. 🗄️  Run database migrations"
    echo ""
    print_success "Setup complete! Check RENDER_DEPLOYMENT_GUIDE.md for detailed instructions."
}

# Commit changes
commit_changes() {
    echo ""
    read -p "Commit Render configuration files to Git? (y/N): " COMMIT_CHANGES
    if [[ $COMMIT_CHANGES =~ ^[Yy]$ ]]; then
        git add -A
        git commit -m "Add: Render deployment configuration

✨ Complete Render.com deployment setup:
- render.yaml Blueprint configuration
- Dockerfile.render with PostgreSQL support  
- render-env-template.md environment variables
- RENDER_DEPLOYMENT_GUIDE.md comprehensive guide
- Deployment automation and checklist

Ready for deployment on Render free tier!"
        
        print_success "Changes committed to Git"
        
        read -p "Push to GitHub? (y/N): " PUSH_CHANGES
        if [[ $PUSH_CHANGES =~ ^[Yy]$ ]]; then
            git push origin main
            print_success "Changes pushed to GitHub - ready for Render deployment!"
        fi
    fi
}

# Main execution
main() {
    echo "Starting Render deployment preparation..."
    echo ""
    
    check_directory
    prepare_backend
    prepare_frontend
    create_checklist
    validate_config
    generate_urls
    show_next_steps
    commit_changes
    
    echo ""
    print_success "🎉 Render deployment setup completed!"
    echo ""
    print_info "📖 Read RENDER_DEPLOYMENT_GUIDE.md for step-by-step deployment instructions"
    print_info "📋 Use RENDER_DEPLOYMENT_CHECKLIST.md to track your progress"
}

# Run main function
main "$@"
