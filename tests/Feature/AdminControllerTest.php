<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RolePermission;
use App\Models\FoodItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdminUser;
    protected User $adminUser;
    protected User $waiterUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create super admin user
        $this->superAdminUser = User::create([
            'name' => 'Super Admin Manager',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create admin user
        $this->adminUser = User::create([
            'name' => 'Admin Manager',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create waiter user
        $this->waiterUser = User::create([
            'name' => 'Waiter Billy',
            'email' => 'waiter@test.com',
            'password' => bcrypt('password'),
            'role' => 'waiter',
        ]);

        // Create role permissions
        RolePermission::create(['role' => 'admin', 'page' => 'admin_panel', 'is_allowed' => true]);
        RolePermission::create(['role' => 'admin', 'page' => 'manage_roles', 'is_allowed' => true]);
        RolePermission::create(['role' => 'waiter', 'page' => 'admin_panel', 'is_allowed' => false]);
    }

    /**
     * Test guest cannot access admin.
     */
    public function test_guest_cannot_access_admin_panel(): void
    {
        $response = $this->get(route('admin.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test non-admin waiter cannot access admin.
     */
    public function test_waiter_cannot_access_admin_panel(): void
    {
        $response = $this->actingAs($this->waiterUser)->get(route('admin.index'));
        $response->assertStatus(403);
    }

    /**
     * Test admin dashboard page loads successfully.
     */
    public function test_admin_dashboard_loads_successfully(): void
    {
        FoodItem::create(['name' => 'Burger', 'price' => 250.00, 'status' => 'available']);
        Order::create([
            'customer_name' => 'John Doe',
            'contact_number' => '9876543210',
            'total_amount' => 500.00,
            'status' => 'completed',
            'payment_status' => 'paid'
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.index'));

        $response->assertStatus(200);
        $response->assertViewHasAll([
            'foodItems',
            'orders',
            'users',
            'permissions',
            'totalRevenue',
            'activeOrdersCount',
            'totalFoodItemsCount',
            'availableRoles',
            'pendingUsers',
        ]);
    }

    /**
     * Test adding a food item.
     */
    public function test_admin_can_store_food_item(): void
    {
        $payload = [
            'name' => 'Tandoori Chicken',
            'price' => 350.00,
            'description' => 'Delicious tandoori chicken',
            'status' => 'available',
            'image' => \Illuminate\Http\UploadedFile::fake()->create('chicken.jpg', 100, 'image/jpeg'),
        ];

        $response = $this->actingAs($this->adminUser)->postJson(route('admin.food-items.store'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Food item added successfully!'
            ]);

        $this->assertDatabaseHas('food_items', [
            'name' => 'Tandoori Chicken',
            'price' => 350.00,
            'status' => 'available',
        ]);
    }

    /**
     * Test updating a food item.
     */
    public function test_admin_can_update_food_item(): void
    {
        $foodItem = FoodItem::create([
            'name' => 'Paneer Tikka',
            'price' => 200.00,
            'description' => 'Old description',
            'status' => 'available',
        ]);

        $payload = [
            'name' => 'Paneer Tikka Masala',
            'price' => 240.00,
            'description' => 'Updated description',
            'status' => 'unavailable',
        ];

        $response = $this->actingAs($this->adminUser)->putJson(route('admin.food-items.update', $foodItem), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Food item updated successfully!'
            ]);

        $this->assertDatabaseHas('food_items', [
            'id' => $foodItem->id,
            'name' => 'Paneer Tikka Masala',
            'price' => 240.00,
            'status' => 'unavailable',
        ]);
    }

    /**
     * Test deleting a food item.
     */
    public function test_admin_can_delete_food_item(): void
    {
        $foodItem = FoodItem::create([
            'name' => 'Garlic Naan',
            'price' => 50.00,
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->adminUser)->deleteJson(route('admin.food-items.delete', $foodItem));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Food item deleted successfully!'
            ]);

        $this->assertDatabaseMissing('food_items', [
            'id' => $foodItem->id,
        ]);
    }

    /**
     * Test toggling status of a food item directly.
     */
    public function test_admin_can_toggle_food_item_status(): void
    {
        $foodItem = FoodItem::create([
            'name' => 'Spring Roll',
            'price' => 150.00,
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->adminUser)->patchJson(route('admin.food-items.status', $foodItem));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Food item status changed to Unavailable successfully!',
                'status' => 'unavailable'
            ]);

        $this->assertEquals('unavailable', $foodItem->fresh()->status);
    }

    /**
     * Test updating an order status.
     */
    public function test_admin_can_update_order_status(): void
    {
        $order = Order::create([
            'customer_name' => 'Alice',
            'contact_number' => '1234567890',
            'total_amount' => 150.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->adminUser)->patchJson(route('admin.orders.status', $order), [
            'status' => 'preparing'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Order status updated successfully!'
            ]);

        $this->assertEquals('preparing', $order->fresh()->status);
    }

    /**
     * Test updating order status to completed automatically sets payment status to paid.
     */
    public function test_admin_completing_order_leaves_payment_status_as_unpaid(): void
    {
        $order = Order::create([
            'customer_name' => 'Bob',
            'contact_number' => '9999999999',
            'total_amount' => 300.00,
            'status' => 'ready',
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->adminUser)->patchJson(route('admin.orders.status', $order), [
            'status' => 'completed'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('completed', $order->fresh()->status);
        $this->assertEquals('unpaid', $order->fresh()->payment_status);
    }

    /**
     * Test updating order payment status.
     */
    public function test_admin_can_update_order_payment_status(): void
    {
        $order = Order::create([
            'customer_name' => 'Charlie',
            'contact_number' => '8888888888',
            'total_amount' => 120.00,
            'status' => 'preparing',
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->adminUser)->patchJson(route('admin.orders.payment', $order), [
            'payment_status' => 'paid'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Order payment status updated successfully!'
            ]);

        $this->assertEquals('paid', $order->fresh()->payment_status);
    }

    /**
     * Test deleting an order.
     */
    public function test_admin_can_delete_order(): void
    {
        $order = Order::create([
            'customer_name' => 'Dave',
            'contact_number' => '7777777777',
            'total_amount' => 80.00,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->adminUser)->deleteJson(route('admin.orders.delete', $order));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Order deleted successfully!'
            ]);

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);
    }

    /**
     * Test updating role permissions.
     */
    public function test_admin_can_update_role_permissions(): void
    {
        $payload = [
            'role' => 'waiter',
            'page' => 'kitchen_terminal',
            'is_allowed' => 1,
        ];

        $response = $this->actingAs($this->superAdminUser)->postJson(route('admin.permissions.update'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Permission configuration updated successfully!'
            ]);

        $this->assertDatabaseHas('role_permissions', [
            'role' => 'waiter',
            'page' => 'kitchen_terminal',
            'is_allowed' => true,
        ]);
    }

    /**
     * Test updating user role.
     */
    public function test_admin_can_update_user_role(): void
    {
        $response = $this->actingAs($this->superAdminUser)->patchJson(route('admin.users.role', $this->waiterUser), [
            'role' => 'chef',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => "User's role updated to Chef successfully!"
            ]);

        $this->assertEquals('chef', $this->waiterUser->fresh()->role);
    }

    /**
     * Test updating user details, specifically assigning roles to pending users.
     */
    public function test_admin_can_update_user_details_including_pending_users(): void
    {
        $pendingUser = User::create([
            'name' => 'New Pending Employee',
            'email' => 'pendingemp@test.com',
            'password' => bcrypt('password123'),
            'role' => 'pending',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdminUser)->putJson(route('admin.users.update', $pendingUser), [
            'name' => 'Approved Employee',
            'email' => 'pendingemp@test.com',
            'role' => 'chef',
            'salary' => 45000,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Employee account updated successfully!'
            ]);

        $this->assertEquals('chef', $pendingUser->fresh()->role);
        $this->assertEquals('Approved Employee', $pendingUser->fresh()->name);
        $this->assertEquals(45000, $pendingUser->fresh()->salary);
    }

    /**
     * Test deleting user.
     */
    public function test_admin_can_delete_user(): void
    {
        $response = $this->actingAs($this->superAdminUser)->deleteJson(route('admin.users.delete', $this->waiterUser));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User account deleted successfully!'
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $this->waiterUser->id,
        ]);
    }

    /**
     * Test admin cannot delete themselves.
     */
    public function test_admin_cannot_delete_themselves(): void
    {
        $response = $this->actingAs($this->superAdminUser)->deleteJson(route('admin.users.delete', $this->superAdminUser));

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Action denied: You cannot delete your own account.'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->superAdminUser->id,
        ]);
    }

    /**
     * Test admin can filter orders via AJAX.
     */
    public function test_admin_can_filter_orders_via_ajax(): void
    {
        $food = FoodItem::create(['name' => 'Burger', 'price' => 250.00, 'status' => 'available']);
        
        // Order 1: Two days ago
        $order1 = new Order([
            'customer_name' => 'Alice Doe',
            'contact_number' => '1111111111',
            'total_amount' => 250.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
        $order1->created_at = '2026-06-28 12:00:00';
        $order1->save();

        // Order 2: Yesterday
        $order2 = new Order([
            'customer_name' => 'Bob Smith',
            'contact_number' => '2222222222',
            'total_amount' => 500.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
        $order2->created_at = '2026-06-29 12:00:00';
        $order2->save();

        // Order 3: Older Date
        $order3 = new Order([
            'customer_name' => 'Charlie Brown',
            'contact_number' => '3333333333',
            'total_amount' => 750.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
        $order3->created_at = '2026-06-30 12:00:00';
        $order3->save();

        // Order 4: Real Today
        $orderToday = new Order([
            'customer_name' => 'Dave Today',
            'contact_number' => '4444444444',
            'total_amount' => 120.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
        $orderToday->created_at = today();
        $orderToday->save();

        // 1. Test Filter Type: single date (2026-06-29)
        $response = $this->actingAs($this->adminUser)
            ->get(
                route('admin.index', ['filter_type' => 'single', 'filter_date' => '2026-06-29']),
                ['X-Requested-With' => 'XMLHttpRequest']
            );

        $response->assertStatus(200);
        $response->assertSee('Bob Smith');
        $response->assertDontSee('Alice Doe');
        $response->assertDontSee('Charlie Brown');
        $response->assertDontSee('Dave Today');

        // 2. Test Filter Type: custom range (from 2026-06-28 to 2026-06-29)
        $response = $this->actingAs($this->adminUser)
            ->get(
                route('admin.index', ['filter_type' => 'range', 'from_date' => '2026-06-28', 'to_date' => '2026-06-29']),
                ['X-Requested-With' => 'XMLHttpRequest']
            );

        $response->assertStatus(200);
        $response->assertSee('Alice Doe');
        $response->assertSee('Bob Smith');
        $response->assertDontSee('Charlie Brown');
        $response->assertDontSee('Dave Today');

        // 3. Test Filter Type: today
        $response = $this->actingAs($this->adminUser)
            ->get(
                route('admin.index', ['filter_type' => 'today']),
                ['X-Requested-With' => 'XMLHttpRequest']
            );

        $response->assertStatus(200);
        $response->assertSee('Dave Today');
        $response->assertDontSee('Alice Doe');
        $response->assertDontSee('Bob Smith');
        $response->assertDontSee('Charlie Brown');

        // 4. Test Filter Type: all (Default)
        $response = $this->actingAs($this->adminUser)
            ->get(
                route('admin.index'),
                ['X-Requested-With' => 'XMLHttpRequest']
            );

        $response->assertStatus(200);
        $response->assertSee('Alice Doe');
        $response->assertSee('Bob Smith');
        $response->assertSee('Charlie Brown');
        $response->assertSee('Dave Today');
    }

    /**
     * Test that admin cannot print invoice for non-completed order.
     */
    public function test_admin_cannot_print_invoice_for_non_completed_order(): void
    {
        $order = Order::create([
            'customer_name' => 'John Doe',
            'contact_number' => '1234567890',
            'total_amount' => 50.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.orders.invoice', $order));

        $response->assertStatus(403);
    }

    /**
     * Test that admin can print invoice for completed order.
     */
    public function test_admin_can_print_invoice_for_completed_order(): void
    {
        $order = Order::create([
            'customer_name' => 'John Doe',
            'contact_number' => '1234567890',
            'total_amount' => 50.00,
            'status' => 'completed',
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.orders.invoice', $order));

        $response->assertStatus(200)
            ->assertViewIs('invoice');
    }

    public function test_admin_can_toggle_role_status(): void
    {
        // Create custom_role active record
        \App\Models\RolePermission::create([
            'role' => 'custom_role',
            'page' => 'role_active',
            'is_allowed' => true,
        ]);

        // Toggle to inactive (0)
        $response = $this->actingAs($this->superAdminUser)->postJson(route('admin.roles.toggle-status'), [
            'role' => 'custom_role',
            'status' => 0
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => "Role 'Custom role' deactivated successfully.",
                'is_active' => false
            ]);

        $this->assertDatabaseHas('role_permissions', [
            'role' => 'custom_role',
            'page' => 'role_active',
            'is_allowed' => false
        ]);
    }

    public function test_admin_cannot_deactivate_admin_role(): void
    {
        $response = $this->actingAs($this->superAdminUser)->postJson(route('admin.roles.toggle-status'), [
            'role' => 'admin',
            'status' => 0
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'The Admin role cannot be deactivated.'
            ]);
    }

    /**
     * Test admin-role page access restrictions.
     */
    public function test_roles_page_access_restrictions(): void
    {
        // Guest is redirected to login
        $this->get(route('admin.roles.index'))->assertRedirect(route('login'));

        // Waiter gets 403
        $this->actingAs($this->waiterUser)->get(route('admin.roles.index'))->assertStatus(403);

        // Admin gets 200
        $this->actingAs($this->adminUser)->get(route('admin.roles.index'))->assertStatus(200);
    }

    /**
     * Test standard admin cannot hit role management endpoints directly.
     */
    public function test_waiter_cannot_update_roles_or_permissions(): void
    {
        // Try to update permissions
        $this->actingAs($this->waiterUser)->postJson(route('admin.permissions.update'), [
            'role' => 'waiter',
            'page' => 'kitchen_terminal',
            'is_allowed' => 1
        ])->assertStatus(403);

        // Try to create role
        $this->actingAs($this->waiterUser)->postJson(route('admin.roles.create'), [
            'role_name' => 'new_test_role'
        ])->assertStatus(403);

        // Try to toggle status
        $this->actingAs($this->waiterUser)->postJson(route('admin.roles.toggle-status'), [
            'role' => 'waiter',
            'status' => 0
        ])->assertStatus(403);

        // Try to update user role
        $this->actingAs($this->waiterUser)->patchJson(route('admin.users.role', $this->waiterUser), [
            'role' => 'chef'
        ])->assertStatus(403);

        // Try to delete user
        $this->actingAs($this->waiterUser)->deleteJson(route('admin.users.delete', $this->waiterUser))->assertStatus(403);

        // Try to delete role
        $this->actingAs($this->waiterUser)->deleteJson(route('admin.roles.delete', 'waiter'))->assertStatus(403);
    }

    /**
     * Test role deletion rules and restrictions.
     */
    public function test_role_deletion_behavior(): void
    {
        // Create custom role and assign to user
        RolePermission::create(['role' => 'custom_cleaner', 'page' => 'can_insert', 'is_allowed' => true]);
        $cleanerUser = User::create([
            'name' => 'Cleaner John',
            'email' => 'cleaner@test.com',
            'password' => bcrypt('password'),
            'role' => 'custom_cleaner',
        ]);

        // Waiter cannot delete custom role
        $this->actingAs($this->waiterUser)
            ->deleteJson(route('admin.roles.delete', 'custom_cleaner'))
            ->assertStatus(403);

        // Admin can delete custom role
        $response = $this->actingAs($this->adminUser)
            ->deleteJson(route('admin.roles.delete', 'custom_cleaner'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => "Role 'Custom cleaner' deleted successfully."
            ]);

        // Database entries of permissions for that role must be gone
        $this->assertDatabaseMissing('role_permissions', [
            'role' => 'custom_cleaner',
        ]);

        // Users carrying that role must be reset to 'pending'
        $this->assertEquals('pending', $cleanerUser->fresh()->role);

        // Try to delete system role (admin) - should be blocked
        $response2 = $this->actingAs($this->adminUser)
            ->deleteJson(route('admin.roles.delete', 'admin'));

        $response2->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'System protected roles cannot be deleted.'
            ]);

        // Try to delete waiter role - should succeed
        $response3 = $this->actingAs($this->adminUser)
            ->deleteJson(route('admin.roles.delete', 'waiter'));
        $response3->assertStatus(200);
        $this->assertEquals('pending', $this->waiterUser->fresh()->role);
    }

    /**
     * Test adding a category.
     */
    public function test_admin_can_store_category(): void
    {
        $payload = [
            'name' => 'Soups',
        ];

        $response = $this->actingAs($this->adminUser)->postJson(route('admin.categories.store'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Category added successfully!'
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Soups',
        ]);
    }

    /**
     * Test deleting a category and nullifying its associated items.
     */
    public function test_admin_can_delete_category(): void
    {
        $category = \App\Models\Category::create(['name' => 'Dessert Corner']);
        
        $foodItem = FoodItem::create([
            'name' => 'Gulab Jamun Extreme',
            'price' => 50.00,
            'description' => 'Extreme sweets',
            'status' => 'available',
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($this->adminUser)->deleteJson(route('admin.categories.delete', $category));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Category deleted successfully!'
            ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);

        // Asserts that the food item's category_id was set to null
        $this->assertNull($foodItem->fresh()->category_id);
    }
}
