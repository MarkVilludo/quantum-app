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
            ->post(route('users.destroy.post', $student))
            ->assertRedirect(route('users.index'));

        $this->assertSoftDeleted($student);
    }

    public function test_soft_deleted_users_hidden_from_default_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student', 'name' => 'Hidden Student']);
        $student->delete();

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertDontSee('Hidden Student');

        $this->actingAs($admin)
            ->get(route('users.index', ['show_deleted' => 1]))
            ->assertOk()
            ->assertSee('Hidden Student');
    }

    public function test_non_admin_is_redirected_from_users(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->get(route('users.index'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_restore_soft_deleted_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $student->delete();

        $this->actingAs($admin)
            ->post(route('users.restore', $student))
            ->assertRedirect(route('users.index', ['show_deleted' => true]));

        $this->assertNotSoftDeleted($student);
    }
}
