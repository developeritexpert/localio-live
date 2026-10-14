<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\ContentTemplate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $serviceGuideSubtexts = [
            1 => '<p>Compare the core services and solutions offered by [business] to find the option that best matches your goals.</p>',
            2 => '<p>Explore available [business] service plans and features designed for different project requirements and skill levels.</p>',
            3 => '<p>Review key services from [business] to select the ideal tier and infrastructure for your setup.</p>',
        ];

        $detailedServicesSubtexts = [
            1 => '<p>In-depth breakdown of features, specifications, and tools included with [business] services.</p>',
            2 => '<p>Comprehensive overview of key capabilities and architecture supported by [business].</p>',
            3 => '<p>A closer look at the advanced functionality, performance features, and options available from [business].</p>',
        ];

        foreach ($serviceGuideSubtexts as $optNum => $subtext) {
            ContentTemplate::where('section_group', 'business_profile')
                ->where('field_key', 'profile_service_guide')
                ->where('option_number', $optNum)
                ->where(function ($q) {
                    $q->whereNull('subtext')->orWhere('subtext', '');
                })
                ->update(['subtext' => $subtext]);
        }

        foreach ($detailedServicesSubtexts as $optNum => $subtext) {
            ContentTemplate::where('section_group', 'business_profile')
                ->where('field_key', 'profile_detailed_services')
                ->where('option_number', $optNum)
                ->where(function ($q) {
                    $q->whereNull('subtext')->orWhere('subtext', '');
                })
                ->update(['subtext' => $subtext]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        ContentTemplate::where('section_group', 'business_profile')
            ->whereIn('field_key', ['profile_service_guide', 'profile_detailed_services'])
            ->update(['subtext' => null]);
    }
};
