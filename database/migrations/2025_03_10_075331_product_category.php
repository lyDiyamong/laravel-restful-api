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

        Schema::create('product_category', function (Blueprint $table) {

            $table->foreignUuid('category_id');
            $table->foreignUuid('product_id');

            $table->foreign('category_id')->references( "category_id")->on("categories")->onDelete('cascade');
            $table->foreign('product_id')->references("product_id")->on("products")->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('product_category');
    }
};
