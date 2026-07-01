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
        $foodItems = FoodItem::where('status', 'available')->get();
        $tables = Table::orderBy('table_number')->get();
        return view('order', compact('foodItems', 'tables'));
    }

    /**
     * Store a newly created order.
     */
    public function store(StoreOrderRequest $request)
    {
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
                if ($table->status !== 'available') {
                    return response()->json([
                        'success' => false,
                        'message' => "Table {$table->table_number} is already occupied.",
                    ], 400);
                }
            }



            // Create the order with customer details
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'contact_number' => $request->contact_number,
                'total_amount' => 0.00,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'table_id' => $request->table_id,
                'special_instructions' => $request->special_instructions,
            ]);

            // Mark table occupied
            if ($table) {
                $table->update(['status' => 'occupied']);
            }

            $totalAmount = 0.00;

            // Iterate over items to save them and calculate total amount based on DB prices
            foreach ($request->items as $itemData) {
                $foodItem = FoodItem::findOrFail($itemData['food_item_id']);
                
                $itemPrice = $foodItem->price;
                $quantity = (int) $itemData['quantity'];
                
                // Add modifier price if selected
                $modifiers = $itemData['modifiers'] ?? '';
                if (str_contains(strtolower($modifiers), 'extra cheese')) {
                    $itemPrice += 30.00;
                }
                
                $itemTotal = $itemPrice * $quantity;
                $totalAmount += $itemTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'food_item_id' => $foodItem->id,
                    'quantity' => $quantity,
                    'price' => $itemPrice,
                    'modifiers' => $modifiers,
                ]);
            }



            // Update order total amount
            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

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
            ->where('payment_status', '!=', 'paid')
            ->latest()
            ->get();

        return response()->json($pendingOrders);
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

        DB::beginTransaction();

        try {
            $order->update([
                'status' => 'completed',
                'payment_status' => 'paid',
            ]);

            if ($order->table) {
                $order->table->update(['status' => 'available']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order #' . $order->id . ' completed and payment recorded successfully!',
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
            'status' => 'required|in:pending,preparing,ready,completed',
        ]);

        if ($request->status === 'completed' && $order->status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot complete an order that has not been accepted yet.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $updateData = ['status' => $request->status];

            $order->update($updateData);

            DB::commit();

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
}
