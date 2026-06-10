<?php

namespace App\Livewire\Vendor;

use Livewire\Component;
use App\Models\Business;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\BusinessChangeRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;



class EditListing extends Component
{
    use WithFileUploads;

    public $business;
    public $lang_id;
    public $name;
    public $description;
    public $editingField = null;

    public $newIcon;
    public $icon_id;
    public $website_url;         //Add website_url
    public $business_images;    //Add Business image


    //Feature & Pricing
    public $featuresText;
    public $startingPrice;
    public $freeTrialText;


    public $currency;
    public $timeUnit;
    public $additionalInfo;

    // Reviews & Feedback
    public $message;
    public $isInappropriate = false;
    public $reviewId;


    public $fieldMap = [
        'name' => ['type' => 'translation', 'field' => 'name'],
        'tagline' => ['type' => 'translation', 'field' => 'tagline'],
        'description' => ['type' => 'translation', 'field' => 'description'],

        //Add website Url
        'website_url' => ['type' => 'business', 'field' => 'website_url'],

        'icon_id' => ['type' => 'business', 'field' => 'icon_id'],
        'business_images' => ['type' => 'business', 'field' => 'business_images'],

        // Add this top feature & Pricings in
        'features' => ['type' => 'business', 'field' => 'features'],
        'starting_price' => ['type' => 'business', 'field' => 'starting_price'],
        'free_trial_text' => ['type' => 'business', 'field' => 'free_trial_text'],
        // Add more mappings as needed
    ];

    // Add Top Feature & Pricing
    // public $startingPrice, $currency, $timeUnit, $additionalInfo;
    public $alternativeBusiness, $averageRating, $easeOfUseAvg, $valueForMoneyAvg, $customerServiceAvg, $exclusiveFeatureAvg;
    public $ratingBreakdown, $totalReviews;
    public $link, $default_image;
    public $features, $starting_price, $free_trial_text; //add feature section



    public $languages;
    public $userInactiveReviewMessage;
    public $topReviews;
    public $allReviews;
    public $ourReviews;
    public $trustpilotReviews;
    public $reviewsPaginated; // if you need pagination


    public $newImages = [];
    public $replaceImages = []; // ['0' => UploadedFile, '2' => UploadedFile]
    public $deleteImageIndexes = [];



    public function mount(){
        if (!$this->lang_id) {
            $this->lang_id = getCurrentLanguageID();
        }

        $this->loadBusiness();

    }

    public function loadBusiness()
    {
        $this->business = Business::where('id', 20)
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $this->lang_id),
                'reviews' => fn($q) => $q
                    ->with('translations')
                    ->whereHas('translations', fn($q) => $q->where('language_id', $this->lang_id))
                    ->where('status', 'active'),
                'products' => fn($q) => $q->with([
                    'prices',
                    'translations' => fn($q) => $q->where('lang_id', $this->lang_id),
                ])->where(function ($query) {
                    $query->where('active_all_countries', 1)
                        ->orWhere(function ($q) {
                            $q->where('active_all_countries', 0)
                                ->whereHas('countries', fn($q) => $q->where('country_id', getCurrentCountry()));
                        });
                }),
                'category.translation' => fn($q) => $q->where('lang_id', $this->lang_id),
                'pricingOptions.translations' => fn($q) => $q->where('lang_id', $this->lang_id),
                'features.translations' => fn($q) => $q->where('lang_id', $this->lang_id),
                'websites' => fn($q) => $q->where('country_id', getCurrentCountry()),
            ])
            ->first();

        if (!$this->business) {
            $this->dispatch('swal:toast', [
                'type' => 'error',
                'message' => 'Business not found.',
            ]);
            return;
        }

        $this->languages = \App\Models\Language::all();

        // User
        $user = auth()->user();

        $this->userInactiveReviewMessage = null;
        if ($user) {
            $userReview = \App\Models\Review::where('business_id', $this->business->id)
                ->where('user_id', $user->id)
                ->where('status', 'inactive')
                ->first();

            if ($userReview) {
                $this->userInactiveReviewMessage = 'Your review for this product has been deactivated by the admin.';
            }
        }

        // Translations
        $translation = $this->business->translations->firstWhere('lang_id', $this->lang_id);
        $this->name = $translation?->name ?? '';
        $this->description = $translation?->description ?? '';

        // Pricing
        $price = getBusinessesWithStartingPrice($this->business);
        $this->startingPrice = $price[0]['starting_price']['amount'] ?? null;
        $this->currency = $price[0]['starting_price']['currency'] ?? '$';
        $this->timeUnit = ucfirst($price[0]['starting_price']['time_unit'] ?? 'Month');
        $this->additionalInfo = $price[0]['starting_price']['additional_info'] ?? 'NA';

        // Ratings Averages
        $reviews = \App\Models\Review::where('business_id', $this->business->id)->get();
        $this->averageRating = round($reviews->avg('rating'), 1);
        $this->easeOfUseAvg = round($reviews->avg('ease_of_use_rating'), 1);
        $this->valueForMoneyAvg = round($reviews->avg('value_for_money_rating'), 1);
        $this->customerServiceAvg = round($reviews->avg('customer_service_rating'), 1);
        $this->exclusiveFeatureAvg = round($reviews->avg('exclusive_service_rating'), 1);


        // Reviews
        $this->topReviews = \App\Models\Review::with([
            'user',
            'business',
            'translations' => fn($q) => $q->where('language_id', $this->lang_id),
        ])
            ->where('business_id', $this->business->id)
            ->whereHas('translations', fn($q) => $q->where('language_id', $this->lang_id))
            ->orderByDesc('rating')
            ->take(3)
            ->get();

        $this->allReviews = \App\Models\Review::with([
            'user',
            'translations' => fn($q) => $q->where('language_id', $this->lang_id),
        ])
            ->where('business_id', $this->business->id)
            ->whereHas('translations', fn($q) => $q->where('language_id', $this->lang_id))
            ->latest()
            ->get();

        $this->ourReviews = \App\Models\Review::with([
            'user',
            'translations' => fn($q) => $q->where('language_id', $this->lang_id),
        ])
            ->where('business_id', $this->business->id)
            ->where('user_id', auth()->id())
            ->whereHas('translations', fn($q) => $q->where('language_id', $this->lang_id))
            ->latest()
            ->get();

        $this->trustpilotReviews = \App\Models\Review::with([
            'user',
            'business',
            'translations' => fn($q) => $q->where('language_id', $this->lang_id),
        ])
            ->where('business_id', $this->business->id)
            ->whereHas('translations', fn($q) => $q->where('language_id', $this->lang_id))
            ->latest()
            ->get();

        // Rating breakdown
        $userRatingCounts = \App\Models\Review::where('business_id', $this->business->id)
            ->selectRaw('ROUND(rating) as rounded_rating, COUNT(DISTINCT user_id) as user_count')
            ->groupBy('rounded_rating')
            ->pluck('user_count', 'rounded_rating');

        $this->ratingBreakdown = collect(range(1, 5))->mapWithKeys(fn($i) => [$i => $userRatingCounts->get($i, 0)]);
        $this->totalReviews = $this->business->reviews->where('status', 'active')->count();

        // Alternatives
        $this->alternativeBusiness = Business::where('category_id', $this->business->category_id)
            ->where('id', '!=', $this->business->id)
            ->where('status', 1)
            ->where(function ($query) {
                $query->where('active_all_countries', 1)
                    ->orWhereHas('countries', fn($q) => $q->where('country_id', getCurrentCountry()));
            })
            ->whereHas('languages', fn($q) => $q->where('language_id', $this->lang_id))
            ->with([
                'translations' => fn($q) => $q->where('lang_id', $this->lang_id),
                'reviews' => fn($q) => $q->where('status', 'active'),
                'websites' => fn($q) => $q->where('country_id', getCurrentCountry()),
            ])
            ->withCount([
                'reviews as average_rating' => fn($q) => $q->select(DB::raw('coalesce(avg(rating),0)')),
            ])
            ->orderByDesc('average_rating')
            ->limit(3)
            ->get() ?? collect();

        $this->link = $this->business->websites->first()->website_url
            ?? $this->business->affiliate_link
            ?? $this->business->permanent_url
            ?? '#';

        $this->default_image = \App\Models\WebSetting::where('key', 'user_default_image')->value('value');
    }

    public function startEditing($field){
        $this->editingField = $field;
    }


    // public function saveAllFields($field)
    // {
    //     $fieldsToSave = [];

    //     if ($field === 'name_and_icon') {
    //         $fieldsToSave = ['name', 'icon_id' ,'website_url'];
    //     } elseif ($field === 'description_and_images') {
    //         $fieldsToSave = ['description', 'business_images'];
    //     } elseif ($field) {
    //         $fieldsToSave = [$field];
    //     } else {
    //         $fieldsToSave = [$this->editingField];
    //     }

    //     $models = [
    //         'business' => $this->business,
    //         'translation' => $this->business->translations->firstWhere('lang_id', $this->lang_id),
    //     ];

    //     $changedFields = [];

    //     foreach ($fieldsToSave as $singleField) {
    //         if (!isset($this->fieldMap[$singleField])) continue;

    //         $map = $this->fieldMap[$singleField];
    //         $model = $models[$map['type']] ?? null;

    //         if (!$model) continue;
    //         $value = null;

    //         // === ICON Upload ===
    //         if ($singleField === 'icon_id') {
    //             if (!$this->newIcon) {
    //                 continue; //Skip if no new icon is uploaded
    //             }

    //             $path = $this->storeFile($this->newIcon, 'business_icon/pending');
    //             $value = $path;

    //             if ($model->{$map['field']} === $value) {
    //                 continue; //Skip if value is unchanged
    //             }

    //             // Save change request
    //             \App\Models\BusinessChangeRequest::create([
    //                 'business_id' => $this->business->id,
    //                 'field'       => $singleField,
    //                 'type'        => $map['type'],
    //                 'column'      => $map['field'],
    //                 'value'       => $value,
    //                 'lang_id'     => $map['type'] === 'translation' ? $this->lang_id : null,
    //                 'requested_by'=> auth()->id(),
    //                 'status'      => 'pending',
    //                 'action_type' => 'replace',
    //             ]);

    //             $changedFields[] = str_replace('_', ' ', $singleField);
    //             continue; //Prevent falling into regular field block
    //         }

    //         // === BUSINESS IMAGES Handling with Proper Indexing ===
    //         elseif ($singleField === 'business_images') {
    //             $existing = $model->{$map['field']} ?? [];
    //             if (!is_array($existing)) {
    //                 $existing = json_decode($existing, true) ?? [];
    //             }

    //             $hasChanges = false;

    //             // === HANDLE DELETIONS ===
    //             if (!empty($this->deleteImageIndexes)) {
    //                 $deletePayload = [];

    //                 foreach ($this->deleteImageIndexes as $index) {
    //                     if (isset($existing[$index])) {
    //                         $deletePayload[$index] = $existing[$index]; // Store index => path
    //                     }
    //                 }

    //                 if (!empty($deletePayload)) {
    //                     \App\Models\BusinessChangeRequest::create([
    //                         'business_id'  => $this->business->id,
    //                         'field'        => $singleField,
    //                         'type'         => $map['type'],
    //                         'column'       => $map['field'],
    //                         'value'        => json_encode($deletePayload), // {0: "path1", 2: "path2"}
    //                         'lang_id'      => $map['type'] === 'translation' ? $this->lang_id : null,
    //                         'requested_by' => auth()->id(),
    //                         'status'       => 'pending',
    //                         'action_type'  => 'delete',
    //                     ]);

    //                     $changedFields[] = str_replace('_', ' ', $singleField);
    //                     $hasChanges = true;
    //                 }
    //             }

    //             // === HANDLE REPLACEMENTS ===
    //             if (!empty($this->replaceImages)) {
    //                 $replacePayload = [];

    //                 foreach ($this->replaceImages as $index => $uploadedFile) {
    //                     if (isset($existing[$index]) && $uploadedFile) {
    //                         $path = $this->storeFile($uploadedFile, 'business_gallery/pending');
    //                         $replacePayload[$index] = [
    //                             'old' => $existing[$index], // Original image path
    //                             'new' => $path              // New image path
    //                         ];
    //                     }
    //                 }

    //                 if (!empty($replacePayload)) {
    //                     \App\Models\BusinessChangeRequest::create([
    //                         'business_id'  => $this->business->id,
    //                         'field'        => $singleField,
    //                         'type'         => $map['type'],
    //                         'column'       => $map['field'],
    //                         'value'        => json_encode($replacePayload), // {0: {old: "path1", new: "new_path1"}}
    //                         'lang_id'      => $map['type'] === 'translation' ? $this->lang_id : null,
    //                         'requested_by' => auth()->id(),
    //                         'status'       => 'pending',
    //                         'action_type'  => 'replace',
    //                     ]);

    //                     $changedFields[] = str_replace('_', ' ', $singleField);
    //                     $hasChanges = true;
    //                 }
    //             }

    //             // === HANDLE ADDITIONS ===
    //             if (!empty($this->newImages)) {
    //                 $addPayload = [];

    //                 foreach ($this->newImages as $image) {
    //                     if ($image) {
    //                         $path = $this->storeFile($image, 'business_gallery/pending');
    //                         $addPayload[] = $path; // Simple array for new images
    //                     }
    //                 }

    //                 if (!empty($addPayload)) {
    //                     \App\Models\BusinessChangeRequest::create([
    //                         'business_id'  => $this->business->id,
    //                         'field'        => $singleField,
    //                         'type'         => $map['type'],
    //                         'column'       => $map['field'],
    //                         'value'        => json_encode($addPayload), // ["new_path1", "new_path2"]
    //                         'lang_id'      => $map['type'] === 'translation' ? $this->lang_id : null,
    //                         'requested_by' => auth()->id(),
    //                         'status'       => 'pending',
    //                         'action_type'  => 'add',
    //                     ]);

    //                     $changedFields[] = str_replace('_', ' ', $singleField);
    //                     $hasChanges = true;
    //                 }
    //             }

    //             // Skip to next field since we handled all image operations
    //             if ($hasChanges) {
    //                 continue;
    //             }
    //         }

    //         // === Regular Fields (like name, description) ===
    //         else {
    //             $value = $this->{$singleField};
    //             if ($model->{$map['field']} == $value) continue;

    //             // === SAVE as BusinessChangeRequest ===
    //             \App\Models\BusinessChangeRequest::create([
    //                 'business_id' => $this->business->id,
    //                 'field' => $singleField,
    //                 'type' => $map['type'],
    //                 'column' => $map['field'],
    //                 'value' => is_array($value) ? json_encode($value) : $value,
    //                 'lang_id' => $map['type'] === 'translation' ? $this->lang_id : null,
    //                 'requested_by' => auth()->id(),
    //                 'status' => 'pending',
    //                 'action_type' => 'update',
    //             ]);

    //             $changedFields[] = str_replace('_', ' ', $singleField);
    //         }
    //     }

    //     // === FEEDBACK ===
    //     if (count($changedFields) > 0) {
    //         $message = implode(' & ', array_map('ucfirst', $changedFields)) . ' change sent for approval.';
    //         $this->dispatch('swal:toast', ['type' => 'success', 'message' => $message]);
    //     } else {
    //         $this->dispatch('swal:toast', ['type' => 'info', 'message' => 'No changes made.']);
    //     }


    //     // Feature & Pricing or Free Trial
    //     // Initialize flag
    //     $changesMade = false;

    //     // Validation
    //     $this->validate([
    //         'featuresText'   => 'nullable|string',
    //         'startingPrice'  => 'nullable|numeric|min:0',
    //         'freeTrialText'  => 'nullable|string|max:500',
    //     ]);

    //     // --- Features ---
    //     $newFeatures = preg_split("/\r\n|\r|\n/", $this->featuresText);
    //     $newFeatures = array_filter(array_map('trim', $newFeatures)); // remove empty

    //     $currentFeatures = $this->business->features->map(fn($f) =>
    //         trim($f->translations->firstWhere('lang_id', $this->lang_id)?->name ?? $f->name)
    //     )->filter()->values()->toArray();

    //     // Save only if there is a real change
    //     if (!empty($newFeatures) && $newFeatures !== $currentFeatures) {
    //         BusinessChangeRequest::create([
    //             'business_id' => $this->business->id,
    //             'field'       => 'features',
    //             'type'        => 'business',
    //             'column'      => 'features',
    //             'value'       => json_encode($newFeatures),
    //             'requested_by'=> auth()->id(),
    //             'status'      => 'pending',
    //             'action_type' => 'update',
    //         ]);
    //         $changesMade = true;
    //     }

    //     // --- Starting Price ---
    //     if ($this->startingPrice !== null && $this->startingPrice != $this->business->starting_price) {
    //         BusinessChangeRequest::create([
    //             'business_id' => $this->business->id,
    //             'field'       => 'starting_price',
    //             'type'        => 'business',
    //             'column'      => 'starting_price',
    //             'value'       => $this->startingPrice,
    //             'requested_by'=> auth()->id(),
    //             'status'      => 'pending',
    //             'action_type' => 'update',
    //         ]);
    //         $changesMade = true;
    //     }

    //     // --- Free Trial ---
    //     $currentFreeTrial = trim($this->business->free_trial_text ?? '');
    //     $newFreeTrial = trim($this->freeTrialText ?? '');
    //     if (!empty($newFreeTrial) && $newFreeTrial !== $currentFreeTrial) {
    //         BusinessChangeRequest::create([
    //             'business_id' => $this->business->id,
    //             'field'       => 'free_trial_text',
    //             'type'        => 'business',
    //             'column'      => 'free_trial_text',
    //             'value'       => $newFreeTrial,
    //             'requested_by'=> auth()->id(),
    //             'status'      => 'pending',
    //             'action_type' => 'update',
    //         ]);
    //         $changesMade = true;
    //     }

    //     // --- FEEDBACK ---
    //     if ($changesMade) {
    //         $this->dispatch('swal:toast', [
    //             'type' => 'success',
    //             'message' => 'Features, Price & Trial sent for approval.'
    //         ]);
    //     } else {
    //         $this->dispatch('swal:toast', [
    //             'type' => 'info',
    //             'message' => 'No changes were made.'
    //         ]);
    //     }
    //     // End Feature & Pricing or Free Trial

    //     // === RESET STATE ===
    //     $this->editingField = null;
    //     $this->newIcon = null;
    //     $this->newImages = [];
    //     $this->replaceImages = [];
    //     $this->deleteImageIndexes = [];

    //     $this->loadBusiness();
    // }


    public function saveAllFields($field)

{
    // dump($this->newIcon);
    // dump($this->replaceImages);
    // dd($field);
    $fieldsToSave = [];

    if ($field === 'name_and_icon') {
        $fieldsToSave = ['name', 'icon_id', 'website_url'];
    } elseif ($field === 'description_and_images') {
        $fieldsToSave = ['description', 'business_images'];
    } elseif ($field) {
        $fieldsToSave = [$field];
    } else {
        $fieldsToSave = [$this->editingField];
    }

    $models = [
        'business' => $this->business,
        'translation' => $this->business->translations->firstWhere('lang_id', $this->lang_id),
    ];

    $changedFields = [];

    foreach ($fieldsToSave as $singleField) {
        if (!isset($this->fieldMap[$singleField])) continue;

        $map = $this->fieldMap[$singleField];
        $model = $models[$map['type']] ?? null;

        if (!$model) continue;

        // ICON
        if ($singleField === 'icon_id' && $this->newIcon) {
            $path = $this->storeFile($this->newIcon, 'business_gallery/pending');
            // dd($path);
            // dd('end');

            if ($model->{$map['field']} !== $path) {
                BusinessChangeRequest::create([
                    'business_id' => $this->business->id,
                    'field' => $singleField,
                    'type' => $map['type'],
                    'column' => $map['field'],
                    'value' => $path,
                    'lang_id' => $map['type'] === 'translation' ? $this->lang_id : null,
                    'requested_by'=> auth()->id(),
                    'status' => 'pending',
                    'action_type' => 'replace',
                ]);
                $changedFields[] = str_replace('_', ' ', $singleField);
            }
            continue;
        }

        // BUSINESS IMAGES
        if ($singleField === 'business_images') {
            $existing = $model->{$map['field']} ?? [];
            if (!is_array($existing)) $existing = json_decode($existing, true) ?? [];
            // dd($singleField);
            $hasChanges = false;

            // Deletions
            if (!empty($this->deleteImageIndexes)) {
                $deletePayload = [];
                foreach ($this->deleteImageIndexes as $index) {
                    if (isset($existing[$index])) $deletePayload[$index] = $existing[$index];
                }
                if (!empty($deletePayload)) {
                    BusinessChangeRequest::create([
                        'business_id' => $this->business->id,
                        'field' => $singleField,
                        'type' => $map['type'],
                        'column' => $map['field'],
                        'value' => json_encode($deletePayload),
                        'lang_id' => $map['type'] === 'translation' ? $this->lang_id : null,
                        'requested_by'=> auth()->id(),
                        'status' => 'pending',
                        'action_type' => 'delete',
                    ]);
                    $changedFields[] = str_replace('_', ' ', $singleField);
                    $hasChanges = true;
                }
            }

            // Replacements
            if (!empty($this->replaceImages)) {
                $replacePayload = [];
                foreach ($this->replaceImages as $index => $file) {
                    if (isset($existing[$index])) {
                        $path = $this->storeFile($file, 'business_gallery/pending');
                        $replacePayload[$index] = ['old' => $existing[$index], 'new' => $path];
                    }
                }
                if (!empty($replacePayload)) {
                    BusinessChangeRequest::create([
                        'business_id' => $this->business->id,
                        'field' => $singleField,
                        'type' => $map['type'],
                        'column' => $map['field'],
                        'value' => json_encode($replacePayload),
                        'lang_id' => $map['type'] === 'translation' ? $this->lang_id : null,
                        'requested_by'=> auth()->id(),
                        'status' => 'pending',
                        'action_type' => 'replace',
                    ]);
                    $changedFields[] = str_replace('_', ' ', $singleField);
                    $hasChanges = true;
                }
            }

            // Additions
            if (!empty($this->newImages)) {
                $addPayload = [];
                foreach ($this->newImages as $file) {
                    $path = $this->storeFile($file, 'business_gallery/pending');
                    $addPayload[] = $path;
                }
                if (!empty($addPayload)) {
                    BusinessChangeRequest::create([
                        'business_id' => $this->business->id,
                        'field' => $singleField,
                        'type' => $map['type'],
                        'column' => $map['field'],
                        'value' => json_encode($addPayload),
                        'lang_id' => $map['type'] === 'translation' ? $this->lang_id : null,
                        'requested_by'=> auth()->id(),
                        'status' => 'pending',
                        'action_type' => 'add',
                    ]);
                    $changedFields[] = str_replace('_', ' ', $singleField);
                    $hasChanges = true;
                }
            }


            if ($hasChanges) continue;
        }

        // Regular fields
        $value = $this->{$singleField};
        if ($model->{$map['field']} != $value) {
            BusinessChangeRequest::create([
                'business_id' => $this->business->id,
                'field' => $singleField,
                'type' => $map['type'],
                'column' => $map['field'],
                'value' => $value,
                'lang_id' => $map['type'] === 'translation' ? $this->lang_id : null,
                'requested_by'=> auth()->id(),
                'status' => 'pending',
                'action_type' => 'update',
            ]);
            $changedFields[] = str_replace('_', ' ', $singleField);
        }
    }

    if ($changedFields) {
        $this->dispatch('swal:toast', ['type' => 'success', 'message' => implode(' & ', $changedFields).' change sent for approval.']);
    } else {
        $this->dispatch('swal:toast', ['type' => 'info', 'message' => 'No changes made.']);
    }

    $this->editingField = null;
    $this->newIcon = null;
    $this->newImages = [];
    $this->replaceImages = [];
    $this->deleteImageIndexes = [];

    $this->loadBusiness();
}


// Freature section
public function saveFeatureAndPricing()
{
    $changesMade = false;

    $this->validate([
        'featuresText'   => 'nullable|string',
        'startingPrice'  => 'nullable|numeric|min:0',
        'freeTrialText'  => 'nullable|string|max:500',
    ]);

    // Features
    $newFeatures = array_filter(array_map('trim', preg_split("/\r\n|\r|\n/", $this->featuresText)));
    $currentFeatures = $this->business->features->map(fn($f) =>
        trim($f->translations->firstWhere('lang_id', $this->lang_id)?->name ?? $f->name)
    )->filter()->values()->toArray();

    if (!empty($newFeatures) && $newFeatures !== $currentFeatures) {
        BusinessChangeRequest::create([
            'business_id' => $this->business->id,
            'field' => 'features',
            'type' => 'business',
            'column' => 'features',
            'value' => json_encode($newFeatures),
            'requested_by'=> auth()->id(),
            'status' => 'pending',
            'action_type' => 'update',
        ]);
        $changesMade = true;
    }

    // Starting Price
    if ($this->startingPrice !== null && $this->startingPrice != $this->business->starting_price) {
        BusinessChangeRequest::create([
            'business_id' => $this->business->id,
            'field' => 'starting_price',
            'type' => 'business',
            'column' => 'starting_price',
            'value' => $this->startingPrice,
            'requested_by'=> auth()->id(),
            'status' => 'pending',
            'action_type' => 'update',
        ]);
        $changesMade = true;
    }

    // Free Trial
    $newFreeTrial = trim($this->freeTrialText ?? '');
    $currentFreeTrial = trim($this->business->free_trial_text ?? '');
    if (!empty($newFreeTrial) && $newFreeTrial !== $currentFreeTrial) {
        BusinessChangeRequest::create([
            'business_id' => $this->business->id,
            'field' => 'free_trial_text',
            'type' => 'business',
            'column' => 'free_trial_text',
            'value' => $newFreeTrial,
            'requested_by'=> auth()->id(),
            'status' => 'pending',
            'action_type' => 'update',
        ]);
        $changesMade = true;
    }

    if ($changesMade) {
        $this->dispatch('swal:toast', ['type'=>'success','message'=>'Features, Price & Trial sent for approval.']);
    } else {
        $this->dispatch('swal:toast', ['type'=>'info','message'=>'No changes were made.']);
    }

    $this->loadBusiness();
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

    public function deleteImage($index)
    {
        if (!isset($this->business->business_images[$index])) return;

        // Push index to delete queue (preserve the actual index)
        $this->deleteImageIndexes[] = (int)$index;

        // Mark the section as "dirty" so Livewire knows there's a change
        $this->editingField = 'description';
    }

    // Helper method to replace image at specific index
    public function replaceImage($index, $newImage)
    {
        if (!isset($this->business->business_images[$index])) return;

        // Store replacement with preserved index
        $this->replaceImages[(int)$index] = $newImage;

        // Mark the section as "dirty"
        $this->editingField = 'description';
    }


    public function updatedLangId()
    {
        // dd('Lang Changed To: ' . $this->lang_id); // Check in laravel.log
        $this->loadBusiness();
    }

    protected $rules = [
        'message' => 'required|string|max:500',
    ];


    public function render()
    {
        return view('livewire.vendor.edit-listing');
    }
}
