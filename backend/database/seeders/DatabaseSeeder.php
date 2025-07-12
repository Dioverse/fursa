<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;
use App\Models\OrderItem;
use App\Models\Distributor;
use App\Models\DiscountUser;
use App\Models\ShippingAddress;
use Illuminate\Database\Seeder;
use App\Models\DistributorProductPrice;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin, distributor, and customer users
        $admin = User::factory()->create(['role' => 'admin']);
        $distributors = User::factory()->count(5)->create(['role' => 'distributor']);
        $customers = User::factory()->count(10)->create(['role' => 'customer']);

        // Distributors
        $distributors->each(function ($user) {
            Distributor::factory()->create(['user_id' => $user->id]);
        });

        // Categories and Products
        $categories = Category::factory()->count(5)->create();
        $products = Product::factory()
            ->count(100)
            ->make()
            ->each(function ($product) use ($categories) {
                $product->category_id = $categories->random()->id;
                $product->save();
            });

        // Distributor Product Prices
        $products->each(function ($product) {
            DistributorProductPrice::factory()->create([
                'product_id' => $product->id,
            ]);
        });

        // Orders + Order Items for Customers
        $customers->each(function ($customer) use ($products) {
            $order = Order::factory()->create(['user_id' => $customer->id]);

            OrderItem::factory()->count(3)->create([
                'order_id' => $order->id,
                'product_id' => $products->random()->id,
            ]);

            ShippingAddress::factory()->create([
                'user_id' => $customer->id,
            ]);

            Payment::factory()->create([
                'order_id' => $order->id,
            ]);
        });

        // Discounts and Link to Users
        $discounts = Discount::factory()->count(3)->create();

        foreach ($discounts as $discount) {
            DiscountUser::factory()->create([
                'discount_id' => $discount->id,
                'user_id' => $customers->random()->id,
            ]);
        }
    }
}
