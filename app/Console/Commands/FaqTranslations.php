<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Faq;
use App\Models\FaqTranslation;
use App\Models\Language;
use Stichoza\GoogleTranslate\GoogleTranslate;

class FaqTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:faq-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $faqs = FaqTranslation::where('lang_id', 1)->get();
        // dump($faq);

        //language map : locale => lang_id
        $language = [
            'fr-fr' => 2,
        ];
        foreach ($faqs as $faq) {
            foreach($language as $locale => $langId){
                // Check if already translated (optional to skip)
                $exists = FaqTranslation::where('faq_id', $faq->faq_id)
                ->where('lang_id', $langId)
                ->exists();

                if($exists){
                    $this->line(" FAQ ID {$faq->id} already translated to {$locale}. Skipping.");
                    continue;
                }
                //Translate with fallback
                $translatedQuestion = website_translator($faq->question, $locale);
                $translatedAnswer = website_translator($faq->answer, $locale);

                $translations[] = [
                    'faq_id' => $faq->id,
                    'lang_id' => $langId,
                    'question' => $translatedQuestion ?: $faq->question,
                    'answer'  => $translatedAnswer ?: $faq->answer,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $this->info("Prepared translation for FAQ ID {$faq->id} - {$locale}");
            }

            if (!empty($translations)) {
                DB::table('faq_translations')->upsert(
                    $translations,
                    ['faq_id', 'lang_id'],
                    ['question', 'answer', 'updated_at']
                );
            }

            $this->info("FAQ translations imported successfully.");
        }
    }
}
