<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\ContentTemplate;
use App\Models\Language;
use App\Services\ContentTemplateService;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        ContentTemplateService::seedDefaultBusinessCompareOptionsIfMissing();

        // Also ensure options exist for all other active languages
        $languages = Language::where('status', 1)->get();
        foreach ($languages as $lang) {
            if ($lang->id > 1) {
                ContentTemplateService::ensureTemplatesExistForLanguage('business_compare', $lang->id);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        ContentTemplate::where('section_group', 'business_compare')
            ->whereIn('field_key', [
                'compare_overview',
                'compare_interactive_tool',
                'compare_search_placeholder',
                'compare_popular',
                'compare_recent',
                'compare_explore',
                'compare_page_discussions',
                'compare_discussions_prompt_placeholder',
            ])
            ->delete();
    }
};
