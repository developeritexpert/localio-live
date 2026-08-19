<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. business_faq_categories table
        if (!Schema::hasTable('business_faq_categories')) {
            Schema::create('business_faq_categories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id');
                $table->string('name');
                $table->integer('position')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();

                $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            });
        }

        // 2. Add columns to business_faqs
        if (Schema::hasTable('business_faqs')) {
            Schema::table('business_faqs', function (Blueprint $table) {
                if (!Schema::hasColumn('business_faqs', 'business_faq_category_id')) {
                    $table->unsignedBigInteger('business_faq_category_id')->nullable()->after('business_id');
                    $table->foreign('business_faq_category_id')->references('id')->on('business_faq_categories')->onDelete('set null');
                }
                if (!Schema::hasColumn('business_faqs', 'helpful_count')) {
                    $table->unsignedInteger('helpful_count')->default(0)->after('position');
                }
                if (!Schema::hasColumn('business_faqs', 'not_helpful_count')) {
                    $table->unsignedInteger('not_helpful_count')->default(0)->after('helpful_count');
                }
            });
        }

        // 3. business_faq_feedbacks table
        if (!Schema::hasTable('business_faq_feedbacks')) {
            Schema::create('business_faq_feedbacks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_faq_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->boolean('is_helpful')->nullable();
                $table->string('report_reason')->nullable();
                $table->text('report_details')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();

                $table->foreign('business_faq_id')->references('id')->on('business_faqs')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('business_faq_feedbacks');
        
        if (Schema::hasTable('business_faqs')) {
            Schema::table('business_faqs', function (Blueprint $table) {
                if (Schema::hasColumn('business_faqs', 'business_faq_category_id')) {
                    $table->dropForeign(['business_faq_category_id']);
                    $table->dropColumn('business_faq_category_id');
                }
                if (Schema::hasColumn('business_faqs', 'helpful_count')) {
                    $table->dropColumn('helpful_count');
                }
                if (Schema::hasColumn('business_faqs', 'not_helpful_count')) {
                    $table->dropColumn('not_helpful_count');
                }
            });
        }

        Schema::dropIfExists('business_faq_categories');
    }
};
