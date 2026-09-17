<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentTemplate;
use App\Models\DefaultRatingCriteria;

class MissingContentTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $langId = 1;

        // Helper to insert options for a specific section and field
        $insertOptions = function ($sectionGroup, $fieldKey, $options) use ($langId) {
            $existingMax = ContentTemplate::where('section_group', $sectionGroup)
                ->where('field_key', $fieldKey)
                ->where('lang_id', $langId)
                ->max('option_number') ?? 0;

            foreach ($options as $index => $opt) {
                ContentTemplate::updateOrCreate([
                    'section_group' => $sectionGroup,
                    'field_key'     => $fieldKey,
                    'headline'      => $opt['headline'],
                    'subtext'       => $opt['subtext'] ?? '',
                    'lang_id'       => $langId,
                ], [
                    'option_number' => $existingMax + $index + 1,
                    'is_active'     => true,
                ]);
            }
        };

        // --- 1. Business Profile ---
        $insertOptions('business_profile', 'profile_h1', [
            ['headline' => '[category_cap] review [year]'],
            ['headline' => '[category_cap] pricing, reviews & features [year]'],
            ['headline' => 'Is it worth it? [year] review']
        ]);

        $insertOptions('business_profile', 'profile_subtext', [
            ['headline' => 'Real reviews, user discussions & alternatives.'],
            ['headline' => 'Explore verified reviews, feature breakdowns, and top alternatives.']
        ]);

        $insertOptions('business_profile', 'profile_what_is', [
            ['headline' => 'What is it?'],
            ['headline' => 'Overview']
        ]);

        $insertOptions('business_profile', 'profile_pros_cons', [
            ['headline' => 'Pros & Cons', 'subtext' => '<p>See what users love and what could be improved.</p>'],
            ['headline' => 'What users love (and hate)', 'subtext' => '<p>A balanced look at the strengths and weaknesses.</p>']
        ]);

        $insertOptions('business_profile', 'profile_compare', [
            ['headline' => 'Compare alternatives', 'subtext' => '<p>See how it stacks up against the competition.</p>'],
            ['headline' => 'Top Competitors', 'subtext' => '<p>Discover the best alternatives in [category_lc].</p>']
        ]);

        // --- 2. Category Discussions ---
        $insertOptions('category_discussions', 'discussions_h1', [
            ['headline' => '[category_cap] Community Discussions'],
            ['headline' => 'Talk about [category_lc] with the community']
        ]);
        $insertOptions('category_discussions', 'discussions_subtext', [
            ['headline' => 'Ask questions, share experiences, exchange tips, and discuss [category_lc] with other users.'],
            ['headline' => 'Join the conversation and connect with other [category_lc] users.']
        ]);

        // --- 3. Business Alternatives ---
        $insertOptions('business_alternatives', 'alternatives_h1', [
            ['headline' => 'Best alternatives & competitors [year]'],
            ['headline' => 'Top alternatives for [category_lc]']
        ]);
        $insertOptions('business_alternatives', 'alternatives_subtext', [
            ['headline' => 'Compare the top competitors side-by-side.'],
            ['headline' => 'Discover the best options available in the market today.']
        ]);

        // --- 4. Business FAQs ---
        $insertOptions('business_faqs', 'faqs_h1', [
            ['headline' => 'Frequently Asked Questions'],
            ['headline' => 'Common questions']
        ]);
        $insertOptions('business_faqs', 'faqs_subtext', [
            ['headline' => 'Find answers to the most common questions from real users.'],
            ['headline' => 'Everything you need to know, answered.']
        ]);

        // --- 5. Business Compare ---
        $insertOptions('business_compare', 'compare_h1', [
            ['headline' => 'Compare [category_lc] providers'],
            ['headline' => 'Side-by-side comparison']
        ]);
        $insertOptions('business_compare', 'compare_subtext', [
            ['headline' => 'See how different providers stack up in features and pricing.'],
            ['headline' => 'A detailed look at the top contenders.']
        ]);

        // --- 6. Business Reviews ---
        $insertOptions('business_reviews', 'reviews_h1', [
            ['headline' => 'Verified User Reviews'],
            ['headline' => 'What real users are saying']
        ]);
        $insertOptions('business_reviews', 'reviews_subtext', [
            ['headline' => 'Read honest feedback from the community.'],
            ['headline' => 'Unbiased reviews to help you make the right choice.']
        ]);

        // --- 7. Business Discussions ---
        $insertOptions('business_discussions', 'discussions_h1', [
            ['headline' => 'Community Discussions'],
            ['headline' => 'User Forum']
        ]);
        $insertOptions('business_discussions', 'discussions_subtext', [
            ['headline' => 'Join the conversation and share your experiences.'],
            ['headline' => 'Connect with other users and exchange tips.']
        ]);

        // --- 8. Criteria Ratings ---
        $criteriaList = DefaultRatingCriteria::all();

        foreach ($criteriaList as $criteria) {
            $critSection = 'criteria_ratings_' . $criteria->id;

            // Range 1 (1.0 - 1.9)
            $insertOptions($critSection, 'criteria_' . $criteria->id . '_range_1', [
                ['headline' => 'Users have reported significant issues.', 'subtext' => '<p>This area requires major improvements.</p>'],
                ['headline' => 'Falls below expectations.', 'subtext' => '<p>Many users found this aspect lacking.</p>']
            ]);

            // Range 2 (2.0 - 2.9)
            $insertOptions($critSection, 'criteria_' . $criteria->id . '_range_2', [
                ['headline' => 'Leaves room for improvement.', 'subtext' => '<p>There are noticeable flaws that affect the experience.</p>'],
                ['headline' => 'Below average performance.', 'subtext' => '<p>Users feel it could be much better.</p>']
            ]);

            // Range 3 (3.0 - 3.9)
            $insertOptions($critSection, 'criteria_' . $criteria->id . '_range_3', [
                ['headline' => 'Solid and meets general expectations.', 'subtext' => '<p>A reliable choice with decent performance.</p>'],
                ['headline' => 'Users are generally satisfied.', 'subtext' => '<p>Good overall, though not exceptional.</p>']
            ]);

            // Range 4 (4.0 - 4.9)
            $insertOptions($critSection, 'criteria_' . $criteria->id . '_range_4', [
                ['headline' => 'Highly rated by the community.', 'subtext' => '<p>Offers a premium experience in this area.</p>'],
                ['headline' => 'Excellent performance.', 'subtext' => '<p>Users strongly recommend it.</p>']
            ]);

            // Range 5 (5.0)
            $insertOptions($critSection, 'criteria_' . $criteria->id . '_range_5', [
                ['headline' => 'Flawless! Considered the best in the industry.', 'subtext' => '<p>An unmatched experience.</p>'],
                ['headline' => 'Perfect score—unmatched by competitors.', 'subtext' => '<p>The absolute gold standard.</p>']
            ]);
        }
    }
}
