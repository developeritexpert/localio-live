<?php

// namespace App\Console\Commands;

// use Illuminate\Console\Command;

// class ReviewTranslations extends Command
// {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:review-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Review and output translations for testing';

    /**
     * Execute the console command.
     */
//     public function handle()
//     {
//         $text = 'Welcome to our Website';
//         $languages = [
//             'es-mx',
//         ];
//         // foreach ($samples as $text) {
//             $this->info("Original: {$text}");

//             foreach ($languages as $lang_code) {
//                 $translated = website_translator($text, $lang_code);

//                 if ($translated) {
//                     $this->line("  [{$lang_code}] => {$translated}");
//                 } else {
//                     $this->warn("  [{$lang_code}] => (no translation)");
//                 }
//             }

//             $this->line(str_repeat('-', 50));
//          // }
//         $this->info("Translation review complete.");
//     }
// }



namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReviewTranslations extends Command
{
    protected $signature = 'app:review-translations';
    protected $description = 'Review and store translations for user reviews in multiple languages';

    public function handle()
    {
        // Define languages (replace language_id as per your DB)
        $languages = [
            'en-in' => 5,       // English India
            // Add more as needed
        ];

        // Fetch all reviews (only id)
        $reviews = DB::table('reviews')->select('id')->get();

        foreach ($reviews as $review) {
            $this->info("Review ID: {$review->id}");

            foreach ($languages as $langCode => $languageId) {
                // Check if translation exists
                $exists = DB::table('review_translations')
                    ->where('reviews_id', $review->id)
                    ->where('language_id', $languageId)
                    ->exists();

                if ($exists) {
                    $this->warn("  [{$langCode}] => Already translated.");
                    continue;
                }

                // Since original content is missing in `reviews`, fallback content is used
                DB::table('review_translations')->insert([
                    'language_id' => $languageId,
                    'reviews_id'  => $review->id,
                    'description' => website_translator('Default review text', $langCode),
                    'title'       => website_translator('No title provided', $langCode),
                    'pros'        => null,
                    'cons'        => null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                $this->info("  [{$langCode}] => Translation saved.");
            }

            $this->line(str_repeat('-', 50));
        }

        $this->info("All translations processed successfully.");
    }
}
