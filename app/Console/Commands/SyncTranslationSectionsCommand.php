<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ContentTemplateService;
use App\Models\Category;
use App\Models\Business;
use App\Models\Feature;
use App\Models\ContentTemplate;

class SyncTranslationSectionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:sync-page-sections 
                            {--type=all : Entity type to sync: all, category, feature, business, template}
                            {--limit= : Limit number of entities to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync and hash resolved English frontend page texts for categories, features, businesses, and templates.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = strtolower($this->option('type') ?: 'all');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        $this->info("Starting page sections synchronization (type: {$type})...");

        $categoryKeys = [
            'compare_top_rated',
            'top_10',
            'pros_cons',
            'popular_comparisons',
            'compare_tool',
            'explore_subcategories',
            'category_page_faqs',
            'category_faq_search',
            'explore_link',
            'faq_h1',
            'faq_header_intro',
            'category_discussions_empty_headline',
        ];

        $featureKeys = [
            'feature_h1',
            'compare_top_rated',
            'top_10',
            'pros_cons',
            'popular_comparisons',
            'compare_tool',
            'view_all_link',
        ];

        $businessKeys = [
            'alternatives_subtext',
            'profile_what_is',
            'profile_pros_cons',
            'profile_what_offers',
            'profile_faqs',
            'profile_reviews',
            'profile_discussions',
            'profile_at_a_glance',
        ];

        $totalSynced = 0;

        // 1. Categories
        if (in_array($type, ['all', 'category', 'categories'], true)) {
            $catQuery = Category::query();
            if ($limit) {
                $catQuery->take($limit);
            }
            $categories = $catQuery->get();
            $this->info("Syncing {$categories->count()} categories...");

            foreach ($categories as $cat) {
                foreach ($categoryKeys as $k) {
                    $row = ContentTemplateService::syncEntitySectionSource('category', $cat->id, $k, 'category');
                    if ($row) {
                        $totalSynced++;
                    }
                }
            }
        }

        // 2. Businesses
        if (in_array($type, ['all', 'business', 'businesses'], true)) {
            $bizQuery = Business::query();
            if ($limit) {
                $bizQuery->take($limit);
            }
            $businesses = $bizQuery->get();
            $this->info("Syncing {$businesses->count()} businesses...");

            foreach ($businesses as $biz) {
                foreach ($businessKeys as $k) {
                    $row = ContentTemplateService::syncEntitySectionSource('business', $biz->id, $k, 'business_alternatives');
                    if ($row) {
                        $totalSynced++;
                    }
                }
            }
        }

        // 3. Features
        if (in_array($type, ['all', 'feature', 'features'], true)) {
            $featQuery = Feature::query();
            if ($limit) {
                $featQuery->take($limit);
            }
            $features = $featQuery->get();
            $this->info("Syncing {$features->count()} features...");

            foreach ($features as $feat) {
                foreach ($featureKeys as $k) {
                    $row = ContentTemplateService::syncEntitySectionSource('feature', $feat->id, $k, 'features');
                    if ($row) {
                        $totalSynced++;
                    }
                }
            }
        }

        // 4. Global Templates without variables
        if (in_array($type, ['all', 'template', 'templates'], true)) {
            $templates = ContentTemplate::where('lang_id', 1)->where('is_active', true)->get();
            $globalCount = 0;
            foreach ($templates as $tmpl) {
                $comb = ($tmpl->headline ?? '') . ' ' . ($tmpl->subtext ?? '');
                if (!ContentTemplateService::templateHasVariables($comb)) {
                    $row = ContentTemplateService::syncEntitySectionSource('template', 0, $tmpl->field_key, $tmpl->section_group);
                    if ($row) {
                        $globalCount++;
                        $totalSynced++;
                    }
                }
            }
            $this->info("Synced {$globalCount} global boilerplate templates (no variables).");
        }

        $this->info("Sync complete! Total section references synced: {$totalSynced}");
        return 0;
    }
}