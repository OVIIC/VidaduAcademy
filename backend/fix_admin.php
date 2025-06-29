<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "Fixing admin user permissions...\n";

// Nájdi admin používateľa
$adminUser = User::where('email', 'admin@vidaduacademy.com')->first();

if (!$adminUser) {
    echo "Admin user not found!\n";
    exit(1);
}

echo "Found admin user: {$adminUser->name} ({$adminUser->email})\n";

// Nájdi alebo vytvor admin rolu
$adminRole = Role::firstOrCreate(['name' => 'admin']);
echo "Admin role: {$adminRole->name}\n";

// Pridaj všetky potrebné oprávnenia admin role
$permissions = [
    'access admin panel',
    'view users', 'create users', 'edit users', 'delete users',
    'view courses', 'create courses', 'edit courses', 'delete courses', 'publish courses',
    'view content', 'create content', 'edit content', 'delete content',
    'view categories', 'create categories', 'edit categories', 'delete categories',
    'view enrollments', 'create enrollments', 'edit enrollments', 'delete enrollments',
    'view security logs', 'manage security settings', 'view audit logs',
    'manage system settings', 'view analytics', 'manage roles', 'manage permissions'
];

foreach ($permissions as $permissionName) {
    $permission = Permission::firstOrCreate(['name' => $permissionName]);
    if (!$adminRole->hasPermissionTo($permission)) {
        $adminRole->givePermissionTo($permission);
        echo "Added permission: {$permissionName}\n";
    }
}

// Priradíme admin rolu používateľovi
if (!$adminUser->hasRole('admin')) {
    $adminUser->assignRole('admin');
    echo "Assigned admin role to user\n";
}

echo "\nAdmin user setup complete!\n";
echo "User: {$adminUser->email}\n";
echo "Roles: " . $adminUser->roles->pluck('name')->join(', ') . "\n";
echo "Can access admin panel: " . ($adminUser->can('access admin panel') ? 'YES' : 'NO') . "\n";
