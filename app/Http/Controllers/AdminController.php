<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use App\Models\RolePermission;
use App\Models\Feature;
use App\Models\Icon;
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
        $foodItems = FoodItem::with('category')->latest()->get();
        $categories = \App\Models\Category::orderBy('order_weight')->orderBy('name')->get();

        $ordersQuery = Order::with(['orderItems.foodItem', 'table']);
        $filterType = $request->input('filter_type', 'all');

        if ($filterType === 'today') {
            $ordersQuery->whereDate('created_at', today());
        } elseif ($filterType === 'single') {
            if ($request->filled('filter_date')) {
                $ordersQuery->whereDate('created_at', $request->input('filter_date'));
            }
        } elseif ($filterType === 'range') {
            if ($request->filled('from_date')) {
                $ordersQuery->whereDate('created_at', '>=', $request->input('from_date'));
            }
            if ($request->filled('to_date')) {
                $ordersQuery->whereDate('created_at', '<=', $request->input('to_date'));
            }
        }

        $orders = $ordersQuery->latest()->get();

        if ($request->ajax()) {
            return view('admin.orders_rows', compact('orders'))->render();
        }

        $users = \App\Models\User::where('role', '!=', 'pending')->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")->orderBy('name')->get();
        $pendingUsers = \App\Models\User::where('role', 'pending')->orderBy('created_at', 'desc')->get();
        $permissions = \App\Models\RolePermission::all();
        $customers = \App\Models\Customer::orderBy('total_spend', 'desc')->get();

        // Query Chart.js Analytics Data for the current week (7 days)
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $driver = DB::connection()->getDriverName();
        if ($driver === 'pgsql') {
            $currentWeekSales = Order::where('status', 'completed')
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->select(DB::raw('CAST(created_at AS DATE) as date'), DB::raw('SUM(total_amount) as total'))
                ->groupBy(DB::raw('CAST(created_at AS DATE)'))
                ->get()
                ->pluck('total', 'date')
                ->toArray();
        } else {
            $currentWeekSales = Order::where('status', 'completed')
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
                ->groupBy('date')
                ->get()
                ->pluck('total', 'date')
                ->toArray();
        }

        $dailySales = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = now()->startOfWeek()->addDays($i);
            $dateStr = $date->format('Y-m-d');
            $dayName = $date->format('D'); // Mon, Tue, etc.
            $total = isset($currentWeekSales[$dateStr]) ? (float) $currentWeekSales[$dateStr] : 0.0;
            
            $dailySales->push((object)[
                'date' => $dayName . ' (' . $date->format('d M') . ')',
                'total' => $total
            ]);
        }

        $categorySales = DB::table('order_items')
            ->join('food_items', 'order_items.food_item_id', '=', 'food_items.id')
            ->leftJoin('categories', 'food_items.category_id', '=', 'categories.id')
            ->selectRaw("COALESCE(categories.name, 'Uncategorized') as category_name")
            ->selectRaw('SUM(order_items.quantity * order_items.price) as total_sales')
            ->groupBy(DB::raw("COALESCE(categories.name, 'Uncategorized')"))
            ->orderBy('total_sales', 'desc')
            ->get();

        $totalCategorySales = $categorySales->sum('total_sales');
        $categorySalesData = collect();
        foreach ($categorySales as $cat) {
            $percentage = $totalCategorySales > 0 ? ($cat->total_sales / $totalCategorySales) * 100 : 0;
            $categorySalesData->push((object)[
                'name' => $cat->category_name,
                'percentage' => round($percentage, 1),
                'total' => (float) $cat->total_sales
            ]);
        }

        // Fetch distinct available roles dynamically
        $availableRoles = \App\Models\RolePermission::select('role')->distinct()->pluck('role');

        $tables = Table::orderBy('table_number')->get();

        // Query Hourly sales distribution (busy hours) - database driver agnostic
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $hourlySales = Order::where('status', 'completed')
                ->select(DB::raw('CAST(strftime("%H", created_at) AS INTEGER) as hour'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
                ->groupBy('hour')
                ->orderBy('hour', 'asc')
                ->get();
        } elseif ($driver === 'pgsql') {
            $hourlySales = Order::where('status', 'completed')
                ->select(DB::raw('CAST(EXTRACT(HOUR FROM created_at) AS INTEGER) as hour'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
                ->groupBy(DB::raw('CAST(EXTRACT(HOUR FROM created_at) AS INTEGER)'))
                ->orderBy('hour', 'asc')
                ->get();
        } else {
            $hourlySales = Order::where('status', 'completed')
                ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
                ->groupBy('hour')
                ->orderBy('hour', 'asc')
                ->get();
        }

        // Query Waiter performance ranking
        $waiterPerformance = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(orders.total_amount) as total_revenue'))
            ->groupBy('users.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Calculate dynamic dashboard stats
        $thisWeekRevenue = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->startOfWeek())
            ->sum('total_amount');
        $lastWeekRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->sum('total_amount');
        if ($lastWeekRevenue > 0) {
            $revenueChange = (($thisWeekRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100;
        } else {
            $revenueChange = $thisWeekRevenue > 0 ? 100 : 0;
        }
        $revenueTrendText = ($revenueChange >= 0 ? '↑ ' : '↓ ') . number_format(abs($revenueChange), 1) . '% this week';

        $totalOrdersFilled = Order::where('status', 'completed')->count();
        $currentlyPreparing = Order::where('status', 'preparing')->count();

        $topSellingItem = DB::table('order_items')
            ->join('food_items', 'order_items.food_item_id', '=', 'food_items.id')
            ->select('food_items.name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('food_items.name')
            ->orderBy('total_qty', 'desc')
            ->first();
        $topSellingName = $topSellingItem ? $topSellingItem->name : 'N/A';
        $topSellingQty = $topSellingItem ? (int) $topSellingItem->total_qty : 0;

        $lowestSellingQuery = DB::table('food_items')
            ->leftJoin('order_items', 'food_items.id', '=', 'order_items.food_item_id')
            ->select('food_items.name', DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_qty'))
            ->groupBy('food_items.id', 'food_items.name')
            ->orderBy('total_qty', 'asc')
            ->first();
        $lowestSellingName = $lowestSellingQuery ? $lowestSellingQuery->name : 'N/A';
        $lowestSellingQty = $lowestSellingQuery ? (int) $lowestSellingQuery->total_qty : 0;

        // Daily Report Stats
        $dailyDateLabel = now()->format('d M Y');
        $dailySalesAmount = Order::where('status', 'completed')->whereDate('created_at', today())->sum('total_amount');
        $dailyOrdersCount = Order::where('status', 'completed')->whereDate('created_at', today())->count();

        // Weekly Report Stats
        $weeklyDateLabel = now()->startOfWeek()->format('d M') . ' - ' . now()->endOfWeek()->format('d M Y');
        $weeklySalesAmount = Order::where('status', 'completed')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount');
        $weeklyOrdersCount = Order::where('status', 'completed')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        // Monthly Report Stats
        $monthlyDateLabel = now()->format('F Y');
        $monthlySalesAmount = Order::where('status', 'completed')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_amount');
        $monthlyOrdersCount = Order::where('status', 'completed')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        // Yearly Report Stats
        $yearlyDateLabel = now()->format('Y');
        $yearlySalesAmount = Order::where('status', 'completed')->whereYear('created_at', now()->year)->sum('total_amount');
        $yearlyOrdersCount = Order::where('status', 'completed')->whereYear('created_at', now()->year)->count();

        // --- AI PREDICTIVE INSIGHTS CALCULATIONS ---

        // 1. Market Basket Analysis (Product Pairings)
        $orderItemsByOrder = DB::table('order_items')
            ->join('food_items', 'order_items.food_item_id', '=', 'food_items.id')
            ->select('order_id', 'food_items.name')
            ->get()
            ->groupBy('order_id');

        $pairCounts = [];
        $itemCounts = [];
        foreach ($orderItemsByOrder as $orderId => $items) {
            $uniqueNames = $items->pluck('name')->unique()->values()->toArray();
            foreach ($uniqueNames as $name) {
                $itemCounts[$name] = ($itemCounts[$name] ?? 0) + 1;
            }
            $count = count($uniqueNames);
            for ($i = 0; $i < $count; $i++) {
                for ($j = $i + 1; $j < $count; $j++) {
                    $itemA = $uniqueNames[$i];
                    $itemB = $uniqueNames[$j];
                    
                    $key1 = "{$itemA} & {$itemB}";
                    $key2 = "{$itemB} & {$itemA}";
                    $pairCounts[$key1] = ($pairCounts[$key1] ?? 0) + 1;
                    $pairCounts[$key2] = ($pairCounts[$key2] ?? 0) + 1;
                }
            }
        }

        $recommendations = [];
        foreach ($pairCounts as $pair => $count) {
            [$itemA, $itemB] = explode(' & ', $pair);
            $confidence = $itemCounts[$itemA] > 0 ? ($count / $itemCounts[$itemA]) * 100 : 0;
            if ($confidence >= 25 && $itemCounts[$itemA] >= 2) {
                $recommendations[] = (object)[
                    'item_a' => $itemA,
                    'item_b' => $itemB,
                    'confidence' => round($confidence, 1),
                    'count' => $count
                ];
            }
        }
        usort($recommendations, fn($a, $b) => $b->confidence <=> $a->confidence);
        $topPairings = array_slice($recommendations, 0, 5);

        // 2. Sales & Revenue Trend Forecasting (Demand Prediction)
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $dailyRevenueQuery = Order::where('status', 'completed')
                ->where('created_at', '>=', now()->subDays(14))
                ->select(DB::raw('date(created_at) as date'), DB::raw('SUM(total_amount) as total'))
                ->groupBy('date')
                ->get()
                ->pluck('total', 'date')
                ->toArray();
        } elseif ($driver === 'pgsql') {
            $dailyRevenueQuery = Order::where('status', 'completed')
                ->where('created_at', '>=', now()->subDays(14))
                ->select(DB::raw('CAST(created_at AS DATE) as date'), DB::raw('SUM(total_amount) as total'))
                ->groupBy(DB::raw('CAST(created_at AS DATE)'))
                ->get()
                ->pluck('total', 'date')
                ->toArray();
        } else {
            $dailyRevenueQuery = Order::where('status', 'completed')
                ->where('created_at', '>=', now()->subDays(14))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
                ->groupBy('date')
                ->get()
                ->pluck('total', 'date')
                ->toArray();
        }

        $n = 14;
        $x = [];
        $y = [];
        for ($i = 0; $i < $n; $i++) {
            $dateStr = now()->subDays($n - 1 - $i)->format('Y-m-d');
            $x[] = $i + 1;
            $y[] = isset($dailyRevenueQuery[$dateStr]) ? (float)$dailyRevenueQuery[$dateStr] : 0.0;
        }

        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXX = 0;
        $sumXY = 0;
        for ($i = 0; $i < $n; $i++) {
            $sumXX += $x[$i] * $x[$i];
            $sumXY += $x[$i] * $y[$i];
        }

        $denominator = ($n * $sumXX) - ($sumX * $sumX);
        if ($denominator != 0) {
            $m = (($n * $sumXY) - ($sumX * $sumY)) / $denominator;
            $c = ($sumY - ($m * $sumX)) / $n;
        } else {
            $m = 0;
            $c = $sumY > 0 ? $sumY / $n : 0;
        }

        // Get start of current week (Monday) and start of previous week (Monday)
        $startOfThisWeek = now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $startOfLastWeek = $startOfThisWeek->copy()->subWeek();

        // 1. Generate actual sales for the current week (Monday to Sunday)
        $thisWeekOrders = Order::where('status', 'completed')
            ->where('created_at', '>=', $startOfThisWeek->copy()->startOfDay())
            ->where('created_at', '<=', $startOfThisWeek->copy()->addDays(6)->endOfDay())
            ->get();

        $thisWeekSalesByDay = array_fill(0, 7, 0.0);
        foreach ($thisWeekOrders as $order) {
            $dayOfWeek = (int)$order->created_at->format('N'); // 1 (Mon) to 7 (Sun)
            $thisWeekSalesByDay[$dayOfWeek - 1] += (float)$order->total_amount;
        }

        $actualSales = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfThisWeek->copy()->addDays($i);
            $isFuture = $date->isFuture() && !$date->isToday();
            $actualSales[] = (object)[
                'date' => $date->format('D (d M)'), // e.g. Mon (13 Jul)
                'amount' => $isFuture ? null : round($thisWeekSalesByDay[$i], 2)
            ];
        }

        // 2. Generate sales forecast for current week (Monday to Sunday)
        $historyStart = now()->subDays(14)->startOfDay();
        $salesForecast = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfThisWeek->copy()->addDays($i);
            $diffDays = $historyStart->diffInDays($date) + 1;
            
            $forecastVal = ($m * $diffDays) + $c;
            if ($forecastVal < 0) $forecastVal = $n > 0 ? max(0, $sumY / $n) : 0;
            
            $salesForecast[] = (object)[
                'date' => $date->format('D (d M)'), // e.g. Mon (13 Jul)
                'amount' => round($forecastVal, 2)
            ];
        }

        // 16-day completed orders count query (database-agnostic)
        if ($driver === 'sqlite') {
            $dailyOrdersQuery = Order::where('status', 'completed')
                ->where('created_at', '>=', now()->subDays(16)->startOfDay())
                ->select(DB::raw('date(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->groupBy('date')
                ->get()
                ->pluck('count', 'date')
                ->toArray();
        } elseif ($driver === 'pgsql') {
            $dailyOrdersQuery = Order::where('status', 'completed')
                ->where('created_at', '>=', now()->subDays(16)->startOfDay())
                ->select(DB::raw('CAST(created_at AS DATE) as date'), DB::raw('COUNT(*) as count'))
                ->groupBy(DB::raw('CAST(created_at AS DATE)'))
                ->get()
                ->pluck('count', 'date')
                ->toArray();
        } else {
            $dailyOrdersQuery = Order::where('status', 'completed')
                ->where('created_at', '>=', now()->subDays(16)->startOfDay())
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->groupBy('date')
                ->get()
                ->pluck('count', 'date')
                ->toArray();
        }

        $completedOrdersCountHistory = [];
        for ($i = 0; $i < 16; $i++) {
            $date = now()->subDays(15 - $i);
            $dateStr = $date->format('Y-m-d');
            $completedOrdersCountHistory[] = (object)[
                'date' => $date->format('d M'), // e.g. 14 Jul
                'count' => isset($dailyOrdersQuery[$dateStr]) ? (int)$dailyOrdersQuery[$dateStr] : 0
            ];
        }

        // Top 5 selling items query
        $top5Selling = DB::table('order_items')
            ->join('food_items', 'order_items.food_item_id', '=', 'food_items.id')
            ->select('food_items.name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('food_items.id', 'food_items.name')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get();

        // Bottom 5 selling items query
        $bottom5Selling = DB::table('food_items')
            ->leftJoin('order_items', 'food_items.id', '=', 'order_items.food_item_id')
            ->select('food_items.name', DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_qty'))
            ->groupBy('food_items.id', 'food_items.name')
            ->orderBy('total_qty', 'asc')
            ->limit(5)
            ->get();

        // 3. Busy Hour / Kitchen Prep Delays
        $completedOrders = Order::where('status', 'completed')
            ->whereNotNull('created_at')
            ->whereNotNull('updated_at')
            ->get();

        // --- Live KPI Performance calculations ---
        $totalCompletedOrders = count($completedOrders);
        $averageOrderValue = 0.0;
        $averagePrepTime = 0;
        $dineInPercentage = 0;
        $takeawayPercentage = 0;
        $busiestHour = "N/A";

        if ($totalCompletedOrders > 0) {
            $totalSales = 0.0;
            $dineInCount = 0;
            $totalPrepMins = 0;
            $validPrepCount = 0;
            $hourlyCountsGroup = [];

            foreach ($completedOrders as $order) {
                $totalSales += (float)$order->total_amount;
                
                if ($order->table_id !== null) {
                    $dineInCount++;
                }

                $prepTimeMins = $order->created_at->diffInMinutes($order->updated_at);
                if ($prepTimeMins > 0 && $prepTimeMins < 120) {
                    $totalPrepMins += $prepTimeMins;
                    $validPrepCount++;
                }

                $hourStr = $order->created_at->format('H');
                $hourlyCountsGroup[$hourStr] = ($hourlyCountsGroup[$hourStr] ?? 0) + 1;
            }

            $averageOrderValue = $totalSales / $totalCompletedOrders;
            $dineInPercentage = round(($dineInCount / $totalCompletedOrders) * 100);
            $takeawayPercentage = 100 - $dineInPercentage;
            $averagePrepTime = $validPrepCount > 0 ? round($totalPrepMins / $validPrepCount) : 0;

            if (count($hourlyCountsGroup) > 0) {
                arsort($hourlyCountsGroup);
                $peakHour = (int)key($hourlyCountsGroup);
                $busiestHour = sprintf('%02d:00 - %02d:00', $peakHour, ($peakHour + 1) % 24);
            }
        }

        $hourlyPrepTimes = [];
        $hourlyCounts = [];
        foreach ($completedOrders as $order) {
            $hour = (int)$order->created_at->format('H');
            $prepTimeMinutes = $order->created_at->diffInMinutes($order->updated_at);
            if ($prepTimeMinutes > 0 && $prepTimeMinutes < 120) {
                $hourlyPrepTimes[$hour] = ($hourlyPrepTimes[$hour] ?? 0) + $prepTimeMinutes;
                $hourlyCounts[$hour] = ($hourlyCounts[$hour] ?? 0) + 1;
            }
        }

        $busyHourStaffing = [];
        foreach ($hourlyPrepTimes as $hour => $totalMinutes) {
            $count = $hourlyCounts[$hour];
            $avg = $count > 0 ? $totalMinutes / $count : 0;
            
            $prepTimeLabel = round($avg) . " mins";
            $recommendation = "Normal Speed";
            $statusColor = "badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill";
            if ($avg > 15 && $count >= 2) {
                $recommendation = "Critical Delay - Add Staff";
                $statusColor = "badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill fw-bold";
            } elseif ($avg > 10 && $count >= 2) {
                $recommendation = "Slow - Monitor Kitchen";
                $statusColor = "badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-pill";
            }
            
            $busyHourStaffing[] = (object)[
                'hour' => sprintf('%02d:00 - %02d:00', $hour, ($hour + 1) % 24),
                'avg_prep_time' => $prepTimeLabel,
                'orders_count' => $count,
                'recommendation' => $recommendation,
                'status_color' => $statusColor
            ];
        }
        usort($busyHourStaffing, fn($a, $b) => $b->orders_count <=> $a->orders_count);
        $busyHourStaffing = array_slice($busyHourStaffing, 0, 5);

        // 4. Customer Retention Segmentation
        $customersData = DB::table('orders')
            ->whereNotNull('contact_number')
            ->where('contact_number', '!=', '')
            ->select('customer_name', 'contact_number', DB::raw('MAX(created_at) as last_visit'), DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(total_amount) as total_spend'))
            ->groupBy('customer_name', 'contact_number')
            ->get();

        $customerSegments = [
            'champions' => [],
            'attention' => [],
            'at_risk' => []
        ];

        foreach ($customersData as $c) {
            $lastVisitDate = \Carbon\Carbon::parse($c->last_visit);
            $daysSinceLastVisit = $lastVisitDate->diffInDays(now());
            
            if ($daysSinceLastVisit <= 7 && $c->total_orders >= 3) {
                $customerSegments['champions'][] = $c;
            } elseif ($daysSinceLastVisit > 7 && $daysSinceLastVisit <= 30 && $c->total_orders >= 1) {
                $customerSegments['attention'][] = $c;
            } elseif ($daysSinceLastVisit > 30) {
                $customerSegments['at_risk'][] = $c;
            }
        }

        $modules = \App\Models\Module::visible()->whereNull('parent_id')->orderBy('order_weight')->get();
        $allSettledOrders = Order::where('payment_status', 'paid')->with(['orderItems.foodItem', 'table'])->latest()->get();

        return view('admin', compact(
            'foodItems', 
            'categories',
            'orders', 
            'users',
            'permissions',
            'totalRevenue', 
            'activeOrdersCount', 
            'totalFoodItemsCount',
            'dailySales',
            'categorySalesData',
            'availableRoles',
            'tables',
            'customers',
            'hourlySales',
            'waiterPerformance',
            'revenueTrendText',
            'revenueChange',
            'totalOrdersFilled',
            'currentlyPreparing',
            'topSellingName',
            'topSellingQty',
            'lowestSellingName',
            'lowestSellingQty',
            'dailyDateLabel',
            'dailySalesAmount',
            'dailyOrdersCount',
            'weeklyDateLabel',
            'weeklySalesAmount',
            'weeklyOrdersCount',
            'monthlyDateLabel',
            'monthlySalesAmount',
            'monthlyOrdersCount',
            'yearlyDateLabel',
            'yearlySalesAmount',
            'yearlyOrdersCount',
            'pendingUsers',
            'modules',
            'allSettledOrders',
            'topPairings',
            'salesForecast',
            'busyHourStaffing',
            'customerSegments',
            'actualSales',
            'top5Selling',
            'bottom5Selling',
            'completedOrdersCountHistory',
            'averageOrderValue',
            'averagePrepTime',
            'dineInPercentage',
            'takeawayPercentage',
            'busiestHour'
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
            'description' => 'required|string',
            'status' => 'required|in:available,unavailable',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        try {
            $data = $request->only(['name', 'price', 'description', 'status', 'category_id']);
            if (!isset($data['category_id']) || is_null($data['category_id'])) {
                $uncat = \App\Models\Category::firstOrCreate(['name' => 'Uncategorized']);
                $data['category_id'] = $uncat->id;
            }

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
                
                $destinationPath = public_path('images/food');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                $file->move($destinationPath, $filename);
                $data['image_path'] = '/images/food/' . $filename;
            }

            FoodItem::create($data);
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
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        try {
            $data = $request->only(['name', 'price', 'description', 'status', 'category_id']);
            if (!isset($data['category_id']) || is_null($data['category_id'])) {
                $uncat = \App\Models\Category::firstOrCreate(['name' => 'Uncategorized']);
                $data['category_id'] = $uncat->id;
            }

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
                
                $destinationPath = public_path('images/food');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                $file->move($destinationPath, $filename);
                
                // Delete old image if it exists and is not the default
                if ($foodItem->image_path && file_exists(public_path($foodItem->image_path))) {
                    @unlink(public_path($foodItem->image_path));
                }
                
                $data['image_path'] = '/images/food/' . $filename;
            }

            $foodItem->update($data);
            // Load category relation for returning the updated item
            $foodItem->load('category');
            return response()->json([
                'success' => true, 
                'message' => 'Food item updated successfully!',
                'food_item' => $foodItem
            ]);
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

    public function storeCategory(Request $request)
    {
        if (!Auth::user()->hasPermission('can_insert')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to insert items.'], 403);
        }
        $request->validate([
            'name' => 'required|string|unique:categories,name|max:100',
        ]);

        try {
            $category = \App\Models\Category::create(['name' => trim($request->name)]);
            return response()->json([
                'success' => true, 
                'message' => 'Category added successfully!',
                'category' => $category
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to store category: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save category.'], 500);
        }
    }

    public function sortCategories(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|integer|exists:categories,id',
            'order.*.order_weight' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->order as $orderData) {
                \App\Models\Category::where('id', $orderData['id'])->update([
                    'order_weight' => $orderData['order_weight']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Categories sorted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to sort categories: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update sorting order.'], 500);
        }
    }

    public function updateCategory(Request $request, \App\Models\Category $category)
    {
        if (!Auth::user()->hasPermission('can_update')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to update items.'], 403);
        }
        if (strtolower($category->name) === 'uncategorized') {
            return response()->json(['success' => false, 'message' => 'The Uncategorized category cannot be renamed.'], 400);
        }

        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
        ]);

        try {
            $category->update([
                'name' => trim($request->name)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category renamed successfully!',
                'category' => $category
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to rename category: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to rename category.'], 500);
        }
    }

    public function deleteCategory(\App\Models\Category $category)
    {
        if (!Auth::user()->hasPermission('can_delete')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to delete items.'], 403);
        }
        
        if (strtolower($category->name) === 'uncategorized') {
            return response()->json(['success' => false, 'message' => 'The Uncategorized category cannot be deleted.'], 400);
        }

        try {
            // Nullify associated food items
            \App\Models\FoodItem::where('category_id', $category->id)->update(['category_id' => null]);
            
            $category->delete();
            return response()->json(['success' => true, 'message' => 'Category deleted successfully!']);
        } catch (\Exception $e) {
            Log::error('Admin failed to delete category: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete category.'], 500);
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
        $request->validate(['status' => 'required|in:pending,preparing,ready,delivered,completed']);

        try {
            $order->update(['status' => $request->status]);

            // Broadcast the order status update safely
            try {
                event(new \App\Events\OrderUpdated($order));
            } catch (\Exception $e) {
                Log::warning('Broadcast failed on status update: ' . $e->getMessage());
            }

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
            $wasPaid = $order->payment_status === 'paid';
            $order->update($updateData);

            if ($request->payment_status === 'paid' && !$wasPaid) {
                \App\Models\Customer::recordOrderPayment($order);
            }

            // Releasing the associated table once payment is completed
            if ($request->payment_status === 'paid' && $order->table) {
                $order->table->update(['status' => 'available']);
            }

            // Broadcast the order update safely
            try {
                event(new \App\Events\OrderUpdated($order));
            } catch (\Exception $e) {
                Log::warning('Broadcast failed on payment update: ' . $e->getMessage());
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

    public function storeTable(Request $request)
    {
        if (!Auth::user()->hasPermission('can_insert')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to insert items.'], 403);
        }

        $request->validate([
            'table_number' => 'required|string|max:50|unique:tables,table_number',
            'capacity' => 'required|integer|min:1',
        ]);

        try {
            Table::create([
                'table_number' => $request->table_number,
                'capacity' => $request->capacity,
                'status' => 'available',
            ]);

            return response()->json(['success' => true, 'message' => 'Table added successfully!']);
        } catch (\Exception $e) {
            Log::error('Admin failed to store table: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to add table.'], 500);
        }
    }

    public function updateTable(Request $request, Table $table)
    {
        if (!Auth::user()->hasPermission('can_update')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to update items.'], 403);
        }

        $request->validate([
            'table_number' => 'required|string|max:50|unique:tables,table_number,' . $table->id,
            'capacity' => 'required|integer|min:1',
        ]);

        try {
            $table->update([
                'table_number' => $request->table_number,
                'capacity' => $request->capacity,
            ]);

            return response()->json(['success' => true, 'message' => 'Table updated successfully!']);
        } catch (\Exception $e) {
            Log::error('Admin failed to update table: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update table.'], 500);
        }
    }

    public function deleteTable(Table $table)
    {
        if (!Auth::user()->hasPermission('can_delete')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to delete items.'], 403);
        }

        if ($table->status === 'occupied') {
            return response()->json(['success' => false, 'message' => 'Cannot delete table while it is occupied.'], 400);
        }

        try {
            $table->delete();
            return response()->json(['success' => true, 'message' => 'Table deleted successfully!']);
        } catch (\Exception $e) {
            Log::error('Admin failed to delete table: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete table.'], 500);
        }
    }

    public function toggleTableStatus(Table $table)
    {
        if (!Auth::user()->hasPermission('can_update')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to update items.'], 403);
        }

        if ($table->status === 'occupied') {
            return response()->json(['success' => false, 'message' => 'Cannot deactivate table while it is occupied.'], 400);
        }

        try {
            $newStatus = $table->status === 'available' ? 'unavailable' : 'available';
            $table->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Table status changed to ' . ucfirst($newStatus) . ' successfully!',
                'status' => $newStatus
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to toggle table status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to toggle table status.'], 500);
        }
    }

    /**
     * Display a printable thermal receipt/invoice for an order.
     */
    public function invoice(Order $order)
    {
        if ($order->status !== 'completed') {
            abort(403, 'Invoices can only be printed or downloaded for completed orders.');
        }
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
     * Create a new employee user account.
     */
    public function storeUser(Request $request)
    {
        if (!Auth::user()->hasPermission('can_insert')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to create user accounts.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'salary' => 'nullable|numeric|min:0',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'role' => $request->role,
                'salary' => $request->salary ?: 0.00,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee account created successfully!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'salary' => number_format($user->salary, 2, '.', ''),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to create employee account: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create employee account.'], 500);
        }
    }

    /**
     * Toggle an employee user's active status.
     */
    public function toggleUserStatus(Request $request, User $user)
    {
        if (!Auth::user()->hasPermission('can_update')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to update account statuses.'], 403);
        }

        // Don't let users deactivate themselves
        if (Auth::id() === $user->id) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You cannot deactivate your own account.'], 400);
        }

        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        try {
            $user->update(['is_active' => $request->is_active]);
            $statusStr = $user->is_active ? 'activated' : 'deactivated';
            return response()->json([
                'success' => true,
                'message' => "Employee account has been {$statusStr} successfully!"
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to toggle user active status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update account status.'], 500);
        }
    }

    /**
     * Edit/update employee details.
     */
    public function updateUser(Request $request, User $user)
    {
        if (!Auth::user()->hasPermission('can_update')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to edit employee details.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string',
            'salary' => 'nullable|numeric|min:0',
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'salary' => $request->salary ?: 0.00,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee account updated successfully!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'salary' => number_format($user->salary, 2, '.', ''),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to update employee account details: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update employee details.'], 500);
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
     * Update an employee user's salary.
     */
    public function updateUserSalary(Request $request, User $user)
    {
        if (!Auth::user()->hasPermission('can_update')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to update salaries.'], 403);
        }

        $request->validate([
            'salary' => 'required|numeric|min:0',
        ]);

        try {
            $user->update(['salary' => $request->salary]);
            return response()->json(['success' => true, 'message' => 'Salary updated successfully!']);
        } catch (\Exception $e) {
            Log::error('Admin failed to update user salary: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update salary.'], 500);
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

            // Create default page and action permissions for new role (bulk insert)
            $permissionsList = ['waiter_terminal', 'kitchen_terminal', 'admin_panel', 'can_insert', 'can_update', 'can_delete', 'role_active'];
            $newPermissions = [];
            $now = now();
            foreach ($permissionsList as $page) {
                $newPermissions[] = [
                    'role' => $roleKey,
                    'page' => $page,
                    'is_allowed' => ($page === 'role_active') ? true : false,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
            RolePermission::insert($newPermissions);

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

    /**
     * Toggle active/inactive status of a custom or waiter/chef role.
     */
    public function toggleRoleStatus(Request $request)
    {
        if (!Auth::user()->hasPermission('can_update')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to update roles.'], 403);
        }

        $request->validate([
            'role' => 'required|string',
            'status' => 'required|integer|in:0,1'
        ]);

        $role = $request->input('role');
        $status = (int)$request->input('status');

        // Prevent deactivating admin role
        if ($role === 'admin') {
            return response()->json(['success' => false, 'message' => 'The Admin role cannot be deactivated.'], 400);
        }

        try {
            // Update or create the role_active permission record
            RolePermission::updateOrCreate(
                ['role' => $role, 'page' => 'role_active'],
                ['is_allowed' => $status === 1]
            );

            $statusText = $status === 1 ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Role '" . ucfirst(str_replace('_', ' ', $role)) . "' " . $statusText . " successfully.",
                'is_active' => $status === 1
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to toggle role status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to change role status.'], 500);
        }
    }


    public function crmIndex(Request $request)
    {
        if (!Auth::user()->hasPermission('admin_panel')) {
            return response()->json(['success' => false, 'message' => 'Access Denied.'], 403);
        }

        $customers = \App\Models\Customer::orderBy('total_spend', 'desc')->get();
        return response()->json($customers);
    }

    public function crmStore(Request $request)
    {
        if (!Auth::user()->hasPermission('can_insert')) {
            return response()->json(['success' => false, 'message' => 'Access Denied: You do not have permission to insert records.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:customers,phone_number|max:15',
        ]);

        try {
            $customer = \App\Models\Customer::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'total_spend' => 0.00,
                'total_visits' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Customer profile added successfully!',
                'customer' => $customer
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to create customer profile: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create customer profile.'], 500);
        }
    }

    /**
     * Display the dedicated Role Management Panel for Super Admin.
     */
    public function adminRole(Request $request)
    {
        $users = \App\Models\User::orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")->orderBy('name')->get();
        $permissions = \App\Models\RolePermission::all();
        $availableRoles = \App\Models\RolePermission::select('role')->distinct()->pluck('role');
        $features = Feature::all();

        // Get modules ordered hierarchically
        $roots = \App\Models\Module::whereNull('parent_id')->orderBy('order_weight')->with('children')->get();
        $flatModules = collect();
        foreach ($roots as $root) {
            $flatModules->push($root);
            foreach ($root->children as $child) {
                $flatModules->push($child);
            }
        }

        // Available parents (only root-level items can be parents to keep it 2 levels deep)
        $parentModules = \App\Models\Module::whereNull('parent_id')->orderBy('order_weight')->get();
        $icons = Icon::orderBy('name', 'asc')->get();

        return view('admin_role', compact('users', 'permissions', 'availableRoles', 'features', 'roots', 'flatModules', 'parentModules', 'icons'));
    }

    /**
     * Toggle status of a dynamic feature flag.
     */
    public function toggleFeature(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Access Denied: You must be an administrator.'], 403);
        }

        $request->validate([
            'key' => 'required|string|exists:features,key',
            'is_enabled' => 'required|boolean',
        ]);

        try {
            $feature = Feature::where('key', $request->key)->firstOrFail();
            $feature->update(['is_enabled' => $request->is_enabled]);

            return response()->json([
                'success' => true,
                'message' => "Feature '" . $feature->display_name . "' has been " . ($feature->is_enabled ? 'enabled' : 'disabled') . "."
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to toggle feature: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update feature toggle status.'], 500);
        }
    }

    /**
     * Delete a custom role dynamically.
     */
    public function deleteRole(string $role)
    {
        // Block deletion of system roles
        $systemRoles = ['admin'];
        if (in_array(strtolower($role), $systemRoles)) {
            return response()->json(['success' => false, 'message' => 'System protected roles cannot be deleted.'], 400);
        }

        try {
            // Delete permission records for this role
            RolePermission::where('role', $role)->delete();

            // Update any user with this role to 'pending'
            \App\Models\User::where('role', $role)->update(['role' => 'pending']);

            return response()->json([
                'success' => true,
                'message' => "Role '" . ucfirst(str_replace('_', ' ', $role)) . "' deleted successfully."
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to delete role: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete role.'], 500);
        }
    }

}
