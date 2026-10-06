<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ContentTemplate;
use App\Models\ContentTemplateAssignment;
use App\Services\ContentTemplateService;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('business_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('business_translations', 'compare_alternatives_under_10_headline')) {
                $table->string('compare_alternatives_under_10_headline')->nullable()->after('compare_alternatives_subtext');
            }
            if (!Schema::hasColumn('business_translations', 'compare_alternatives_under_10_subtext')) {
                $table->mediumText('compare_alternatives_under_10_subtext')->nullable()->after('compare_alternatives_under_10_headline');
            }
            if (!Schema::hasColumn('business_translations', 'community_says_under_10_headline')) {
                $table->string('community_says_under_10_headline')->nullable()->after('community_says_subtext');
            }
            if (!Schema::hasColumn('business_translations', 'community_says_under_10_subtext')) {
                $table->mediumText('community_says_under_10_subtext')->nullable()->after('community_says_under_10_headline');
            }
        });

        ContentTemplateService::seedDefaultBusinessAlternativesOptionsIfMissing();
        ContentTemplateService::assignMissingForBusinesses(1, 'business_alternatives');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $fieldKeys = [
            'compare_alternatives_under_10',
            'community_says_under_10',
        ];

        ContentTemplateAssignment::where('entity_type', 'business')
            ->whereIn('field_key', $fieldKeys)
            ->delete();

        ContentTemplate::where('section_group', 'business_alternatives')
            ->whereIn('field_key', $fieldKeys)
            ->delete();

        Schema::table('business_translations', function (Blueprint $table) {
            $cols = [
                'compare_alternatives_under_10_headline',
                'compare_alternatives_under_10_subtext',
                'community_says_under_10_headline',
                'community_says_under_10_subtext',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('business_translations', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
