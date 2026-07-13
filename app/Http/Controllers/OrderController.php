<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\FoodItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display the order placement view.
     */
    public function index()
    {
        if (!\App\Models\Feature::isActive('waiter_terminal')) {
            abort(403, 'Access Denied: Waiter Terminal & Table Ordering is currently disabled.');
        }
        $foodItems = FoodItem::with('category')
            ->where('food_items.status', 'available')
            ->leftJoin('categories', 'food_items.category_id', '=', 'categories.id')
            ->orderByRaw('CASE WHEN categories.order_weight IS NULL THEN 9999 ELSE categories.order_weight END')
            ->orderBy('categories.name')
            ->orderBy('food_items.name')
            ->select('food_items.*')
            ->get();
        $tables = Table::orderBy('table_number')->get();
        $categories = \App\Models\Category::orderBy('order_weight')->orderBy('name')->get();
        return view('order', compact('foodItems', 'tables', 'categories'));
    }

    /**
     * Store a newly created order.
     */
    public function store(StoreOrderRequest $request)
    {
        if (!\App\Models\Feature::isActive('waiter_terminal')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied: Waiter Terminal & Table Ordering is currently disabled.'
            ], 403);
        }
        if (!Auth::user()->hasPermission('can_insert')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied: You do not have permission to place orders.'
            ], 403);
        }

        DB::beginTransaction();

        try {
            $table = null;
            if ($request->filled('table_id')) {
                $table = Table::findOrFail($request->table_id);
                if (!$request->filled('append_to_order_id') && $table->status !== 'available') {
                    return response()->json([
                        'success' => false,
                        'message' => "Table {$table->table_number} is already occupied.",
                    ], 400);
                }
            }

            if ($request->filled('append_to_order_id')) {
                $order = Order::findOrFail($request->append_to_order_id);
                if ($order->payment_status === 'paid') {
                    return response()->json([
                        'success' => false,
                        'message' => "Order #{$order->id} has already been paid/completed.",
                    ], 400);
                }

                // If order status was 'ready', set it back to 'preparing' to alert the kitchen
                if ($order->status === 'ready') {
                    $order->status = 'preparing';
                }

                // Append any new special instructions
                if ($request->filled('special_instructions')) {
                    $order->special_instructions = trim(($order->special_instructions ?? '') . "\n[Add-on]: " . $request->special_instructions);
                }
            } else {
                // Calculate daily sequence number
                $today = now()->toDateString();
                $maxDailyNo = Order::whereDate('created_at', $today)->max('daily_no');
                $dailyNo = ($maxDailyNo ?? 0) + 1;

                // Create the order with customer details
                $order = Order::create([
                    'customer_name' => $request->customer_name,
                    'contact_number' => $request->contact_number,
                    'total_amount' => 0.00,
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'table_id' => $request->table_id,
                    'special_instructions' => $request->special_instructions,
                    'user_id' => Auth::id(),
                    'daily_no' => $dailyNo,
                ]);

                // Mark table occupied
                if ($table) {
                    $table->update(['status' => 'occupied']);
                }
            }

            $totalAmount = $request->filled('append_to_order_id') ? (float) $order->total_amount : 0.00;

            // Batch load all food items in a single query to optimize database calls
            $foodItemIds = array_column($request->items, 'food_item_id');
            $foodItemsMap = FoodItem::whereIn('id', $foodItemIds)->get()->keyBy('id');

            // Iterate over items to calculate total amount and accumulate for bulk insert
            $orderItems = [];
            $now = now();
            
            foreach ($request->items as $itemData) {
                $foodItemId = $itemData['food_item_id'];
                $foodItem = $foodItemsMap->get($foodItemId);
                
                if (!$foodItem) {
                    throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Food item ID {$foodItemId} not found.");
                }
                
                $itemPrice = $foodItem->price;
                $quantity = (int) $itemData['quantity'];
                
                // Add modifier price if selected
                $modifiers = $itemData['modifiers'] ?? '';
                if (str_contains(strtolower($modifiers), 'extra cheese')) {
                    $itemPrice += 30.00;
                }
                
                $itemTotal = $itemPrice * $quantity;
                $totalAmount += $itemTotal;

                $orderItems[] = [
                    'order_id' => $order->id,
                    'food_item_id' => $foodItem->id,
                    'quantity' => $quantity,
                    'price' => $itemPrice,
                    'modifiers' => $modifiers,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Perform single bulk database insert
            if (!empty($orderItems)) {
                OrderItem::insert($orderItems);
            }

            // Calculate tax (5% GST)
            $taxAmount = round($totalAmount * 0.05, 2);

            // Update order total amount and save other dirty attributes (like status/instructions)
            $order->total_amount = $totalAmount;
            $order->tax_amount = $taxAmount;
            $order->save();

            DB::commit();

            // Broadcast the new order placement safely
            try {
                event(new \App\Events\OrderUpdated($order));
            } catch (\Exception $e) {
                Log::warning('Broadcast failed on order placement: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $order->id,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order placement failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while placing the order. Please try again.',
            ], 500);
        }
    }

    /**
     * Retrieve all pending orders.
     */
    public function getPendingOrders()
    {
        $pendingOrders = Order::with(['orderItems.foodItem', 'table'])
            ->whereDate('created_at', \Carbon\Carbon::today())
            ->where(function($query) {
                $query->where('payment_status', '!=', 'paid')
                      ->orWhere('status', 'completed');
            })
            ->latest()
            ->get();

        return response()->json($pendingOrders);
    }

    /**
     * Mark an order as delivered.
     */
    public function deliver(Order $order)
    {
        if (!Auth::user()->hasPermission('can_update')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied: You do not have permission to update orders.'
            ], 403);
        }

        if ($order->status !== 'ready') {
            return response()->json([
                'success' => false,
                'message' => 'Only orders that have been marked as ready by the kitchen can be delivered.'
            ], 400);
        }

        try {
            $order->update([
                'status' => 'delivered'
            ]);

            // Broadcast the order delivery status safely
            try {
                event(new \App\Events\OrderUpdated($order));
            } catch (\Exception $e) {
                Log::warning('Broadcast failed on order delivery: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Order #' . $order->id . ' marked as delivered successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to deliver order: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark order as delivered. Please try again.',
            ], 500);
        }
    }

    /**
     * Complete an order.
     */
    public function complete(Order $order)
    {
        if (!Auth::user()->hasPermission('can_update')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied: You do not have permission to update orders.'
            ], 403);
        }

        if ($order->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'This order has already been completed.'
            ], 400);
        }

        if ($order->status !== 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Only orders that have been marked as delivered can be completed.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $order->update([
                'status' => 'completed',
            ]);

            if ($order->table) {
                $order->table->update(['status' => 'available']);
            }

            DB::commit();

            // Broadcast the order completion status safely
            try {
                event(new \App\Events\OrderUpdated($order));
            } catch (\Exception $e) {
                Log::warning('Broadcast failed on order completion: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Order #' . $order->id . ' completed successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to complete order: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete order. Please try again.',
            ], 500);
        }
    }

    /**
     * Display the kitchen terminal board.
     */
    public function kitchen()
    {
        return view('kitchen');
    }

    /**
     * Update order status dynamically.
     */
    public function updateStatus(Request $request, Order $order)
    {
        if (!Auth::user()->hasPermission('can_update')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied: You do not have permission to update orders.'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,preparing,ready,delivered,completed',
        ]);

        if ($request->status === 'ready' && $order->status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot mark an order as ready that has not been accepted yet.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $updateData = ['status' => $request->status];

            $order->update($updateData);

            DB::commit();

            // Broadcast the status update to all connected terminals safely
            try {
                event(new \App\Events\OrderUpdated($order));
            } catch (\Exception $e) {
                Log::warning('Broadcast failed on order status update: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Order status updated to ' . ucfirst($request->status) . '!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status. Please try again.',
            ], 500);
        }
    }

    /**
     * Look up a customer name by contact number.
     */
    public function lookupCustomer(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric|digits:10',
        ]);

        $customer = \App\Models\Customer::where('phone_number', $request->phone)->first();

        if (!$customer) {
            // Fallback to orders table to import customer
            $lastOrder = Order::where('contact_number', $request->phone)
                ->whereNotNull('customer_name')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastOrder) {
                $customer = \App\Models\Customer::create([
                    'phone_number' => $request->phone,
                    'name' => $lastOrder->customer_name,
                    'total_spend' => 0,
                    'total_visits' => 0,
                ]);
            }
        }

        if ($customer) {
            return response()->json([
                'success' => true,
                'name' => $customer->name,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Customer not found',
        ]);
    }

    /**
     * Search customers by name or phone number for autocomplete.
     */
    public function searchCustomers(Request $request)
    {
        $query = $request->query('query');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $customers = \App\Models\Customer::where('name', 'like', "%{$query}%")
            ->orWhere('phone_number', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'phone_number']);

        return response()->json($customers);
    }

    /**
     * Get the active order for a given table.
     */
    public function getActiveOrderByTable(Table $table)
    {
        $activeOrder = Order::where('table_id', $table->id)
            ->where('payment_status', '!=', 'paid')
            ->first();

        if ($activeOrder) {
            return response()->json([
                'success' => true,
                'order' => $activeOrder,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No active order found for this table.',
        ]);
    }
}
