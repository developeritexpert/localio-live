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
            $table->text('reviews_pro_cons_intro')->nullable()->after('reviews_description_2');
            $table->text('reviews_pro_cons_summary')->nullable()->after('reviews_pro_cons_intro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_translations', function (Blueprint $table) {
            $table->dropColumn(['reviews_pro_cons_intro', 'reviews_pro_cons_summary']);
        });
    }
};
