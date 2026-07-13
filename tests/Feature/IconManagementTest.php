<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Icon;
use App\Models\RolePermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IconManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles & permissions
        $permissions = [
            ['role' => 'admin', 'page' => 'admin_panel', 'is_allowed' => true],
            ['role' => 'admin', 'page' => 'manage_roles', 'is_allowed' => true],
            ['role' => 'waiter', 'page' => 'waiter_terminal', 'is_allowed' => true],
            ['role' => 'waiter', 'page' => 'manage_roles', 'is_allowed' => false],
        ];

        foreach ($permissions as $p) {
            RolePermission::create($p);
        }
    }

    /**
     * Test guest and non-admin are blocked from /icon routes.
     */
    public function test_guest_and_non_admin_are_blocked_from_icons_management(): void
    {
        // Guest is redirected or blocked
        $response = $this->get('/icon');
        $response->assertRedirect('/login');

        // Waiter is blocked with 403 Forbidden
        $waiter = User::factory()->create(['role' => 'waiter']);
        $response = $this->actingAs($waiter)->get('/icon');
        $response->assertStatus(403);

        $response = $this->actingAs($waiter)->post('/icon', [
            'name' => 'Test Icon',
            'class' => 'bi-test',
        ]);
        $response->assertStatus(403);
    }

    /**
     * Test admin can access icons index.
     */
    public function test_admin_can_access_icons_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $icon = Icon::create(['name' => 'Home Icon', 'class' => 'bi-house-door']);

        $response = $this->actingAs($admin)->get('/icon');
        $response->assertStatus(200);
        $response->assertSee('Home Icon');
        $response->assertSee('bi-house-door');
    }

    /**
     * Test admin can create icon.
     */
    public function test_admin_can_create_icon(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->postJson('/icon', [
            'name' => 'Search Icon',
            'class' => 'bi-search',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Icon configuration created successfully.',
        ]);

        $this->assertDatabaseHas('icons', [
            'name' => 'Search Icon',
            'class' => 'bi-search',
        ]);
    }

    /**
     * Test admin validation for class name structure.
     */
    public function test_admin_creation_fails_on_invalid_class_format(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->postJson('/icon', [
            'name' => 'Invalid Icon',
            'class' => 'fa-search', // not matching bi-
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['class']);
    }

    /**
     * Test admin can edit icon.
     */
    public function test_admin_can_edit_icon(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $icon = Icon::create(['name' => 'Old Name', 'class' => 'bi-old']);

        $response = $this->actingAs($admin)->putJson("/icon/{$icon->id}", [
            'name' => 'New Name',
            'class' => 'bi-new',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('icons', [
            'id' => $icon->id,
            'name' => 'New Name',
            'class' => 'bi-new',
        ]);
    }

    /**
     * Test admin can delete icon.
     */
    public function test_admin_can_delete_icon(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $icon = Icon::create(['name' => 'Delete Me', 'class' => 'bi-trash']);

        $response = $this->actingAs($admin)->deleteJson("/icon/{$icon->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('icons', [
            'id' => $icon->id,
        ]);
    }
}
