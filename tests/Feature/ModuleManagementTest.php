<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Module;
use App\Models\RolePermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ModuleManagementTest extends TestCase
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
            'email' => 'admin_mod@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Waiter user
        $this->waiter = User::create([
            'name' => 'Waiter User',
            'email' => 'waiter_mod@example.com',
            'password' => Hash::make('password'),
            'role' => 'waiter',
        ]);

        // Seed Role Permissions for Admin and Waiter
        RolePermission::create([
            'role' => 'admin',
            'page' => 'manage_roles',
            'is_allowed' => true,
        ]);
        RolePermission::create([
            'role' => 'waiter',
            'page' => 'manage_roles',
            'is_allowed' => false,
        ]);

        // Seed default navigation modules
        Module::create([
            'title' => 'Analytics Hub',
            'url' => '#dashboard-pane',
            'icon_class' => 'bi-graph-up-arrow',
            'is_visible' => true,
            'order_weight' => 1,
        ]);
    }

    /**
     * Test admin can view role manager which includes modules lists.
     */
    public function test_admin_can_access_modules_view()
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin-role');
        $response->assertStatus(200);
        $response->assertSee('Module Builder');
        $response->assertSee('Analytics Hub');
    }

    /**
     * Test non-admin is blocked from modules CRUD API endpoints.
     */
    public function test_non_admin_is_blocked_from_modules_crud()
    {
        $this->actingAs($this->waiter);

        // Try to create module
        $response = $this->post(route('admin.modules.store'), [
            'title' => 'New Tab',
            'url' => '#new-pane',
        ]);
        $response->assertStatus(403);
    }

    /**
     * Test admin can create a parent and a child module.
     */
    public function test_admin_can_create_modules()
    {
        $this->actingAs($this->admin);

        // 1. Create root module
        $response = $this->post(route('admin.modules.store'), [
            'title' => 'Testimonials',
            'url' => '#testimonials-pane',
            'icon_class' => 'bi-chat',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('modules', [
            'title' => 'Testimonials',
            'url' => '#testimonials-pane',
            'parent_id' => null,
        ]);

        $parentModule = Module::where('title', 'Testimonials')->first();

        // 2. Create child module
        $response = $this->post(route('admin.modules.store'), [
            'title' => 'Sub Testimonials',
            'url' => '#sub-testimonials-pane',
            'parent_id' => $parentModule->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('modules', [
            'title' => 'Sub Testimonials',
            'parent_id' => $parentModule->id,
        ]);
    }

    /**
     * Test admin can toggle visibility of a module.
     */
    public function test_admin_can_toggle_visibility()
    {
        $this->actingAs($this->admin);

        $module = Module::where('title', 'Analytics Hub')->first();
        $this->assertTrue($module->is_visible);

        $response = $this->post(route('admin.modules.toggle', $module));
        $response->assertStatus(200);
        
        $module->refresh();
        $this->assertFalse($module->is_visible);
    }

    /**
     * Test admin can edit module details.
     */
    public function test_admin_can_edit_module()
    {
        $this->actingAs($this->admin);

        $module = Module::where('title', 'Analytics Hub')->first();

        $response = $this->put(route('admin.modules.update', $module), [
            'title' => 'Super Dashboard',
            'url' => '#dashboard-pane',
            'icon_class' => 'bi-speedometer',
        ]);
        $response->assertStatus(200);
        
        $module->refresh();
        $this->assertEquals('Super Dashboard', $module->title);
        $this->assertEquals('bi-speedometer', $module->icon_class);
    }

    /**
     * Test admin can delete module.
     */
    public function test_admin_can_delete_module()
    {
        $this->actingAs($this->admin);

        $module = Module::where('title', 'Analytics Hub')->first();

        $response = $this->delete(route('admin.modules.destroy', $module));
        $response->assertStatus(200);
        
        $this->assertDatabaseMissing('modules', [
            'title' => 'Analytics Hub',
        ]);
    }

    /**
     * Test admin can sort modules.
     */
    public function test_admin_can_sort_modules()
    {
        $this->actingAs($this->admin);

        $module = Module::where('title', 'Analytics Hub')->first();

        $response = $this->post(route('admin.modules.sort'), [
            'order' => [
                [
                    'id' => $module->id,
                    'order_weight' => 50,
                ]
            ]
        ]);
        $response->assertStatus(200);

        $module->refresh();
        $this->assertEquals(50, $module->order_weight);
    }
}
