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
        Schema::create('get_listed', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lang_id');
        
            // Banner section
            $table->string('banner_heading')->nullable();
            $table->text('banner_sub_heading')->nullable();
            $table->string('banner_button')->nullable();
            $table->string('banner_image_left')->nullable();
            $table->string('banner_image_right')->nullable();
        
            // Section 1
            $table->string('section_1_image')->nullable();
            $table->string('section_1_title')->nullable();
            $table->text('section_1_description')->nullable();
        
            // Section 2
            $table->string('section_2_title')->nullable();
            $table->text('section_2_description')->nullable();
            $table->string('section_2_image')->nullable();
        
            // Section 3
            $table->string('section_3_title')->nullable();
            $table->text('section_3_description')->nullable();
            $table->string('section_3_image')->nullable();
            $table->string('section_3_button')->nullable();
        
            // Claim section
            $table->text('claim_section')->nullable();

        
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('get_listed');
    }
};
