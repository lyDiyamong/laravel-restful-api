<?php

namespace Database\Factories;

use App\Models\Seller;
use App\Models\Transaction;
use App\Models\User;
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
        // Use User model directly if sellers are users
        $seller = User::has('products')->inRandomOrder()->first();


        // Fallback in case there is no seller with products
        if (!$seller || $seller->products->isEmpty()) {
            throw new \Exception('No seller with products found.');
        }

        $buyer = User::where('user_id', '!=', $seller->user_id)->inRandomOrder()->first();

        if (!$buyer) {
            throw new \Exception('No suitable buyer found.');
        }

        return [
            'quantity' => $this->faker->numberBetween(1, 5),
            'buyer_id' => $buyer->id,
            'product_id' => $seller->products->random()->product_id,
        ];
    }
}
