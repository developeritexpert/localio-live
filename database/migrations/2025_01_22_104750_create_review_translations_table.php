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
        Schema::create('review_translations', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('language_id');
            $table->unsignedBigInteger('reviews_id'); // Foreign key to reviews table
            $table->string('title')->nullable(); // Title for the review translation
            $table->text('description'); // Description for the review translation
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_translations');
    }
};
