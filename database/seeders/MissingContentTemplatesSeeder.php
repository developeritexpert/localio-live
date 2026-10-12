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

        $insertOptions('business_profile', 'profile_what_offers', [
            ['headline' => 'What [business] offers'],
            ['headline' => 'What does [business] offer?']
        ]);

        $insertOptions('business_profile', 'profile_pros_cons', [
            ['headline' => 'Pros & Cons', 'subtext' => '<p>See what users love and what could be improved.</p>'],
            ['headline' => 'What users love (and hate)', 'subtext' => '<p>A balanced look at the strengths and weaknesses.</p>']
        ]);

        $insertOptions('business_profile', 'profile_compare', [
            ['headline' => 'Compare alternatives', 'subtext' => '<p>See how it stacks up against the competition.</p>'],
            ['headline' => 'Top Competitors', 'subtext' => '<p>Discover the best alternatives in [category_lc].</p>']
        ]);

        $insertOptions('business_profile', 'profile_discussions_prompt_placeholder', [
            ['headline' => 'Ask a question or share something about [business]...'],
            ['headline' => 'Have a question about [business]? Ask the community...'],
            ['headline' => 'Share your thoughts or ask a question about [business]...'],
            ['headline' => 'Start a conversation or ask about [business]...']
        ]);

        $insertOptions('business_profile', 'profile_discussions_empty_headline', [
            ['headline' => 'No discussions about [business] yet.'],
            ['headline' => 'No discussions about [business].'],
            ['headline' => 'There are no discussions about [business] yet.'],
            ['headline' => 'Be the first to discuss [business].']
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
        $insertOptions('category_discussions', 'category_discussions_prompt_placeholder', [
            ['headline' => 'Ask a question or share something about [category_cap]'],
            ['headline' => 'Have a question about [category_cap]? Ask the community'],
            ['headline' => 'Share your thoughts or ask a question about [category_cap]'],
            ['headline' => 'Start a conversation or ask about [category_cap]...']
        ]);
        $insertOptions('category_discussions', 'category_discussions_empty_headline', [
            ['headline' => 'No discussions about [category_cap].'],
            ['headline' => 'There are no discussions about [category_cap] yet.'],
            ['headline' => 'No questions or discussions for [category_cap] yet.'],
            ['headline' => 'Be the first to discuss [category_cap].']
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
            ['headline' => 'Comparing [business] with other options? Explore popular comparisons across ratings, features, pros & cons, user reviews, and other important details. See where the leading [entities1_lc] differ and what stands out about each option. Create your own comparison to explore the differences that matter most to you.'],
            ['headline' => 'Want to see how [business] compares with other [entities1_lc]? Explore ratings, features, pros & cons, user reviews, and key details across popular comparisons. Discover where the options differ and which strengths stand out. Build your own comparison and take a closer look at the options you are considering.'],
            ['headline' => 'Looking to compare [business] with another [entity1_lc]? Browse popular comparisons and explore differences across ratings, features, pros & cons, and user reviews. See what sets each option apart before exploring the details further. Create a comparison and focus on what matters most for your search.'],
            ['headline' => 'Explore how [business] compares with other leading [entities1_lc]. Popular comparisons bring together ratings, features, pros & cons, user reviews, and other useful details. See the differences between the options and discover what stands out about each one. Create your own comparison to explore the choices that interest you most.'],
            ['headline' => 'Considering [business] alongside other options? Compare leading [entities1_lc] across ratings, features, pros & cons, user reviews, and key details. Explore where they differ and which strengths stand out most. Build your own comparison and discover how the options match your priorities.'],
            ['headline' => 'See how [business] compares with other options in [category_lc]. Explore popular comparisons covering ratings, features, pros & cons, user reviews, and other important details. Discover the differences that set each option apart. Create your own comparison for a more detailed look at the choices that matter to you.'],
            ['headline' => 'Want a closer look at [business] and its alternatives? Explore popular comparisons across ratings, features, pros & cons, user reviews, and more. See where the leading options differ and what each one has to offer. Create your own comparison and explore the differences most relevant to your needs.'],
            ['headline' => 'Compare [business] with other [entities1_lc] and see what separates the leading options. Explore ratings, features, pros & cons, user reviews, and other key details in popular comparisons. Discover where each option stands out. Create your own comparison to examine the options you want to know more about.'],
            ['headline' => 'Thinking about [business] and other options? Explore popular comparisons and see how the options differ across ratings, features, pros & cons, user reviews, and more. Take a closer look at the strengths that stand out across the leading choices. Create your own comparison and explore the details most important to you.'],
            ['headline' => 'Discover how [business] compares with other leading options. Browse popular comparisons featuring ratings, features, pros & cons, user reviews, and other useful information. See what distinguishes one option from another. Create your own comparison to explore the differences you want to examine further.'],
            ['headline' => 'Looking at [business] alongside other [entities1_lc]? Explore popular comparisons and see how the options differ across ratings, features, pros & cons, and user reviews. Compare the details that stand out and explore each option more closely. Create your own comparison around the choices you are most interested in.'],
            ['headline' => 'Explore [business] comparisons and discover how it differs from other leading [entities1_lc]. Ratings, features, pros & cons, user reviews, and key details make the differences easier to explore. Browse popular comparisons or create your own to examine the options that best match what you are looking for.'],
            ['headline' => 'See how [business] stacks up against other options in [category_lc]. Explore popular comparisons across ratings, features, pros & cons, user reviews, and important details. Discover the strengths and differences that stand out. Create your own comparison for a closer look at the options that interest you most.'],
            ['headline' => 'Interested in comparing [business] with other [entities1_lc]? Explore popular comparisons and discover differences across ratings, features, pros & cons, user reviews, and more. See what stands out about the leading choices. Create your own comparison to explore the options that matter most to your decision.'],
            ['headline' => 'Compare [business] with leading alternatives and explore what makes each option different. Popular comparisons bring together ratings, features, pros & cons, user reviews, and other useful details. See where the options differ most. Create your own comparison and discover which details matter most to you.'],
            ['headline' => 'Want to compare [business] with other options? Browse popular comparisons across ratings, features, pros & cons, user reviews, and more. Discover where the leading [entities1_lc] differ and what stands out about each one. Create your own comparison for a detailed view of the choices that interest you.'],
            ['headline' => 'Explore how [business] compares across the details that matter. Popular comparisons include ratings, features, pros & cons, user reviews, and other useful details about the leading options. See where the differences become clear. Create your own comparison and focus on the choices most relevant to you.'],
            ['headline' => 'Considering several options alongside [business]? Explore popular comparisons and see how leading [entities1_lc] differ across ratings, features, pros & cons, user reviews, and key details. Discover what makes each option stand out. Create your own comparison to examine the alternatives you want to explore further.'],
            ['headline' => 'Discover the differences between [business] and other leading [entities1_lc]. Browse popular comparisons covering ratings, features, pros & cons, user reviews, and more. Explore what stands out about each option and where they differ most. Create your own comparison around the options that catch your interest.'],
            ['headline' => 'Compare [business] with other options and explore the details behind the differences. Popular comparisons bring together ratings, features, pros & cons, user reviews, and other important information. See what makes each option stand out. Create your own comparison to take a closer look at the choices that interest you.']
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
        $insertOptions('business_discussions', 'discussions_prompt_placeholder', [
            ['headline' => 'Ask a question or share something about [business]'],
            ['headline' => 'Have a question about [business]? Ask the community'],
            ['headline' => 'Share your thoughts or ask a question about [business]'],
            ['headline' => 'Start a conversation or ask about [business]...']
        ]);
        $insertOptions('business_discussions', 'discussions_empty_headline', [
            ['headline' => 'No discussions about [business].'],
            ['headline' => 'There are no discussions about [business] yet.'],
            ['headline' => 'No questions or discussions for [business] yet.'],
            ['headline' => 'Be the first to discuss [business].']
        ]);

        // --- 8. Criteria Ratings ---
        $criteriaList = DefaultRatingCriteria::all();

        foreach ($criteriaList as $criteria) {
            $critSection = 'criteria_ratings_' . $criteria->id;

            if ($criteria->id == 1 || $criteria->key === 'features') {
                $insertOptions($critSection, 'criteria_' . $criteria->id . '_under_10', [
                    [
                        'headline' => null,
                        'subtext'  => '<p>Discover what [business] offers and select any feature to find more [entities1_lc] that offer it.</p>'
                    ]
                ]);
            }

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
