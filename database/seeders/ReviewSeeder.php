<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $customers = Customer::all();

        if ($products->isEmpty() || $customers->isEmpty()) {
            return;
        }

        // Create reviews ensuring unique product-customer combinations
        $createdReviews = [];
        $targetReviews = min(50, count($products) * count($customers));

        for ($i = 0; $i < $targetReviews; $i++) {
            $productId = $products->random()->id;
            $customerId = $customers->random()->id;
            $key = "$productId-$customerId";

            // Skip if this combination already exists
            if (isset($createdReviews[$key])) {
                continue;
            }

            $createdReviews[$key] = true;

            Review::create([
                'product_id' => $productId,
                'customer_id' => $customerId,
                'order_id' => null,
                'rating' => fake()->numberBetween(1, 5),
                'title' => fake()->sentence(5),
                'comment' => fake()->paragraph(3),
                'is_verified_purchase' => fake()->boolean(70),
                'is_approved' => fake()->boolean(80),
            ]);
        }
    }
}
