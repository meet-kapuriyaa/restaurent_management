<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RolePermission;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $waiterUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create waiter user
        $this->waiterUser = User::create([
            'name' => 'Waiter User',
            'email' => 'waiter@test.com',
            'password' => bcrypt('password'),
            'role' => 'waiter',
        ]);

        // Set up permissions
        RolePermission::create(['role' => 'admin', 'page' => 'admin_panel', 'is_allowed' => true]);
        RolePermission::create(['role' => 'admin', 'page' => 'can_insert', 'is_allowed' => true]);
        RolePermission::create(['role' => 'admin', 'page' => 'can_update', 'is_allowed' => true]);
        RolePermission::create(['role' => 'admin', 'page' => 'can_delete', 'is_allowed' => true]);

        RolePermission::create(['role' => 'waiter', 'page' => 'admin_panel', 'is_allowed' => false]);
    }

    /**
     * Test admin can view table list in admin dashboard.
     */
    public function test_admin_can_view_table_list(): void
    {
        Table::create(['table_number' => 'T10', 'capacity' => 4, 'status' => 'available']);

        $response = $this->actingAs($this->adminUser)->get(route('admin.index'));

        $response->assertStatus(200)
            ->assertSee('T10')
            ->assertSee('4 Seats');
    }

    /**
     * Test admin can create a new table.
     */
    public function test_admin_can_create_table(): void
    {
        $response = $this->actingAs($this->adminUser)->postJson(route('admin.tables.store'), [
            'table_number' => 'T20',
            'capacity' => 6,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('tables', [
            'table_number' => 'T20',
            'capacity' => 6,
            'status' => 'available',
        ]);
    }

    /**
     * Test admin can update a table.
     */
    public function test_admin_can_update_table(): void
    {
        $table = Table::create(['table_number' => 'T30', 'capacity' => 2, 'status' => 'available']);

        $response = $this->actingAs($this->adminUser)->putJson(route('admin.tables.update', $table), [
            'table_number' => 'T35',
            'capacity' => 4,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('tables', [
            'id' => $table->id,
            'table_number' => 'T35',
            'capacity' => 4,
        ]);
    }

    /**
     * Test admin can delete a table.
     */
    public function test_admin_can_delete_table(): void
    {
        $table = Table::create(['table_number' => 'T40', 'capacity' => 2, 'status' => 'available']);

        $response = $this->actingAs($this->adminUser)->deleteJson(route('admin.tables.delete', $table));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('tables', ['id' => $table->id]);
    }

    /**
     * Test admin cannot delete an occupied table.
     */
    public function test_admin_cannot_delete_occupied_table(): void
    {
        $table = Table::create(['table_number' => 'T50', 'capacity' => 2, 'status' => 'occupied']);

        $response = $this->actingAs($this->adminUser)->deleteJson(route('admin.tables.delete', $table));

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Cannot delete table while it is occupied.'
            ]);

        $this->assertDatabaseHas('tables', ['id' => $table->id]);
    }

    /**
     * Test non-admin cannot manage tables.
     */
    public function test_non_admin_cannot_manage_tables(): void
    {
        $table = Table::create(['table_number' => 'T60', 'capacity' => 2, 'status' => 'available']);

        // Create table
        $response = $this->actingAs($this->waiterUser)->postJson(route('admin.tables.store'), [
            'table_number' => 'T70',
            'capacity' => 6,
        ]);
        $response->assertStatus(403);

        // Update table
        $response = $this->actingAs($this->waiterUser)->putJson(route('admin.tables.update', $table), [
            'table_number' => 'T65',
            'capacity' => 4,
        ]);
        $response->assertStatus(403);

        // Delete table
        $response = $this->actingAs($this->waiterUser)->deleteJson(route('admin.tables.delete', $table));
        $response->assertStatus(403);
    }
}
