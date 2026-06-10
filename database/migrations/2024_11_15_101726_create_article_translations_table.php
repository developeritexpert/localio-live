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
        Schema::create('article_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id'); // Foreign key column without constraint
            $table->unsignedBigInteger('language_id'); // Foreign key column without constraint
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            // Manually define the created_at and updated_at columns
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            
    
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_translations');
    }
};
