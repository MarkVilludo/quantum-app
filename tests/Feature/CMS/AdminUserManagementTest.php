<?php

namespace Tests\Feature\CMS;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_soft_deleted_user_resolves_on_edit_route(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $student->delete();

        $this->actingAs($admin)
            ->get(route('users.edit', $student))
            ->assertOk();
    }

    public function test_admin_can_soft_delete_active_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($admin)
            ->delete(route('users.destroy', $student))
            ->assertRedirect(route('users.index'));

        $this->assertSoftDeleted($student);
    }

    public function test_admin_can_restore_soft_deleted_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $student->delete();

        $this->actingAs($admin)
            ->post(route('users.restore', $student))
            ->assertRedirect(route('users.index'));

        $this->assertNotSoftDeleted($student);
    }
}
