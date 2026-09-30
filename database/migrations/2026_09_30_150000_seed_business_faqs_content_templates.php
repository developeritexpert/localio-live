<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\ContentTemplate;
use App\Services\ContentTemplateService;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        ContentTemplateService::seedDefaultBusinessFaqOptionsIfMissing();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        ContentTemplate::where('section_group', 'business_faqs')
            ->whereIn('field_key', [
                'faq_search_headline',
                'faq_search_placeholder',
                'faq_accordion_headline',
                'faq_explore',
                'faq_page_discussions',
                'faq_discussions_prompt_placeholder',
            ])
            ->delete();
    }
};
