#!/bin/bash

echo "🔧 NUCLEAR OPTION - MINIMAL WORKING DEPLOYMENT"
echo "============================================="
echo "Creating absolute minimal working Laravel deployment"
echo ""

# Create the simplest possible nixpacks.toml
cat > backend/nixpacks.toml << 'EOF'
[variables]
PHP_VERSION = "8.3"

[phases.setup]
nixPkgs = ["php83", "php83Extensions.pdo", "php83Extensions.pdo_sqlite"]

[phases.install]
cmds = ["composer install --no-dev --no-interaction"]

[phases.build]
cmds = [
  "mkdir -p database",
  "touch database/database.sqlite",
  "echo 'APP_KEY=base64:z0a3Q3u2vGAZ0dYflkfr2ELJ/CR7A6HjH44IMcpzjGo=' > .env",
  "echo 'DB_CONNECTION=sqlite' >> .env",
  "echo 'DB_DATABASE=/opt/render/project/src/database/database.sqlite' >> .env"
]

[start]
cmd = "php artisan serve --host=0.0.0.0 --port=$PORT"
EOF

echo "✅ Created nuclear option nixpacks.toml"
echo ""

echo "🚨 NUCLEAR DEPLOYMENT READY"
echo "==========================="
echo ""
echo "This is the ABSOLUTE MINIMAL deployment to get Laravel working:"
echo "- Minimal PHP extensions"
echo "- Simple .env creation" 
echo "- SQLite database"
echo "- No complex build steps"
echo ""
echo "To deploy:"
echo "1. git add backend/nixpacks.toml"
echo "2. git commit -m 'NUCLEAR: Absolute minimal deployment for immediate fix'"
echo "3. git push origin main"
echo ""
