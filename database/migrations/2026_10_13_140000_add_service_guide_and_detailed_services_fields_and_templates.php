<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ContentTemplate;
use App\Models\Language;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add service_guide_html and comparison_fit_summary to business_translations
        if (Schema::hasTable('business_translations')) {
            Schema::table('business_translations', function (Blueprint $table) {
                if (!Schema::hasColumn('business_translations', 'service_guide_html')) {
                    $table->mediumText('service_guide_html')->nullable()->after('after_image_description');
                }
                if (!Schema::hasColumn('business_translations', 'comparison_fit_summary')) {
                    $table->text('comparison_fit_summary')->nullable()->after('service_guide_html');
                }
            });
        }

        // 2. Seed Content Templates for profile_service_guide
        $serviceGuideTemplates = [
            1 => 'Which [business] service fits your needs?',
            2 => 'Find the right [business] service for your needs',
            3 => 'Which [business] solution is right for you?',
        ];

        // 3. Seed Content Templates for profile_detailed_services
        $detailedServicesTemplates = [
            1 => 'Detailed services',
            2 => '[business] detailed services',
            3 => 'Detailed services & features',
        ];

        $languages = Language::all();
        if ($languages->isEmpty()) {
            $languages = collect([(object)['id' => 1]]);
        }

        foreach ($languages as $lang) {
            foreach ($serviceGuideTemplates as $optNum => $headline) {
                ContentTemplate::firstOrCreate(
                    [
                        'section_group' => 'business_profile',
                        'field_key'     => 'profile_service_guide',
                        'lang_id'       => $lang->id,
                        'option_number' => $optNum,
                    ],
                    [
                        'headline' => $headline,
                        'subtext'  => null,
                        'status'   => 1,
                    ]
                );
            }

            foreach ($detailedServicesTemplates as $optNum => $headline) {
                ContentTemplate::firstOrCreate(
                    [
                        'section_group' => 'business_profile',
                        'field_key'     => 'profile_detailed_services',
                        'lang_id'       => $lang->id,
                        'option_number' => $optNum,
                    ],
                    [
                        'headline' => $headline,
                        'subtext'  => null,
                        'status'   => 1,
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('business_translations')) {
            Schema::table('business_translations', function (Blueprint $table) {
                if (Schema::hasColumn('business_translations', 'service_guide_html')) {
                    $table->dropColumn('service_guide_html');
                }
                if (Schema::hasColumn('business_translations', 'comparison_fit_summary')) {
                    $table->dropColumn('comparison_fit_summary');
                }
            });
        }

        ContentTemplate::where('section_group', 'business_profile')
            ->whereIn('field_key', ['profile_service_guide', 'profile_detailed_services'])
            ->delete();
    }
};
