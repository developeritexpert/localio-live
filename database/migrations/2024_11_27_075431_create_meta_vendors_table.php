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
        Schema::create('meta_vendors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Foreign key column without constraint
            $table->string('job_title');  // Job title
            $table->string('company_name');  // Company name
            $table->string('company_size');  // Company size
            $table->string('product_name')->nullable();  // Product name
            $table->string('product_url')->nullable();  // Product URL
            $table->string('website_url')->nullable();  // Website URL (nullable)
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_vendors');
    }
};
