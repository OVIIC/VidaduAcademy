#!/bin/bash

echo "🔧 RENDER DEPLOYMENT FIX"
echo "========================="
echo "Fixing PostgreSQL driver and deployment issues"
echo ""

# First, let's update nixpacks.toml to ensure proper PostgreSQL driver installation
echo "1. Updating nixpacks.toml to include PostgreSQL driver fix..."

# Create a backup and update nixpacks.toml
cp backend/nixpacks.toml backend/nixpacks.toml.backup

cat > backend/nixpacks.toml << 'EOF'
[variables]
PHP_VERSION = "8.3"

[phases.setup]
nixPkgs = [
    "php83", 
    "php83Extensions.bcmath", 
    "php83Extensions.ctype", 
    "php83Extensions.curl", 
    "php83Extensions.dom", 
    "php83Extensions.fileinfo", 
    "php83Extensions.filter", 
    "php83Extensions.hash", 
    "php83Extensions.mbstring", 
    "php83Extensions.openssl", 
    "php83Extensions.pcre", 
    "php83Extensions.pdo", 
    "php83Extensions.pdo_mysql", 
    "php83Extensions.pdo_pgsql", 
    "php83Extensions.pdo_sqlite", 
    "php83Extensions.session", 
    "php83Extensions.tokenizer", 
    "php83Extensions.xml", 
    "php83Extensions.zip",
    "postgresql"
]

[phases.install]
cmds = [
  "composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs"
]

[phases.build]
cmds = [
  "php artisan key:generate --force || true",
  "php artisan config:cache || true",
  "php artisan route:cache || true",
  "php artisan view:cache || true"
]

[phases.start]
cmd = "php artisan serve --host=0.0.0.0 --port=$PORT"

[start]
cmd = "php artisan serve --host=0.0.0.0 --port=$PORT"
EOF

echo "✅ Updated nixpacks.toml with PostgreSQL driver fix"
echo ""

echo "2. Creating emergency .env generator script..."

# Create a script that will run on Render to create .env file
cat > backend/create-env.php << 'EOF'
<?php
echo "Creating .env file from environment variables...\n";

$envContent = "APP_NAME=" . (getenv('APP_NAME') ?: 'VidaduAcademy') . "\n";
$envContent .= "APP_ENV=" . (getenv('APP_ENV') ?: 'production') . "\n";
$envContent .= "APP_KEY=" . getenv('APP_KEY') . "\n";
$envContent .= "APP_DEBUG=" . (getenv('APP_DEBUG') ?: 'false') . "\n";
$envContent .= "APP_URL=" . (getenv('RENDER_EXTERNAL_URL') ?: 'http://localhost') . "\n";
$envContent .= "\n";
$envContent .= "LOG_CHANNEL=" . (getenv('LOG_CHANNEL') ?: 'stderr') . "\n";
$envContent .= "LOG_LEVEL=" . (getenv('LOG_LEVEL') ?: 'error') . "\n";
$envContent .= "\n";
$envContent .= "DB_CONNECTION=" . (getenv('DB_CONNECTION') ?: 'pgsql') . "\n";
$envContent .= "DATABASE_URL=" . getenv('DATABASE_URL') . "\n";
$envContent .= "\n";
$envContent .= "CACHE_DRIVER=" . (getenv('CACHE_DRIVER') ?: 'file') . "\n";
$envContent .= "SESSION_DRIVER=" . (getenv('SESSION_DRIVER') ?: 'database') . "\n";
$envContent .= "QUEUE_CONNECTION=" . (getenv('QUEUE_CONNECTION') ?: 'database') . "\n";
$envContent .= "\n";
$envContent .= "STRIPE_KEY=" . getenv('STRIPE_KEY') . "\n";
$envContent .= "STRIPE_SECRET=" . getenv('STRIPE_SECRET') . "\n";
$envContent .= "STRIPE_WEBHOOK_SECRET=" . getenv('STRIPE_WEBHOOK_SECRET') . "\n";

file_put_contents('.env', $envContent);
echo "✅ .env file created successfully\n";
?>
EOF

echo "✅ Created .env generator script"
echo ""

echo "3. Updating nixpacks.toml to include .env creation..."

# Update nixpacks.toml to include .env creation in build phase
cat > backend/nixpacks.toml << 'EOF'
[variables]
PHP_VERSION = "8.3"

[phases.setup]
nixPkgs = [
    "php83", 
    "php83Extensions.bcmath", 
    "php83Extensions.ctype", 
    "php83Extensions.curl", 
    "php83Extensions.dom", 
    "php83Extensions.fileinfo", 
    "php83Extensions.filter", 
    "php83Extensions.hash", 
    "php83Extensions.mbstring", 
    "php83Extensions.openssl", 
    "php83Extensions.pcre", 
    "php83Extensions.pdo", 
    "php83Extensions.pdo_mysql", 
    "php83Extensions.pdo_pgsql", 
    "php83Extensions.pdo_sqlite", 
    "php83Extensions.session", 
    "php83Extensions.tokenizer", 
    "php83Extensions.xml", 
    "php83Extensions.zip",
    "postgresql"
]

[phases.install]
cmds = [
  "composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs"
]

[phases.build]
cmds = [
  "php create-env.php",
  "php artisan key:generate --force || true",
  "php artisan config:cache || true",
  "php artisan route:cache || true",
  "php artisan view:cache || true"
]

[phases.start]
cmd = "php artisan serve --host=0.0.0.0 --port=$PORT"

[start]
cmd = "php artisan serve --host=0.0.0.0 --port=$PORT"
EOF

echo "✅ Updated nixpacks.toml with .env creation"
echo ""

echo "4. Committing changes..."
git add backend/nixpacks.toml backend/create-env.php

echo "✅ Ready to commit and deploy!"
echo ""
echo "🚀 NEXT STEPS:"
echo "1. Run: git commit -m 'FIX: Add PostgreSQL driver and .env creation for Render'"
echo "2. Run: git push origin main"
echo "3. Wait 2-3 minutes for Render to redeploy"
echo "4. Test the application"
echo ""
echo "🎯 This fix addresses:"
echo "- Missing PostgreSQL driver"
echo "- Missing .env file creation"
echo "- Proper build phase setup"
echo ""
