<?php

namespace App\Livewire;

use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Business;
use App\Models\BusinessCountrie;
use App\Models\BusinessCountry;
use App\Models\BusinessLanguage;
use App\Models\BusinessTranslation;
use App\Models\BusinessWebsite;
use App\Models\Category;
use App\Models\Country;
use App\Models\Language;
use App\Models\PricingOption;
use App\Models\Feature;
use App\Models\BusinessCategoryTopic;
use App\Models\BusinessTopicDescription;
use App\Services\MediaService;
use App\Services\TranslationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
// use Livewire\Features\SupportBrowserEvents\Browser;


use App\Models\BusinessFaq;
use App\Models\BusinessFaqTranslation;
use App\Models\BusinessFaqCategory;

class BusinessEdit extends Component
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
    public $active_all_countries = 1;

    // Form fields
    public $name = '';
    public $website_url = '';
    public $websiteUrls = [''];
    public $affiliate_partner = '';
    public $affiliate_link = '';
    public $is_affiliate_partner = false;
    public $description_title = '';
    public $business_description = '';
    public $alternatives_title = '';
    public $alternatives_description = '';
    public $alternatives_title_2 = '';
    public $alternatives_description_2 = '';
    public $reviews_title = '';
    public $reviews_description = '';
    public $reviews_title_2 = '';
    public $reviews_description_2 = '';
    public $faqs_title = '';
    public $faqs_description = '';
    public $faqs_title_2 = '';
    public $faqs_description_2 = '';
    public $comparison_title = '';
    public $comparison_description = '';
    public $comparison_title_2 = '';
    public $comparison_description_2 = '';
    public $alternatives_meta_title = '';
    public $alternatives_meta_description = '';
    public $reviews_meta_title = '';
    public $reviews_meta_description = '';
    public $faqs_meta_title = '';
    public $faqs_meta_description = '';
    public $comparison_meta_title = '';
    public $comparison_meta_description = '';
    public $short_description = '';
    public $after_image_description = '';
    public $features = ['', '', ''];
    public $headquaters = '';
    public $pricingOptions = '';
    public $year_found = '';
    public $languages_supported = 1;
    public $support_options = '';
    public $permanent_url = '';
    public $status = 0;
    public $meta_title = '';
    public $meta_description = '';
    public $admin_rating = null;
    public $selected_category = null;
    public $selected_sub_categories = [];


    // Category features
    public $categoryFeatures = [];
    public $categoryFeaturesEnglish = [];
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
    public $faqCategoryId = null;
    public $newCategoryName = '';
    public $businessFaqCategories = [];
    public $faqQuestion = '';
    public $faqAnswer = '';
    public $editingFAQId = null;

    // JSON FAQ Upload
    public string $faqJsonData = '';
    public string $faqJsonApplyStatus = '';
    public string $faqJsonApplyMessage = '';

    public $faqEditId;
    public $editingFAQ;

    // USPs
    public $businessUsps = [
        ['text' => ''],
        ['text' => ''],
        ['text' => ''],
        ['text' => ''],
        ['text' => ''],
    ];

    // Pros & Cons
    public $businessPros = [
        ['text' => ''], ['text' => ''], ['text' => '']
    ];
    public $businessCons = [
        ['text' => ''], ['text' => ''], ['text' => '']
    ];

    // Offerings
    public $businessOfferings = [
        ['headline' => '', 'top_text' => '', 'bottom_text' => '', 'image' => null]
    ];
    public $newOfferingImages = []; // Array of temporary UploadedFile objects

    public $pro_cons_intro = '';
    public $pro_cons_summary = '';
    public $pro_cons_headline = '';

    // AI Output Upload
    public string $aiOutputText = '';
    public string $aiApplyStatus = '';  // success | error | empty string
    public string $aiApplyMessage = '';



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
    public $slug = '';
    public bool $isSlugCustomized = false;
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



    protected $listeners = [
        'refreshPricingOptions' => 'refreshPricingOptions',
        'updatePricingOptions' => 'updatePricingOptions',
        'updateSelectedFeatures' => 'updateSelectedFeatures',
        'featuresUpdated' => 'handleFeaturesUpdated',
        'aiContentGenerated' => 'handleAiContentGenerated',
        'updateTopicDescription' => 'updateTopicDescription',
        'featuresSelected'=>'updateFeatures',
    ];

    // #[On('featuresSelected')]
    public function updateFeatures($data)
    {
        $this->selectedFeatures = $data['features'] ?? [];
    }

    public $ratingTexts = []; // criteria_key => ['intro_text' => '', 'end_text' => '']

    public function mount($id = null)
    {
        if(isset($id) && $id != null){
            $this->businessId = $id;
            $this->editBusiness($id);
        }
        else{
            // Add Business
            $this->showAddForm();
        }

        $this->selectedPricingOptions = [];
        $this->selectedFeatures = [];
        $this->ai_reference_url = '';
        $this->loadInitialData();

        if ($id) {
            $business = Business::with(['countries', 'websites', 'pricingOptions', 'features'])->findOrFail($id);
            $this->selectedPricingOptions = $business->pricingOptions->pluck('id')->toArray();
            $this->selectedFeatures = $business->features->pluck('id')->toArray();
            $this->selectedCountries = $business->countries->pluck('id')->toArray();
            $this->countryWebsiteUrls = [];
            foreach ($business->websites as $website) {
                $this->countryWebsiteUrls[$website->country_id] = [
                    'url' => $website->website_url,
                    'is_affiliate' => !empty($website->is_affiliate),
                    'status' => $website->status ?? 1
                ];
            }
            if ($business && $business->business_images) {
                $this->business_images = $business->business_images;
            }
        }

        $this->countries = Country::with('language')->get();

        if ($this->selected_category) {
            $this->loadCategoryFeatures($this->selected_category);
            $this->loadCategoryTopics($this->selected_category);
        }

        // slug is loaded directly from translation in editBusiness()


    }

    public function render()
    {
        return view('livewire.business-edit' ,[
            'countries' => $this->countries,
        ]);
    }




    public function updateTopicDescription($topicId, $value)
    {
        $this->topicDescriptions[$topicId] = $value;
    }

    public function translateBusiness($id)
    {
        $this->resetErrorBag(); // Clear previous validation errors
        $this->selectedBusinessId = $id;
        $this->selectedBusiness = Business::with('translations')->findOrFail($id);
        $this->selectedLanguages = [];
        $this->fieldsToTranslate = ['name', 'description', 'short_description'];
        $this->selectAllLanguages = false;
        $this->isTranslating = false;

        // Fetch associated languages for the given business
        $businessLanguages = BusinessLanguage::with('language')
            ->where('business_id', $id)
            ->get();

        $this->sourceLanguages = $businessLanguages->map(function ($businessLang) {
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
            ->map(function ($language) {
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


    public function closeModal()
    {
        $this->resetErrorBag(); // Clear validation errors
        $this->showTranslateModal = false;
        $this->selectedBusiness = null;
        $this->selectedLanguages = [];
        $this->fieldsToTranslate = ['description', 'name', 'short_description'];
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

                $this->dispatch(
                    'show-toast',
                    type: 'success',
                    message: $message
                );

                $this->dispatch('translation-completed');

                // Reset form after successful translation
                $this->closeModal();
            } else {
                $this->dispatch(
                    'show-toast',
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

            $this->dispatch(
                'show-toast',
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

    // Ai content

    public function handleAiContentGenerated($data)
    {
        // $data = ['fieldType' => ..., 'content' => ..., 'businessId' => ...]
        // dd($data);

        if ($this->businessId === $data['businessId']) {

            if ($data['fieldType'] === 'description') {
                $this->business_description = $data['content'];
            } elseif ($data['fieldType'] === 'meta') {
                // Remove 'json' prefix if present (case-insensitive)
                $jsonString = preg_replace('/^json\s*/i', '', trim($data['content']));

                $decoded = json_decode($jsonString, true);

                if (is_array($decoded)) {
                    $this->meta_title = $decoded['meta_title'] ?? '';
                    $this->meta_description = $decoded['meta_description'] ?? '';
                    $this->primary_keywords = $decoded['primary_keywords'] ?? '';
                    $this->secondary_keywords = is_array($decoded['secondary_keywords']) ? implode(', ', $decoded['secondary_keywords']) : ($decoded['secondary_keywords'] ?? '');
                    $this->long_tail_keywords = is_array($decoded['long_tail_keywords']) ? implode(', ', $decoded['long_tail_keywords']) : ($decoded['long_tail_keywords'] ?? '');
                    $this->high_intent_keywords = is_array($decoded['high_intent_keywords']) ? implode(', ', $decoded['high_intent_keywords']) : ($decoded['high_intent_keywords'] ?? '');
                } else {
                    logger()->error('Invalid JSON for meta content:', ['raw' => $data['content']]);
                }
            } elseif ($data['fieldType'] === 'detailtopic') {
                // Remove 'json' prefix if present
                $content = preg_replace('/^json\s*/i', '', trim($data['content']));
                $decoded = json_decode($content, true);

                if (is_array($decoded)) {
                    foreach ($decoded as $entry) {
                        if (isset($entry['topic_id']) && isset($entry['description'])) {
                            $topicId = (int) $entry['topic_id'];
                            $description = trim($entry['description']);

                            // Save to DB
                            \App\Models\BusinessTopicDescription::updateOrCreate(
                                [
                                    'business_id' => $this->businessId,
                                    'topic_id'    => $topicId,
                                ],
                                [
                                    'description' => $description,
                                ]
                            );

                            // Store for frontend
                            $this->topicDescriptions[$topicId] = $description;
                        }
                    }
                } else {
                    logger()->error('Invalid JSON for detailtopic fieldType', [
                        'raw' => $data['content'],
                    ]);
                }
            }
        }
    }
    public function boot(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }


    public function updatedSelectedCategory($value)
    {
        // Clear the selected features when category changes
        $this->selectedFeatures = [];

        // Load features for the new category
        $this->loadCategoryFeatures($value);


        $this->loadCategoryTopics($value);


        // Notify JS to reinitialize
        $this->dispatch('categoryChanged', $this->categoryFeatures);
    }

    public function loadCategoryTopics($categoryId)
    {
        $this->categoryTopics = BusinessCategoryTopic::where('category_id', $categoryId)->get();

        // dd('Loaded topics:', $this->categoryTopics->toArray()); // ✅ Add log

        if ($this->businessId) {
            $existing = BusinessTopicDescription::where('business_id', $this->businessId)
                ->whereIn('topic_id', $this->categoryTopics->pluck('id'))
                ->pluck('description', 'topic_id')
                ->toArray();

            $this->topicDescriptions = $existing;
        } else {
            $this->topicDescriptions = [];
        }
    }

    private function extractSlugFromUrl($url)
    {
        if (empty($url)) {
            return '';
        }

        // Parse the URL and get the path
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '';

        // Remove leading slash and get the last segment
        $slug = trim($path, '/');

        // If there are multiple segments, get the last one
        $segments = explode('/', $slug);
        return end($segments);
    }
    public function updateSlug()
    {
        if ($this->slug) {
            $this->permanent_url = 'https://localio.com/' . $this->slug;
        }
    }
    public function editUrl($countryId, $index = 0)
    {
        $this->editingUrl = (string) $countryId;

        if (isset($this->countryWebsiteUrls[$countryId])) {
            $setting = $this->countryWebsiteUrls[$countryId];
            if (isset($setting[0]) && is_array($setting[0])) {
                $setting = $setting[0];
            }
            $this->editUrlValue = is_array($setting) ? ($setting['url'] ?? '') : (is_string($setting) ? $setting : '');
            $this->editIsAffiliate = is_array($setting) ? !empty($setting['is_affiliate']) : false;
        }
    }
    public function saveUrlEdit($countryId, $index = 0)
    {
        if ($this->editIsAffiliate) {
            $this->validate([
                'editUrlValue' => 'required|url',
            ]);
        } else {
            $this->resetValidation(['editUrlValue']);
            $this->editUrlValue = null;
        }

        if (isset($this->countryWebsiteUrls[$countryId])) {
            $this->countryWebsiteUrls[$countryId] = [
                'url' => $this->editIsAffiliate ? $this->editUrlValue : null,
                'is_affiliate' => (bool)$this->editIsAffiliate,
                'status' => 1
            ];
        }

        $this->cancelUrlEdit();
        $this->dispatch('notify', ['message' => 'Country setting updated successfully!', 'type' => 'success']);
    }

    public function cancelUrlEdit()
    {
        $this->editingUrl = null;
        $this->editUrlValue = '';
        $this->editIsAffiliate = false;
        $this->resetValidation(['editUrlValue']);
    }
    public function updatedSlug($value)
    {
        $this->isSlugCustomized = true;
    }
    public function updatedName($value)
    {
        if (!$this->isSlugCustomized) {
            $this->slug = Str::slug($value);
        }
    }
    protected function loadCategoryFeatures($categoryId)
    {
        $this->lang_id = getCurrentLanguageID();
        if (!$categoryId) {
            $this->categoryFeatures = [];
            return;
        }

        $translatedFeatures = $this->categoryFeatures = Feature::where('category_id', $categoryId)
        ->where('status', 'active')
        ->with([
            'translations' => function ($query) {
                $query->whereIn('lang_id', [$this->lang_id, 1]);
            }
        ])
        ->get();

    }

    public function refreshPricingOptions()
    {
        $this->loadPricingOptions();
        $this->dispatch('pricingOptionsRefreshed');
    }

    public function updatePricingOptions($options)
    {
        $this->selectedPricingOptions = $options;
    }

    public function updatedIconFile()
    {
        try {
            $this->validateOnly('icon_file');
            $this->iconError = null; // Clear previous error

            if ($this->icon_file) {
                // Store the file directly in public storage
                $filename = time() . '_' . \Str::random(10) . '.' . $this->icon_file->getClientOriginalExtension();
                $path = $this->icon_file->storeAs('business_icon', $filename, 'public');

                // You can then store the path in the database or return it
                $this->iconFilePath = $path;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->iconError = "Invalid file type! Only PNG, SVG are allowed.";
            $this->icon_file = null; // Prevent preview of invalid files
        }
    }

    public function updatedImageFile()
    {
        try {
            $this->validateOnly('image_file');
            $this->imageError = null;

            if ($this->image_file) {
                // Store the file directly in public storage
                $filename = time() . '_' . \Str::random(10) . '.' . $this->image_file->getClientOriginalExtension();
                $path = $this->image_file->storeAs('business_icon', $filename, 'public');

                // You can then store the path in the database or return it
                $this->imageFilePath = $path;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->imageError = "Invalid file type! Only JPG, PNG, SVG are allowed.";
            $this->image_file = null; // Prevent preview of invalid files
        }
    }

    public function updated($propertyName)
    {
        $this->touchedFields[$propertyName] = true;

        // Validate only touched fields in real-time
        $this->validateOnly($propertyName, $this->getValidationRules());
    }

    protected function loadInitialData()
    {
        $this->lang_id = getCurrentLanguageID();
        $this->loadCategories();
        $this->loadLanguages();
        $this->loadCountries();
        $this->loadBusinesses();
        $this->loadPricingOptions();
    }

    protected function loadCategories()
    {
        $this->categories = Category::with(['categoryTranslations' => function ($query) {
            $query->where('lang_id', $this->lang_id);
        }])->whereHas('categoryTranslations', function ($query) {
            $query->where('lang_id', $this->lang_id);
        })->get();
    }

    // Show Add URL Form
    public function showAddUrlForm()
    {

        $this->showUrlForm = true;
        $this->resetUrlForm();
    }

    public function cancelAddUrl()
    {
        $this->showUrlForm = false;
        $this->resetUrlForm();
    }
    public function resetUrlForm()
    {
        $this->selectedCountryForUrl = '';
        $this->newWebsiteUrl = '';
        $this->countryIsAffiliate = true;
        $this->resetValidation(['selectedCountryForUrl', 'newWebsiteUrl']);
    }

    public function addCountryWebsiteUrl()
    {
        $selectedId = (int) $this->selectedCountryForUrl;
        $countryId = null;

        if ($selectedId && Country::where('id', $selectedId)->exists()) {
            $countryId = $selectedId;
        } else {
            $lang = Language::find($selectedId);
            if ($lang && $lang->country_id) {
                $countryId = $lang->country_id;
            }
        }

        $isAff = !empty($this->countryIsAffiliate);

        if ($isAff) {
            $this->validate([
                'selectedCountryForUrl' => 'required',
                'newWebsiteUrl' => 'required|url',
            ]);
        } else {
            $this->validate([
                'selectedCountryForUrl' => 'required',
            ]);
            $this->newWebsiteUrl = null;
        }

        if (!$countryId) {
            $this->addError('selectedCountryForUrl', 'Please select a valid country/region.');
            return;
        }

        // Save or update the country setting
        $this->countryWebsiteUrls[$countryId] = [
            'url' => $isAff ? $this->newWebsiteUrl : null,
            'is_affiliate' => $isAff,
            'status' => 1
        ];

        $this->showUrlForm = false;
        $this->resetUrlForm();

        $this->dispatch('notify', ['message' => 'Country setting saved successfully!', 'type' => 'success']);
    }
    public function removeCountryWebsiteUrl($countryId, $index = 0)
    {
        if (isset($this->countryWebsiteUrls[$countryId])) {
            unset($this->countryWebsiteUrls[$countryId]);
            $this->dispatch('notify', ['message' => 'Country setting removed successfully!', 'type' => 'success']);
        } else {
            $this->dispatch('notify', ['message' => 'Setting not found!', 'type' => 'error']);
        }
    }


    protected function loadLanguages()
    {
        $this->languages = Language::all();
    }

    protected function loadPricingOptions()
    {
        $lang_id = getCurrentLanguageID();

        $this->pricingOptions = PricingOption::with(['translations' => function ($query) use ($lang_id) {
            $query->whereIn('lang_id', [$this->lang_id , 1]);
        }])->where('status', 1)->get();

    }

    protected function loadCountries()
    {
        $this->countries = Country::with('language')->get();
    }

    protected function loadBusinesses()
    {
        $this->businesses = Business::whereHas('translations', function ($query) {
            $query->where([['lang_id', $this->lang_id], ['status', 1]]);
        })->with(['translations' => function ($query) {
            $query->where('lang_id', $this->lang_id);
        }])->get();
        $this->dispatch('businessLoaded');
    }

    public function updatedCountrySearch()
    {
        $this->countries = Country::with('language')
            ->when($this->countrySearch, function ($query) {
                $query->where('name', 'like', '%' . $this->countrySearch . '%')
                    ->orWhereHas('language', function ($q) {
                        $q->where('name', 'like', '%' . $this->countrySearch . '%');
                    });
            })
            ->get();
    }
    public function toggleCountrySelection($countryId)
    {
        if (($key = array_search($countryId, $this->selectedCountries)) !== false) {
            unset($this->selectedCountries[$key]);
            $this->selectedCountries = array_values($this->selectedCountries);

            if ($this->selectedCountry == $countryId) {
                $this->selectedCountry = null;
                $this->selectedCountryName = null;
            }
        } else {
            $this->selectedCountries[] = $countryId;

            if ($this->selectedCountry === null) {
                $this->updateSelectedCountry($countryId);
            }
        }
    }
    public function updateSelectedCountry($countryId = null)
    {
        $countryId = $countryId ?: $this->selectedCountry;

        if ($countryId) {
            $this->selectedCountry = $countryId;
            $country = collect($this->countries)->firstWhere('id', $countryId);
            $this->selectedCountryName = $country ? $country->name : null;

            if ($this->businessId) {
                $this->loadCountrySpecificData($countryId);
            }
        }
    }
    protected function loadCountrySpecificData($countryId)
    {
        if ($this->businessId) {
            $websiteData = BusinessWebsite::where('business_id', $this->businessId)
                ->where('country_id', $countryId)
                ->first();

            $this->websiteUrls = $websiteData ? [$websiteData->website_url] : [''];
        }
    }
    public function selectAllCountries()
    {
        $this->selectedCountries = collect($this->countries)->pluck('id')->toArray();

        if ($this->selectedCountry === null && !empty($this->selectedCountries)) {
            $this->updateSelectedCountry($this->selectedCountries[0]);
        }
    }

    public function clearAllCountries()
    {
        $this->selectedCountries = [];
        $this->selectedCountry = null;
        $this->selectedCountryName = null;
    }

    public function toggleAllCountries()
    {
        if ($this->active_all_countries == 1) {
            $this->selectAllCountries();
        } elseif (empty($this->selectedCountries) && collect($this->countries)->isNotEmpty()) {
            $this->toggleCountrySelection($this->countries[0]->id);
        }
    }
    public function addWebsiteUrl()
    {
        $this->websiteUrls[] = '';
    }
    public function removeWebsiteUrl($index)
    {
        if (count($this->websiteUrls) > 1) {
            unset($this->websiteUrls[$index]);
            $this->websiteUrls = array_values($this->websiteUrls);
        }
    }

    public function showAddForm()
    {
        $this->resetForm();
        $this->addbusiness = false;
        $this->editMode = true;
        $this->countryIsAffiliate = true;
        $this->is_affiliate_partner = 0;
        // Dispatch an event to trigger Select2 initialization
        $this->dispatch('showForm');
    }
    public function editBusiness($id = 19)
    {
        $this->resetForm();
        $this->editMode = true;
        $this->addbusiness = false;
        $this->businessId = $id;

        $business = Business::with(['translations', 'languages', 'countries', 'websites', 'pricingOptions', 'features', 'usps', 'proCons', 'offerings', 'subCategories'])->findOrFail($id);

        // Load existing USPs into the form slots (pad to 5 empty slots)
        $existingUsps = $business->usps->pluck('text')->toArray();
        $this->businessUsps = array_map(
            fn($text) => ['text' => $text],
            array_pad($existingUsps, 5, '')
        );

        // Load existing Pros
        $existingPros = $business->proCons->where('type', 'pro')->pluck('text')->toArray();
        $this->businessPros = array_map(
            fn($text) => ['text' => $text],
            array_pad($existingPros, 3, '')
        );

        // Load existing Cons
        $existingCons = $business->proCons->where('type', 'con')->pluck('text')->toArray();
        $this->businessCons = array_map(
            fn($text) => ['text' => $text],
            array_pad($existingCons, 3, '')
        );
        
        $this->pro_cons_intro = $business->pro_cons_intro ?? '';
        $this->pro_cons_summary = $business->pro_cons_summary ?? '';
        $this->pro_cons_headline = $business->pro_cons_headline ?? '';

        // Load existing rating texts
        $existingTexts = \App\Models\BusinessRatingText::where('business_id', $id)->get();
        $this->ratingTexts = [];
        foreach ($existingTexts as $txt) {
            $this->ratingTexts[$txt->criteria_key] = [
                'intro_text' => $txt->intro_text ?? '',
                'end_text' => $txt->end_text ?? '',
            ];
        }

        // Load existing Offerings (restricted to one for now)
        if ($business->offerings->count() > 0) {
            $offering = $business->offerings->first();
            $this->businessOfferings = [
                [
                    'headline' => $offering->headline,
                    'top_text' => $offering->top_text,
                    'bottom_text' => $offering->bottom_text,
                    'image' => $offering->image, // existing image string
                ]
            ];
        } else {
            $this->businessOfferings = [
                ['headline' => '', 'top_text' => '', 'bottom_text' => '', 'image' => null]
            ];
        }

        // Set the selected countries (IDs) for the business
        $this->selectedCountries = $business->countries->pluck('id')->toArray();
        $this->active_all_countries = $business->active_all_countries;
        // Set selected pricing options and features
        $this->selectedPricingOptions = $business->pricingOptions->pluck('id')->toArray();
        $this->lang_supported = $business->languages->pluck('id')->toArray();
        $this->selected_category = $business->category_id ?? null;
        $this->admin_rating = $business->admin_rating;
        $this->selected_sub_categories = $business->subCategories ? $business->subCategories->pluck('id')->toArray() : [];
        if ($this->selected_category) {
            $this->loadCategoryTopics($this->selected_category);
            $this->loadCategoryFeatures($this->selected_category);
            $this->selectedFeatures = $business->features->pluck('id')->toArray();
        }
        // Fetch the translation data for the selected language
        $translation = $business->translations->firstWhere('lang_id', $this->lang_id)
            ?? $business->translations->first();
        $this->status = (int) $business->status;
        $this->name = $translation->name ?? '';
        $this->affiliate_partner = $business->affiliate_partner;
        $this->affiliate_link = $business->affiliate_link;
        $this->is_affiliate = (bool) $business->is_affiliate ?? '';
        $this->meta_title = $business->meta_title;
        $this->meta_description = $business->meta_description;
        $this->icon_id = $business->icon_id;
        $this->image_id = $business->image_id;

        //Add this line to load business images array from
        $this->business_images = $business->business_images ?? [];

        $this->headquaters = $translation->headquarters ?? '';
        $this->year_found = $business->year_found;
        $this->languages_supported = $business->languages_supported;
        $this->support_options = $translation->support_options ?? '';
        $this->description_title = $translation->description_title ?? '';
        $this->business_description = $translation->description ?? '';
        $this->alternatives_title = $translation->alternatives_title ?? '';
        $this->alternatives_description = $translation->alternatives_description ?? '';
        $this->alternatives_title_2 = $translation->alternatives_title_2 ?? '';
        $this->alternatives_description_2 = $translation->alternatives_description_2 ?? '';
        $this->reviews_title = $translation->reviews_title ?? '';
        $this->reviews_description = $translation->reviews_description ?? '';
        $this->reviews_title_2 = $translation->reviews_title_2 ?? '';
        $this->reviews_description_2 = $translation->reviews_description_2 ?? '';
        $this->faqs_title = $translation->faqs_title ?? '';
        $this->faqs_description = $translation->faqs_description ?? '';
        $this->faqs_title_2 = $translation->faqs_title_2 ?? '';
        $this->faqs_description_2 = $translation->faqs_description_2 ?? '';
        $this->comparison_title = $translation->comparison_title ?? '';
        $this->comparison_description = $translation->comparison_description ?? '';
        $this->comparison_title_2 = $translation->comparison_title_2 ?? '';
        $this->comparison_description_2 = $translation->comparison_description_2 ?? '';
        $this->alternatives_meta_title = $translation->alternatives_meta_title ?? '';
        $this->alternatives_meta_description = $translation->alternatives_meta_description ?? '';
        $this->reviews_meta_title = $translation->reviews_meta_title ?? '';
        $this->reviews_meta_description = $translation->reviews_meta_description ?? '';
        $this->faqs_meta_title = $translation->faqs_meta_title ?? '';
        $this->faqs_meta_description = $translation->faqs_meta_description ?? '';
        $this->comparison_meta_title = $translation->comparison_meta_title ?? '';
        $this->comparison_meta_description = $translation->comparison_meta_description ?? '';
        $this->short_description = $translation->short_description ?? '';
        $this->after_image_description = $translation->after_image_description ?? '';
        $this->permanent_url = $business->permanent_url ?? '';
        $this->slug = $translation->slug ?? '';
        $this->isSlugCustomized = !empty($this->slug) && !empty($this->name) && $this->slug !== Str::slug($this->name);
        $this->is_affiliate_partner = $business->is_affiliate ? 1 : 0;
        $this->primary_keywords = $translation->primary_keywords ?? '';
        $this->secondary_keywords = $translation->secondary_keywords ?? '';
        $this->long_tail_keywords = $translation->long_tail_keywords ?? '';
        $this->high_intent_keywords = $translation->high_intent_keywords ?? '';
        // Load website URLs grouped by country
        $this->countryWebsiteUrls = [];
        foreach ($business->websites as $website) {
            $this->countryWebsiteUrls[$website->country_id] = [
                'url' => $website->website_url,
                'is_affiliate' => !empty($website->is_affiliate),
                'status' => $website->status ?? 1
            ];
        }

        // Automatically select the first country (if any) to display its URL form
        if (!empty($this->selectedCountries)) {
            $this->updateSelectedCountry($this->selectedCountries[0]);
        }
        // Dispatch events to update Select2 components
        $this->dispatch('showForm');
        $this->dispatch('pricingOptionsLoaded', options: $this->selectedPricingOptions);
        $this->dispatch('featuresLoaded', options: $this->selectedFeatures);
        $this->dispatch('businessLoaded');
    }

    //Save The Business Images
    public function updatedNewBusinessImages()
    {
        $totalImages = count($this->business_images) + count($this->new_business_images);

        if ($totalImages > 5) {
            $this->addError('new_business_images', 'Maximum 5 images allowed. You already have ' . count($this->business_images) . ' images.');
            $this->new_business_images = array_slice($this->new_business_images, 0, 5 - count($this->business_images));
            return;
        }

        $this->resetErrorBag('new_business_images');
    }

    // Save business images
    public function BusinessImageSave()
    {
        $totalImages = count($this->business_images) + count($this->new_business_images);

        if ($totalImages > 5) {
            $this->addError('new_business_images', 'Maximum 5 images allowed.');
            return;
        }

        $this->validate([
            'new_business_images.*' => 'nullable|image|max:2048',
        ]);

        $storedNewImages = [];
        foreach ($this->new_business_images as $image) {
            if (is_object($image)) {
                $fileName = $this->storeFile($image, 'business/gallery');
                $storedNewImages[] = $fileName;
            }
        }

        // Merge existing with new images
        $allImages = array_merge($this->business_images, $storedNewImages);

        if ($this->businessId) {
            $business = Business::find($this->businessId);
            if ($business) {
                $business->business_images = $allImages;
                $business->save();
                session()->flash('success', 'Images saved successfully.');
            }
        }

        $this->business_images = $allImages;
        $this->new_business_images = []; // Clear new uploads
    }

    // Remove business image
    public function RemoveBusniessImage($index)
    {
        unset($this->business_images[$index]);
        $this->business_images = array_values($this->business_images);

        // Update database immediately
        if ($this->businessId) {
            $business = Business::find($this->businessId);
            if ($business) {
                $business->business_images = $this->business_images;
                $business->save();
            }
        }
    }

    // Remove new uploaded image (before save)
    public function removeNewImage($index)
    {
        unset($this->new_business_images[$index]);
        $this->new_business_images = array_values($this->new_business_images);
        $this->resetErrorBag('new_business_images');
    }

    // store new business in DB
    public function storeBusiness()
    {
        if (empty($this->slug) && !empty($this->name)) {
            $this->slug = Str::slug($this->name);
        }

        // Mark all fields as touched before validation
        foreach (array_keys($this->getValidationRules()) as $field) {
            $this->touchedFields[$field] = true;
        }

        $this->validateForm();

        $this->isSubmitting = true;

        try {
            DB::beginTransaction();

            $business = $this->createBusiness();

            $this->createBusinessTranslation($business);

            $this->syncBusinessRelationships($business);

            $this->saveUsps($business->id);
            $this->saveProsCons($business->id);
            $this->saveOfferings($business->id);
            $this->saveRatingTexts($business->id);

            DB::commit();

            BusinessLanguage::create([
                'business_id' => $business->id,
                'language_id' => $this->languages_supported,
            ]);

            $this->isSubmitting = false;
            session()->flash('success', 'Business saved successfully.');
            return redirect()->route('business.listing.livewire');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->isSubmitting = false;
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateBusiness()
    {
        if (empty($this->slug) && !empty($this->name)) {
            $this->slug = Str::slug($this->name);
        }

        // Mark all fields as touched before validation
        foreach (array_keys($this->getValidationRules()) as $field) {
            $this->touchedFields[$field] = true;
        }

        $this->validateForm();
        $this->isSubmitting = true;

        try {
            $business = Business::findOrFail($this->businessId);

            DB::beginTransaction();
            // dd('State before saving:', $this->countryWebsiteUrls);

            $this->updateBusinessData($business);
            $this->updateBusinessTranslation($business);

            $this->syncBusinessRelationships($business);

            $this->saveTopicDescriptions();

            $this->saveUsps($business->id);
            $this->saveProsCons($business->id);
            $this->saveOfferings($business->id);
            $this->saveRatingTexts($business->id);

            DB::commit();

            $this->editMode = false;
            $this->isSubmitting = false;
            session()->flash('success', 'Business updated successfully.');

            return redirect()->route('business.listing.livewire' , ['id' => $this->businessId]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->isSubmitting = false;
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    /**
     * Save USPs for a business (delete + re-insert pattern)
     */
    public function saveUsps($businessId)
    {
        \App\Models\BusinessUsp::where('business_id', $businessId)->delete();
        $uspsToInsert = [];
        foreach ($this->businessUsps as $usp) {
            $text = trim($usp['text'] ?? '');
            if (!empty($text)) {
                $uspsToInsert[] = [
                    'business_id' => $businessId,
                    'text' => $text,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        if (!empty($uspsToInsert)) {
            \App\Models\BusinessUsp::insert($uspsToInsert);
        }
    }

    public function saveProsCons($businessId)
    {
        // Pros & Cons are now user review-based
    }

    public function saveOfferings($businessId)
    {
        // For simplicity, we delete and recreate offerings.
        \App\Models\BusinessOffering::where('business_id', $businessId)->delete();
        foreach ($this->businessOfferings as $index => $offering) {
            if (empty(trim($offering['headline'] ?? '')) && empty(trim($offering['top_text'] ?? ''))) {
                continue; // skip empty offerings
            }

            $imagePath = $offering['image'] ?? null;
            // Check if a new image was uploaded for this offering
            if (isset($this->newOfferingImages[$index]) && $this->newOfferingImages[$index]) {
                $file = $this->newOfferingImages[$index];
                $path = $this->storeFile($file, 'offerings'); // Assuming storeFile returns the file path
                if ($path) {
                    $imagePath = $path;
                }
            }

            \App\Models\BusinessOffering::create([
                'business_id' => $businessId,
                'headline' => trim($offering['headline'] ?? ''),
                'top_text' => trim($offering['top_text'] ?? ''),
                'bottom_text' => trim($offering['bottom_text'] ?? ''),
                'image' => $imagePath,
            ]);
        }
    }

    public function saveRatingTexts($businessId)
    {
        if (empty($this->ratingTexts) || !is_array($this->ratingTexts)) return;

        foreach ($this->ratingTexts as $key => $data) {
            $intro = $data['intro_text'] ?? null;
            $end = $data['end_text'] ?? null;

            if ($intro !== null || $end !== null) {
                \App\Models\BusinessRatingText::updateOrCreate(
                    [
                        'business_id' => $businessId,
                        'criteria_key' => (string)$key,
                    ],
                    [
                        'intro_text' => $intro,
                        'end_text' => $end,
                    ]
                );
            }
        }
    }



    public function addUsp()
    {
        if (count($this->businessUsps) < 6) {
            $this->businessUsps[] = ['text' => ''];
        }
    }

    public function removeUsp($index)
    {
        unset($this->businessUsps[$index]);
        $this->businessUsps = array_values($this->businessUsps);
    }

    public function addPro()
    {
        $this->businessPros[] = ['text' => ''];
    }

    public function removePro($index)
    {
        unset($this->businessPros[$index]);
        $this->businessPros = array_values($this->businessPros);
    }

    public function addCon()
    {
        $this->businessCons[] = ['text' => ''];
    }

    public function removeCon($index)
    {
        unset($this->businessCons[$index]);
        $this->businessCons = array_values($this->businessCons);
    }

    public function addOffering()
    {
        $this->businessOfferings[] = ['headline' => '', 'top_text' => '', 'bottom_text' => '', 'image' => null];
    }

    public function removeOffering($index)
    {
        unset($this->businessOfferings[$index]);
        $this->businessOfferings = array_values($this->businessOfferings);
    }

    public function saveTopicDescriptions()
    {
        if (!$this->businessId || empty($this->topicDescriptions)) {
            return;
        }

        foreach ($this->topicDescriptions as $topicId => $description) {
            BusinessTopicDescription::updateOrCreate(
                [
                    'business_id' => $this->businessId,
                    'topic_id' => $topicId,
                ],
                [
                    'description' => $description,
                ]
            );
        }
    }
    public function updatedLanguagesSupported($value)
{
    if (!$value) {
        return;
    }

    // Sirf edit mode mein hi translation switch karo
    if (!$this->businessId) {
        return;
    }

    $business = Business::with('translations')->find($this->businessId);

    if (!$business) {
        return;
    }

    // Us language ki translation dhundo — agar nahi mili to empty rakho
    // taaki naya translation manually likha ja sake
    $translation = $business->translations->firstWhere('lang_id', $value);

    $this->name                     = $translation->name ?? '';
    $this->headquaters               = $translation->headquarters ?? '';
    $this->support_options           = $translation->support_options ?? '';
    $this->description_title         = $translation->description_title ?? '';
    $this->business_description      = $translation->description ?? '';
    $this->alternatives_title        = $translation->alternatives_title ?? '';
    $this->alternatives_description  = $translation->alternatives_description ?? '';
    $this->alternatives_title_2      = $translation->alternatives_title_2 ?? '';
    $this->alternatives_description_2= $translation->alternatives_description_2 ?? '';
    $this->reviews_title             = $translation->reviews_title ?? '';
    $this->reviews_description       = $translation->reviews_description ?? '';
    $this->reviews_title_2           = $translation->reviews_title_2 ?? '';
    $this->reviews_description_2     = $translation->reviews_description_2 ?? '';
    $this->faqs_title                = $translation->faqs_title ?? '';
    $this->faqs_description          = $translation->faqs_description ?? '';
    $this->faqs_title_2              = $translation->faqs_title_2 ?? '';
    $this->faqs_description_2        = $translation->faqs_description_2 ?? '';
    $this->comparison_title          = $translation->comparison_title ?? '';
    $this->comparison_description    = $translation->comparison_description ?? '';
    $this->comparison_title_2        = $translation->comparison_title_2 ?? '';
    $this->comparison_description_2  = $translation->comparison_description_2 ?? '';
    $this->short_description         = $translation->short_description ?? '';
    $this->after_image_description   = $translation->after_image_description ?? '';
    $this->primary_keywords          = $translation->primary_keywords ?? '';
    $this->secondary_keywords        = $translation->secondary_keywords ?? '';
    $this->long_tail_keywords        = $translation->long_tail_keywords ?? '';
    $this->high_intent_keywords      = $translation->high_intent_keywords ?? '';

    // CKEditor fields (business_description, after_image_description) ko
    // visually refresh karne ke liye JS event bhejo
    $this->dispatch('languageSwitched');

    if (!$translation) {
        $this->dispatch('notify', [
            'message' => 'No translation exists for this language yet — you can create one.',
            'type' => 'info'
        ]);
    }
}

    protected function getValidationRules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:191',
                Rule::unique('business_translations', 'name')
                    ->where('lang_id', $this->languages_supported ?? 1)
                    ->ignore($this->businessId, 'business_id'),
            ],
            'affiliate_partner' => 'nullable|string|max:191',
            'countryIsAffiliate' => 'boolean',
            'affiliate_link' => $this->is_affiliate_partner ? 'required|url|max:191' : 'nullable',
            'icon_file' => 'nullable|mimes:png,svg|max:2048',
            'image_file' => 'nullable|mimes:jpg,png,svg|max:2048',
            'active_all_countries' => 'required|boolean',
            'headquaters' => 'nullable|string|max:191',
            'languages_supported' => 'required',
            'support_options' => 'nullable|string',
            'website_url' => 'nullable|url',
            'selectedCountries' => (int)$this->active_all_countries === 1 ? 'nullable|array' : 'required|array|min:1',
            'year_found' => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
            'meta_title' => 'nullable|string|max:191',
            'meta_description' => 'nullable|string|max:255',
            'admin_rating' => 'nullable|numeric|min:1|max:5',
            'description_title' => 'nullable|string|max:255',
            'business_description' => 'nullable|string',
            'alternatives_title' => 'nullable|string|max:255',
            'alternatives_description' => 'nullable|string',
            'alternatives_title_2' => 'nullable|string|max:255',
            'alternatives_description_2' => 'nullable|string',
            'reviews_title' => 'nullable|string|max:255',
            'reviews_description' => 'nullable|string',
            'reviews_title_2' => 'nullable|string|max:255',
            'reviews_description_2' => 'nullable|string',
            'faqs_title' => 'nullable|string|max:255',
            'faqs_description' => 'nullable|string',
            'faqs_title_2' => 'nullable|string|max:255',
            'faqs_description_2' => 'nullable|string',
            'comparison_title' => 'nullable|string|max:255',
            'comparison_description' => 'nullable|string',
            'comparison_title_2' => 'nullable|string|max:255',
            'comparison_description_2' => 'nullable|string',
            'short_description' => 'nullable|string',
            'after_image_description' => 'nullable|string',
            'slug' => [
                'required',
                'string',
                'max:191',
                'regex:/^[a-zA-Z0-9\-_]+$/',
                Rule::unique('business_translations', 'slug')->ignore($this->businessId, 'business_id'),
            ],
            'countryWebsiteUrls' => 'nullable',
            'features' => 'nullable',
            'status' => 'boolean',
            'is_affiliate_partner' => 'boolean',
            'newWebsiteUrl' => 'nullable|url',
            'selectedPricingOptions' => 'nullable|array',
            'selected_sub_categories' => 'nullable|array',
            'selectedFeatures' => 'nullable|array',
            'primary_keywords' => 'nullable|string|max:500',
            'secondary_keywords' => 'nullable|string|max:1000',
            'long_tail_keywords' => 'nullable|string|max:1000',
            'high_intent_keywords' => 'nullable|string|max:1000',
            'pro_cons_intro' => 'nullable|string',
            'pro_cons_summary' => 'nullable|string',
        ];
    }

    protected function validateForm()
    {
        return $this->validate($this->getValidationRules());
    }

    // Method to check if a field has an error and was touched
    public function hasError($field)
    {
        return isset($this->touchedFields[$field]) && $this->getErrorBag()->has($field);
    }

    // create
    protected function createBusiness()
    {

        // Handle file uploads
        $iconPath = null;
        $imagePath = null;

        try {
            if ($this->icon_file) {
                $iconPath = $this->storeFile($this->icon_file, 'business/icon');
                // $iconPath = $this->storeFile($this->icon_file, 'business_icon');
                // Log the file path for debugging
                Log::info("Icon file stored at: " . $iconPath);
            }

            if ($this->image_file) {
                $imagePath = $this->storeFile($this->image_file, 'business/image');
                // $imagePath = $this->storeFile($this->image_file, 'business_image');
                // Log the file path for debugging
                Log::info("Image file stored at: " . $imagePath);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Upload Error: ' . $e->getMessage());
        }

        return Business::create([
            'affiliate_partner' => $this->is_affiliate_partner ? $this->affiliate_partner : null,
            'affiliate_link' => $this->affiliate_link,
            'is_affiliate' => $this->is_affiliate,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'category_id' => $this->selected_category,
            'active_all_countries' => $this->active_all_countries,
            'icon_id' => $iconPath,
            'image_id' => $imagePath,
            'languages_supported' => $this->languages_supported,
            'created_by' => auth()->id(),
            'year_found' => $this->year_found,
            'permanent_url' => $this->permanent_url,
            'status' => (bool)$this->status,
            'admin_rating' => ($this->admin_rating !== '' && $this->admin_rating !== null) ? $this->admin_rating : null,
            'pro_cons_intro' => $this->pro_cons_intro,
            'pro_cons_summary' => $this->pro_cons_summary,
            'pro_cons_headline' => $this->pro_cons_headline,
        ]);
    }

    protected function createBusinessTranslation($business)
    {
        $slug = $this->slug ? Str::slug($this->slug) : Str::slug($this->name);
        $business->translations()->create([
            'name' => $this->name,
            'slug' => $slug,
            'lang_id' => $this->languages_supported,
            'headquarters' => $this->headquaters,
            'support_options' => $this->support_options,
            'description_title' => $this->description_title,
            'description' => $this->business_description,
            'alternatives_title' => $this->alternatives_title,
            'alternatives_description' => $this->alternatives_description,
            'alternatives_title_2' => $this->alternatives_title_2,
            'alternatives_description_2' => $this->alternatives_description_2,
            'reviews_title' => $this->reviews_title,
            'reviews_description' => $this->reviews_description,
            'reviews_title_2' => $this->reviews_title_2,
            'reviews_description_2' => $this->reviews_description_2,
            'faqs_title' => $this->faqs_title,
            'faqs_description' => $this->faqs_description,
            'faqs_title_2' => $this->faqs_title_2,
            'faqs_description_2' => $this->faqs_description_2,
            'comparison_title' => $this->comparison_title,
            'comparison_description' => $this->comparison_description,
            'comparison_title_2' => $this->comparison_title_2,
            'comparison_description_2' => $this->comparison_description_2,
            'short_description' => $this->short_description,
            'after_image_description' => $this->after_image_description,
            'primary_keywords' => $this->primary_keywords,
            'secondary_keywords' => $this->secondary_keywords,
            'long_tail_keywords' => $this->long_tail_keywords,
            'high_intent_keywords' => $this->high_intent_keywords,
            'status' => 1
        ]);
    }

    protected function updateBusinessData($business)
    {
        // Handle file uploads
        $iconPath = $business->icon_id;
        $imagePath = $business->image_id;

        try {
            if ($this->icon_file) {
                // Delete the old file if it exists
                if ($business->icon_id && Storage::disk('public')->exists($business->icon_id)) {
                    Storage::disk('public')->delete($business->icon_id);
                    Log::info("Deleted old icon file: " . $business->icon_id);
                }
                $iconPath = $this->storeFile($this->icon_file, 'business/icon');
                Log::info("Updated icon file stored at: " . $iconPath);
            }

            if ($this->image_file) {
                // Delete the old file if it exists
                if ($business->image_id && Storage::disk('public')->exists($business->image_id)) {
                    Storage::disk('public')->delete($business->image_id);
                    Log::info("Deleted old image file: " . $business->image_id);
                }
                $imagePath = $this->storeFile($this->image_file, 'business/image');
                Log::info("Updated image file stored at: " . $imagePath);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Upload Error: ' . $e->getMessage());
        }

        $business->update([
            'affiliate_partner' => $this->is_affiliate_partner ? $this->affiliate_partner : null,
            'affiliate_link' => $this->affiliate_link,
            'is_affiliate' => (int) $this->is_affiliate,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'category_id' => $this->selected_category,
            'active_all_countries' => $this->active_all_countries,
            'icon_id' => $iconPath,
            'image_id' => $imagePath,
            'languages_supported' => $this->languages_supported,
            'year_found' => $this->year_found,
            'permanent_url' => $this->permanent_url,
            'status' => (bool)$this->status,
            'admin_rating' => ($this->admin_rating !== '' && $this->admin_rating !== null) ? $this->admin_rating : null,
            'pro_cons_intro' => $this->pro_cons_intro,
            'pro_cons_summary' => $this->pro_cons_summary,
            'pro_cons_headline' => $this->pro_cons_headline,
        ]);
    }

    protected function updateBusinessTranslation($business)
    {
        $slug = $this->slug ? Str::slug($this->slug) : Str::slug($this->name);
        $business->translations()->updateOrCreate(
            ['business_id' => $business->id, 'lang_id' => $this->languages_supported],
            [
                'name' => $this->name,
                'slug' => $slug,
                'headquarters' => $this->headquaters,
                'support_options' => $this->support_options,
                'description_title' => $this->description_title,
                'description' => $this->business_description,
                'alternatives_title' => $this->alternatives_title,
                'alternatives_description' => $this->alternatives_description,
                'alternatives_title_2' => $this->alternatives_title_2,
                'alternatives_description_2' => $this->alternatives_description_2,
                'reviews_title' => $this->reviews_title,
                'reviews_description' => $this->reviews_description,
                'reviews_title_2' => $this->reviews_title_2,
                'reviews_description_2' => $this->reviews_description_2,
                'faqs_title' => $this->faqs_title,
                'faqs_description' => $this->faqs_description,
                'faqs_title_2' => $this->faqs_title_2,
                'faqs_description_2' => $this->faqs_description_2,
                'comparison_title' => $this->comparison_title,
                'comparison_description' => $this->comparison_description,
                'comparison_title_2' => $this->comparison_title_2,
                'comparison_description_2' => $this->comparison_description_2,
                'alternatives_meta_title' => $this->alternatives_meta_title,
                'alternatives_meta_description' => $this->alternatives_meta_description,
                'reviews_meta_title' => $this->reviews_meta_title,
                'reviews_meta_description' => $this->reviews_meta_description,
                'faqs_meta_title' => $this->faqs_meta_title,
                'faqs_meta_description' => $this->faqs_meta_description,
                'comparison_meta_title' => $this->comparison_meta_title,
                'comparison_meta_description' => $this->comparison_meta_description,
                'short_description' => $this->short_description,
                'after_image_description' => $this->after_image_description,
                'primary_keywords' => $this->primary_keywords,
                'secondary_keywords' => $this->secondary_keywords,
                'long_tail_keywords' => $this->long_tail_keywords,
                'high_intent_keywords' => $this->high_intent_keywords,
                'status' => 1
            ]
        );
    }

    protected function syncBusinessRelationships($business)
    {
        // Sync country relationships
        $business->countries()->sync((int)$this->active_all_countries === 1 ? [] : ($this->selectedCountries ?? []));


        // Sync sub categories
        $business->subCategories()->sync($this->selected_sub_categories ?? []);

        // Sync features
        $business->features()->sync($this->selectedFeatures);

        // Sync supported languages
        $business->supportedLanguages()->sync($this->lang_supported);


        $business->websites()->delete();


        // Sync website URLs for each country
        if (!empty($this->countryWebsiteUrls)) {
            foreach ($this->countryWebsiteUrls as $countryId => $urlData) {
                if (empty($urlData)) continue;

                // Normalize if nested
                if (isset($urlData[0]) && is_array($urlData[0])) {
                    $urlData = $urlData[0];
                }

                if (is_array($urlData)) {
                    $websiteUrl = !empty($urlData['url']) ? str_replace('\/', '/', $urlData['url']) : null;
                    $isAffiliate = !empty($urlData['is_affiliate']) ? 1 : 0;

                    $business->websites()->create([
                        'business_id' => $business->id,
                        'country_id' => $countryId,
                        'website_url' => $websiteUrl,
                        'is_affiliate' => $isAffiliate,
                        'status' => $urlData['status'] ?? 1
                    ]);
                }
            }
        }
    }
    public function updatedAffiliateLink($value)
    {
        // Auto-detect if URL looks like an affiliate link
        $affiliateKeywords = ['affiliate', 'aff', 'ref', 'partner', 'track', 'click', 'utm_'];

        foreach ($affiliateKeywords as $keyword) {
            if (stripos($value, $keyword) !== false) {
                $this->is_affiliate_partner = 1;
                break;
            }
        }
    }
    protected function storeFile($file, $directory)
    {
        if (!$file || !$file->isValid()) {
            throw new \Exception("Invalid file upload.");
        }
        try {
            $filename = time() . '_' . \Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path($directory);
            if (!\File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath, 0755, true);
            }
            $fullPath = $destinationPath . '/' . $filename;
            // Instead of copy(), use file_get_contents + file_put_contents
            $contents = file_get_contents($file->getRealPath());
            if ($contents === false || strlen($contents) === 0) {
                throw new \Exception("Uploaded file is empty or unreadable.");
            }
            file_put_contents($fullPath, $contents);
            return $directory . '/' . $filename;
        } catch (\Exception $e) {
            throw new \Exception("Upload failed: " . $e->getMessage());
        }
    }



    // public function deleteBusiness($id)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $business = Business::findOrFail($id);
    //         $business->countries()->detach();
    //         $business->languages()->detach();
    //         $business->features()->detach();
    //         $business->pricingOptions()->detach();
    //         $business->products()->detach();


    //         // Delete one-to-many related models
    //         $business->translations()->delete();
    //         $business->websites()->delete();
    //         $business->wishlists()->delete();
    //         $business->reviews()->delete();

    //         // Delete media files
    //         if ($business->icon_id && Storage::disk('public')->exists($business->icon_id)) {
    //             Storage::disk('public')->delete($business->icon_id);
    //         }

    //         if ($business->image_id && Storage::disk('public')->exists($business->image_id)) {
    //             Storage::disk('public')->delete($business->image_id);
    //         }

    //         // Delete the business
    //         $business->delete();

    //         DB::commit();

    //         session()->flash('success', 'Business deleted successfully.');
    //         return redirect()->route('business');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         session()->flash('error', 'An error occurred: ' . $e->getMessage());
    //     }
    // }


    // -----------------------------------------------------------------------
    // AI Output Upload — parse full AI-generated text blob into form fields
    // -----------------------------------------------------------------------

    public function parseAndApplyAiOutput(): void
    {
        $raw = trim($this->aiOutputText);

        if ($raw === '') {
            $this->aiApplyStatus = 'error';
            $this->aiApplyMessage = 'Please paste AI output content first.';
            return;
        }

        $sections = $this->parseAiSections($raw);

        if (empty($sections)) {
            $this->aiApplyStatus = 'error';
            $this->aiApplyMessage = 'Could not parse any sections. Make sure the format uses [section_key] markers.';
            return;
        }

        $populated = 0;

        // --- Scalar / plain-text fields ---
        $scalarMap = [
            'name'                       => 'name',
            'short_description'          => 'short_description',
            'description_title'          => 'description_title',
            'business_description'       => 'business_description',
            'pro_cons_headline'          => 'pro_cons_headline',
            'pro_cons_intro'             => 'pro_cons_intro',
            'pro_cons_summary'           => 'pro_cons_summary',
            'after_image_description'    => 'after_image_description',
            'alternatives_title'         => 'alternatives_title',
            'alternatives_description'   => 'alternatives_description',
            'alternatives_title_2'       => 'alternatives_title_2',
            'alternatives_description_2' => 'alternatives_description_2',
            'reviews_title'              => 'reviews_title',
            'reviews_description'        => 'reviews_description',
            'reviews_title_2'            => 'reviews_title_2',
            'reviews_description_2'      => 'reviews_description_2',
            'faqs_title'                 => 'faqs_title',
            'faqs_description'           => 'faqs_description',
            'faqs_title_2'               => 'faqs_title_2',
            'faqs_description_2'         => 'faqs_description_2',
            'comparison_title'           => 'comparison_title',
            'comparison_description'     => 'comparison_description',
            'comparison_title_2'         => 'comparison_title_2',
            'comparison_description_2'   => 'comparison_description_2',
            'meta_title'                    => 'meta_title',
            'meta_description'              => 'meta_description',
            'alternatives_meta_title'       => 'alternatives_meta_title',
            'alternatives_meta_description' => 'alternatives_meta_description',
            'reviews_meta_title'            => 'reviews_meta_title',
            'reviews_meta_description'      => 'reviews_meta_description',
            'faqs_meta_title'               => 'faqs_meta_title',
            'faqs_meta_description'         => 'faqs_meta_description',
            'comparison_meta_title'         => 'comparison_meta_title',
            'comparison_meta_description'   => 'comparison_meta_description',
        ];

        foreach ($scalarMap as $key => $property) {
            if (array_key_exists($key, $sections) && $sections[$key] !== '') {
                $this->{$property} = $sections[$key];
                $populated++;
            }
        }

        // --- Offerings array fields (nested) ---
        if (array_key_exists('offerings_headline', $sections) && $sections['offerings_headline'] !== '') {
            $this->businessOfferings[0]['headline'] = $sections['offerings_headline'];
            $populated++;
        }
        if (array_key_exists('offerings_top_text', $sections) && $sections['offerings_top_text'] !== '') {
            $this->businessOfferings[0]['top_text'] = $sections['offerings_top_text'];
            $populated++;
        }

        // --- USPs (each non-empty line = one USP, max 6) ---
        if (array_key_exists('usps', $sections) && $sections['usps'] !== '') {
            $lines = array_values(array_filter(
                array_map('trim', explode(PHP_EOL, $sections['usps'])),
                fn($l) => $l !== ''
            ));
            $usps = array_map(fn($t) => ['text' => $t], array_slice($lines, 0, 6));
            while (count($usps) < 5) {
                $usps[] = ['text' => ''];
            }
            $this->businessUsps = $usps;
            $populated++;
        }

        // --- Pros (each non-empty line = one pro) ---
        if (array_key_exists('pros', $sections) && $sections['pros'] !== '') {
            $lines = array_values(array_filter(
                array_map('trim', explode(PHP_EOL, $sections['pros'])),
                fn($l) => $l !== ''
            ));
            $this->businessPros = array_map(fn($t) => ['text' => $t], $lines);
            $populated++;
        }

        // --- Cons (each non-empty line = one con) ---
        if (array_key_exists('cons', $sections) && $sections['cons'] !== '') {
            $lines = array_values(array_filter(
                array_map('trim', explode(PHP_EOL, $sections['cons'])),
                fn($l) => $l !== ''
            ));
            $this->businessCons = array_map(fn($t) => ['text' => $t], $lines);
            $populated++;
        }

        // --- Rating Texts (Features, Ease of use, Value for money) ---
        $ratingKeys = [
            'features_intro_text'        => ['features', 'intro_text'],
            'features_end_text'          => ['features', 'end_text'],
            'ease_of_use_intro_text'     => ['ease_of_use', 'intro_text'],
            'ease_of_use_end_text'       => ['ease_of_use', 'end_text'],
            'value_for_money_intro_text' => ['value_for_money', 'intro_text'],
            'value_for_money_end_text'   => ['value_for_money', 'end_text'],
        ];
        foreach ($ratingKeys as $sectionKey => [$critKey, $fieldKey]) {
            if (array_key_exists($sectionKey, $sections) && $sections[$sectionKey] !== '') {
                $this->ratingTexts[$critKey][$fieldKey] = $sections[$sectionKey];
                $populated++;
            }
        }

        // --- Features (line by line or JSON array) ---
        if (array_key_exists('features', $sections) && $sections['features'] !== '') {
            $featureRaw = trim($sections['features']);
            $featureNames = [];
            if (str_starts_with($featureRaw, '[') && str_ends_with($featureRaw, ']')) {
                $decoded = json_decode($featureRaw, true);
                if (is_array($decoded)) {
                    foreach ($decoded as $item) {
                        if (is_array($item)) {
                            $name = trim($item['name'] ?? $item['feature_name'] ?? '');
                            if ($name) $featureNames[] = $name;
                        } elseif (is_string($item)) {
                            $featureNames[] = trim($item);
                        }
                    }
                }
            } else {
                $lines = array_values(array_filter(
                    array_map('trim', explode(PHP_EOL, $featureRaw)),
                    fn($l) => $l !== ''
                ));
                foreach ($lines as $l) {
                    $cleaned = preg_replace('/^[-*•\d.]+\s*/', '', $l);
                    if (!empty($cleaned)) $featureNames[] = $cleaned;
                }
            }

            if (!empty($featureNames)) {
                $lang_id = $this->lang_id ?? getCurrentLanguageID();
                $matchedFeatureIds = \DB::table('feature_translations')
                    ->where('lang_id', $lang_id)
                    ->whereIn('name', $featureNames)
                    ->pluck('feature_id')
                    ->toArray();

                if (empty($matchedFeatureIds)) {
                    $matchedFeatureIds = \DB::table('feature_translations')
                        ->where('lang_id', 1)
                        ->whereIn('name', $featureNames)
                        ->pluck('feature_id')
                        ->toArray();
                }

                if (!empty($matchedFeatureIds)) {
                    $this->selectedFeatures = array_values(array_unique(array_merge($this->selectedFeatures, $matchedFeatureIds)));
                    $this->dispatch('featuresLoaded', options: $this->selectedFeatures);
                    $populated++;
                }
            }
        }

        // --- Dispatch browser event so Alpine.js can update CKEditor (wire:ignore) fields ---
        $this->dispatch('ai-content-applied', fields: [
            'features_intro_text'        => $this->ratingTexts['features']['intro_text'] ?? '',
            'features_end_text'          => $this->ratingTexts['features']['end_text'] ?? '',
            'ease_of_use_intro_text'     => $this->ratingTexts['ease_of_use']['intro_text'] ?? '',
            'ease_of_use_end_text'       => $this->ratingTexts['ease_of_use']['end_text'] ?? '',
            'value_for_money_intro_text' => $this->ratingTexts['value_for_money']['intro_text'] ?? '',
            'value_for_money_end_text'   => $this->ratingTexts['value_for_money']['end_text'] ?? '',
            'business_description'       => $this->business_description,
            'pro_cons_intro'             => $this->pro_cons_intro,
            'pro_cons_summary'           => $this->pro_cons_summary,
            'offerings_top_text'         => $this->businessOfferings[0]['top_text'] ?? '',
            'after_image_description'    => $this->after_image_description,
            'alternatives_description'   => $this->alternatives_description,
            'alternatives_description_2' => $this->alternatives_description_2,
            'reviews_description'        => $this->reviews_description,
            'reviews_description_2'      => $this->reviews_description_2,
            'faqs_description'           => $this->faqs_description,
            'faqs_description_2'         => $this->faqs_description_2,
            'comparison_description'     => $this->comparison_description,
            'comparison_description_2'   => $this->comparison_description_2,
        ]);

        $this->aiApplyStatus = 'success';
        $this->aiApplyMessage = '\u2713 Content applied to ' . $populated . ' field group(s) successfully.';
    }

    /**
     * Parse a section-delimited AI output string into an associative array.
     * Format: each section starts with [key] on its own line; value is all text
     * until the next [key] marker or end of string.
     */
    private function parseAiSections(string $text): array
    {
        $sections = [];
        preg_match_all(
            '/^\[([a-z_0-9]+)\]\s*$(.+?)(?=^\[[a-z_0-9]+\]\s*$|\z)/ms',
            $text,
            $matches,
            PREG_SET_ORDER
        );
        foreach ($matches as $match) {
            $sections[strtolower($match[1])] = trim($match[2]);
        }
        return $sections;
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'website_url',
            'websiteUrls',
            'affiliate_partner',
            'affiliate_link',
            'is_affiliate_partner',
            'business_description',
            'features',
            'headquaters',
            'year_found',
            'languages_supported',
            'support_options',
            'permanent_url',
            'meta_title',
            'meta_description',
            'selected_category',
            'icon_file',
            'image_file',
            'icon_id',
            'image_id',
            'selectedCountries',
            'selectedCountry',
            'selectedCountryName',
            'businessId',
            'editMode',
            'isSubmitting',
            'selectedPricingOptions',
            'categoryFeatures',
            'selectedFeatures',
        ]);
        $this->is_affiliate_partner = true;
        $this->affiliate_link = '';
        $this->websiteUrls = [''];
        $this->features = ['', '', ''];
       $this->languages_supported = 1;
        $this->active_all_countries = 1;
        $this->loadCountries();
    }

    // AI Auto-fill methods (stubs)
    public function autoFillCountryData()
    {
        // Implement AI auto-fill for country data
        $this->dispatch('notify', ['message' => 'Auto-fill for country data will be implemented']);
    }

    public function setFieldIdAndOpenModal($fieldType, $modalId, $businessId)
    {
        $this->dispatch('openBusinessAiModal', [
            'fieldType' => $fieldType,
            'modalId' => $modalId,
            'businessId' => $businessId,
        ]);
    }

    public function autoFillFeatures()
    {
        // Implement AI auto-fill for features
        $this->dispatch('notify', ['message' => 'Auto-fill for features will be implemented']);
    }
    public function autoFillCompanyInfo()
    {
        // Implement AI auto-fill for company info
        $this->dispatch('notify', ['message' => 'Auto-fill for company info will be implemented']);
    }

    public function fullAutoFill()
    {
        // Implement full AI auto-fill
        $this->dispatch('notify', ['message' => 'Full auto-fill will be implemented']);
    }


    // FAQ
    public function manageFAQs($businessId)
    {
        $this->selectedBusinessForFAQ = $businessId;
        $this->loadBusinessFAQs();
        $this->showFAQSection = true;

        // Hide other sections if they're open
        $this->addbusiness = false;
        $this->editMode = false;
        $this->isSubmitting = false;
        // $this->showEditForm = false;
        // $this->showTranslateForm = false;
    }

    public function loadBusinessFAQs()
    {
        if (!$this->selectedBusinessForFAQ) return;

        $this->businessFaqCategories = BusinessFaqCategory::where('business_id', $this->selectedBusinessForFAQ)
            ->ordered()
            ->get()
            ->toArray();

        $this->businessFAQs = BusinessFaq::where('business_id', $this->selectedBusinessForFAQ)
            ->with(['translation' => function ($query) {
                $query->where('lang_id', $this->lang_id);
            }, 'category'])
            ->ordered()
            ->get()
            ->map(function ($faq, $index) {
                $translation = $faq->translation;
                return [
                    'id' => $faq->id,
                    'business_faq_category_id' => $faq->business_faq_category_id,
                    'category_name' => $faq->category->name ?? 'General / Uncategorized',
                    'question' => $translation->question ?? '',
                    'answer' => $translation->answer ?? '',
                    'position' => $faq->position,
                    'status' => $faq->status
                ];
            })
            ->toArray();
    }


    public function addFaqCategory()
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:100',
        ]);

        if (!$this->selectedBusinessForFAQ) return;

        $nextPos = BusinessFaqCategory::where('business_id', $this->selectedBusinessForFAQ)->max('position') + 1;
        BusinessFaqCategory::create([
            'business_id' => $this->selectedBusinessForFAQ,
            'name' => trim($this->newCategoryName),
            'position' => $nextPos,
            'status' => 1
        ]);

        $this->newCategoryName = '';
        $this->loadBusinessFAQs();
        session()->flash('message', 'FAQ Category added successfully!');
    }

    public function deleteFaqCategory($catId)
    {
        $cat = BusinessFaqCategory::where('business_id', $this->selectedBusinessForFAQ)->find($catId);
        if ($cat) {
            BusinessFaq::where('business_faq_category_id', $catId)->update(['business_faq_category_id' => null]);
            $cat->delete();
            $this->loadBusinessFAQs();
            session()->flash('message', 'FAQ Category deleted successfully!');
        }
    }

    public function addFAQ()
    {
        $this->validate([
            'faqQuestion' => 'required|string|max:500',
            'faqAnswer' => 'required|string|max:2000',
        ]);

        if (!$this->selectedBusinessForFAQ) return;

        DB::transaction(function () {
            // Get next position
            $nextPosition = BusinessFaq::where('business_id', $this->selectedBusinessForFAQ)
                ->max('position') + 1;

            // Create FAQ
            $faq = BusinessFaq::create([
                'business_id' => $this->selectedBusinessForFAQ,
                'business_faq_category_id' => !empty($this->faqCategoryId) ? $this->faqCategoryId : null,
                'position' => $nextPosition,
                'status' => 1
            ]);

            // Create translation
            BusinessFaqTranslation::create([
                'business_faq_id' => $faq->id,
                'lang_id' => $this->lang_id,
                'question' => $this->faqQuestion,
                'answer' => $this->faqAnswer
            ]);
        });

        $this->resetFAQForm();
        $this->loadBusinessFAQs();

        session()->flash('message', 'FAQ added successfully!');
    }

    public function editFAQ($faqId)
    {
        $faq = BusinessFaq::with(['translation' => function ($query) {
            $query->where('lang_id', $this->lang_id);
        }])->find($faqId);

        if (!$faq || !$faq->translation) return;

        $this->editingFAQId = $faqId;
        $this->faqCategoryId = $faq->business_faq_category_id;
        $this->faqQuestion = $faq->translation->question;
        $this->faqAnswer = $faq->translation->answer;
    }

    public function updateFAQ()
    {
        $this->validate([
            'faqQuestion' => 'required|string|max:500',
            'faqAnswer' => 'required|string|max:2000',
        ]);

        if (!$this->editingFAQId) return;

        DB::transaction(function () {
            // Update or create translation
            BusinessFaqTranslation::updateOrCreate(
                [
                    'business_faq_id' => $this->editingFAQId,
                    'lang_id' => $this->lang_id
                ],
                [
                    'question' => $this->faqQuestion,
                    'answer' => $this->faqAnswer
                ]
            );
        });

        $this->resetFAQForm();
        $this->loadBusinessFAQs();

        session()->flash('message', 'FAQ updated successfully!');
    }

    public function deleteFAQ($faqId)
    {
        DB::transaction(function () use ($faqId) {
            $faq = BusinessFaq::find($faqId);
            if (!$faq) return;

            $deletedPosition = $faq->position;

            // Delete the FAQ (translations will be deleted automatically due to cascade)
            $faq->delete();

            // Reorder remaining FAQs
            BusinessFaq::where('business_id', $this->selectedBusinessForFAQ)
                ->where('position', '>', $deletedPosition)
                ->decrement('position');
        });

        $this->loadBusinessFAQs();

        session()->flash('message', 'FAQ deleted successfully!');
    }

    public function reorderFAQs($orderedIds)
    {

        // dd($this->selectedBusinessForFAQ);
        if (!$this->selectedBusinessForFAQ) return;


        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $faqId) {
                BusinessFaq::where('id', $faqId)
                    ->where('business_id', $this->selectedBusinessForFAQ)
                    ->update(['position' => $index + 1]);
            }
        });

        $this->loadBusinessFAQs();

        session()->flash('message', 'FAQ order updated successfully!');
        // dd('here');
    }

    public function toggleFAQStatus($faqId)
    {
        $faq = BusinessFaq::find($faqId);
        if (!$faq) return;

        $faq->update(['status' => !$faq->status]);
        $this->loadBusinessFAQs();

        $status = $faq->status ? 'activated' : 'deactivated';
        session()->flash('message', "FAQ {$status} successfully!");
    }

    public function cancelFAQEdit()
    {
        $this->resetFAQForm();
    }

    public function closeFAQSection()
    {
        $this->showFAQSection = false;
        $this->resetFAQForm();
        $this->selectedBusinessForFAQ = null;
        $this->businessFAQs = [];
    }

    public function uploadFaqJson(): void
    {
        $raw = trim($this->faqJsonData);

        if ($raw === '') {
            $this->faqJsonApplyStatus = 'error';
            $this->faqJsonApplyMessage = 'Please paste JSON content first.';
            return;
        }

        if (!$this->selectedBusinessForFAQ) {
            $this->faqJsonApplyStatus = 'error';
            $this->faqJsonApplyMessage = 'No business selected for FAQ.';
            return;
        }

        $items = json_decode($raw, true);

        if (!is_array($items)) {
            $this->faqJsonApplyStatus = 'error';
            $this->faqJsonApplyMessage = 'Invalid JSON format. Expected an array of FAQ objects.';
            return;
        }

        $addedCount = 0;

        DB::transaction(function () use ($items, &$addedCount) {
            $currentPosition = BusinessFaq::where('business_id', $this->selectedBusinessForFAQ)
                ->max('position') ?? 0;

            foreach ($items as $item) {
                if (!is_array($item)) continue;

                $question = trim($item['question'] ?? $item['faqQuestion'] ?? $item['q'] ?? '');
                $answer = trim($item['answer'] ?? $item['faqAnswer'] ?? $item['a'] ?? '');
                $categoryName = trim($item['category'] ?? $item['category_name'] ?? $item['cat'] ?? '');

                if (empty($question) || empty($answer)) continue;

                $categoryId = null;
                if (!empty($categoryName)) {
                    $catObj = BusinessFaqCategory::where('business_id', $this->selectedBusinessForFAQ)
                        ->whereRaw('LOWER(name) = ?', [strtolower($categoryName)])
                        ->first();
                    if ($catObj) {
                        $categoryId = $catObj->id;
                    }
                }

                $currentPosition++;

                $faq = BusinessFaq::create([
                    'business_id' => $this->selectedBusinessForFAQ,
                    'business_faq_category_id' => $categoryId,
                    'position' => $currentPosition,
                    'status' => 1
                ]);

                BusinessFaqTranslation::create([
                    'business_faq_id' => $faq->id,
                    'lang_id' => $this->lang_id,
                    'question' => $question,
                    'answer' => $answer
                ]);

                $addedCount++;
            }
        });

        if ($addedCount > 0) {
            $this->loadBusinessFAQs();
            $this->faqJsonApplyStatus = 'success';
            $this->faqJsonApplyMessage = '✓ Successfully uploaded and created ' . $addedCount . ' FAQ entries.';
            $this->faqJsonData = '';
        } else {
            $this->faqJsonApplyStatus = 'error';
            $this->faqJsonApplyMessage = 'No valid FAQ entries found. Make sure each item has question and answer keys.';
        }
    }

    private function resetFAQForm()
    {
        $this->faqQuestion = '';
        $this->faqAnswer = '';
        $this->faqCategoryId = null;
        $this->editingFAQId = null;
        $this->resetValidation(['faqQuestion', 'faqAnswer']);
    }
}
