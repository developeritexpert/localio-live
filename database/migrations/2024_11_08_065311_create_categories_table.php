<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('image')->nullable();  // Replacing `image` field
            $table->unsignedBigInteger('category_icon')->nullable();  // For category icon
            $table->integer('total_products')->default(0);
            $table->integer('total_review')->default(0);
            $table->boolean('status')->default(1);  // Active/Inactive status
            $table->timestamps();
        
            // Foreign key constraints
            $table->foreign('image')->references('id')->on('media')->onDelete('set null');
            $table->foreign('category_icon')->references('id')->on('media')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('categories');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
