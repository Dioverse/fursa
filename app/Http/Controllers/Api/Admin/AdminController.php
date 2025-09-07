<?php

namespace App\Http\Controllers\Api\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = User::where('role', 'admin')->get();
        return response()->json([
            'message' => 'List of admin users retrieved successfully.',
            'data' => $admins,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Validate input (only allow 'admin' as role)
        $request->validate([
            'first_name'    => ['sometimes', 'string', 'max:255'],
            'last_name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'phone'    => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        // Force role to 'admin' (no role input allowed)
        $user = User::create([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'password'          => Hash::make($request->password),
            'role'              => 'admin',
            'status'            => 'approved',
            'email_verified_at' => now()
        ]);

        return response()->json([
            'message' => 'Admin user created successfully',
            'user'    => $user,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json([
                'message' => 'Admin user not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'Admin user retrieved successfully.',
            'user'    => $admin,
        ]);
    }

    /**
     * Update the specified admin user in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json([
                'message' => 'Admin user not found.',
            ], 404);
        }

        $request->validate([
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name'  => ['sometimes', 'string', 'max:255'],
            'email'      => ['sometimes', 'email', "unique:users,email,{$admin->id}"],
            'phone'      => ['sometimes', 'string', 'max:20'],
            'password'   => ['sometimes', 'string', 'min:6'],
        ]);

        $admin->update([
            'first_name' => $request->first_name ?? $admin->first_name,
            'last_name'  => $request->last_name ?? $admin->last_name,
            'email'      => $request->email ?? $admin->email,
            'phone'      => $request->phone ?? $admin->phone,
            'password'   => $request->filled('password') ? Hash::make($request->password) : $admin->password,
        ]);

        return response()->json([
            'message' => 'Admin user updated successfully.',
            'user'    => $admin,
        ]);
    }

    /**
     * Remove the specified admin user from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json([
                'message' => 'Admin user not found.',
            ], 404);
        }

        $admin->delete();

        return response()->json([
            'message' => 'Admin user deleted successfully.',
        ]);
    }

    public function dashboard(): JsonResponse
    {
        $today = Carbon::today();
        $lastMonth = Carbon::now()->subMonth();

        // One-time total counts
        $productsCount = Product::count();
        $totalOrders = Order::count();
        $activeUsers = User::where('ban', false)->count();
        $distributorsCount = Distributor::count();

        // Optimized metrics for today and last month
        $dailyMetrics = Payment::where('status', 'successful')
            ->whereDate('paid_at', $today)
            ->selectRaw('count(distinct order_id) as orders_today, sum(amount) as revenue_today')
            ->first();

        $monthlyMetrics = Payment::where('status', 'successful')
            ->selectRaw('sum(amount) as total_revenue')
            ->selectRaw('sum(case when paid_at >= ? and paid_at < ? then amount else 0 end) as revenue_last_month', [
                $lastMonth->startOfMonth(),
                $lastMonth->endOfMonth()->addDay()
            ])->first();

        $usersMetrics = User::selectRaw('count(id) as total_users')
            ->selectRaw('count(case when created_at >= ? then id else null end) as new_users_today', [$today])
            ->selectRaw('count(case when ban = false and updated_at >= ? and updated_at < ? then id else null end) as active_users_last_month', [
                $lastMonth->startOfMonth(),
                $lastMonth->endOfMonth()->addDay()
            ])
            ->first();

        // Efficiently fetch recent orders with relationships
        $recentOrders = Order::with('user:id,first_name,last_name')->withCount('orderItem')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Optimized order status counts
        $orderStatuses = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $revenueOverview = Payment::select(
            DB::raw('sum(amount) as total_revenue'),
            DB::raw('MONTH(paid_at) as month'),
            DB::raw('YEAR(paid_at) as year')
        )
            ->where('status', 'successful')
            ->where('paid_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Prepare monthly revenue data for chart
        $revenueData = array_fill(0, 12, 0);
        $months = [];
        $currentMonth = Carbon::now()->subMonths(11)->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $months[] = $currentMonth->format('F');
            $revenueData[$i] = $revenueOverview->where('year', $currentMonth->year)->where('month', $currentMonth->month)->first()->total_revenue ?? 0;
            $currentMonth->addMonth();
        }

        // Calculate changes, with robust checks for zero division
        $revenueLastMonth = $monthlyMetrics->revenue_last_month;
        $totalRevenueChange = (isset($revenueLastMonth) && $revenueLastMonth > 0) ? (($monthlyMetrics->total_revenue - $revenueLastMonth) / $revenueLastMonth) * 100 : 0;

        $ordersLastMonth = Order::whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()->addDay()])->count();
        $totalOrdersChange = ($ordersLastMonth > 0) ? (($totalOrders - $ordersLastMonth) / $ordersLastMonth) * 100 : 0;

        $activeUsersLastMonth = $usersMetrics->active_users_last_month;
        $activeUsersChange = (isset($activeUsersLastMonth) && $activeUsersLastMonth > 0) ? (($activeUsers - $activeUsersLastMonth) / $activeUsersLastMonth) * 100 : 0;

        $distributorsLastMonth = Distributor::whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()->addDay()])->count();
        $distributorsChange = ($distributorsLastMonth > 0) ? (($distributorsCount - $distributorsLastMonth) / $distributorsLastMonth) * 100 : 0;

        return response()->json([
            "message" => "Report metrics retrieved successfully",
            "data" => [
                'ordersToday' => $dailyMetrics->orders_today,
                'revenueToday' => $dailyMetrics->revenue_today,
                'newUsersToday' => $usersMetrics->new_users_today,
                'productsCount' => $productsCount,
                'totalRevenue' => $monthlyMetrics->total_revenue,
                'totalOrders' => $totalOrders,
                'activeUsers' => $activeUsers,
                'distributorsCount' => $distributorsCount,
                'totalRevenueChange' => $totalRevenueChange,
                'totalOrdersChange' => $totalOrdersChange,
                'activeUsersChange' => $activeUsersChange,
                'distributorsChange' => $distributorsChange,
                'revenue_overview' => [
                    'months' => $months,
                    'values' => $revenueData,
                ],
                'orders_by_status' => $orderStatuses,
                'recentOrders' => $recentOrders
            ]
        ]);
    }
    



    // public function dashboard(): JsonResponse
    // {
    //     $today = Carbon::today();
    //     $lastMonth = Carbon::now()->subMonth();

    //     // One-time total counts, which are constant
    //     $productsCount = Product::count();
    //     $totalOrders = Order::count();
    //     $activeUsers = User::where('ban', false)->count();
    //     $distributorsCount = Distributor::count();

    //     // Optimized metrics for today and last month in single queries
    //     $dailyMetrics = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
    //         ->whereDate('orders.created_at', $today)
    //         ->selectRaw('count(distinct orders.id) as orders_today, sum(order_items.unit_price * order_items.quantity) as revenue_today')
    //         ->first();

    //     $monthlyMetrics = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
    //         ->selectRaw('sum(order_items.unit_price * order_items.quantity) as total_revenue')
    //         ->selectRaw('sum(case when orders.created_at >= ? and orders.created_at < ? then order_items.unit_price * order_items.quantity else 0 end) as revenue_last_month', [
    //             $lastMonth->startOfMonth(),
    //             $lastMonth->endOfMonth()->addDay()
    //         ])->first();

    //     $usersMetrics = User::selectRaw('count(id) as total_users')
    //         ->selectRaw('count(case when created_at >= ? then id else null end) as new_users_today', [$today])
    //         ->selectRaw('count(case when ban = false and updated_at >= ? and updated_at < ? then id else null end) as active_users_last_month', [
    //             $lastMonth->startOfMonth(),
    //             $lastMonth->endOfMonth()->addDay()
    //         ])
    //         ->first();

    //     // Efficiently fetch recent orders with relationships
    //     $recentOrders = Order::with('user:id,first_name,last_name')->withCount('orderItem')
    //         ->orderByDesc('created_at')
    //         ->limit(5)
    //         ->get();

    //     // Optimized order status counts
    //     $orderStatuses = Order::selectRaw('status, count(*) as count')
    //         ->groupBy('status')
    //         ->pluck('count', 'status');

    //     $revenueOverview = Order::select(
    //         DB::raw('sum(order_items.unit_price * order_items.quantity) as total_revenue'),
    //         DB::raw('MONTH(orders.created_at) as month'),
    //         DB::raw('YEAR(orders.created_at) as year')
    //     )->join('order_items', 'orders.id', '=', 'order_items.order_id')
    //         ->where('status', 'delivered')
    //         ->where('orders.created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
    //         ->groupBy('year', 'month')
    //         ->orderBy('year', 'asc')
    //         ->orderBy('month', 'asc')
    //         ->get();

    //     // Prepare monthly revenue data for chart
    //     $revenueData = array_fill(0, 12, 0);
    //     $months = [];
    //     $currentMonth = Carbon::now()->subMonths(11)->startOfMonth();
    //     for ($i = 0; $i < 12; $i++) {
    //         $months[] = $currentMonth->format('F');
    //         $revenueData[$i] = $revenueOverview->where('year', $currentMonth->year)->where('month', $currentMonth->month)->first()->total_revenue ?? 0;
    //         $currentMonth->addMonth();
    //     }

    //     // Calculate changes
    //     // $totalRevenueChange = $monthlyMetrics->revenue_last_month ? (($monthlyMetrics->total_revenue - $monthlyMetrics->revenue_last_month) / $monthlyMetrics->revenue_last_month) * 100 : 0;
    //     $ordersLastMonth = Order::whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()->addDay()])->count();
    //     // $totalOrdersChange = $ordersLastMonth ? (($totalOrders - $ordersLastMonth) / $ordersLastMonth) * 100 : 0;
    //     // $activeUsersChange = $usersMetrics->active_users_last_month ? (($activeUsers - $usersMetrics->active_users_last_month) / $usersMetrics->active_users_last_month) * 100 : 0;
    //     $distributorsLastMonth = Distributor::whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()->addDay()])->count();
    //     // $distributorsChange = $distributorsLastMonth ? (($distributorsCount - $distributorsLastMonth) / $distributorsLastMonth) * 100 : 0;

    //     // Calculate changes
    //     $revenueLastMonth = $monthlyMetrics->revenue_last_month;
    //     $totalRevenueChange = (isset($revenueLastMonth) && $revenueLastMonth > 0) ? (($monthlyMetrics->total_revenue - $revenueLastMonth) / $revenueLastMonth) * 100 : 0;

    //     $totalOrdersChange = ($ordersLastMonth > 0) ? (($totalOrders - $ordersLastMonth) / $ordersLastMonth) * 100 : 0;

    //     $activeUsersLastMonth = $usersMetrics->active_users_last_month;
    //     $activeUsersChange = (isset($activeUsersLastMonth) && $activeUsersLastMonth > 0) ? (($activeUsers - $activeUsersLastMonth) / $activeUsersLastMonth) * 100 : 0;

    //     $distributorsChange = ($distributorsLastMonth > 0) ? (($distributorsCount - $distributorsLastMonth) / $distributorsLastMonth) * 100 : 0;

    //     return response()->json([
    //         "message" => "Report metrics retrieved successfully",
    //         "data" => [
    //             'ordersToday' => $dailyMetrics->orders_today,
    //             'revenueToday' => $dailyMetrics->revenue_today,
    //             'newUsersToday' => $usersMetrics->new_users_today,
    //             'productsCount' => $productsCount,
    //             'totalRevenue' => $monthlyMetrics->total_revenue,
    //             'totalOrders' => $totalOrders,
    //             'activeUsers' => $activeUsers,
    //             'distributorsCount' => $distributorsCount,
    //             'totalRevenueChange' => $totalRevenueChange,
    //             'totalOrdersChange' => $totalOrdersChange,
    //             'activeUsersChange' => $activeUsersChange,
    //             'distributorsChange' => $distributorsChange,
    //             'revenue_overview' => [
    //                 'months' => $months,
    //                 'values' => $revenueData,
    //             ],
    //             'orders_by_status' => $orderStatuses,
    //             'recentOrders' => $recentOrders
    //         ]
    //     ]);
    // }

    // public function dashboard()
    // {
    //     // Today's date
    //     $today = Carbon::today();

    //     // Metrics for today
    //     $ordersToday = Order::whereDate('created_at', $today)->count();
    //     $revenueToday = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
    //                   ->whereDate('orders.created_at', $today)
    //                   ->sum(DB::raw('order_items.unit_price * order_items.quantity'));
    //     $newUsersToday = User::whereDate('created_at', $today)->count();
    //     $productsCount = Product::count();

    //     // Metrics for overall totals
    //     $totalRevenue = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
    //                   ->sum(DB::raw('order_items.unit_price * order_items.quantity'));
    //     $totalOrders = Order::count();
    //     $activeUsers = User::where('ban', false)->count();
    //     $distributorsCount = Distributor::count();

    //     // Percentage change from last month
    //     $lastMonth = Carbon::now()->subMonth();
    //     $revenueLastMonth = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
    //                       ->whereMonth('orders.created_at', $lastMonth->month)
    //                       ->whereYear('orders.created_at', $lastMonth->year)
    //                       ->sum(DB::raw('order_items.unit_price * order_items.quantity'));
    //     $totalRevenueChange = $revenueLastMonth ? (($totalRevenue - $revenueLastMonth) / $revenueLastMonth) * 100 : 0;

    //     $ordersLastMonth = Order::whereMonth('created_at', $lastMonth->month)
    //                             ->whereYear('created_at', $lastMonth->year)
    //                             ->count();
    //     $totalOrdersChange = $ordersLastMonth ? (($totalOrders - $ordersLastMonth) / $ordersLastMonth) * 100 : 0;

    //     $activeUsersLastMonth = User::where('ban', false)
    //                                 ->whereMonth('updated_at', $lastMonth->month)
    //                                 ->whereYear('updated_at', $lastMonth->year)
    //                                 ->count();
    //     $activeUsersChange = $activeUsersLastMonth ? (($activeUsers - $activeUsersLastMonth) / $activeUsersLastMonth) * 100 : 0;

    //     $distributorsLastMonth = Distributor::whereMonth('created_at', $lastMonth->month)
    //                                         ->whereYear('created_at', $lastMonth->year)
    //                                         ->count();
    //     $distributorsChange = $distributorsLastMonth ? (($distributorsCount - $distributorsLastMonth) / $distributorsLastMonth) * 100 : 0;

    //     // Revenue Overview for last 12 months
    //     $revenueOverview = [];
    //     $months = [];
    //     for ($i = 11; $i >= 0; $i--) {
    //         $month = Carbon::now()->subMonths($i);
    //         $months[] = $month->format('F'); // Month name
    //         $revenueOverview[] = Order::whereYear('created_at', $month->year)
    //             ->whereMonth('created_at', $month->month)
    //             ->where('status', 'Delivered') // Only count completed orders
    //             ->sum('total_amount'); // Assuming 'total_amount' is the revenue field
    //     }

    //     // Orders by Status
    //     $orderStatuses = Order::selectRaw('status, COUNT(*) as count')
    //         ->groupBy('status')
    //         ->get()
    //         ->pluck('count', 'status'); // ['Pending' => 45, 'Processing' => 32, ...]

    //     $orders = Order::with('user:id,first_name,last_name')->withCount('orderItem')
    //                 ->orderBy('created_at', 'desc')
    //                 ->limit(5)
    //                 ->get();

    //     return response()->json([
    //         "message" => "Report metrics retrieved successfully",
    //         "data" => [
    //             'ordersToday' => $ordersToday,
    //             'revenueToday' => $revenueToday,
    //             'newUsersToday' => $newUsersToday,
    //             'productsCount' => $productsCount,
    //             'totalRevenue' => $totalRevenue,
    //             'totalOrders' => $totalOrders,
    //             'activeUsers' => $activeUsers,
    //             'distributorsCount' => $distributorsCount,
    //             'totalRevenueChange' => $totalRevenueChange,
    //             'totalOrdersChange' => $totalOrdersChange,
    //             'activeUsersChange' => $activeUsersChange,
    //             'distributorsChange' => $distributorsChange,
    //             'revenue_overview' => [
    //                 'months' => $months,
    //                 'values' => $revenueOverview,
    //             ],
    //             'orders_by_status' => [
    //                 'pending' => $orderStatuses->get('Pending', 0),
    //                 'processing' => $orderStatuses->get('Processing', 0),
    //                 'shipped' => $orderStatuses->get('Shipped', 0),
    //                 'delivered' => $orderStatuses->get('Delivered', 0),
    //                 'cancelled' => $orderStatuses->get('Cancelled', 0),
    //             ],
    //             'recentOrders' => $orders
    //         ]
    //     ]);
    // }
}