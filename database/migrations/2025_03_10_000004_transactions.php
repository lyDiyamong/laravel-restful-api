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
            $table->foreignUuid('buyer_id')->constrained("buyers", "buyer_id")->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained("products", "product_id")->onDelete('cascade');
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
