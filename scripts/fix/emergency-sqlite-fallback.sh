#!/bin/bash

echo "🚨 EMERGENCY SQLITE FALLBACK"
echo "============================"
echo "Creating emergency deployment with SQLite fallback"
echo ""

# Create emergency nixpacks.toml with SQLite fallback
echo "Creating emergency nixpacks.toml with SQLite fallback..."

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
    "php83Extensions.pdo_sqlite", 
    "php83Extensions.session", 
    "php83Extensions.tokenizer", 
    "php83Extensions.xml", 
    "php83Extensions.zip"
]

[phases.install]
cmds = [
  "composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs"
]

[phases.build]
cmds = [
  "php create-env-sqlite.php",
  "mkdir -p database",
  "touch database/database.sqlite",
  "chmod 664 database/database.sqlite",
  "php artisan key:generate --force || true",
  "php artisan migrate --force || true",
  "php artisan config:cache || true",
  "php artisan route:cache || true",
  "php artisan view:cache || true"
]

[phases.start]
cmd = "php artisan serve --host=0.0.0.0 --port=$PORT"

[start]
cmd = "php artisan serve --host=0.0.0.0 --port=$PORT"
EOF

echo "✅ Created emergency nixpacks.toml"

# Create SQLite-specific .env generator
echo "Creating SQLite .env generator..."

cat > backend/create-env-sqlite.php << 'EOF'
<?php
echo "Creating .env file with SQLite fallback...\n";

$envContent = "APP_NAME=" . (getenv('APP_NAME') ?: 'VidaduAcademy') . "\n";
$envContent .= "APP_ENV=" . (getenv('APP_ENV') ?: 'production') . "\n";
$envContent .= "APP_KEY=" . getenv('APP_KEY') . "\n";
$envContent .= "APP_DEBUG=" . (getenv('APP_DEBUG') ?: 'false') . "\n";
$envContent .= "APP_URL=" . (getenv('RENDER_EXTERNAL_URL') ?: 'http://localhost') . "\n";
$envContent .= "\n";
$envContent .= "LOG_CHANNEL=" . (getenv('LOG_CHANNEL') ?: 'stderr') . "\n";
$envContent .= "LOG_LEVEL=" . (getenv('LOG_LEVEL') ?: 'error') . "\n";
$envContent .= "\n";
$envContent .= "# SQLite Database Configuration\n";
$envContent .= "DB_CONNECTION=sqlite\n";
$envContent .= "DB_DATABASE=/opt/render/project/src/database/database.sqlite\n";
$envContent .= "\n";
$envContent .= "CACHE_DRIVER=" . (getenv('CACHE_DRIVER') ?: 'file') . "\n";
$envContent .= "SESSION_DRIVER=" . (getenv('SESSION_DRIVER') ?: 'database') . "\n";
$envContent .= "QUEUE_CONNECTION=" . (getenv('QUEUE_CONNECTION') ?: 'database') . "\n";
$envContent .= "\n";
$envContent .= "STRIPE_KEY=" . getenv('STRIPE_KEY') . "\n";
$envContent .= "STRIPE_SECRET=" . getenv('STRIPE_SECRET') . "\n";
$envContent .= "STRIPE_WEBHOOK_SECRET=" . getenv('STRIPE_WEBHOOK_SECRET') . "\n";

file_put_contents('.env', $envContent);
echo "✅ .env file created with SQLite configuration\n";
?>
EOF

echo "✅ Created SQLite .env generator"
echo ""

echo "🚨 EMERGENCY DEPLOYMENT READY"
echo "=============================="
echo ""
echo "⚠️  This is an EMERGENCY fallback that uses SQLite instead of PostgreSQL"
echo "⚠️  Use this ONLY if PostgreSQL driver issues persist"
echo ""
echo "To deploy emergency fallback:"
echo "1. git add backend/nixpacks.toml backend/create-env-sqlite.php"
echo "2. git commit -m 'EMERGENCY: SQLite fallback for immediate recovery'"
echo "3. git push origin main"
echo ""
echo "💡 After emergency deployment works, we can work on PostgreSQL fix"
echo ""
