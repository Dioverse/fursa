<?php

namespace App\Http\Controllers\Api\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Distributor;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
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
            'message' => 'List of admins retrieved successfully.',
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
            'message' => 'Admin created successfully',
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
                'message' => [
                    'admin' =>$admin,
                    'id' =>$id
                ],
            ], 404);
        }

        return response()->json([
            'message' => 'Admin retrieved successfully.',
            'user'    => $admin,
        ]);
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json([
                'message' => 'Admin not found 45.',
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
            'message' => 'Admin updated successfully.',
            'user'    => $admin,
        ]);
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json([
                'message' => 'Admin not found. 56',
            ], 404);
        }

        $admin->delete();

        return response()->json([
            'message' => 'Admin deleted successfully.',
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




    /**
     * Return site settings (site_name, site_logo, tax)
     */
    public function siteSettings()
    {
        $settings = GeneralSetting::first(['site_name', 'site_logo', 'tax']);

        if (!$settings) {
            return response()->json(['message' => 'Settings not found'], 404);
        }

        return response()->json([
            'site_name' => $settings->site_name,
            'site_logo' => $settings->site_logo,
            'tax' => $settings->tax,
        ]);
    }

    /**
     * Update site name only
     */
    public function siteNameUpdate(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
        ]);

        $settings = GeneralSetting::firstOrFail();
        $settings->update(['site_name' => $request->site_name]);

        return response()->json(['message' => 'Site name updated successfully']);
    }

    /**
     * Update site logo only
     */
    public function siteLogoUpdate(Request $request)
    {
        $request->validate([
            'site_logo' => 'required|image|mimes:jpg,jpeg,png|max:204',
        ]);

        $settings = GeneralSetting::firstOrFail();

        // Upload logo
        $filename = 'site_logo_' . time() . '.' . $request->site_logo->extension();
        $request->site_logo->move(public_path('assets/images'), $filename);
        $logoPath = url('public/assets/images/' . $filename);

        $settings->update(['site_logo' => $logoPath]);

        return response()->json(['message' => 'Site logo updated successfully']);
    }


    /**
     * Update tax only
     */
    public function taxUpdate(Request $request)
    {
        $request->validate([
            'tax' => 'required|numeric|min:0.1|max:100',
        ]);

        $settings = GeneralSetting::firstOrFail();
        $settings->update(['tax' => $request->tax]);

        return response()->json(['message' => 'Tax updated successfully']);
    }

    /**
     * Return payment gateways (Paystack, Flutterwave, etc.)
     */
    public function paymentSettings()
    {
        $settings = GeneralSetting::first(['gateways']);

        if (!$settings) {
            return response()->json(['message' => 'Settings not found'], 404);
        }

        return response()->json([
            'gateways' => $settings->gateways,
        ]);
    }

    /**
     * Update payment gateway settings
     */
    public function updatePaystack(Request $request)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
            'currency' => 'required|string|size:3',
            'public_key' => 'required|string',
            'secret_key' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:102', // optional upload
        ]);

        $settings = GeneralSetting::firstOrFail();
        $gateways = is_array($settings->gateways)
                    ? $settings->gateways
                    : json_decode($settings->gateways, true);


        // Handle image upload
        if ($request->hasFile('image')) {
            $filename = 'paystack_' . time() . '.' . $request->image->extension();
            $request->image->move(public_path('assets/images'), $filename);
            $imagePath = url('public/assets/images/' . $filename);
        } else {
            $imagePath = $gateways['paystack']['image'] ?? null;
        }

        $gateways['paystack'] = [
            'image' => $imagePath,
            'status' => $request->status,
            'currency' => strtoupper($request->currency),
            'public_key' => $request->public_key,
            'secret_key' => $request->secret_key,
        ];

        $settings->update(['gateways' => json_encode($gateways)]);

        return response()->json(['message' => 'Paystack configuration updated successfully']);
    }

    public function updateFlutterwave(Request $request)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
            'currency' => 'required|string|size:3',
            'public_key' => 'required|string',
            'secret_key' => 'required|string',
            'encryption_key' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:102', // optional upload
        ]);
        print_r(GeneralSetting::getNested('gateways.paystack'));

        $settings = GeneralSetting::firstOrFail();
        $gateways = is_array($settings->gateways)
                    ? $settings->gateways
                    : json_decode($settings->gateways, true);

        // Handle image upload
        if ($request->hasFile('image')) {
            $filename = 'flutterwave_' . time() . '.' . $request->image->extension();
            $request->image->move(public_path('assets/images'), $filename);
            $imagePath = url('public/assets/images/' . $filename);
        } else {
            $imagePath = $gateways['flutterwave']['image'] ?? null;
        }

        $gateways['flutterwave'] = [
            'image' => $imagePath,
            'status' => $request->status,
            'currency' => strtoupper($request->currency),
            'public_key' => $request->public_key,
            'secret_key' => $request->secret_key,
            'encryption_key' => $request->encryption_key,
        ];

        $settings->update(['gateways' => json_encode($gateways)]);

        return response()->json(['message' => 'Flutterwave configuration updated successfully']);
    }

}