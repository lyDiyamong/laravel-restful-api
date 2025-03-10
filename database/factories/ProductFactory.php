<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 500), // 10.00 - 500.00
            'quantity' => $this->faker->numberBetween(1, 100),
            'image' => $this->faker->imageUrl(640, 480, 'products', true),
            'status' => Product::AVAILABLE_PRODUCT, // or random between AVAILABLE/UNAVAILABLE
            'seller_id' => User::factory(), // assumes seller is a User with user_id
        ];
    }
}
