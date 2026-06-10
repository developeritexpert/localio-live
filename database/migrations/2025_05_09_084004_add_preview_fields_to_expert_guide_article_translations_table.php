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
        Schema::table('expert_guide_article_translations', function (Blueprint $table) {
            $table->string('preview_title')->nullable();
            $table->text('preview_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expert_guide_article_translations', function (Blueprint $table) {
            $table->dropColumn(['preview_title', 'preview_description']);
        });
    }
};
