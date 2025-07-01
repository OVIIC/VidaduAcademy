<?php

// Temporary debug script for checking database status
// Remove this file after debugging

echo "🔍 Database Debug Information\n";
echo "================================\n\n";

// Check environment variables
echo "📋 Environment Variables:\n";
echo "APP_ENV: " . env('APP_ENV', 'not set') . "\n";
echo "APP_KEY: " . (env('APP_KEY') ? 'SET (****)' : 'NOT SET') . "\n";
echo "DB_CONNECTION: " . env('DB_CONNECTION', 'not set') . "\n"; 
echo "DATABASE_URL: " . (env('DATABASE_URL') ? 'SET (****)' : 'NOT SET') . "\n\n";

try {
    // Test database connection
    echo "🔌 Database Connection Test:\n";
    $pdo = DB::connection()->getPdo();
    echo "✅ Database connection: OK\n\n";
    
    // Check if tables exist
    echo "📊 Database Tables:\n";
    $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    
    if (empty($tables)) {
        echo "❌ No tables found! Migrations probably not run.\n\n";
    } else {
        echo "✅ Found " . count($tables) . " tables:\n";
        foreach ($tables as $table) {
            echo "  - " . $table->table_name . "\n";
        }
        echo "\n";
    }
    
    // Check migration status
    echo "📈 Migration Status:\n";
    try {
        $migrations = DB::table('migrations')->get();
        echo "✅ Migrations table exists with " . $migrations->count() . " migrations\n";
        
        // Show last few migrations
        echo "Last 3 migrations:\n";
        $recent = DB::table('migrations')->orderBy('id', 'desc')->limit(3)->get();
        foreach ($recent as $migration) {
            echo "  - " . $migration->migration . " (batch: " . $migration->batch . ")\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Migrations table not found: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n🏁 Debug completed\n";
