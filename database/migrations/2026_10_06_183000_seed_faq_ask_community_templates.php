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
        ContentTemplateService::seedDefaultBusinessFaqOptionsIfMissing();

        // 2. Ensure for all active languages and assign for businesses
        $languages = Language::where('status', 1)->get();
        foreach ($languages as $lang) {
            ContentTemplateService::ensureTemplatesExistForLanguage('business_faqs', $lang->id);
            ContentTemplateService::assignMissingForBusinesses($lang->id, 'business_faqs');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $fieldKeys = [
            'faq_ask_community',
        ];

        ContentTemplateAssignment::where('entity_type', 'business')
            ->whereIn('field_key', $fieldKeys)
            ->delete();

        ContentTemplate::where('section_group', 'business_faqs')
            ->whereIn('field_key', $fieldKeys)
            ->delete();
    }
};
