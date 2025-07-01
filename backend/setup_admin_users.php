<?php

/**
 * Enhanced Admin User Creation Script for VidaduAcademy
 * This script creates multiple admin users with different roles
 */

echo "🔧 VidaduAcademy Admin User Creation\n";
echo "====================================\n\n";

// Try to bootstrap Laravel
try {
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    echo "✅ Laravel bootstrapped successfully\n";
} catch (Exception $e) {
    echo "❌ Failed to bootstrap Laravel: " . $e->getMessage() . "\n";
    echo "Make sure you're running this from the Laravel backend directory\n";
    exit(1);
}

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// Test database connection
echo "🔌 Testing database connection...\n";
try {
    DB::connection()->getPdo();
    echo "✅ Database connection successful\n\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "Fix database connection before creating admin users\n";
    exit(1);
}

// Check if users table exists
try {
    $hasUsersTable = DB::getSchemaBuilder()->hasTable('users');
    if (!$hasUsersTable) {
        echo "❌ Users table doesn't exist. Run migrations first:\n";
        echo "php artisan migrate\n";
        exit(1);
    }
    echo "✅ Users table exists\n";
} catch (Exception $e) {
    echo "⚠️  Could not check users table: " . $e->getMessage() . "\n";
}

// Admin users to create
$adminUsers = [
    [
        'name' => 'VidaduAcademy Admin',
        'email' => 'admin@vidaduacademy.com',
        'password' => 'admin123',
        'role' => 'super_admin'
    ],
    [
        'name' => 'Content Manager',
        'email' => 'content@vidaduacademy.com', 
        'password' => 'content123',
        'role' => 'content_admin'
    ],
    [
        'name' => 'Support Manager',
        'email' => 'support@vidaduacademy.com',
        'password' => 'support123', 
        'role' => 'support_admin'
    ],
    [
        'name' => 'Test Admin',
        'email' => 'test@vidaduacademy.com',
        'password' => 'test123',
        'role' => 'admin'
    ]
];

echo "👥 Creating admin users...\n";
echo "=========================\n\n";

$created = 0;
$updated = 0;

foreach ($adminUsers as $userData) {
    try {
        // Check if user already exists
        $existingUser = User::where('email', $userData['email'])->first();
        
        if ($existingUser) {
            // Update existing user
            $existingUser->update([
                'name' => $userData['name'],
                'password' => Hash::make($userData['password']),
                'email_verified_at' => now(),
                'is_admin' => true,
            ]);
            
            echo "🔄 Updated existing user: {$userData['email']}\n";
            echo "   Name: {$userData['name']}\n";
            echo "   Role: {$userData['role']}\n";
            echo "   Password: {$userData['password']}\n\n";
            $updated++;
            
        } else {
            // Create new user
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'email_verified_at' => now(),
                'is_admin' => true,
            ]);
            
            echo "✅ Created new admin user: {$userData['email']}\n";
            echo "   Name: {$userData['name']}\n";
            echo "   Role: {$userData['role']}\n";
            echo "   Password: {$userData['password']}\n";
            echo "   ID: {$user->id}\n\n";
            $created++;
        }
        
    } catch (Exception $e) {
        echo "❌ Failed to create user {$userData['email']}: " . $e->getMessage() . "\n\n";
    }
}

// Summary
echo "📊 SUMMARY:\n";
echo "===========\n";
echo "✅ Created: $created admin users\n";
echo "🔄 Updated: $updated admin users\n";
echo "📝 Total admin users in database: " . User::where('is_admin', true)->count() . "\n\n";

// Show all admin users
echo "👥 ALL ADMIN USERS:\n";
echo "==================\n";
$admins = User::where('is_admin', true)->get();
foreach ($admins as $admin) {
    echo "- {$admin->name} ({$admin->email}) - ID: {$admin->id}\n";
}

echo "\n🔐 ADMIN LOGIN CREDENTIALS:\n";
echo "===========================\n";
echo "Primary Admin:\n";
echo "  Email: admin@vidaduacademy.com\n";
echo "  Password: admin123\n\n";

echo "Content Manager:\n";
echo "  Email: content@vidaduacademy.com\n";
echo "  Password: content123\n\n";

echo "Support Manager:\n";
echo "  Email: support@vidaduacademy.com\n";
echo "  Password: support123\n\n";

echo "Test Admin:\n";
echo "  Email: test@vidaduacademy.com\n";
echo "  Password: test123\n\n";

echo "🌐 ADMIN PANEL ACCESS:\n";
echo "======================\n";
echo "Local: http://localhost:8000/admin\n";
echo "Production: https://vidaduacademy-backend.onrender.com/admin\n";
echo "Frontend Admin: https://vidaduacademy.onrender.com/admin\n\n";

echo "💡 NEXT STEPS:\n";
echo "==============\n";
echo "1. Test admin login on the frontend\n";
echo "2. Access admin dashboard\n";  
echo "3. Create courses and manage content\n";
echo "4. Change default passwords for security\n\n";

echo "🏁 Admin user creation complete!\n";
