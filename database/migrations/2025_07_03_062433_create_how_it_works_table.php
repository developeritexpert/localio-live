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
        Schema::create('how_it_works', function (Blueprint $table) {
            $table->id();
        $table->unsignedBigInteger('lang_id')->nullable();

        // Banner Section
        $table->string('banner_title')->nullable();
        $table->text('banner_description')->nullable();
        $table->string('banner_image_left')->nullable();
        $table->string('banner_image_right')->nullable();

        // Main Heading
        $table->string('main_heading')->nullable();



        // Section 1 – Title & Description only (static best value etc.)
        $table->string('section_1_title')->nullable();
        $table->text('section_1_description')->nullable();

        // Section 2
        $table->string('section_2_title')->nullable();
        $table->text('section_2_description')->nullable();
        $table->string('section_2_button')->nullable();

        // Section 3
        $table->string('section_3_title')->nullable();
        $table->text('section_3_description')->nullable();
        $table->string('section_3_image')->nullable();


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
        Schema::dropIfExists('how_it_works');
    }
};
