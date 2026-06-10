<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Language;
use App\Models\Filter;
use App\Models\FilterTranslation;
use App\Models\FilterOption;
use App\Models\FilterOptionTranslation;
use App\Models\CategoryTranslation;
use Illuminate\Support\Str;



class TranslateWebsiteContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:translate-website-content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate the website in all the languages.';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        saveLog('lamnguiage');

        $languages = Language::where('is_active_translation', 1)
            ->where('is_valid_language_code', 1)
            ->get();
    
        //:::::::::::::: Category Translation :::::::::://
        $categories = Category::all();
    
        foreach ($categories as $category) {
        
            foreach ($languages as $language) {
              $translationExists = CategoryTranslation::where([['category_id',$category->id],['lang_id',$language->id]])->exists();
              if (!$translationExists) {
                    // saveLog($language->id);
            
                    $translatedName = website_translator($category->name, $language->lang_code); // Translated name
                    $translatedDescription = website_translator($category->description, $language->lang_code); // Translated description
            
                    CategoryTranslation::create([
                        'category_id' => $category->id,
                        'lang_id' => $language->id,
                        'name' => $translatedName,
                        'description' => $translatedDescription,
                        'slug' => Str::slug($translatedName), // Generate slug dynamically
                    ]);
                }
            }
        }

        //:::::::::::::: Filter Translation :::::::::://
        $filters = Filter::all();
    
        foreach ($filters as $filter) {
        
            foreach ($languages as $language) {
              $ftranslationExists = FilterTranslation::where([['filter_id',$filter->id],['language_id',$language->id]])->exists();
              if (!$ftranslationExists) {
                    // saveLog($language->id);
            
                    $ftranslatedName = website_translator($filter->name, $language->lang_code); 
            
                    FilterTranslation::create([
                        'filter_id' => $filter->id,
                        'language_id' => $language->id,
                        'name' => $ftranslatedName,
                        'slug' => Str::slug($ftranslatedName), 
                    ]);
                }
            }
        }

        //:::::::::::::: Filter Option Translation :::::::::://
        $filterOptions = FilterOption::all();
    
        foreach ($filterOptions as $option) {
        
            foreach ($languages as $language) {
              $fotranslationExists = FilterOptionTranslation::where([['filter_option_id',$option->id],['language_id',$language->id]])->exists();
              if (!$fotranslationExists) {
                    // saveLog($language->id);
            
                    $fotranslatedName = website_translator($option->name, $language->lang_code); 
            
                    FilterOptionTranslation::create([
                        'filter_option_id' => $option->id,
                        'language_id' => $language->id,
                        'name' => $fotranslatedName,
                    ]);
                }
            }
        }
    }
}
