<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RolePermission;
use App\Models\FoodItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $waiterUser;
    protected User $chefUser;
    protected \App\Models\Table $table;

    protected function setUp(): void
    {
        parent::setUp();

        $this->table = \App\Models\Table::create([
            'table_number' => 'T1',
            'capacity' => 4,
            'status' => 'available',
        ]);

        // Create user with waiter role
        $this->waiterUser = User::create([
            'name' => 'Test Waiter',
            'email' => 'waiter@test.com',
            'password' => bcrypt('password'),
            'role' => 'waiter',
        ]);

        // Create user with chef role
        $this->chefUser = User::create([
            'name' => 'Test Chef',
            'email' => 'chef@test.com',
            'password' => bcrypt('password'),
            'role' => 'chef',
        ]);

        // Seed Role Permissions for testing
        RolePermission::create(['role' => 'waiter', 'page' => 'waiter_terminal', 'is_allowed' => true]);
        RolePermission::create(['role' => 'waiter', 'page' => 'kitchen_terminal', 'is_allowed' => false]);
        RolePermission::create(['role' => 'waiter', 'page' => 'can_insert', 'is_allowed' => true]);
        RolePermission::create(['role' => 'waiter', 'page' => 'can_update', 'is_allowed' => true]);

        RolePermission::create(['role' => 'chef', 'page' => 'waiter_terminal', 'is_allowed' => false]);
        RolePermission::create(['role' => 'chef', 'page' => 'kitchen_terminal', 'is_allowed' => true]);
        RolePermission::create(['role' => 'chef', 'page' => 'can_update', 'is_allowed' => true]);
    }

    /**
     * Test that guest is redirected to login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('orders.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that waiter cannot access kitchen.
     */
    public function test_waiter_cannot_access_kitchen_terminal(): void
    {
        $response = $this->actingAs($this->waiterUser)->get(route('orders.kitchen'));
        $response->assertStatus(403);
    }

    /**
     * Test that chef cannot access waiter terminal.
     */
    public function test_chef_cannot_access_waiter_terminal(): void
    {
        $response = $this->actingAs($this->chefUser)->get(route('orders.index'));
        $response->assertStatus(403);
    }

    /**
     * Test that the order placement page loads successfully with necessary data for waiter.
     */
    public function test_order_placement_page_loads_successfully(): void
    {
        FoodItem::create(['name' => 'Margherita Pizza', 'price' => 12.99, 'status' => 'available']);

        $response = $this->actingAs($this->waiterUser)->get(route('orders.index'));

        $response->assertStatus(200);
        $response->assertViewHas('foodItems');
    }

    /**
     * Test placing an order successfully updates order_items tables, 
     * calculates the total price from database prices, and records customer details.
     */
    public function test_can_place_order_successfully(): void
    {
        $item1 = FoodItem::create(['name' => 'Truffle Burger', 'price' => 15.50, 'status' => 'available']);
        $item2 = FoodItem::create(['name' => 'Mango Smoothie', 'price' => 5.25, 'status' => 'available']);

        $payload = [
            'customer_name' => 'Alice Smith',
            'contact_number' => '1555019900',
            'table_id' => $this->table->id,
            'items' => [
                ['food_item_id' => $item1->id, 'quantity' => 2],
                ['food_item_id' => $item2->id, 'quantity' => 1]
            ]
        ];

        $response = $this->actingAs($this->waiterUser)->postJson(route('orders.store'), $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Order placed successfully!'
            ]);

        // Assert database contains the correct total: (15.50 * 2) + (5.25 * 1) = 36.25
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Alice Smith',
            'contact_number' => '1555019900',
            'table_id' => $this->table->id,
            'total_amount' => 36.25,
            'status' => 'pending',
            'payment_status' => 'unpaid'
        ]);

        $order = Order::first();

        // Verify order items saved
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'food_item_id' => $item1->id,
            'quantity' => 2,
            'price' => 15.50
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'food_item_id' => $item2->id,
            'quantity' => 1,
            'price' => 5.25
        ]);
    }

    /**
     * Test placing an order successfully without table_id.
     */
    public function test_can_place_order_without_table_id(): void
    {
        $item1 = FoodItem::create(['name' => 'Truffle Burger', 'price' => 15.50, 'status' => 'available']);

        $payload = [
            'customer_name' => 'Alice Smith',
            'contact_number' => '1555019900',
            'table_id' => null,
            'items' => [
                ['food_item_id' => $item1->id, 'quantity' => 2]
            ]
        ];

        $response = $this->actingAs($this->waiterUser)->postJson(route('orders.store'), $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Order placed successfully!'
            ]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Alice Smith',
            'contact_number' => '1555019900',
            'table_id' => null,
            'total_amount' => 31.00,
            'status' => 'pending',
            'payment_status' => 'unpaid'
        ]);
    }

    /**
     * Test order validation fails if fields are missing or invalid.
     */
    public function test_order_placement_fails_on_validation_errors(): void
    {
        $response = $this->actingAs($this->waiterUser)->postJson(route('orders.store'), [
            'customer_name' => '',
            'contact_number' => '',
            'items' => []
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customer_name', 'contact_number', 'items']);
    }

    /**
     * Test retrieving pending orders list.
     */
    public function test_can_retrieve_pending_orders(): void
    {
        $item = FoodItem::create(['name' => 'Caesar Salad', 'price' => 9.99, 'status' => 'available']);
        
        $order = Order::create([
            'customer_name' => 'Bob Jones',
            'contact_number' => '555-1234',
            'total_amount' => 9.99,
            'status' => 'pending',
            'payment_status' => 'unpaid'
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'food_item_id' => $item->id,
            'quantity' => 1,
            'price' => 9.99
        ]);

        $response = $this->actingAs($this->waiterUser)->getJson(route('orders.pending'));

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'id' => $order->id,
                'customer_name' => 'Bob Jones',
                'contact_number' => '555-1234',
                'total_amount' => 9.99,
                'status' => 'pending',
            ]);
    }

    /**
     * Test pending orders endpoint returns unpaid active orders, today's completed/paid orders, and excludes older paid completed orders.
     */
    public function test_pending_orders_endpoint_returns_completed_but_unpaid_orders_and_excludes_paid_orders(): void
    {
        $item = FoodItem::create(['name' => 'Burger', 'price' => 5.00, 'status' => 'available']);

        // 1. Create a completed but unpaid order
        $order1 = Order::create([
            'customer_name' => 'Unpaid Completed Customer',
            'contact_number' => '555-0001',
            'total_amount' => 5.00,
            'status' => 'completed',
            'payment_status' => 'unpaid'
        ]);
        OrderItem::create(['order_id' => $order1->id, 'food_item_id' => $item->id, 'quantity' => 1, 'price' => 5.00]);

        // 2. Create today's completed and paid order
        $order2 = Order::create([
            'customer_name' => 'Today Paid Completed Customer',
            'contact_number' => '555-0002',
            'total_amount' => 5.00,
            'status' => 'completed',
            'payment_status' => 'paid',
            'created_at' => now(),
        ]);
        OrderItem::create(['order_id' => $order2->id, 'food_item_id' => $item->id, 'quantity' => 1, 'price' => 5.00]);

        // 3. Create yesterday's completed and paid order
        $order3 = Order::create([
            'customer_name' => 'Yesterday Paid Completed Customer',
            'contact_number' => '555-0003',
            'total_amount' => 5.00,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);
        $order3->created_at = now()->subDay();
        $order3->save();
        OrderItem::create(['order_id' => $order3->id, 'food_item_id' => $item->id, 'quantity' => 1, 'price' => 5.00]);

        $response = $this->actingAs($this->waiterUser)->getJson(route('orders.pending'));

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'id' => $order1->id,
                'customer_name' => 'Unpaid Completed Customer',
            ])
            ->assertJsonFragment([
                'id' => $order2->id,
                'customer_name' => 'Today Paid Completed Customer',
            ])
            ->assertJsonMissing([
                'id' => $order3->id,
                'customer_name' => 'Yesterday Paid Completed Customer',
            ]);
    }

    /**
     * Test the pending orders ?mine=1 filter returns only orders placed by the current user.
     */
    public function test_pending_orders_mine_filter_returns_only_own_orders(): void
    {
        $item = FoodItem::create(['name' => 'Test Item', 'price' => 10.00, 'status' => 'available']);

        // Order placed by waiterUser
        $myOrder = Order::create([
            'customer_name' => 'My Customer',
            'contact_number' => '1111111111',
            'total_amount' => 10.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'user_id' => $this->waiterUser->id,
        ]);

        // Order placed by a different user (no user_id = system or another waiter)
        $otherOrder = Order::create([
            'customer_name' => 'Other Customer',
            'contact_number' => '2222222222',
            'total_amount' => 20.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'user_id' => null,
        ]);

        // Without mine filter - should return both
        $responseAll = $this->actingAs($this->waiterUser)->getJson(route('orders.pending'));
        $responseAll->assertStatus(200)->assertJsonCount(2);

        // With mine=1 - should return only mine
        $responseMine = $this->actingAs($this->waiterUser)->getJson(route('orders.pending', ['mine' => 1]));
        $responseMine->assertStatus(200)->assertJsonCount(1);
        $responseMine->assertJsonFragment(['id' => $myOrder->id]);
        $responseMine->assertJsonMissing(['id' => $otherOrder->id]);
    }

    /**
     * Test marking an order as delivered.
     */
    public function test_can_deliver_order(): void
    {
        $order = Order::create([
            'customer_name' => 'Charlie Brown',
            'contact_number' => '555-9876',
            'total_amount' => 15.00,
            'status' => 'ready',
            'payment_status' => 'unpaid',
            'table_id' => $this->table->id
        ]);

        $response = $this->actingAs($this->waiterUser)->postJson(route('orders.deliver', $order));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Order #' . $order->id . ' marked as delivered successfully!'
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'delivered'
        ]);
    }

    /**
     * Test completing an order successfully marks the order as completed/paid.
     */
    public function test_can_complete_order(): void
    {
        $order = Order::create([
            'customer_name' => 'Charlie Brown',
            'contact_number' => '555-9876',
            'total_amount' => 15.00,
            'status' => 'delivered',
            'payment_status' => 'unpaid',
            'table_id' => $this->table->id
        ]);

        // Mark table occupied first to test release
        $this->table->update(['status' => 'occupied']);

        $response = $this->actingAs($this->waiterUser)->postJson(route('orders.complete', $order));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        // Assert order status is completed, but payment status is still unpaid
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
            'payment_status' => 'unpaid'
        ]);

        // Assert table is released
        $this->freshTableStatus = $this->table->fresh()->status;
        $this->assertEquals('available', $this->freshTableStatus);
    }

    /**
     * Test completing an order that is not delivered fails.
     */
    public function test_cannot_complete_non_delivered_order(): void
    {
        $order = Order::create([
            'customer_name' => 'Charlie Brown',
            'contact_number' => '555-9876',
            'total_amount' => 15.00,
            'status' => 'ready',
            'payment_status' => 'unpaid'
        ]);

        $response = $this->actingAs($this->waiterUser)->postJson(route('orders.complete', $order));

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Only orders that have been marked as delivered can be completed.'
            ]);
    }

    /**
     * Test that an order cannot be completed twice.
     */
    public function test_cannot_complete_order_twice(): void
    {
        $order = Order::create([
            'customer_name' => 'Charlie Brown',
            'contact_number' => '555-9876',
            'total_amount' => 15.00,
            'status' => 'completed',
            'payment_status' => 'unpaid'
        ]);

        $response = $this->actingAs($this->waiterUser)->postJson(route('orders.complete', $order));

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'This order has already been completed.'
            ]);
    }

    /**
     * Test that the kitchen display terminal page loads successfully.
     */
    public function test_kitchen_page_loads_successfully(): void
    {
        $response = $this->actingAs($this->chefUser)->get(route('orders.kitchen'));

        $response->assertStatus(200);
    }

    /**
     * Test that the kitchen can update order status successfully.
     */
    public function test_can_update_order_status(): void
    {
        $order = Order::create([
            'customer_name' => 'John Doe',
            'contact_number' => '555-4321',
            'total_amount' => 10.00,
            'status' => 'pending',
            'payment_status' => 'unpaid'
        ]);

        // Transition from pending to preparing
        $response = $this->actingAs($this->chefUser)->postJson(route('orders.status', $order), ['status' => 'preparing']);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Order status updated to Preparing!'
            ]);

        $this->assertEquals('preparing', $order->fresh()->status);

        // Transition from preparing to ready
        $response2 = $this->actingAs($this->chefUser)->postJson(route('orders.status', $order), ['status' => 'ready']);

        $response2->assertStatus(200);
        $this->assertEquals('ready', $order->fresh()->status);
    }

    /**
     * Test that the kitchen cannot mark an order as ready that is pending/not accepted.
     */
    public function test_cannot_mark_pending_order_ready_without_accepting(): void
    {
        $order = Order::create([
            'customer_name' => 'Jane Smith',
            'contact_number' => '555-9999',
            'total_amount' => 12.00,
            'status' => 'pending',
            'payment_status' => 'unpaid'
        ]);

        $response = $this->actingAs($this->chefUser)->postJson(route('orders.status', $order), ['status' => 'ready']);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Cannot mark an order as ready that has not been accepted yet.'
            ]);

        $this->assertEquals('pending', $order->fresh()->status);
    }

    /**
     * Test that kitchen marking an order as ready updates status to ready but does not mark payment as paid.
     */
    public function test_kitchen_marking_ready_does_not_mark_payment_as_paid(): void
    {
        $order = Order::create([
            'customer_name' => 'Jane Smith',
            'contact_number' => '555-9999',
            'total_amount' => 12.00,
            'status' => 'preparing',
            'payment_status' => 'unpaid'
        ]);

        $response = $this->actingAs($this->chefUser)->postJson(route('orders.status', $order), ['status' => 'ready']);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $order = $order->fresh();
        $this->assertEquals('ready', $order->status);
        $this->assertEquals('unpaid', $order->payment_status);
    }

    /**
     * Test lookup customer by phone.
     */
    public function test_can_lookup_returning_customer_by_phone(): void
    {
        Order::create([
            'customer_name' => 'Alice Doe',
            'contact_number' => '1234567890',
            'total_amount' => 15.00,
            'status' => 'completed',
            'payment_status' => 'paid'
        ]);

        $response = $this->actingAs($this->waiterUser)->getJson(route('customers.lookup', ['phone' => '1234567890']));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'name' => 'Alice Doe'
            ]);
    }

    /**
     * Test retrieve active order by table.
     */
    public function test_can_get_active_order_by_table(): void
    {
        $activeOrder = Order::create([
            'customer_name' => 'Bob Smith',
            'contact_number' => '0987654321',
            'total_amount' => 20.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'table_id' => $this->table->id
        ]);

        $response = $this->actingAs($this->waiterUser)->getJson(route('orders.active-by-table', $this->table));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'order' => [
                    'id' => $activeOrder->id,
                    'customer_name' => 'Bob Smith'
                ]
            ]);
    }

    /**
     * Test appending items to an active order.
     */
    public function test_can_append_items_to_active_order(): void
    {
        $food1 = FoodItem::create(['name' => 'Item 1', 'price' => 10.00, 'status' => 'available']);
        $food2 = FoodItem::create(['name' => 'Item 2', 'price' => 5.00, 'status' => 'available']);

        $order = Order::create([
            'customer_name' => 'Charlie Brown',
            'contact_number' => '1122334455',
            'total_amount' => 10.00,
            'status' => 'ready',
            'payment_status' => 'unpaid',
            'table_id' => $this->table->id
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'food_item_id' => $food1->id,
            'quantity' => 1,
            'price' => 10.00
        ]);

        $response = $this->actingAs($this->waiterUser)->postJson(route('orders.store'), [
            'customer_name' => 'Charlie Brown',
            'contact_number' => '1122334455',
            'table_id' => $this->table->id,
            'append_to_order_id' => $order->id,
            'items' => [
                [
                    'food_item_id' => $food2->id,
                    'quantity' => 2,
                    'modifiers' => ''
                ]
            ]
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true
            ]);

        $order = $order->fresh();
        $this->assertEquals(20.00, (float) $order->total_amount); // 10.00 + (2 * 5.00) = 20.00
        $this->assertEquals('preparing', $order->status); // Reset from ready to preparing
        $this->assertCount(2, $order->orderItems);
    }

    /**
     * Test kitchen pending API filters out ready items of an active order.
     */
    public function test_kitchen_pending_api_filters_out_ready_items(): void
    {
        $food1 = FoodItem::create(['name' => 'Item 1', 'price' => 10.00, 'status' => 'available']);
        $food2 = FoodItem::create(['name' => 'Item 2', 'price' => 5.00, 'status' => 'available']);

        $order = Order::create([
            'customer_name' => 'Charlie Brown',
            'contact_number' => '1122334455',
            'total_amount' => 10.00,
            'status' => 'preparing',
            'payment_status' => 'unpaid',
            'table_id' => $this->table->id
        ]);

        // Create first item as ready
        $item1 = OrderItem::create([
            'order_id' => $order->id,
            'food_item_id' => $food1->id,
            'quantity' => 1,
            'price' => 10.00,
            'status' => 'ready'
        ]);

        // Create second item as pending
        $item2 = OrderItem::create([
            'order_id' => $order->id,
            'food_item_id' => $food2->id,
            'quantity' => 2,
            'price' => 5.00,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->waiterUser)->getJson(route('orders.pending'));

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertCount(1, $data);
        
        // The loaded orderItems relation should only contain the pending item, not the ready one
        $returnedOrder = $data[0];
        $this->assertCount(1, $returnedOrder['order_items']);
        $this->assertEquals($item2->id, $returnedOrder['order_items'][0]['id']);
    }
}
