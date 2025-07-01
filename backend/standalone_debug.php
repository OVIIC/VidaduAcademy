<?php

// Standalone database debug script - doesn't require Laravel bootstrap
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 Standalone Database Debug Report\n";
echo "=====================================\n\n";

// Check current directory and files
echo "📁 Current Directory: " . getcwd() . "\n";
echo "📄 Files in current directory:\n";
$files = scandir('.');
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        echo "  - $file" . (is_dir($file) ? '/' : '') . "\n";
    }
}
echo "\n";

// Check environment variables
echo "📋 Environment Variables (from \$_ENV and getenv):\n";
$envVars = ['DATABASE_URL', 'DB_CONNECTION', 'APP_ENV', 'APP_KEY', 'RENDER_EXTERNAL_URL', 'PORT'];
foreach ($envVars as $var) {
    $value = $_ENV[$var] ?? getenv($var);
    if ($var === 'DATABASE_URL' && $value) {
        $parsed = parse_url($value);
        echo "$var: SET (scheme: " . ($parsed['scheme'] ?? 'unknown') . ", host: " . ($parsed['host'] ?? 'unknown') . ")\n";
    } elseif ($var === 'APP_KEY' && $value) {
        echo "$var: SET (****)\n";
    } else {
        echo "$var: " . ($value ?: 'NOT SET') . "\n";
    }
}
echo "\n";

// Check .env file
echo "📄 .env File Analysis:\n";
if (file_exists('.env')) {
    echo "✅ .env file exists (" . filesize('.env') . " bytes)\n";
    $content = file_get_contents('.env');
    $lines = explode("\n", $content);
    $dbLines = array_filter($lines, function($line) {
        return strpos(trim($line), 'DB_') === 0 || strpos(trim($line), 'DATABASE_') === 0;
    });
    
    if ($dbLines) {
        echo "Database configuration in .env:\n";
        foreach ($dbLines as $line) {
            echo "  " . trim($line) . "\n";
        }
    } else {
        echo "No database configuration found in .env\n";
    }
} else {
    echo "❌ .env file does not exist\n";
}
echo "\n";

// Check PHP extensions
echo "🔧 PHP Extensions Check:\n";
$required = ['pdo', 'pdo_pgsql', 'pdo_mysql', 'pdo_sqlite'];
foreach ($required as $ext) {
    echo "$ext: " . (extension_loaded($ext) ? "✅ LOADED" : "❌ NOT LOADED") . "\n";
}
echo "All available PDO drivers: " . implode(', ', PDO::getAvailableDrivers()) . "\n\n";

// Raw database connection test
echo "🔌 Raw Database Connection Test:\n";
$databaseUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');

if (!$databaseUrl || $databaseUrl === 'postgresql://username:password@host:port/database') {
    echo "❌ DATABASE_URL not configured properly\n";
    echo "Testing SQLite fallback...\n";
    
    try {
        $sqliteDb = __DIR__ . '/database/database.sqlite';
        if (!is_dir(dirname($sqliteDb))) {
            mkdir(dirname($sqliteDb), 0755, true);
            echo "Created database directory\n";
        }
        if (!file_exists($sqliteDb)) {
            touch($sqliteDb);
            echo "Created SQLite database file\n";
        }
        
        $pdo = new PDO('sqlite:' . $sqliteDb, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        echo "✅ SQLite connection successful\n";
        echo "Database file: $sqliteDb\n";
        
        // Test a simple query
        $result = $pdo->query("SELECT sqlite_version() as version")->fetch();
        echo "SQLite version: " . $result['version'] . "\n";
        
    } catch (Exception $e) {
        echo "❌ SQLite connection failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "Testing PostgreSQL connection...\n";
    echo "DATABASE_URL is configured\n";
    
    try {
        $pdo = new PDO($databaseUrl, null, null, [
            PDO::ATTR_TIMEOUT => 15,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false
        ]);
        
        echo "✅ PostgreSQL connection successful\n";
        echo "Server info: " . $pdo->getAttribute(PDO::ATTR_SERVER_INFO) . "\n";
        echo "Server version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
        
        // Test a simple query
        $result = $pdo->query("SELECT version() as version")->fetch();
        echo "PostgreSQL version: " . substr($result['version'], 0, 50) . "...\n";
        
        // Check if any tables exist
        $stmt = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Tables found: " . count($tables) . "\n";
        if ($tables) {
            echo "Sample tables: " . implode(', ', array_slice($tables, 0, 5)) . "\n";
        }
        
    } catch (PDOException $e) {
        echo "❌ PostgreSQL connection failed\n";
        echo "Error Code: " . $e->getCode() . "\n";
        echo "Error Message: " . $e->getMessage() . "\n";
        
        // Provide specific diagnostics
        if (strpos($e->getMessage(), 'could not connect to server') !== false) {
            echo "\n🔍 DIAGNOSIS: Database server unreachable\n";
            echo "- Check if PostgreSQL service is running on Render\n";
            echo "- Verify DATABASE_URL host and port\n";
        } elseif (strpos($e->getMessage(), 'authentication failed') !== false) {
            echo "\n🔍 DIAGNOSIS: Authentication failed\n";
            echo "- Check DATABASE_URL username and password\n";
        } elseif (strpos($e->getMessage(), 'database') !== false && strpos($e->getMessage(), 'does not exist') !== false) {
            echo "\n🔍 DIAGNOSIS: Database does not exist\n";
            echo "- Check DATABASE_URL database name\n";
        }
    }
}

// Check Laravel files
echo "\n🎯 Laravel Application Check:\n";
if (file_exists('artisan')) {
    echo "✅ Laravel artisan found\n";
    echo "✅ This appears to be a Laravel application\n";
    
    // Check important Laravel directories
    $laravelDirs = ['app', 'config', 'database', 'routes', 'storage'];
    foreach ($laravelDirs as $dir) {
        echo "$dir/: " . (is_dir($dir) ? "✅" : "❌") . "\n";
    }
    
    // Check storage permissions
    if (is_dir('storage')) {
        $storagePerms = substr(sprintf('%o', fileperms('storage')), -4);
        echo "storage/ permissions: $storagePerms\n";
        
        $logDir = 'storage/logs';
        if (is_dir($logDir)) {
            echo "✅ storage/logs exists\n";
            $logs = glob($logDir . '/*.log');
            if ($logs) {
                echo "Log files found: " . count($logs) . "\n";
                $latestLog = end($logs);
                echo "Latest log: " . basename($latestLog) . "\n";
            }
        } else {
            echo "❌ storage/logs missing\n";
        }
    }
} else {
    echo "❌ Not a Laravel application (artisan not found)\n";
}

echo "\n🏁 Standalone debug completed\n";
echo "Timestamp: " . date('Y-m-d H:i:s T') . "\n";
