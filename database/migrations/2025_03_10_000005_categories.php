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
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid("category_id")->primary();
            $table->string('name');
            $table->text('description')->nullable();
            // $table->foreignUuid('product_id')->constrained("products", "product_id")->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        {
            Schema::dropIfExists('categories');
        }
    }
};
