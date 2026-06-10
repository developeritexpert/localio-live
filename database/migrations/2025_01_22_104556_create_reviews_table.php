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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('lang_id')->constrained('languages')->onDelete('cascade');
        
            // Individual category ratings (0-5 scale)
     
            $table->tinyInteger('ease_of_use_rating')->unsigned()->default(1);
            $table->tinyInteger('value_for_money_rating')->unsigned()->default(1);
            $table->tinyInteger('customer_service_rating')->unsigned()->default(1);
            $table->tinyInteger('exclusive_service_rating')->unsigned()->default(1);
        
            // Average of all ratings 
            $table->decimal('rating', 3, 2)->default(1.00);
        
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
