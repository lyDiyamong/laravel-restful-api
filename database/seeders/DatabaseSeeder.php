<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        User::factory()->count(10)->create();
        $products = Product::factory()->count(20)->create();
        $categories = Category::factory()->count(20)->create();
        Transaction::factory()->count(20)->create();

        User::flushEventListeners();
        Product::flushEventListeners();
        Category::flushEventListeners();
        Transaction::flushEventListeners();


        $products->each(function ($product) use ($categories) {
            // Attach 1–3 random category to each user
            $product->categories()->attach(
                $categories->random(rand(1, 3))->pluck('category_id')->toArray()
            );
        }); 
    }
}
