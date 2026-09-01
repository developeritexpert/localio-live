<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $table->text('compare_alternatives_subtext')->nullable();
            $table->text('community_says_subtext')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropColumn(['compare_alternatives_subtext', 'community_says_subtext']);
        });
    }
};