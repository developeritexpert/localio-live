<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('business_translations', 'description_title')) {
                $table->string('description_title')->nullable()->after('name');
            }
            if (!Schema::hasColumn('business_translations', 'pro_cons_headline')) {
                $table->string('pro_cons_headline')->nullable()->after('high_intent_keywords');
            }
        });

        Schema::table('businesses', function (Blueprint $table) {
            if (!Schema::hasColumn('businesses', 'secondary_category_ids')) {
                $table->json('secondary_category_ids')->nullable()->after('category_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('business_translations', function (Blueprint $table) {
            if (Schema::hasColumn('business_translations', 'description_title')) {
                $table->dropColumn('description_title');
            }
            if (Schema::hasColumn('business_translations', 'pro_cons_headline')) {
                $table->dropColumn('pro_cons_headline');
            }
        });

        Schema::table('businesses', function (Blueprint $table) {
            if (Schema::hasColumn('businesses', 'secondary_category_ids')) {
                $table->dropColumn('secondary_category_ids');
            }
        });
    }
};
