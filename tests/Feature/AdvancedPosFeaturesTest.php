<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RolePermission;
use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderSplit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvancedPosFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

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

        // Permissions
        RolePermission::create(['role' => 'admin', 'page' => 'admin_panel', 'is_allowed' => true]);
        RolePermission::create(['role' => 'admin', 'page' => 'can_update', 'is_allowed' => true]);
    }

    public function test_customer_lookup_profile(): void
    {
        // 1. Initially query lookup -> should return 404/false since no customer is registered
        $response = $this->actingAs($this->adminUser)
            ->getJson(route('customers.lookup', ['phone' => '1234567890']));
        
        $response->assertStatus(200);
        $response->assertJson(['success' => false]);

        // 2. Create an order and mark as paid
        $order = Order::create([
            'customer_name' => 'John Doe',
            'contact_number' => '1234567890',
            'total_amount' => 500.00,
            'status' => 'completed',
            'payment_status' => 'unpaid',
            'user_id' => $this->adminUser->id,
        ]);

        // Mark paid to trigger profile calculation
        $response = $this->actingAs($this->adminUser)
            ->patchJson(route('admin.orders.payment', $order->id), [
                'payment_status' => 'paid',
                'payment_method' => 'cash',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // john should have a customer record
        $customer = Customer::where('phone_number', '1234567890')->first();
        $this->assertNotNull($customer);
        $this->assertEquals(500.00, $customer->total_spend);
        $this->assertEquals(1, $customer->total_visits);

        // 3. Query lookup again -> should return name
        $response = $this->actingAs($this->adminUser)
            ->getJson(route('customers.lookup', ['phone' => '1234567890']));
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'name' => 'John Doe',
        ]);
    }

}
