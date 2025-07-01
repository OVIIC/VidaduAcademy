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
