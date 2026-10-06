<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\ContentTemplate;
use App\Models\ContentTemplateAssignment;
use App\Models\Language;
use App\Services\ContentTemplateService;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Seed options for lang_id 1
        ContentTemplateService::seedDefaultBusinessReviewsOptionsIfMissing();

        // 2. Ensure for all active languages and assign for businesses
        $languages = Language::where('status', 1)->get();
        foreach ($languages as $lang) {
            ContentTemplateService::ensureTemplatesExistForLanguage('business_reviews', $lang->id);
            ContentTemplateService::assignMissingForBusinesses($lang->id, 'business_reviews');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $fieldKeys = [
            'reviews_pros_cons_under_10',
        ];

        ContentTemplateAssignment::where('entity_type', 'business')
            ->whereIn('field_key', $fieldKeys)
            ->delete();

        ContentTemplate::where('section_group', 'business_reviews')
            ->whereIn('field_key', $fieldKeys)
            ->delete();
    }
};
