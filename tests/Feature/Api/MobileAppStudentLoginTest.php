<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Support\MobileAppAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MobileAppStudentLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => database_path('database.sqlite'),
        ]);
    }

    public function test_student_can_login_via_api(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
            'email' => 'student@example.com',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['token', 'user']);
    }

    public function test_teacher_login_returns_clear_student_only_response(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'email' => 'teacher@example.com',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $teacher->email,
            'password' => 'password',
        ]);

        $response->assertForbidden();
        $response->assertJson([
            'message' => MobileAppAccess::studentOnlyMessage(),
            'mobile_app_student_only' => true,
        ]);
        $response->assertJsonMissing(['token']);
    }

    public function test_admin_login_returns_clear_student_only_response(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertForbidden();
        $response->assertJson([
            'mobile_app_student_only' => true,
        ]);
    }

    public function test_teacher_token_cannot_access_protected_api_routes(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        Sanctum::actingAs($teacher);

        $response = $this->getJson('/api/student-profile');

        $response->assertForbidden();
        $response->assertJson([
            'message' => MobileAppAccess::studentOnlyMessage(),
            'mobile_app_student_only' => true,
        ]);
    }
}
