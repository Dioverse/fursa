<?php
namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Shipping;
use App\Constants\Status;
use App\Models\OrderItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Payments\PaymentManager;

class CheckoutController extends Controller
{
    protected $orders;
    protected $payments;

    public function __construct(OrderService $orders, PaymentService $payments)
    {
        $this->orders   = $orders;
        $this->payments = $payments;
    }

    public function initialize(Request $request)
    {
        $user            = $request->user();
        $cartSummary     = $this->getCartSummary($user);
        $shippingAddress = $user->shippingAddress()->orderByDesc("is_default")->get();

        // if error, return immediately
        if ($cartSummary['error'] === true) {
            return response()->json($cartSummary, 422);
        }

        // get gateways (hide secret_key)
        $gateways = GeneralSetting::get("gateways");
        $gateways = collect($gateways)->map(function ($gateway) {
            return Arr::only($gateway, ['status', 'currency', 'image']);
        })->toArray();

        return response()->json([
            "message" => "Checkout",
            "data"    => [
                "gateways"          => $gateways,
                "user_cart"         => $cartSummary,
                "shippingAddresses" => $shippingAddress,
            ],
        ]);
    }

    protected function getShippingAddress($user)
    {
        $ushipAdd = $user->shippingAddress()->orderByDesc("is_default")->first(['id', 'full_name', 'province', 'phone', 'address_line_one', 'city', 'state', 'postal_code', 'country']);
        return $ushipAdd;
    }

    protected function getShippingCost($country, $state, $province)
    {
        $shipping = Shipping::where('country', $country)
            ->where('state', $state)
            ->where('province', $province)
            ->first(['cost', 'min_days', 'max_days']);
        return $shipping ?? "unserviceable";
    }

    protected function shippingCostLogic($user)
    {
        $sA = $this->getShippingAddress($user);

        return empty($sA) ? [] : [
            'userAddress' => $sA,
            'shipCost'    => $this->getShippingCost($sA->country, $sA->state, $sA->province),
        ];
    }

    protected function getCartSummary($user, $make = false)
    {
        // 1. Initialize Tax Rate
        $taxVal = (float) gs("tax");
        // Ensure tax rate is a percentage (e.g., 5.0 -> 0.05)
        $taxRate = $taxVal > 0 ? (float) ($taxVal / 100) : 0.0;

        $cart = $user->cart;

        // 2. Check for Empty Cart
        if (empty($cart) || $cart->cartItems->isEmpty()) {
            return [
                'error'   => true,
                'message' => 'Cart is empty, add some items',
            ];
        }

        // 3. Stock Check and Cart Item Processing (Merged)
        $cartItemsCollection = $cart->cartItems()->with('product')->get();

        // A. Check for unavailable items and prepare error details
        $unavailable = $cartItemsCollection->filter(function ($item) {
            $product = $item->product;
            return empty($product) || $product->stock_quantity < $item->quantity;
        })->map(function ($item) {
            $product = $item->product;
            return [
                'product_id' => $product->id ?? null,
                'name'       => $product->name ?? 'Unknown Product',
                'requested'  => $item->quantity,
                'available'  => $product->stock_quantity ?? 0,
            ];
        })->toArray();

        if (! empty($unavailable)) {
            return [
                'error'    => true,
                'response' => 'unavailable',
                'message'  => 'Unavailable stock quantity in cart',
                'errors'   => $unavailable,
            ];
        }

        // B. Build Cart Summary from available items
        $cartItems = $cartItemsCollection->map(function ($item) {
            $product         = $item->product;
            $originalPrice   = (float) $product->price;
            $discountedPrice = (float) $product->discounted_price;
            $quantity        = $item->quantity;

            return [
                'quantity'         => $quantity,
                'price'            => $originalPrice,
                'discounted_price' => $discountedPrice,
                'subtotal'         => (float) bcmul($discountedPrice, $quantity, 2),
                'originalSubtotal' => (float) bcmul($originalPrice, $quantity, 2),
                'stock_quantity'   => $product->stock_quantity,
            ];
        });

        // 4. Shipping Logic
        $ship = $this->shippingCostLogic($user);

        // Default shipping object for calculation and display
        $defaultShipCost = (object) ['cost' => 0.0, 'min_days' => 0, 'max_days' => 0];

        // Handle Shipping Errors when $make (creating an order) is true
        if ($make) {
            if (isset($ship['shipCost']) && $ship['shipCost'] === "unserviceable") {
                return ['error' => true, 'response' => 'unserviceable', 'message' => "Can't ship/deliver to location currently"];
            }
            if (empty($ship['userAddress'])) {
                return ['error' => true, 'response' => 'noshipping', 'message' => "No shipping address found."];
            }
        }

        // Normalize shipping cost for calculation (defaults to $0.00 if cost is not available or valid)
        $shipCost = $defaultShipCost;
        if (! empty($ship['shipCost']) && is_object($ship['shipCost']) && $ship['shipCost']->cost >= 0) {
            $shipCost = $ship['shipCost'];
        }

                                                                                 // 5. Calculate Totals
        $amount         = (float) round($cartItems->sum('subtotal'), 2);         // Sum of discounted subtotals
        $originalAmount = (float) round($cartItems->sum('originalSubtotal'), 2); // Sum of original subtotals

        $totalBeforeTax = bcadd($amount, (float) $shipCost->cost, 2); // Amount + Shipping Cost
        $tax            = bcmul($totalBeforeTax, $taxRate, 2);
        $payable        = (float) bcadd($totalBeforeTax, $tax, 2); // Total (Amount + Shipping + Tax)

        // 6. Return Summary
        return [
            'error'           => false,
            'id'              => $user->id,
            'first_name'      => $user->first_name,
            'last_name'       => $user->last_name,
            'email'           => $user->email,
            'payable'         => $payable,
            'amount'          => $amount,
            'originalAmount'  => $originalAmount,
            'cart_items'      => $cartItems,
            'shippingAddress' => $ship['userAddress'] ?? [],
            'shippingCost'    => $shipCost,
            'tax'             => (float) $tax,
            'tax_value'       => $taxRate,
        ];
    }

    protected function guestCartSummary(array $cart, string $country, string $state, ?string $province)
    {
        // 1. Initialize Tax Rate
        $taxVal  = (float) gs("tax");
        $taxRate = $taxVal > 0 ? (float) ($taxVal / 100) : 0.0;

        // 2. Validate cart existence
        if (empty($cart)) {
            return [
                'error'   => true,
                'message' => 'Cart is empty, add some items',
            ];
        }

        // Normalize cart structure
        $cartItemsInput = collect($cart)->map(function ($item) {
            return [
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
            ];
        });

        // 3. Load all products in one query
        $productIds = $cartItemsInput->pluck('product_id')->all();

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // 4. Stock check
        $unavailable = [];

        foreach ($cartItemsInput as $item) {
            $product = $products->get($item['product_id']);

            if (! $product || $product->stock_quantity < $item['quantity']) {
                $unavailable[] = [
                    'product_id' => $product->id ?? $item['product_id'],
                    'name'       => $product->name ?? 'Unknown Product',
                    'requested'  => $item['quantity'],
                    'available'  => $product->stock_quantity ?? 0,
                ];
            }
        }

        if (! empty($unavailable)) {
            return [
                'error'    => true,
                'response' => 'unavailable',
                'message'  => 'Unavailable stock quantity in cart',
                'errors'   => $unavailable,
            ];
        }

        // 5. Build cart items same shape as getCartSummary()
        $cartItems = $cartItemsInput->map(function ($item) use ($products) {
            $product         = $products->get($item['product_id']);
            $originalPrice   = (float) $product->price;
            $discountedPrice = (float) $product->discounted_price;
            $quantity        = (int) $item['quantity'];

            return [
                'quantity'         => $quantity,
                'price'            => $originalPrice,
                'discounted_price' => $discountedPrice,
                'subtotal'         => (float) bcmul($discountedPrice, $quantity, 2),
                'originalSubtotal' => (float) bcmul($originalPrice, $quantity, 2),
                'stock_quantity'   => $product->stock_quantity,
            ];
        });

        // 6. Shipping cost by raw country/state/province
        $shipping = $this->getShippingCost($country, $state, $province);

        $defaultShipObj = (object) ['cost' => 0.0, 'min_days' => 0, 'max_days' => 0];

        $shipCost = (is_object($shipping) && $shipping->cost >= 0)
            ? $shipping
            : $defaultShipObj;

        // guest "shippingAddress" structure compatible with frontend
        $guestShippingAddress = [
            'full_name'        => null,
            'phone'            => null,
            'address_line_one' => null,
            'address_line_two' => null,
            'city'             => null,
            'province'         => $province,
            'state'            => $state,
            'postal_code'      => null,
            'country'          => $country,
        ];

        // 7. Totals
        $amount         = (float) round($cartItems->sum('subtotal'), 2);
        $originalAmount = (float) round($cartItems->sum('originalSubtotal'), 2);

        $totalBeforeTax = bcadd($amount, (float) $shipCost->cost, 2);
        $tax            = bcmul($totalBeforeTax, $taxRate, 2);
        $payable        = (float) bcadd($totalBeforeTax, $tax, 2);

        // 8. Final payload – same keys as getCartSummary()
        return [
            'error'           => false,
            'id'              => null,
            'first_name'      => null,
            'last_name'       => null,
            'email'           => null,
            'payable'         => $payable,
            'amount'          => $amount,
            'originalAmount'  => $originalAmount,
            'cart_items'      => $cartItems,
            'shippingAddress' => $guestShippingAddress,
            'shippingCost'    => $shipCost,
            'tax'             => (float) $tax,
            'tax_value'       => $taxRate,
        ];
    }


    private function dispatchNotification($user, $order)
    {
        dispatch(function () use ($user, $order) {
            // Build products + history arrays
            $products = $order->orderItem->map(function ($item, $index) {
                $imagePath = optional($item->product->images->first())->path;
                $image     = rtrim(config("app.storage_url"), "/") . '/' . ltrim($imagePath, "/");
                return [
                    'sno'      => $index + 1,
                    'image'    => $image,
                    'name'     => $item->product->name,
                    'price'    => number_format($item->unit_price, 2),
                    'quantity' => $item->quantity,
                    'total'    => number_format($item->unit_price * $item->quantity, 2),
                ];
            })->toArray();

            $statushistory = $order->statusHstry->map(function ($history, $index) {
                return [
                    'sno'    => $index + 1,
                    'status' => ucfirst($history->status),
                    'date'   => $history->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray();
            notify(
                "ORDER_CONFIRMED", $user,
                [
                    "order_number"  => $order->order_id, "order_date"         => $order->created_at, "total_amount" => $order->total_amount,
                    'shipping_cost' => $order->shipping_cost, 'delivery_date' => $order->delivery_days,
                ],
                ["email"], false, ["products" => $products, "status_history" => $statushistory]
            );
        });
    }

    private function applyStockAndConfirmOrder($user, $order): array
    {
        // Ensure products are loaded
        $order->loadMissing(['orderItem.product']);

        $productUpdates = [];

        foreach ($order->orderItem as $item) {
            $product = $item->product;

            if (! $product || $product->stock_quantity < $item->quantity) {
                return [
                    'error'   => true,
                    'message' => $product
                        ? "Insufficient stock for {$product->name}"
                        : 'Product not found for one of the order items.',
                ];
            }

            $productUpdates[$product->id] = ($productUpdates[$product->id] ?? 0) + $item->quantity;
        }

        if (! empty($productUpdates)) {
            $ids = implode(',', array_keys($productUpdates));

            $cases = '';
            foreach ($productUpdates as $id => $qty) {
                $cases .= " WHEN id = {$id} AND stock_quantity >= {$qty} THEN stock_quantity - {$qty}";
            }

            $query = "UPDATE products
                  SET stock_quantity = CASE{$cases} ELSE stock_quantity END
                  WHERE id IN ({$ids})";

            DB::statement($query);

            $failed = DB::table('products')
                ->whereIn('id', array_keys($productUpdates))
                ->whereRaw('stock_quantity < 0')
                ->exists();

            if ($failed) {
                return [
                    'error'   => true,
                    'message' => 'Stock update failed — some products ran out of stock.',
                ];
            }
        }

        // Clear cart if exists
        if ($user->cart) {
            CartItem::where('cart_id', $user->cart->id)->delete();
        }

        // Confirm order (no payment step here)
        $this->orders->updateStatus($order, Status::O_CONFIRM, false);

        return ['error' => false];
    }

    public function guestR(Request $r)
    {
        $data = $r->validate([
            'cart'              => 'required|array|min:1',
            'cart.*.product_id' => 'required|integer|exists:products,id',
            'cart.*.quantity'   => 'required|integer|min:1',

            'country'           => 'required|string',
            'state'             => 'required|string',
            'province'          => 'nullable|string',
        ]);

        $summary = $this->guestCartSummary(
            $data['cart'],
            $data['country'],
            $data['state'],
            $data['province'] ?? null
        );

        if ($summary['error'] ?? false) {
            // preserve same behaviour as getCartSummary usage
            return response()->json($summary, 422);
        }

        return response()->json([
            'message' => 'Guest cart summary',
            'data'    => [
                'user_cart' => $summary,
            ],
        ], 200);
    }


    public function placeOrder(Request $request, PaymentManager $manager)
    {
        $user = $request->user();

        // ---------- Guest flow: no authenticated user ----------
        if (! $user) {
            $data = $request->validate([
                // user + shipping
                'email'             => 'required|email|unique:users,phone',
                'first_name'        => 'required|string',
                'last_name'         => 'required|string',
                'phone'             => 'required|string|unique:users,phone',
                'address_line_one'  => 'required|string',
                'address_line_two'  => 'nullable|string',
                'province'          => 'nullable|string',
                'city'              => 'required|string',
                'state'             => 'required|string',
                'postal_code'       => 'nullable|string',
                'country'           => 'required|string',

                // cart items from frontend (guest only)
                'cart'              => 'required|array|min:1',
                'cart.*.product_id' => 'required|integer|exists:products,id',
                'cart.*.quantity'   => 'required|integer|min:1',
            ]);

            $cartPayload = $data['cart'];
            unset($data['cart']);

            // Create or reuse user for this email (guest)
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'first_name' => $data['first_name'],
                    'last_name'  => $data['last_name'],
                    'name'       => $data['first_name'] . ' ' . $data['last_name'],
                    'phone'      => $data['phone'] ?? null,
                    'password'   => bcrypt(Str::random(16)),
                ]
            );

            // Ensure default shipping address exists/updated
            $user->shippingAddress()->updateOrCreate(
                ['is_default' => true],
                [
                    'full_name'        => $data['first_name'] . ' ' . $data['last_name'],
                    'phone'            => $data['phone'],
                    'address_line_one' => $data['address_line_one'],
                    'address_line_two' => $data['address_line_two'] ?? null,
                    'province'         => $data['province'] ?? null,
                    'city'             => $data['city'],
                    'state'            => $data['state'],
                    'postal_code'      => $data['postal_code'] ?? null,
                    'country'          => $data['country'],
                ]
            );

            // Build/overwrite cart for this guest user from payload
            $cart = $user->cart ?: Cart::create([
                'user_id' => $user->id,
            ]);

            // Clear any old cart items
            CartItem::where('cart_id', $cart->id)->delete();

            // Insert new cart items
            foreach ($cartPayload as $item) {
                CartItem::create([
                    'cart_id'    => $cart->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                ]);
            }
        }

        // ---------- Shared flow (guest or authenticated) ----------

        // Validate cart, stock, shipping, tax using existing logic
        $cartSummary = $this->getCartSummary($user, true);
        if ($cartSummary['error'] ?? false) {
            return response()->json($cartSummary, 422);
        }

        try {
            DB::beginTransaction();

            // Clean up abandoned pending orders with no payment
            $user->order()
                ->where('status', 'pending')
                ->whereDoesntHave('payment')
                ->delete();

            // Create order using existing service
            $orderCreate = $this->orders->createOrder(
                $user,
                $cartSummary['payable'],
                $cartSummary['shippingCost'],
                $cartSummary['tax']
            );

            if ($orderCreate['error'] ?? false) {
                DB::rollBack();
                return response()->json($orderCreate, 422);
            }

            $orderId  = $orderCreate['orderId'];
            $transRef = $orderCreate['trans_ref'] ?? null;

            /** @var \App\Models\Order $order */
            $order = $user->order()
                ->where('order_id', $orderId)
                ->firstOrFail();

            // ---------- Payment record (Pay on Delivery) ----------

            // Get POD gateway
            $gateCheck = $manager->gateway('pay_on_delivery');
            if ($gateCheck['error'] ?? false) {
                DB::rollBack();
                return response()->json($gateCheck, 422);
            }

            // Use existing trans_ref or generate one
            $transRef = $transRef ?: ('POD-' . Str::upper(Str::random(10)));

            // Create / update payment via PaymentService
            $paymentResult = $this->payments->verifyAndSave(
                $gateCheck['gate'],
                $transRef,
                $order->id,
                $user->id,
                $cartSummary['payable']
            );

            if ($paymentResult['error']) {
                DB::rollBack();
                return response()->json($paymentResult, 422);
            }

            // Mark order method explicitly as Pay on Delivery
            $order->update([
                'payment_method' => 'pay_on_delivery',
            ]);

            // ---------- Stock, cart clear, and confirm (shared helper) ----------
            $result = $this->applyStockAndConfirmOrder($user, $order);
            if ($result['error']) {
                DB::rollBack();
                return response()->json([
                    'error'   => true,
                    'message' => $result['message'],
                ], 422);
            }

            DB::commit();

            // Async notification (reuse existing helper)
            $this->dispatchNotification($user, $order);

            return response()->json([
                'message' => 'Order Placed successfully, support will contact you about your order soon',
                'orderId' => $orderId,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('PlaceOrder (POD) error: ' . $e->getMessage(), [
                'trace'   => $e->getTraceAsString(),
                'user_id' => $user->id ?? null,
            ]);

            return response()->json([
                'error'   => true,
                'message' => 'Failed to place order',
            ], 500);
        }
    }

    public function makeOrder(Request $request, $gateway, PaymentManager $manager)
    {
        $user = $request->user();

        $gateCheck = $manager->gateway($gateway);
        if ($gateCheck['error']) {
            return response()->json($gateCheck, 422);
        }

        // 1. Validate cart & stock
        $cartSummary = $this->getCartSummary($user, true);
        if ($cartSummary['error']) {
            return response()->json($cartSummary, 422);
        }

        try {
            DB::beginTransaction();

            // Clean up abandoned pending orders with no payment
            $user->order()->where('status', 'pending')
                ->whereDoesntHave('payment')->delete();

            // 2. Create fresh order
            $orderCreate = $this->orders->createOrder(
                $user,
                $cartSummary['payable'],
                $cartSummary['shippingCost'],
                $cartSummary['tax']
            );

            if ($orderCreate['error']) {
                DB::rollBack();
                return response()->json($orderCreate, 422);
            }

            // 3. Get gateway config without secret_key
            $gatewayConfig = GeneralSetting::getNested("gateways.$gateway");
            $gateDetails   = Arr::except($gatewayConfig, ['secret_key']);

            DB::commit();

            return response()->json([
                "message" => "Order Created Successfully",
                "data"    => [
                    "orderId"      => $orderCreate['orderId'],
                    "trans_ref"    => $orderCreate['trans_ref'],
                    "total_amount" => $cartSummary['payable'],
                    "gws"          => $gateDetails,
                    "email"        => $user->email,
                    "name"         => $user->name,
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Checkout error: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error'   => true,
                'message' => 'Failed to place order',
            ], 500);
        }
    }

    public function checkout(Request $request, $gateway, $transRef, $orderId, PaymentManager $manager)
    {
        $user = $request->user();

        $gateCheck = $manager->gateway($gateway);
        if ($gateCheck['error']) {
            return response()->json($gateCheck, 422);
        }

        $order = $user->order()
            ->withoutGlobalScope('pending')
            ->where('order_id', $orderId)
            ->where('trans_ref', $transRef)
            ->whereDoesntHave('payment', function ($q) {
                $q->where('status', '!=', 'pending');
            })
            ->first();

        if (! $order) {
            return response()->json([
                'message' => 'Order not found/has been resolved.',
            ], 404);
        }

        try {
            DB::beginTransaction();

            $paymentResult = $this->payments->verifyAndSave(
                $gateCheck['gate'],
                $transRef,
                $order->id,
                $order->user_id,
                $order->total_amount
            );

            if ($paymentResult['error']) {
                DB::rollBack();
                return response()->json($paymentResult, 422);
            }

            if (in_array($paymentResult['status'], ['failed', 'cancelled'])) {
                $order->update(['status' => 'failed']);

                $msg = 'Order cancelled. Payment failed';
                $msg .= ! empty($paymentResult['reason']) ? ' - ' . $paymentResult['reason'] : '';
                $msg .= '.';

                DB::commit();

                return response()->json([
                    'error'   => true,
                    'message' => $msg,
                ], 422);
            }

            // Stock + cart clear + confirm (shared logic)
            $result = $this->applyStockAndConfirmOrder($user, $order);
            if ($result['error']) {
                DB::rollBack();
                return response()->json([
                    'error'   => true,
                    'message' => $result['message'],
                ], 422);
            }

            DB::commit();

            $this->dispatchNotification($user, $order);

            return response()->json([
                'message' => 'Order placed successfully',
                'orderId' => $orderId,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Checkout error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error'   => true,
                'message' => 'Checkout failed',
            ], 500);
        }
    }

    // public function makeOrder(Request $request, $gateway, PaymentManager $manager)
    // {
    //     $user = $request->user();

    //     $gateCheck = $manager->gateway($gateway);
    //     if ($gateCheck['error']) {
    //         return response()->json($gateCheck, 422);
    //     }

    //     // Validate cart & stock
    //     $cartSummary = $this->getCartSummary($user, true);
    //     if ($cartSummary['error']) {
    //         return response()->json($cartSummary, 422);
    //     }

    //     try {
    //         DB::beginTransaction();

    //         // Clean up abandoned pending orders with no payment
    //         $user->order()->where('status', 'pending')
    //             ->whereDoesntHave('payment')->delete();

    //         // Create fresh order
    //         $orderCreate = $this->orders->createOrder(
    //             $user,
    //             $cartSummary['payable'],
    //             $cartSummary['shippingCost'],
    //             $cartSummary['tax']
    //         );

    //         if ($orderCreate['error']) {
    //             DB::rollBack();
    //             return response()->json($orderCreate, 422);
    //         }

    //         // For POD, no need to fetch gateway config
    //         if ($gateway === 'pod') {
    //             DB::commit();
    //             return response()->json([
    //                 "message" => "Order Created Successfully",
    //                 "data"    => [
    //                     "orderId"       => $orderCreate['orderId'],
    //                     "trans_ref"     => $orderCreate['trans_ref'],
    //                     "total_amount"  => $cartSummary['payable'],
    //                     "paymentMethod" => "pay_on_delivery",
    //                     "email"         => $user->email,
    //                     "name"          => $user->name,
    //                 ],
    //             ], 200);
    //         }

    //         // Get gateway config for online payments (excluding secret_key)
    //         $gatewayConfig = GeneralSetting::getNested("gateways.$gateway");
    //         $pubKey        = Arr::except($gatewayConfig, ['secret_key']);

    //         DB::commit();

    //         return response()->json([
    //             "message" => "Order Created Successfully",
    //             "data"    => [
    //                 "orderId"      => $orderCreate['orderId'],
    //                 "trans_ref"    => $orderCreate['trans_ref'],
    //                 "total_amount" => $cartSummary['payable'],
    //                 "gws"          => $pubKey,
    //                 "email"        => $user->email,
    //                 "name"         => $user->name,
    //             ],
    //         ], 200);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error("Checkout error: " . $e->getMessage(), [
    //             'trace' => $e->getTraceAsString(),
    //         ]);
    //         return response()->json([
    //             'error'   => true,
    //             'message' => 'Failed to place order',
    //         ], 500);
    //     }
    // }

    // public function checkout(Request $request, $gateway, $transRef, $orderId, PaymentManager $manager)
    // {
    //     $user = $request->user();

    //     $gateCheck = $manager->gateway($gateway);
    //     if ($gateCheck['error']) {
    //         return response()->json($gateCheck, 422);
    //     }

    //     $order = $user->order()->withoutGlobalScope('pending')->where("order_id", $orderId)->where("trans_ref", $transRef)
    //         ->whereDoesntHave('payment', function ($q) {
    //             $q->where("status", "!=", "pending");
    //         })->first();

    //     if (! $order) {
    //         return response()->json(["message" => "Order not found/has been resolved."], 404);
    //     }

    //     try {
    //         DB::beginTransaction();

    //         // Payment verification
    //         $paymentResult = $this->payments->verifyAndSave($gateCheck['gate'], $transRef, $order->id, $order->user_id, $order->total_amount);
    //         if ($paymentResult['error']) {
    //             DB::rollBack();
    //             return response()->json($paymentResult, 422);
    //         }

    //         // For online payments, check for failed/cancelled status
    //         if ($gateway !== 'pod' && in_array($paymentResult['status'], ['failed', 'cancelled'])) {
    //             $order->update(['status' => 'failed']);
    //             $msg = 'Order cancelled. Payment failed';
    //             $msg .= ! empty($paymentResult['reason']) ? " - " . $paymentResult['reason'] : '';
    //             $msg .= '.';
    //             DB::commit();
    //             return response()->json(['error' => true, 'message' => $msg], 422);
    //         }

    //         // Eager load items + products before use
    //         $order->loadMissing(['orderItem.product']);

    //         // Stock deduction (bulk + safe update)
    //         $productUpdates = [];
    //         foreach ($order->orderItem as $item) {
    //             $product = $item->product;
    //             if ($product->stock_quantity < $item->quantity) {
    //                 DB::rollBack();
    //                 return response()->json([
    //                     'error'   => true,
    //                     'message' => "Insufficient stock for {$product->name}",
    //                 ], 422);
    //             }
    //             $productUpdates[$product->id] = $item->quantity;
    //         }

    //         if (! empty($productUpdates)) {
    //             $ids = implode(',', array_keys($productUpdates));

    //             $cases = '';
    //             foreach ($productUpdates as $id => $qty) {
    //                 $cases .= " WHEN id = {$id} AND stock_quantity >= {$qty} THEN stock_quantity - {$qty}";
    //             }

    //             $query = "UPDATE products SET stock_quantity = CASE{$cases} ELSE stock_quantity END
    //             WHERE id IN ({$ids})";

    //             DB::statement($query);
    //             $failed = DB::table('products')->whereIn('id', array_keys($productUpdates))
    //                 ->whereRaw("stock_quantity < 0")->exists();

    //             if ($failed) {
    //                 DB::rollBack();
    //                 return response()->json(['error' => true, 'message' => 'Stock update failed — some products ran out of stock.'], 422);
    //             }
    //         }

    //         // Confirm order + clear cart
    //         CartItem::where('cart_id', $user->cart->id)->delete();

    //         // Set order status based on payment method
    //         $orderStatus = ($gateway === 'pod') ? Status::O_PENDING : Status::O_CONFIRM;
    //         $this->orders->updateStatus($order, $orderStatus, false);

    //         DB::commit();

    //         $this->dispatchNotification($user, $order);

    //         $message = $gateway === 'pod'
    //             ? 'Order placed successfully. Payment will be collected on delivery'
    //             : 'Order placed successfully';

    //         return response()->json([
    //             'message'       => $message,
    //             'orderId'       => $orderId,
    //             'paymentMethod' => $gateway,
    //         ]);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error("Checkout error: " . $e->getMessage(), [
    //             'trace' => $e->getTraceAsString(),
    //         ]);
    //         return response()->json([
    //             'error'   => true,
    //             'message' => 'Checkout failed',
    //         ], 500);
    //     }
    // }

    public function retryCheckout(Request $request, $transRef, PaymentManager $manager)
    {

    }
}
