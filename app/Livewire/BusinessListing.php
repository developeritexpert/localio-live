<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Business;
use App\Models\BusinessLanguage;
use App\Models\Language;
use App\Models\BusinessTranslation;
use Illuminate\Support\Str;

class BusinessListing extends Component
{
    use WithFileUploads;

    // Form state
    public $editMode = false;
    public $businessId = null;
    public $isSubmitting = false;
    public $addbusiness = true;

    // Country selection
    public $countryWebsiteUrls = []; // Associative array: countryId => [URLs]
    public $selectedCountryForUrl = null; // Country selected when adding a URL
    public $newWebsiteUrl = ''; // URL entered in input field
    public $showUrlForm = false; // Toggle to show/hide form
    public $editingUrl = null;
    public $editUrlValue = '';
    public $editIsAffiliate = false;
    public $selectedCountries = [];
    public $countrySearch = '';
    public $countries = [];
    public $selectedCountry = null;
    public $selectedCountryName = null;
    public $active_all_countries = 0;

    // Form fields
    public $name = '';
    public $website_url = '';
    public $websiteUrls = [''];
    public $affiliate_partner = '';
    public $affiliate_link = '';
    public $is_affiliate_partner = false;
    public $business_description = '';
    public $features = ['', '', ''];
    public $headquaters = '';
    public $pricingOptions = '';
    public $year_found = '';
    public $languages_supported = '';
    public $support_options = '';
    public $permanent_url = '';
    public $status = 0;
    public $meta_title = '';
    public $meta_description = '';
    public $selected_category = null;


    // Category features
    public $categoryFeatures = [];
    public $selectedFeatures = [];


    // File uploads
    public $iconError = null;
    public $imageError = null;
    public $icon_file = null;
    public $image_file = null;
    public $icon_id = null;
    public $image_id = null;

    // New property for multiple Business image upload
    public $business_images = []; //Add this to fix the error

    public $new_business_images = [];

    // FAQ
    public $pageMode;


    public $showFAQSection = false;
    public $selectedBusinessForFAQ = null;
    public $businessFAQs = [];
    public $faqQuestion = '';
    public $faqAnswer = '';
    public $editingFAQId = null;

    public $faqEditId;
    public $editingFAQ;



    protected function rules()
    {
        return array_merge($this->existingRules ?? [], [
            'faqQuestion' => 'required|string|max:500',
            'faqAnswer'   => 'required|string|max:2000',
            'icon_file'   => 'nullable|mimes:png,svg|max:2048',
            'image_file'  => 'nullable|mimes:jpg,jpeg,png,svg|max:4096',
        ]);
    }
    // Data lists
    public $businesses = [];
    public $categories = [];
    public $languages = [];
    public $selectedPricingOptions = [];
    public $lang_id = 1;
    protected $mediaService;
    public $validationErrors = [];
    public $touchedFields = [];
    public $lang_supported = [];
    public $content = '';
    public $editingPermanentUrl = false;
    public $permanentUrlSlug = '';
    public $countryIsAffiliate = true;
    public $is_affiliate = 0;
    public $primary_keywords = '';
    public $secondary_keywords = '';
    public $long_tail_keywords = '';
    public $high_intent_keywords = '';
    // translate form
    public $selectedBusiness = '';
    public $selectedLanguages = [];
    public $sourceLanguages = [];
    public $targetLanguages = [];
    public $fieldsToTranslate = [];
    public $selectAllLanguages = false;
    public $isTranslating = false;
    public $showTranslateModal = false;
    public $selectedBusinessId;
    public $sourceLanguage = null;
    protected $translationService;
    // Ai model
    public $businessDescription = '';
    public $ai_reference_url = '';
    // topic description
    public $categoryTopics = [];
    public $topicDescriptions = [];

    public function mount(){

        $this->businesses = Business::whereHas('translations', function ($query) {
            $query->where([['lang_id', $this->lang_id], ['status', 1]]);
        })->with(['translations' => function ($query) {
            $query->where('lang_id', $this->lang_id);
        }])->get();

    }

    public function render()
    {
        return view('livewire.business-listing');
    }

    public function translateBusiness($id)
    {

        $this->resetErrorBag(); // Clear previous validation errors
        $this->selectedBusinessId = $id;
        $this->selectedBusiness = Business::with('translations')->findOrFail($id);
        $this->selectedLanguages = [];
        $this->fieldsToTranslate = ['name', 'description'];
        $this->selectAllLanguages = false;
        $this->isTranslating = false;

        // Fetch associated languages for the given business
        $businessLanguages = BusinessLanguage::with('language')
            ->where('business_id', $id)
            ->get();


        $this->sourceLanguages = $businessLanguages->map(function($businessLang) {
            return [
                'id' => $businessLang->language->id,
                'name' => $businessLang->language->name,
                'lang_code' => $businessLang->language->lang_code,
                'base_language_id' => $businessLang->language->base_language_id
            ];
        })->toArray();

        // Set default source language available (first available language)
        if (!empty($this->sourceLanguages)) {
            $this->sourceLanguage = $this->sourceLanguages[0]['lang_code'];
        }

        $this->targetLanguages = Language::where('status', 1)->get()
            ->map(function($language) {
                return [
                    'id' => $language->id,
                    'name' => $language->name,
                    'lang_code' => $language->lang_code,
                    'base_language_id' => $language->base_language_id,
                    'country_id' => $language->country_id
                ];
            })->toArray();

        $this->showTranslateModal = true;
        $this->dispatch('show-translate-modal');
    }


    public function startTranslation()
    {
        // Clear previous errors
        $this->resetErrorBag();

        // Validate input
        $this->validate([
            'selectedLanguages' => 'required|array|min:1',
            'selectedLanguages' => 'required|array|min:1',

            'selectedBusinessId' => 'required|exists:businesses,id',
            'sourceLanguage' => 'required|string',
        ], [
            'selectedLanguages.required' => 'Please select at least one target language.',
            'selectedLanguages.min' => 'Please select at least one target language.',
            'selectedBusinessId.required' => 'Business selection is required.',
            'sourceLanguage.required' => 'Source language is required.',
        ]);

        try {
            $this->isTranslating = true;

            $business = Business::with('translations')->findOrFail($this->selectedBusinessId);

            $successCount = 0;
            $totalLanguages = count($this->selectedLanguages);

            foreach ($this->selectedLanguages as $languageId) {
                $result = $this->translateBusinessContent($business, $languageId);
                if ($result) {
                    $successCount++;
                }
            }

            $this->isTranslating = false;

            if ($successCount > 0) {
                $message = $successCount === $totalLanguages
                    ? "Translation completed successfully for all {$totalLanguages} languages!"
                    : "Translation completed for {$successCount} out of {$totalLanguages} languages.";

                $this->dispatch('show-toast',
                    type: 'success',
                    message: $message
                );

                $this->dispatch('translation-completed');

                // Reset form after successful translation
                $this->closeModal();
            } else {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Translation failed. Please try again.'
                );
            }

        } catch (\Exception $e) {
            $this->isTranslating = false;

            saveLog('Translation error: ' . $e->getMessage(), [
                'business_id' => $this->selectedBusinessId,
                'selected_languages' => $this->selectedLanguages,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('show-toast',
                type: 'error',
                message: 'An error occurred during translation. Please try again.'
            );
        }
    }

    private function translateBusinessContent($business, $languageId)
    {
        try {
            $translatedData = [];

            // Find source language ID based on selected source language code
            $sourceLanguage = collect($this->sourceLanguages)
                ->firstWhere('lang_code', $this->sourceLanguage);

            if (!$sourceLanguage) {
            saveLog("Source language not found for code: {$this->sourceLanguage}");
                return false;
            }

            $sourceLanguageId = $sourceLanguage['id'];

            // Find the source translation
            $sourceTranslation = $business->translations->firstWhere('lang_id', $sourceLanguageId);

            if (!$sourceTranslation) {
                Log::warning("No source translation found for business {$business->id} in language {$sourceLanguageId}");
                return false;
            }

            // Get target language code
            $targetLanguage = Language::find($languageId);

            if (!$targetLanguage) {
                saveLog("Target language not found for ID: {$languageId}");
                return false;
            }

            // Translate each field
            foreach ($this->fieldsToTranslate as $field) {
                if (!empty($sourceTranslation->{$field})) {
                    $translatedText = $this->translateText(
                        $sourceTranslation->{$field},
                        $this->sourceLanguage,
                        $targetLanguage->lang_code
                    );
                    $translatedData[$field] = $translatedText;
                }
            }

            // Fill other fields from source as fallback
            $fieldsToKeep = ['headquarters', 'support_options', 'status'];
            foreach ($fieldsToKeep as $field) {
                $translatedData[$field] = $sourceTranslation->{$field} ?? null;
            }

            // Save translation if we have data to save
            if (!empty($translatedData)) {
                BusinessTranslation::updateOrCreate([
                    'business_id' => $business->id,
                    'lang_id' => $languageId,
                ], array_merge($translatedData, [
                    'business_id' => $business->id,
                    'lang_id' => $languageId,
                    'slug' => Str::slug($translatedData['name'] ?? 'business-' . $business->id),
                ]));

                BusinessLanguage::updateOrCreate(
                    [
                        'business_id' => $business->id,
                        'language_id' => $languageId,
                    ],
                    [
                        'created_at' => now(), // or just any field that gets filled
                    ]
                );

                return true;
            }

            return false;

        } catch (\Exception $e) {
            saveLog("Translation failed for business {$business->id} to language {$languageId}: " . $e->getMessage());
            return false;
        }
    }

    private function translateText($text, $sourceLang, $targetLang)
    {
        try {
            $translatedText = website_translator($text, $targetLang);
            return $translatedText ?: $text; // Return original if translation fails
        } catch (\Exception $e) {
            saveLog("Text translation failed: " . $e->getMessage());
            return $text; // Return original text if translation fails
        }
    }


    public function closeModal()
    {
        $this->resetErrorBag(); // Clear validation errors
        $this->showTranslateModal = false;
        $this->selectedBusiness = null;
        $this->selectedLanguages = [];
        $this->fieldsToTranslate = ['description', 'name'];
        $this->selectAllLanguages = false;
        $this->sourceLanguage = null;
        $this->isTranslating = false;

        $this->dispatch('hide-translate-modal');
    }

    public function toggleAllLanguages()
    {
        if ($this->selectAllLanguages) {
            $this->selectedLanguages = collect($this->targetLanguages)
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedLanguages = [];
        }
    }
}
