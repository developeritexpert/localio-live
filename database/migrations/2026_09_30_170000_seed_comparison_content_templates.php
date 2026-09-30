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
        ContentTemplateService::seedDefaultComparisonOptionsIfMissing();

        // Also ensure options exist for all other active languages
        $languages = Language::where('status', 1)->get();
        foreach ($languages as $lang) {
            if ($lang->id > 1) {
                ContentTemplateService::ensureTemplatesExistForLanguage('comparison', $lang->id);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        ContentTemplate::where('section_group', 'comparison')
            ->whereIn('field_key', [
                'comparison_h1',
                'comparison_pros_cons',
                'comparison_reviews',
                'comparison_alternatives',
            ])
            ->delete();
    }
};
