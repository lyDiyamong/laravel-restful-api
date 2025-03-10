<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        // database/migrations/xxxx_xx_xx_create_transactions_table.php

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            // Foreign Key
            $table->foreignUuid('buyer_id');
            $table->foreignUuid('product_id');

            // Reference
            $table->foreign('buyer_id')->references("user_id")->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references("product_id")->on('products')->onDelete('cascade');
            $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        {
            Schema::dropIfExists('transactions');
        }
    }
};
