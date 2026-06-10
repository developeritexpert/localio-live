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
        Schema::create('contact_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lang_id')->default(1);
            $table->string('contact_heading')->nullable();
            $table->string('image_first')->nullable();
            $table->string('image_second')->nullable();
            $table->string('footer_heading')->nullable();
            $table->string('g_button')->nullable();
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
        Schema::dropIfExists('contact_contents');
    }
};
