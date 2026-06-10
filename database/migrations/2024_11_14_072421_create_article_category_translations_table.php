<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('article_category_translations', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreignId('article_category_id')->constrained('article_categories')->onDelete('cascade'); 
    //         $table->foreignId('language_id')->constrained('site_languages')->onDelete('cascade'); 
    //         $table->string('name');  
    //         $table->string('slug')->unique();      
    //         $table->longtext('description')->nullable(); 
    //         $table->timestamps();
    //         $table->enum('status', ['active', 'inactive'])->default('active');
    //         // Ensure unique translation per category per language
    //         $table->unique(['article_category_id', 'language_id']);
    //         $table->timestamps();
    //     });
        
    // }
    public function up(): void
    {
    if (!Schema::hasTable('article_category_translations')) {
        Schema::create('article_category_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_category_id'); // Foreign key column without constraint
            $table->unsignedBigInteger('language_id'); // Foreign key column without constraint
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_category_translations');
    }
};
