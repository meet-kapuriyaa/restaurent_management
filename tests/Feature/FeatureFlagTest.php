<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Feature;
use App\Models\RolePermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FeatureFlagTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $waiter;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin user
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin_test@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Waiter user
        $this->waiter = User::create([
            'name' => 'Waiter User',
            'email' => 'waiter_test@example.com',
            'password' => Hash::make('password'),
            'role' => 'waiter',
        ]);

        // Seed Role Permissions for Waiter
        RolePermission::create([
            'role' => 'waiter',
            'page' => 'waiter_terminal',
            'is_allowed' => true,
        ]);
        RolePermission::create([
            'role' => 'waiter',
            'page' => 'manage_roles',
            'is_allowed' => false,
        ]);

        // Seed default features
        Feature::create([
            'key' => 'waiter_terminal',
            'display_name' => 'Waiter Terminal',
            'is_enabled' => true,
        ]);
        Feature::create([
            'key' => 'audit_reports',
            'display_name' => 'Audit Reports',
            'is_enabled' => true,
        ]);
    }

    /**
     * Test admin can toggle features.
     */
    public function test_admin_can_toggle_features()
    {
        $this->actingAs($this->admin);

        // Turn off Waiter Terminal
        $response = $this->post(route('admin.features.toggle'), [
            'key' => 'waiter_terminal',
            'is_enabled' => 0,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertFalse(Feature::isActive('waiter_terminal'));
    }

    /**
     * Test non-admin cannot toggle features.
     */
    public function test_non_admin_cannot_toggle_features()
    {
        $this->actingAs($this->waiter);

        // Waiter doesn't have manage_roles permission so the route middleware blocks them
        $response = $this->post(route('admin.features.toggle'), [
            'key' => 'waiter_terminal',
            'is_enabled' => 0,
        ]);

        $response->assertStatus(403);
        $this->assertTrue(Feature::isActive('waiter_terminal'));
    }

    /**
     * Test waiter terminal is blocked when toggled off.
     */
    public function test_waiter_terminal_is_blocked_when_toggled_off()
    {
        // Set waiter_terminal to false
        $feature = Feature::where('key', 'waiter_terminal')->first();
        $feature->update(['is_enabled' => false]);

        $this->actingAs($this->waiter);

        // Waiter accessing /order should be blocked
        $response = $this->get('/order');
        $response->assertStatus(403);
        $response->assertSee('Waiter Terminal &amp; Table Ordering is currently disabled.', false);
    }

    /**
     * Test waiter terminal is accessible when toggled on.
     */
    public function test_waiter_terminal_is_accessible_when_toggled_on()
    {
        $this->actingAs($this->waiter);

        // Waiter accessing /order should be successful
        $response = $this->get('/order');
        $response->assertStatus(200);
    }
}
