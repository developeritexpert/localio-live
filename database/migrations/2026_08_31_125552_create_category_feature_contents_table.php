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
        if (!Schema::hasTable('category_feature_contents')) {
            Schema::create('category_feature_contents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
                $table->foreignId('feature_id')->constrained('features')->onDelete('cascade');
                $table->foreignId('lang_id')->constrained('base_languages')->onDelete('cascade');
                $table->longText('description')->nullable();
                $table->longText('text_sections')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_feature_contents');
    }
};
