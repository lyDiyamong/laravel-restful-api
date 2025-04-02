<?php

use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid("user_id")->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('provider')->nullable();
            $table->string('img_profile')->nullable();
            $table->string('password');
            $table->boolean('verified')->default(User::UNVERIFIED_USER);
            $table->string('verification_token')->nullable();
            $table->dateTime("token_expires")->nullable();
            $table->boolean('admin')->default(User::REGULAR_USER);
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
