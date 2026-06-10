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
        Schema::create('articles', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('name'); // Name of the article (string type)
            $table->string('slug')->unique(); // Slug, ensure it's unique (string type)
            $table->text('description')->nullable(); // Description of the article (text type)
            $table->unsignedBigInteger('category_id'); // Foreign key to the category table
            $table->string('image')->nullable(); // Image filename (string type), nullable because not all articles may have an image
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps(); // Created at and Updated at timestamps

            // Foreign key constraint (assuming the category table exists)
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
