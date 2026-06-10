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
        Schema::create('help_center_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lang_id');
            $table->string('banner_headline')->nullable();
            $table->text('banner_description')->nullable();
            $table->string('banner_img')->nullable();

            $table->string('main_heading')->nullable();

            $table->string('left_section_title')->nullable();
            $table->text('left_section_description')->nullable();

            $table->string('faq_section_title')->nullable();
            $table->text('faq_section_description')->nullable();

            $table->string('meta_title');
            $table->text('meta_description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('help_center_contents');
    }
};
