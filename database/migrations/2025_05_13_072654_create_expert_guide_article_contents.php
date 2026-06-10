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
        Schema::create('expert_guide_article_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expert_guide_article_id');
            $table->unsignedBigInteger('lang_id');
            $table->string('section_title')->nullable();
            $table->longText('section_content')->nullable();
            $table->timestamps();
            $table->foreign('expert_guide_article_id')
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
        Schema::dropIfExists('expert_guide_article_contents');
    }
};
