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

    protected User $adminUser;
    protected User $waiterUser;

    protected function setUp(): void
    {
        parent::setUp();

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
    public function test_admin_completing_order_updates_payment_status_to_paid(): void
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
        $this->assertEquals('paid', $order->fresh()->payment_status);
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

        $response = $this->actingAs($this->adminUser)->postJson(route('admin.permissions.update'), $payload);

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
        $response = $this->actingAs($this->adminUser)->patchJson(route('admin.users.role', $this->waiterUser), [
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
     * Test deleting user.
     */
    public function test_admin_can_delete_user(): void
    {
        $response = $this->actingAs($this->adminUser)->deleteJson(route('admin.users.delete', $this->waiterUser));

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
        $response = $this->actingAs($this->adminUser)->deleteJson(route('admin.users.delete', $this->adminUser));

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Action denied: You cannot delete your own account.'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->adminUser->id,
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

        // Order 3: Today
        $order3 = new Order([
            'customer_name' => 'Charlie Brown',
            'contact_number' => '3333333333',
            'total_amount' => 750.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
        $order3->created_at = '2026-06-30 12:00:00';
        $order3->save();

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

        // 3. Test Filter Type: all
        $response = $this->actingAs($this->adminUser)
            ->get(
                route('admin.index', ['filter_type' => 'all']),
                ['X-Requested-With' => 'XMLHttpRequest']
            );

        $response->assertStatus(200);
        $response->assertSee('Alice Doe');
        $response->assertSee('Bob Smith');
        $response->assertSee('Charlie Brown');
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
}
