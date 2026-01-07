<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles if they don't exist (using Spatie Permission)
        // Note: In a real app, these might be seeded.
        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin', 'guard_name' => 'web']);
        }
        if (!Role::where('name', 'student')->exists()) {
            Role::create(['name' => 'student', 'guard_name' => 'web']);
        }
    }

    public function test_admin_can_access_admin_routes()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->getJson('/api/admin/users');

        $response->assertStatus(200);
    }

    public function test_student_cannot_access_admin_routes()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($student)
            ->getJson('/api/admin/users');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_routes()
    {
        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(401);
    }
}
