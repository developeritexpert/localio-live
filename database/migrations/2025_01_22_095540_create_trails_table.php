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
        Schema::create('trails', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('product_id'); // Foreign Key to Products table
            $table->integer('free_trial_days'); // Free trial days
            $table->timestamps(); // created_at and updated_at

            // Foreign Key Constraint (Optional, if you have a products table)
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trails');
    }
};
