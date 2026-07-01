<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RolePermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that user registration defaults to pending role and redirects to home page.
     */
    public function test_user_registration_defaults_to_pending_and_redirects_to_home(): void
    {
        $payload = [
            'name' => 'John Newbie',
            'email' => 'john@newbie.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post(route('register'), $payload);

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'email' => 'john@newbie.com',
            'role' => 'pending',
        ]);

        $user = User::where('email', 'john@newbie.com')->first();
        $this->assertNotNull($user);

        // Verify logged in
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test login redirects all users to home.
     */
    public function test_login_redirects_to_home(): void
    {
        $user = User::create([
            'name' => 'Existing User',
            'email' => 'existing@user.com',
            'password' => bcrypt('password123'),
            'role' => 'waiter',
        ]);

        $response = $this->post('/login', [
            'email' => 'existing@user.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
    }

    /**
     * Test pending user is blocked from accessing restricted terminals.
     */
    public function test_pending_user_is_blocked_from_restricted_terminals(): void
    {
        $pendingUser = User::create([
            'name' => 'Pending Staff',
            'email' => 'pending@staff.com',
            'password' => bcrypt('password123'),
            'role' => 'pending',
        ]);

        // Seed some basic permissions
        RolePermission::create(['role' => 'waiter', 'page' => 'waiter_terminal', 'is_allowed' => true]);
        RolePermission::create(['role' => 'chef', 'page' => 'kitchen_terminal', 'is_allowed' => true]);

        // Act as pending user
        $this->actingAs($pendingUser);

        // 1. Blocked from Waiter Terminal
        $response1 = $this->get(route('orders.index'));
        $response1->assertStatus(403);

        // 2. Blocked from Kitchen Display System
        $response2 = $this->get(route('orders.kitchen'));
        $response2->assertStatus(403);

        // 3. Blocked from Admin Panel
        $response3 = $this->get(route('admin.index'));
        $response3->assertStatus(403);
    }

    /**
     * Test home page displays access pending notification for pending role.
     */
    public function test_home_page_displays_pending_message_for_pending_user(): void
    {
        $pendingUser = User::create([
            'name' => 'Pending Staff',
            'email' => 'pending@staff.com',
            'password' => bcrypt('password123'),
            'role' => 'pending',
        ]);

        $response = $this->actingAs($pendingUser)->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Access Pending Assignment');
        $response->assertSee('Your account registration is successful');
    }

    /**
     * Test home page redirects approved users to their assigned terminal.
     */
    public function test_home_page_redirects_approved_user_to_assigned_terminal(): void
    {
        // 1. Test Waiter Redirects
        $waiterUser = User::create([
            'name' => 'Approved Waiter',
            'email' => 'waiter@staff.com',
            'password' => bcrypt('password123'),
            'role' => 'waiter',
        ]);
        RolePermission::create(['role' => 'waiter', 'page' => 'waiter_terminal', 'is_allowed' => true]);
        
        $response1 = $this->actingAs($waiterUser)->get(route('home'));
        $response1->assertRedirect(route('orders.index'));

        // 2. Test Chef Redirects
        $chefUser = User::create([
            'name' => 'Approved Chef',
            'email' => 'chef@staff.com',
            'password' => bcrypt('password123'),
            'role' => 'chef',
        ]);
        RolePermission::create(['role' => 'chef', 'page' => 'kitchen_terminal', 'is_allowed' => true]);

        $response2 = $this->actingAs($chefUser)->get(route('home'));
        $response2->assertRedirect(route('orders.kitchen'));

        // 3. Test Admin Redirects
        $adminUser = User::create([
            'name' => 'Approved Admin',
            'email' => 'admin@staff.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $response3 = $this->actingAs($adminUser)->get(route('home'));
        $response3->assertRedirect(route('admin.index'));
    }

    /**
     * Test guest cannot update profile.
     */
    public function test_guest_cannot_update_profile(): void
    {
        $response = $this->postJson(route('profile.update'), [
            'email' => 'guest@change.com',
            'current_password' => 'password123'
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test auth user cannot update profile with invalid current password.
     */
    public function test_auth_user_cannot_update_profile_with_invalid_current_password(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@user.com',
            'password' => bcrypt('password123'),
            'role' => 'waiter'
        ]);

        $response = $this->actingAs($user)->postJson(route('profile.update'), [
            'email' => 'updated@user.com',
            'current_password' => 'wrongpassword'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['current_password']);
    }

    /**
     * Test auth user can update profile email and password.
     */
    public function test_auth_user_can_update_profile_email_and_password(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@user.com',
            'password' => bcrypt('password123'),
            'role' => 'waiter'
        ]);

        $response = $this->actingAs($user)->postJson(route('profile.update'), [
            'email' => 'updated@user.com',
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Profile updated successfully!'
        ]);

        $user->refresh();
        $this->assertEquals('updated@user.com', $user->email);
        $this->assertTrue(\Hash::check('newpassword123', $user->password));
    }

    /**
     * Test auth user can access profile page.
     */
    public function test_auth_user_can_access_profile_page(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@user.com',
            'password' => bcrypt('password123'),
            'role' => 'waiter'
        ]);

        $response = $this->actingAs($user)->get(route('profile.show'));
        $response->assertStatus(200);
        $response->assertSee('Profile Information');
        $response->assertSee('Update Password');
    }

    /**
     * Test auth user can update profile info (name and email).
     */
    public function test_auth_user_can_update_profile_info(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@user.com',
            'password' => bcrypt('password123'),
            'role' => 'waiter'
        ]);

        $response = $this->actingAs($user)->postJson(route('profile.info.update'), [
            'name' => 'Brand New Name',
            'email' => 'newbrand@user.com'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Profile information updated successfully!'
        ]);

        $user->refresh();
        $this->assertEquals('Brand New Name', $user->name);
        $this->assertEquals('newbrand@user.com', $user->email);
    }

    /**
     * Test auth user can update profile password.
     */
    public function test_auth_user_can_update_profile_password(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@user.com',
            'password' => bcrypt('password123'),
            'role' => 'waiter'
        ]);

        $response = $this->actingAs($user)->postJson(route('profile.password.update'), [
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Password updated successfully!'
        ]);

        $user->refresh();
        $this->assertTrue(\Hash::check('newpassword123', $user->password));
    }
}
