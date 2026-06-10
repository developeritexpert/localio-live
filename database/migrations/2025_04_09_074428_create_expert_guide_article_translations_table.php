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
        Schema::create('expert_guide_article_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expert_guide_article_id');
            $table->unsignedBigInteger('lang_id'); // FK to languages table
            $table->string('preview_title')->nullable();
            $table->text('preview_description')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->boolean('status')->default(1); // Optional: to enable/disable translations individually
            $table->timestamps();
            $table->foreign('expert_guide_article_id','article_id')
                ->references('id')
                ->on('expert_guide_articles')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_guide_article_translations');
    }
};
