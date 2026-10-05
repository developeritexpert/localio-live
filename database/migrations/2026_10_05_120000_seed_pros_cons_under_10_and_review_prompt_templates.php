<?php

use Illuminate\Database\Migrations\Migration;
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
        ContentTemplateService::seedDefaultBusinessProfileOptionsIfMissing();
        ContentTemplateService::assignMissingForBusinesses(1, 'business_profile');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $fieldKeys = [
            'profile_pros_cons_under_10',
            'profile_review_prompt',
        ];

        ContentTemplateAssignment::where('entity_type', 'business')
            ->whereIn('field_key', $fieldKeys)
            ->delete();

        ContentTemplate::where('section_group', 'business_profile')
            ->whereIn('field_key', $fieldKeys)
            ->delete();
    }
};