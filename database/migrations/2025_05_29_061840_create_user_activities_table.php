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
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable(); // nullable for guest users
            $table->string('session_id'); // for tracking guest users
            $table->string('activity_type'); // view, click, purchase, wishlist
            $table->string('product_id')->nullable();
            $table->string('category_id')->nullable();
            $table->string('business_id')->nullable();
            $table->integer('duration')->default(0); // time spent in seconds
            $table->json('metadata')->nullable(); // additional data
            $table->timestamps();
            
            $table->index(['user_id', 'activity_type']);
            $table->index(['session_id', 'activity_type']);
            $table->index(['product_id', 'created_at']);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
