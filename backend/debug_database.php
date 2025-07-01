<?php

// Enhanced debug script for comprehensive database diagnostics
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 VidaduAcademy Enhanced Database Debug\n";
echo "==========================================\n\n";

// Check environment variables with better handling
echo "📋 Environment Variables:\n";
echo "APP_ENV: " . (env('APP_ENV') ?: getenv('APP_ENV') ?: 'not set') . "\n";
echo "APP_KEY: " . (env('APP_KEY') ? 'SET (****)' : 'NOT SET') . "\n";
echo "DB_CONNECTION: " . (env('DB_CONNECTION') ?: getenv('DB_CONNECTION') ?: 'not set') . "\n";

$databaseUrl = env('DATABASE_URL') ?: getenv('DATABASE_URL');
if ($databaseUrl) {
    // Parse DATABASE_URL to show components safely
    $parsed = parse_url($databaseUrl);
    echo "DATABASE_URL: SET\n";
    echo "  - Scheme: " . ($parsed['scheme'] ?? 'unknown') . "\n";
    echo "  - Host: " . ($parsed['host'] ?? 'unknown') . "\n";
    echo "  - Port: " . ($parsed['port'] ?? 'default') . "\n";
    echo "  - Database: " . (ltrim($parsed['path'] ?? '', '/') ?: 'unknown') . "\n";
    echo "  - User: " . ($parsed['user'] ?? 'unknown') . "\n";
} else {
    echo "DATABASE_URL: NOT SET\n";
}

echo "RENDER_EXTERNAL_URL: " . (getenv('RENDER_EXTERNAL_URL') ?: 'not set') . "\n\n";

// Check .env file
echo "📄 .env File Status:\n";
if (file_exists('.env')) {
    echo "✅ .env file exists\n";
    $envLines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $dbLines = array_filter($envLines, function($line) {
        return strpos(trim($line), 'DB_') === 0 || strpos(trim($line), 'DATABASE_') === 0;
    });
    if ($dbLines) {
        echo "Database config in .env:\n";
        foreach ($dbLines as $line) {
            echo "  " . trim($line) . "\n";
        }
    }
} else {
    echo "❌ .env file missing\n";
}
echo "\n";

// Check PHP extensions
echo "🔧 PHP Extensions:\n";
$required = ['pdo', 'pdo_pgsql', 'pdo_mysql', 'pdo_sqlite'];
foreach ($required as $ext) {
    echo "$ext: " . (extension_loaded($ext) ? "✅" : "❌") . "\n";
}
echo "\n";

// Try raw PDO connection first
echo "🔌 Raw PDO Connection Test:\n";
try {
    $dbUrl = env('DATABASE_URL') ?: getenv('DATABASE_URL');
    
    if (!$dbUrl || $dbUrl === 'postgresql://username:password@host:port/database') {
        echo "❌ DATABASE_URL not properly configured\n";
        echo "Attempting SQLite fallback...\n";
        
        $sqlitePath = __DIR__ . '/database/database.sqlite';
        if (!is_dir(dirname($sqlitePath))) {
            mkdir(dirname($sqlitePath), 0755, true);
        }
        if (!file_exists($sqlitePath)) {
            touch($sqlitePath);
        }
        
        $pdo = new PDO('sqlite:' . $sqlitePath);
        echo "✅ SQLite connection successful\n";
    } else {
        echo "Testing connection to: $dbUrl\n";
        $pdo = new PDO($dbUrl, null, null, [
            PDO::ATTR_TIMEOUT => 10,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        echo "✅ PostgreSQL connection successful\n";
    }
    
    echo "Database driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
    echo "Server version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n\n";
    
} catch (Exception $e) {
    echo "❌ Raw PDO connection failed\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n\n";
    
    // Don't proceed with Laravel tests if basic PDO fails
    echo "🏁 Stopping here due to connection failure\n";
    return;
}

// Now test Laravel connection
echo "🎯 Laravel Database Connection Test:\n";
try {
    $connection = DB::connection();
    $pdo = $connection->getPdo();
    echo "✅ Laravel DB connection: OK\n";
    echo "Connection name: " . $connection->getName() . "\n";
    echo "Driver name: " . $connection->getDriverName() . "\n\n";
    
    // Test with a simple query
    $result = DB::select('SELECT 1 as test');
    echo "✅ Simple query test: OK\n\n";
    
} catch (Exception $e) {
    echo "❌ Laravel DB connection failed\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (line " . $e->getLine() . ")\n\n";
    return;
}

// Check database tables
echo "📊 Database Tables:\n";
try {
    $driver = DB::connection()->getDriverName();
    
    if ($driver === 'sqlite') {
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        $tableNames = array_column($tables, 'name');
    } elseif ($driver === 'pgsql') {
        $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
        $tableNames = array_column($tables, 'tablename');
    } else {
        $tables = DB::select("SELECT TABLE_NAME FROM information_schema.tables WHERE TABLE_SCHEMA = DATABASE()");
        $tableNames = array_column($tables, 'TABLE_NAME');
    }
    
    if (empty($tableNames)) {
        echo "❌ No tables found! Database needs migration.\n\n";
    } else {
        echo "✅ Found " . count($tableNames) . " tables:\n";
        foreach ($tableNames as $table) {
            echo "  - $table\n";
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Failed to list tables: " . $e->getMessage() . "\n\n";
}

// Check migrations
echo "📈 Migration Status:\n";
try {
    if (Schema::hasTable('migrations')) {
        $migrations = DB::table('migrations')->orderBy('batch', 'desc')->orderBy('migration', 'desc')->get();
        echo "✅ Migrations table exists with " . $migrations->count() . " migrations\n";
        
        if ($migrations->count() > 0) {
            echo "Latest migrations:\n";
            $recent = $migrations->take(5);
            foreach ($recent as $migration) {
                echo "  - " . $migration->migration . " (batch: " . $migration->batch . ")\n";
            }
        }
    } else {
        echo "❌ Migrations table does not exist\n";
        echo "Run: php artisan migrate\n";
    }
} catch (Exception $e) {
    echo "❌ Migration check failed: " . $e->getMessage() . "\n";
}

echo "\n🏁 Enhanced debug completed\n";
