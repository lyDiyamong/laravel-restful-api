<?php

namespace Database\Factories;

use App\Models\Seller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Find users who are sellers (have products)
        // Get all distinct seller_ids first, then pick one randomly in PHP
        $sellerIds = Product::select('seller_id')->distinct()->get()->pluck('seller_id');
        
        if ($sellerIds->isEmpty()) {
            throw new \Exception('No products found. Cannot create transaction.');
        }
        
        $randomSellerId = $sellerIds->random();
        $seller = User::find($randomSellerId);
        
        // Find a buyer who is not the seller
        $buyer = User::where('user_id', '!=', $seller->user_id)->inRandomOrder()->first();

        if (!$buyer) {
            throw new \Exception('No suitable buyer found.');
        }
        
        // Get a random product from this seller
        $product = Product::where('seller_id', $seller->user_id)->inRandomOrder()->first();
        
        if (!$product) {
            throw new \Exception('No products found for the selected seller.');
        }

        return [
            'quantity' => $this->faker->numberBetween(1, 5),
            'buyer_id' => $buyer->user_id,
            'product_id' => $product->product_id,
        ];
    }
}
