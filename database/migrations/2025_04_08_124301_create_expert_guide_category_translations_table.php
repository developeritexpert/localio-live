<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        
        Schema::create('expert_guide_category_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expert_guide_category_id');
            $table->unsignedBigInteger('lang_id'); // Reference to languages table
        
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->boolean('status')->default(1); // Optional if translations can be published/unpublished separately
        
            $table->timestamps();
        
            // Foreign key constraints (optional but recommended)
            $table->foreign('expert_guide_category_id','category_id')
                  ->references('id')
                  ->on('expert_guide_categories')
                  ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('expert_guide_category_translations');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
