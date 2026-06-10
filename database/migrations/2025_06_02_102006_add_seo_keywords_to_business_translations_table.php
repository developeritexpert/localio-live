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
        Schema::table('business_translations', function (Blueprint $table) {
            $table->text('primary_keywords')->nullable();
            $table->text('secondary_keywords')->nullable();
            $table->text('long_tail_keywords')->nullable();
            $table->text('high_intent_keywords')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_translations', function (Blueprint $table) {
            $table->dropColumn([
                'primary_keywords',
                'secondary_keywords',
                'long_tail_keywords',
                'high_intent_keywords'
            ]);
        });
    }
};
