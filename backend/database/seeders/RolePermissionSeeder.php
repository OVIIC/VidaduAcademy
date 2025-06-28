<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Course management
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
            'publish courses',
            
            // Content management
            'view content',
            'create content',
            'edit content',
            'delete content',
            
            // Category management
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Enrollment management
            'view enrollments',
            'create enrollments',
            'edit enrollments',
            'delete enrollments',
            
            // Security management
            'view security logs',
            'manage security settings',
            'view audit logs',
            
            // System administration
            'access admin panel',
            'manage system settings',
            'view analytics',
            'manage roles',
            'manage permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $instructorRole = Role::firstOrCreate(['name' => 'instructor']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        // Admin gets all permissions
        $adminRole->givePermissionTo(Permission::all());

        // Instructor gets course and content management permissions
        $instructorPermissions = [
            'view courses',
            'create courses',
            'edit courses',
            'publish courses',
            'view content',
            'create content',
            'edit content',
            'view enrollments',
            'access admin panel',
        ];
        $instructorRole->givePermissionTo($instructorPermissions);

        // Student gets basic view permissions
        $studentPermissions = [
            'view courses',
            'view content',
        ];
        $studentRole->givePermissionTo($studentPermissions);

        // Assign admin role to the first user (test@example.com)
        $adminUser = User::where('email', 'test@example.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('admin');
            echo "Admin role assigned to test@example.com\n";
        }

        // Assign student role to the second user (newuser@example.com)
        $studentUser = User::where('email', 'newuser@example.com')->first();
        if ($studentUser) {
            $studentUser->assignRole('student');
            echo "Student role assigned to newuser@example.com\n";
        }

        echo "Roles and permissions created successfully!\n";
        echo "Admin user: test@example.com (password: password123)\n";
        echo "Student user: newuser@example.com (password: password123)\n";
    }
}
