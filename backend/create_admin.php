<?php

// Simple script to create admin user for VidaduAcademy
// Run this after database is set up

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Create admin user
$admin = User::firstOrCreate([
    'email' => 'admin@vidaduacademy.com'
], [
    'name' => 'VidaduAcademy Admin',
    'email' => 'admin@vidaduacademy.com',
    'password' => Hash::make('admin123'),
    'email_verified_at' => now(),
    'is_admin' => true,
]);

echo "✅ Admin user created/updated:\n";
echo "Email: admin@vidaduacademy.com\n";
echo "Password: admin123\n";
echo "Name: {$admin->name}\n";
echo "ID: {$admin->id}\n";
