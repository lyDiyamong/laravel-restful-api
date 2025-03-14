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
        Product::factory()->count(20)->create();
        Category::factory()->count(10)->create();
        Transaction::factory()->count(20)->create();

    }
}
