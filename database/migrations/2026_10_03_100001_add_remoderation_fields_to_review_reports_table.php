<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('review_reports', function (Blueprint $table) {
            // Detailed report type (replaces generic report_reason string)
            $table->string('report_type')->nullable()->after('report_reason');
            // fake | conflict_of_interest | personal_info | false_allegation
            // hate | spam | wrong_business | copyright | other

            $table->string('reporter_type')->nullable()->after('report_type');
            // user | business

            // Track whether this report triggered re-moderation
            $table->boolean('remoderation_triggered')->default(false)->after('reporter_type');
            $table->string('remoderation_result')->nullable()->after('remoderation_triggered');
            // flagged_by_moderation_api | escalated_to_batch | passed
        });
    }

    public function down(): void
    {
        Schema::table('review_reports', function (Blueprint $table) {
            $table->dropColumn(['report_type','reporter_type','remoderation_triggered','remoderation_result']);
        });
    }
};
