<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Display the Admin Dashboard index with metrics and logs.
     */
    public function index(Request $request)
    {
        // Calculate Metrics
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
        $activeOrdersCount = Order::where('status', '!=', 'completed')->count();
        $totalFoodItemsCount = FoodItem::count();

        // Fetch logs
        $foodItems = FoodItem::latest()->get();

        $ordersQuery = Order::with(['orderItems.foodItem', 'table']);

        if ($request->filled('filter_type')) {
            $filterType = $request->input('filter_type');
            if ($filterType === 'single' && $request->filled('filter_date')) {
                $ordersQuery->whereDate('created_at', $request->input('filter_date'));
            } elseif ($filterType === 'range') {
                if ($request->filled('from_date')) {
                    $ordersQuery->whereDate('created_at', '>=', $request->input('from_date'));
                }
                if ($request->filled('to_date')) {
                    $ordersQuery->whereDate('created_at', '<=', $request->input('to_date'));
                }
            }
        }

        $orders = $ordersQuery->latest()->get();

        if ($request->ajax()) {
            return view('admin.orders_rows', compact('orders'))->render();
        }

        $users = \App\Models\User::orderBy('name')->get();
        $permissions = \App\Models\RolePermission::all();

        // Query Chart.js Analytics Data
        $dailySales = Order::where('status', 'completed')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(7)
            ->get();

        $topSelling = DB::table('order_items')
            ->join('food_items', 'order_items.food_item_id', '=', 'food_items.id')
            ->select('food_items.name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('food_items.name')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        // Fetch distinct available roles dynamically
        $availableRoles = \App\Models\RolePermission::select('role')->distinct()->pluck('role');

        return view('admin', compact(
            'foodItems', 
            'orders', 
            'users',
            'permissions',
            'totalRevenue', 
            'activeOrdersCount', 
            'totalFoodItemsCount',
            'dailySales',
            'topSelling',
            'availableRoles'
        ));
    }

    /* =========================================================================
     * Food Item CRUD
     * ========================================================================= */

    public function storeFoodItem(Request $request)
    {
        if (!Auth::user()->hasPermission('can_insert')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to insert items.'], 403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:available,unavailable',
        ]);

        try {
            FoodItem::create($request->all());
            return response()->json(['success' => true, 'message' => 'Food item added successfully!']);
        } catch (\Exception $e) {
            Log::error('Admin failed to store food item: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save food item.'], 500);
        }
    }

    public function updateFoodItem(Request $request, FoodItem $foodItem)
    {
        if (!Auth::user()->hasPermission('can_update')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to update items.'], 403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:available,unavailable',
        ]);

        try {
            $foodItem->update($request->all());
            return response()->json(['success' => true, 'message' => 'Food item updated successfully!']);
        } catch (\Exception $e) {
            Log::error('Admin failed to update food item: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update food item.'], 500);
        }
    }

    public function deleteFoodItem(FoodItem $foodItem)
    {
        if (!Auth::user()->hasPermission('can_delete')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to delete items.'], 403);
        }
        try {
            $foodItem->delete();
            return response()->json(['success' => true, 'message' => 'Food item deleted successfully!']);
        } catch (\Exception $e) {
            Log::error('Admin failed to delete food item: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete food item.'], 500);
        }
    }

    /* =========================================================================
     * Order Control
     * ========================================================================= */

    public function updateOrderStatus(Request $request, Order $order)
    {
        if (!Auth::user()->hasPermission('can_update')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to update orders.'], 403);
        }
        $request->validate(['status' => 'required|in:pending,preparing,ready,completed']);

        try {
            $updateData = ['status' => $request->status];
            if ($request->status === 'completed') {
                $updateData['payment_status'] = 'paid';
            }
            $order->update($updateData);
            return response()->json(['success' => true, 'message' => 'Order status updated successfully!']);
        } catch (\Exception $e) {
            Log::error('Admin failed to update order status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update order status.'], 500);
        }
    }

    public function updateOrderPayment(Request $request, Order $order)
    {
        if (!Auth::user()->hasPermission('can_update')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to update payments.'], 403);
        }
        $request->validate([
            'payment_status' => 'required|in:paid,unpaid',
            'payment_method' => 'nullable|string|in:cash,card,upi',
        ]);

        try {
            $updateData = ['payment_status' => $request->payment_status];
            if ($request->filled('payment_method')) {
                $updateData['payment_method'] = $request->payment_method;
            }
            $order->update($updateData);

            // Releasing the associated table once payment is completed
            if ($request->payment_status === 'paid' && $order->table) {
                $order->table->update(['status' => 'available']);
            }

            return response()->json(['success' => true, 'message' => 'Order payment status updated successfully!']);
        } catch (\Exception $e) {
            Log::error('Admin failed to update order payment status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update payment status.'], 500);
        }
    }

    public function deleteOrder(Order $order)
    {
        if (!Auth::user()->hasPermission('can_delete')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to delete orders.'], 403);
        }
        try {
            $order->delete();
            return response()->json(['success' => true, 'message' => 'Order deleted successfully!']);
        } catch (\Exception $e) {
            Log::error('Admin failed to delete order: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete order.'], 500);
        }
    }

    /**
     * Display a printable thermal receipt/invoice for an order.
     */
    public function invoice(Order $order)
    {
        $order->load(['orderItems.foodItem']);
        return view('invoice', compact('order'));
    }

    /* =========================================================================
     * Food Item Extra Actions
     * ========================================================================= */

    public function toggleFoodItemStatus(FoodItem $foodItem)
    {
        if (!Auth::user()->hasPermission('can_update')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to update status.'], 403);
        }
        try {
            $newStatus = $foodItem->status === 'available' ? 'unavailable' : 'available';
            $foodItem->update(['status' => $newStatus]);
            return response()->json([
                'success' => true,
                'message' => 'Food item status changed to ' . ucfirst($newStatus) . ' successfully!',
                'status' => $newStatus
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to toggle food item status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to toggle status.'], 500);
        }
    }

    /* =========================================================================
     * Access Control & User Role Management
     * ========================================================================= */

    /**
     * Update a role permission value dynamically.
     */
    public function updatePermissions(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
            'page' => 'required|string|in:waiter_terminal,kitchen_terminal,admin_panel,can_insert,can_update,can_delete',
            'is_allowed' => 'required|boolean',
        ]);

        try {
            RolePermission::updateOrCreate(
                ['role' => $request->role, 'page' => $request->page],
                ['is_allowed' => $request->is_allowed]
            );

            return response()->json([
                'success' => true,
                'message' => 'Permission configuration updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to update permissions: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update permission.'], 500);
        }
    }

    /**
     * Update a user's role.
     */
    public function updateUserRole(Request $request, User $user)
    {
        if (!Auth::user()->hasPermission('can_update')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to update roles.'], 403);
        }

        $request->validate([
            'role' => 'required|string',
        ]);

        try {
            $user->update(['role' => $request->role]);

            return response()->json([
                'success' => true,
                'message' => "User's role updated to " . ucfirst($request->role) . ' successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to update user role: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update user role.'], 500);
        }
    }

    /**
     * Delete an employee user record.
     */
    public function deleteUser(User $user)
    {
        if (!Auth::user()->hasPermission('can_delete')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to delete users.'], 403);
        }

        // Don't let users delete themselves
        if (Auth::id() === $user->id) {
            return response()->json(['success' => false, 'message' => 'Action denied: You cannot delete your own account.'], 400);
        }

        try {
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'User account deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to delete user: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete user account.'], 500);
        }
    }

    /**
     * Create a new role dynamically.
     */
    public function createRole(Request $request)
    {
        if (!Auth::user()->hasPermission('can_insert')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to create roles.'], 403);
        }

        $request->validate([
            'role_name' => 'required|string|max:50',
        ]);

        try {
            $roleKey = strtolower(str_replace(' ', '_', $request->role_name));

            // Verify if role already exists in database
            $exists = RolePermission::where('role', $roleKey)->exists();
            if ($exists) {
                return response()->json(['success' => false, 'message' => 'This role already exists.'], 400);
            }

            // Create default page and action permissions for new role
            $permissionsList = ['waiter_terminal', 'kitchen_terminal', 'admin_panel', 'can_insert', 'can_update', 'can_delete'];
            foreach ($permissionsList as $page) {
                RolePermission::create([
                    'role' => $roleKey,
                    'page' => $page,
                    'is_allowed' => false
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "New role '" . ucfirst($request->role_name) . "' created successfully!",
                'role_key' => $roleKey,
                'role_label' => ucfirst($request->role_name)
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to create role: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create role.'], 500);
        }
    }
}
