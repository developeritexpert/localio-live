<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\StaticContentKey;
use App\Models\ContentTemplate;
use App\Models\ContentTemplateAssignment;
use App\Models\Language;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add glance_option_3_label and glance_option_4_label to category_translations
        if (Schema::hasTable('category_translations')) {
            Schema::table('category_translations', function (Blueprint $table) {
                if (!Schema::hasColumn('category_translations', 'glance_option_3_label')) {
                    $table->string('glance_option_3_label')->nullable()->after('comparison_name');
                }
                if (!Schema::hasColumn('category_translations', 'glance_option_4_label')) {
                    $table->string('glance_option_4_label')->nullable()->after('glance_option_3_label');
                }
            });
        }

        // 2. Add main_offering, suitable_for, glance_option_3, glance_option_4 to business_translations
        if (Schema::hasTable('business_translations')) {
            Schema::table('business_translations', function (Blueprint $table) {
                if (!Schema::hasColumn('business_translations', 'main_offering')) {
                    $table->text('main_offering')->nullable()->after('short_description');
                }
                if (!Schema::hasColumn('business_translations', 'suitable_for')) {
                    $table->text('suitable_for')->nullable()->after('main_offering');
                }
                if (!Schema::hasColumn('business_translations', 'glance_option_3')) {
                    $table->text('glance_option_3')->nullable()->after('suitable_for');
                }
                if (!Schema::hasColumn('business_translations', 'glance_option_4')) {
                    $table->text('glance_option_4')->nullable()->after('glance_option_3');
                }
            });
        }

        // 3. Seed Static Content Keys for "Main offering" and "Suitable for"
        $staticKeys = [
            [
                'key' => 'main_offering',
                'name' => 'Main Offering Label (Business at a Glance)',
                'default_value' => 'Main offering',
                'field_type' => 'text',
            ],
            [
                'key' => 'suitable_for',
                'name' => 'Suitable For Label (Business at a Glance)',
                'default_value' => 'Suitable for',
                'field_type' => 'text',
            ],
        ];

        foreach ($staticKeys as $k) {
            StaticContentKey::updateOrCreate(
                ['key' => $k['key']],
                [
                    'name' => $k['name'],
                    'default_value' => $k['default_value'],
                    'field_type' => $k['field_type'],
                ]
            );
        }
        Cache::forget('static_texts_all');

        // 4. Seed Content Templates for profile_at_a_glance
        $glanceTemplates = [
            1 => '[business] at a glance',
            2 => 'At a glance: [business]',
            3 => '[business] overview',
        ];

        $languages = Language::all();
        foreach ($languages as $lang) {
            foreach ($glanceTemplates as $optNum => $headline) {
                ContentTemplate::firstOrCreate(
                    [
                        'section_group' => 'business_profile',
                        'field_key'     => 'profile_at_a_glance',
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
        if (Schema::hasTable('category_translations')) {
            Schema::table('category_translations', function (Blueprint $table) {
                if (Schema::hasColumn('category_translations', 'glance_option_3_label')) {
                    $table->dropColumn('glance_option_3_label');
                }
                if (Schema::hasColumn('category_translations', 'glance_option_4_label')) {
                    $table->dropColumn('glance_option_4_label');
                }
            });
        }

        if (Schema::hasTable('business_translations')) {
            Schema::table('business_translations', function (Blueprint $table) {
                $cols = ['main_offering', 'suitable_for', 'glance_option_3', 'glance_option_4'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('business_translations', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }

        StaticContentKey::whereIn('key', ['main_offering', 'suitable_for'])->delete();
        Cache::forget('static_texts_all');

        ContentTemplateAssignment::where('field_key', 'profile_at_a_glance')->delete();
        ContentTemplate::where('field_key', 'profile_at_a_glance')->delete();
    }
};
