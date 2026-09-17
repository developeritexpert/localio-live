<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Business;
use App\Models\Language;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BusinessImages extends Component
{
    use WithFileUploads;

    public $lang_id = 1;
    public $businesses = [];

    // Modal state for Manage Images
    public $showImageModal = false;
    public $selectedBusinessId = null;
    public $selectedBusinessName = '';
    public $currentImages = []; // Draft copy of existing images
    public $newUploads = []; // Temporary uploaded files
    public $isTakingScreenshots = false;


    public $screenshotCategories = [];
    public $selectedScreenshotCategory = null;
    public $newScreenshotCategory = '';

    public $categoryPage = 1;
    public $categoriesPerPage = 5;

    public $editingScreenshotCategoryIndex = null;
    public $editingScreenshotCategoryName = '';

    public $showEditCategoryModal = false;
    public $editingCategoryIndex = null;
    public $editingCategoryName = '';

    public $categoryToDeleteIndex = null;
    public $categoryToDeleteName = '';




    private $logCollector = [];

    private function addConsoleLog($msg)
    {
        $this->logCollector[] = $msg;
        Log::info($msg);
    }

    // Modal state for Screenshot URLs
    public $showUrlModal = false;
    public $screenshotUrls = ['', '', '', '', ''];

    // Region Mapping Properties
    public $allLanguages = [];
    public $searchLanguage = '';
    public $categoryTargetMode = 'all'; // 'all' or 'specific'
    public $categoryTargetRegions = [];

    protected $listeners = [
        'closeModal' => 'closeModals'
    ];

    public function mount()
    {
        $this->allLanguages = Language::where('status', 1)->get()->toArray();
        $this->loadBusinesses();
    }

    public function loadBusinesses()
    {
        $this->businesses = Business::with(['translations' => function ($query) {
            $query->where('lang_id', $this->lang_id);
        }])->get();
    }

    public function render()
    {
        return view('livewire.business-images');
    }

    // --- Manage Images Modal Actions ---

    public function openImageModal($businessId)
    {
        $business = Business::with('translations')->findOrFail($businessId);

        $this->selectedBusinessId = $businessId;

        $this->selectedBusinessName = $business->translations->firstWhere('lang_id', $this->lang_id)->name
            ?? $business->translations->first()?->name
            ?? 'Business #' . $businessId;

        // Get the category which should currently be displayed
        $selectedCategoryName = trim(
            $business->screenshot_display_category ?? ''
        );

        $categories = $business->screenshot_categories ?? [];

        $activeCategory = null;

        /*
|--------------------------------------------------------------------------
| 1. Business has explicitly selected a category
|--------------------------------------------------------------------------
*/
        if (!empty($selectedCategoryName)) {

            $activeCategory = collect($categories)
                ->first(function ($category) use ($selectedCategoryName) {

                    return strcasecmp(
                        trim($category['name'] ?? ''),
                        $selectedCategoryName
                    ) === 0;
                });
        }

        /*
|--------------------------------------------------------------------------
| 2. No explicit category selected
|    -> use the category currently marked as DEFAULT
|--------------------------------------------------------------------------
*/
        if (!$activeCategory) {

            $activeCategory = collect($categories)
                ->first(function ($category) {

                    return !empty($category['is_default']);
                });
        }

        /*
|--------------------------------------------------------------------------
| 3. Show images from the active category
|--------------------------------------------------------------------------
*/
        if ($activeCategory) {

            $this->currentImages = $activeCategory['images'] ?? [];
        } else {

            /*
    |--------------------------------------------------------------------------
    | 4. No category and no DEFAULT category
    |--------------------------------------------------------------------------
    */
            $this->currentImages = $business->business_images ?? [];
        }

        $this->newUploads = [];
        $this->showImageModal = true;

        $this->dispatch('show-manage-images-modal');
    }

    public function removeCurrentImage($index)
    {
        if (isset($this->currentImages[$index])) {
            unset($this->currentImages[$index]);
            $this->currentImages = array_values($this->currentImages);
        }
    }

    public function closeManageCategoriesModal()
    {
        $this->dispatch('return-to-screenshot-url-modal');
    }

    public function removeNewUpload($index)
    {
        if (isset($this->newUploads[$index])) {
            unset($this->newUploads[$index]);
            $this->newUploads = array_values($this->newUploads);
        }
    }

    public function takeScreenshots()
    {
        $this->isTakingScreenshots = true;
        $this->logCollector = [];

        $this->addConsoleLog("Starting Automated Screenshot capture process...");

        // Increase script time limit on slower hosts
        try {
            if (function_exists('set_time_limit')) {
                set_time_limit(120);
                $this->addConsoleLog("Extended script execution time limit to 120s.");
            }
        } catch (\Exception $e) {
            $this->addConsoleLog("Failed to extend script time limit: " . $e->getMessage());
        }

        $business = Business::with('websites')->findOrFail($this->selectedBusinessId);
        $urls = [];

        // 1. First priority: Selected Screenshot Category
        $selectedCategoryName = trim($business->screenshot_display_category ?? '');

        if (!empty($selectedCategoryName)) {

            $selectedCategory = collect($business->screenshot_categories ?? [])
                ->first(function ($category) use ($selectedCategoryName) {
                    return strcasecmp(
                        trim($category['name'] ?? ''),
                        $selectedCategoryName
                    ) === 0;
                });

            if ($selectedCategory) {
                $categoryUrls = $selectedCategory['urls'] ?? [];

                $urls = array_values(array_filter($categoryUrls, function ($url) {
                    return !empty(trim($url));
                }));

                $this->addConsoleLog(
                    "Selected screenshot category: " . $selectedCategoryName
                );

                $this->addConsoleLog(
                    "Category URLs found: " . count($urls)
                );
            } else {
                $this->addConsoleLog(
                    "Selected screenshot category not found: " . $selectedCategoryName
                );
            }
        }

        // 2. Second priority: Default Screenshot Category
        if (empty($urls)) {

            $defaultCategory = collect($business->screenshot_categories ?? [])
                ->firstWhere('is_default', true);

            if ($defaultCategory) {

                $defaultUrls = $defaultCategory['urls'] ?? [];

                $urls = array_values(array_filter($defaultUrls, function ($url) {
                    return !empty(trim($url));
                }));

                if (!empty($urls)) {
                    $this->addConsoleLog(
                        "Using default screenshot category: " .
                            ($defaultCategory['name'] ?? 'Default')
                    );

                    $this->addConsoleLog(
                        "Default category URLs found: " . count($urls)
                    );
                }
            }
        }

        // 3. Third priority: Legacy screenshot_urls
        if (empty($urls) && !empty($business->screenshot_urls) && is_array($business->screenshot_urls)) {

            $urls = array_values(array_filter($business->screenshot_urls, function ($url) {
                return !empty(trim($url));
            }));

            if (!empty($urls)) {
                $this->addConsoleLog("Using legacy screenshot_urls.");
            }
        }

        // 4. Fourth priority: affiliate_link
        if (empty($urls) && !empty(trim($business->affiliate_link ?? ''))) {
            $urls[] = trim($business->affiliate_link);

            $this->addConsoleLog("Using affiliate_link as fallback URL.");
        }

        // 5. Fifth priority: Country / regional website URLs
        if (empty($urls) && $business->websites && $business->websites->isNotEmpty()) {

            foreach ($business->websites as $web) {

                $websiteUrl = trim($web->website_url ?? '');

                if (!empty($websiteUrl) && !in_array($websiteUrl, $urls)) {
                    $urls[] = $websiteUrl;
                }
            }

            if (!empty($urls)) {
                $this->addConsoleLog("Using country/regional website URLs.");
            }
        }

        // 6. Sixth priority: Business permanent_url
        if (empty($urls) && !empty(trim($business->permanent_url ?? ''))) {

            $urls[] = trim($business->permanent_url);

            $this->addConsoleLog("Using business permanent_url.");
        }

        $this->addConsoleLog("Detected target URL list: " . implode(', ', $urls));

        if (empty($urls)) {
            session()->flash('modal_error', 'No website or screenshot URL found for this business. Please configure Screenshot URLs first.');
            $this->addConsoleLog("FAILED: No target URLs found.");
            $this->dispatch('print-console-logs', logs: $this->logCollector);
            $this->isTakingScreenshots = false;
            return;
        }

        $generatedImages = [];
        $destinationDir = public_path('business_images');
        $this->addConsoleLog("Target public folder path: " . $destinationDir);

        if (!File::exists($destinationDir)) {
            try {
                File::makeDirectory($destinationDir, 0775, true);
                $this->addConsoleLog("Created public folder: business_images");
            } catch (\Exception $e) {
                $this->addConsoleLog("Failed to create folder: " . $e->getMessage());
            }
        } else {
            // Attempt to automatically make it writable if it isn't
            if (!is_writable($destinationDir)) {
                try {
                    @chmod($destinationDir, 0775);
                } catch (\Exception $e) {
                }
            }
        }

        // Capture up to 5 URLs
        $urlsToCapture = array_slice($urls, 0, 5);

        foreach ($urlsToCapture as $idx => $targetUrl) {
            $targetUrl = trim($targetUrl);
            if (empty($targetUrl)) continue;

            if (!preg_match("~^(?:f|ht)tps?://~i", $targetUrl)) {
                $targetUrl = "https://" . $targetUrl;
            }

            $filename = 'screenshot_' . $this->selectedBusinessId . '_' . time() . '_' . ($idx + 1) . '.jpg';
            $fullPath = $destinationDir . '/' . $filename;

            $this->addConsoleLog("Attempting capture for target URL: " . $targetUrl);
            $captured = $this->captureRealScreenshot($fullPath, $targetUrl);
            if ($captured) {
                $generatedImages[] = 'business_images/' . $filename;
                $this->addConsoleLog("SUCCESS: Screenshot saved as business_images/" . $filename);
            } else {
                $this->addConsoleLog("FAILED: Could not capture screenshot for: " . $targetUrl);
            }
        }
        $business = Business::findOrFail($this->selectedBusinessId);

        $selectedCategoryName = trim($business->screenshot_display_category ?? '');

        if (!empty($generatedImages)) {

            $maxImages = 5;

            $categories = $business->screenshot_categories ?? [];

            /*
    |--------------------------------------------------------------------------
    | 1. Business Edit me specific category selected hai
    |--------------------------------------------------------------------------
    */
            if (!empty($selectedCategoryName)) {

                foreach ($categories as $index => $category) {

                    if (
                        strcasecmp(
                            trim($category['name'] ?? ''),
                            $selectedCategoryName
                        ) === 0
                    ) {

                        $existingImages = $category['images'] ?? [];

                        // Saare NEW screenshots rakho.
                        // Sirf utne OLD screenshots rakho jitne 5 ki limit me fit ho sake.
                        $oldImagesAllowed = max(
                            0,
                            $maxImages - count($generatedImages)
                        );

                        $existingImages = array_slice(
                            $existingImages,
                            0,
                            $oldImagesAllowed
                        );

                        $finalImages = array_merge(
                            $existingImages,
                            $generatedImages
                        );

                        $categories[$index]['images'] = $finalImages;

                        $this->currentImages = $finalImages;

                        break;
                    }
                }

                $business->screenshot_categories = $categories;
                $business->save();

                $this->newUploads = [];

                $this->addConsoleLog(
                    "Screenshots saved inside category: " .
                        $selectedCategoryName
                );
            }

            /*
    |--------------------------------------------------------------------------
    | 2. Business Edit me koi specific category selected nahi hai
    |    -> is_default = true wali category use hogi
    |--------------------------------------------------------------------------
    */ else {

                $defaultCategoryIndex = null;

                foreach ($categories as $index => $category) {

                    if (!empty($category['is_default'])) {
                        $defaultCategoryIndex = $index;
                        break;
                    }
                }

                if ($defaultCategoryIndex !== null) {

                    $existingImages =
                        $categories[$defaultCategoryIndex]['images'] ?? [];

                    // Saare NEW screenshots rakho.
                    // OLD screenshots sirf available space ke hisaab se rakho.
                    $oldImagesAllowed = max(
                        0,
                        $maxImages - count($generatedImages)
                    );

                    $existingImages = array_slice(
                        $existingImages,
                        0,
                        $oldImagesAllowed
                    );

                    $finalImages = array_merge(
                        $existingImages,
                        $generatedImages
                    );

                    $categories[$defaultCategoryIndex]['images'] = $finalImages;

                    $business->screenshot_categories = $categories;
                    $business->save();

                    $this->currentImages = $finalImages;
                    $this->newUploads = [];

                    $this->addConsoleLog(
                        "Screenshots saved inside default category: " .
                            ($categories[$defaultCategoryIndex]['name'] ?? 'Default')
                    );
                }

                /*
        |--------------------------------------------------------------------------
        | 3. Koi default category bhi nahi hai
        |--------------------------------------------------------------------------
        */ else {

                    $business->business_images = array_slice(
                        $generatedImages,
                        0,
                        $maxImages
                    );

                    $business->save();

                    $this->currentImages = $business->business_images;
                    $this->newUploads = [];

                    $this->addConsoleLog(
                        "No default category found. Screenshots saved as business images."
                    );
                }
            }

            session()->flash(
                'modal_success',
                count($generatedImages) .
                    ' website screenshot(s) generated successfully!'
            );
        } else {
            $this->addConsoleLog("All screenshot capture attempts failed. Running self-diagnostics...");
            $this->runScreenshotDiagnostics($urlsToCapture[0] ?? 'None');
            session()->flash('modal_error', 'Could not capture screenshots. Please verify the URL or try uploading images manually.');
        }

        $this->addConsoleLog("Automated Screenshot capture process completed.");
        $this->dispatch('print-console-logs', logs: $this->logCollector);
        $this->isTakingScreenshots = false;
    }


    public function setDefaultScreenshotCategory($categoryName)
    {
        $business = Business::findOrFail($this->selectedBusinessId);

        /*
    |--------------------------------------------------------------------------
    | Only one category can be DEFAULT
    |--------------------------------------------------------------------------
    */
        foreach ($this->screenshotCategories as $index => $category) {

            $this->screenshotCategories[$index]['is_default'] =
                strcasecmp(
                    trim($category['name'] ?? ''),
                    trim($categoryName)
                ) === 0;
        }

        /*
    |--------------------------------------------------------------------------
    | IMPORTANT:
    | Do NOT change screenshot_display_category here.
    |
    | If Business Edit is using "Default (No Category)",
    | it will remain NULL and will automatically follow
    | whichever category is currently DEFAULT.
    |--------------------------------------------------------------------------
    */

        $business->screenshot_categories = $this->screenshotCategories;
        $business->save();

        /*
    |--------------------------------------------------------------------------
    | Find the newly selected DEFAULT category
    |--------------------------------------------------------------------------
    */
        $defaultCategory = collect(
            $this->screenshotCategories
        )->first(function ($category) use ($categoryName) {

            return strcasecmp(
                trim($category['name'] ?? ''),
                trim($categoryName)
            ) === 0;
        });

        /*
    |--------------------------------------------------------------------------
    | Update current modal images immediately
    |--------------------------------------------------------------------------
    */
        $this->currentImages = $defaultCategory['images'] ?? [];

        /*
    |--------------------------------------------------------------------------
    | Do NOT set selectedScreenshotCategory here.
    | That property belongs to the Screenshot URLs modal.
    |--------------------------------------------------------------------------
    */

        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'Default screenshot category updated successfully.'
        );
    }

    public function getDisplayImages($business)
    {
        $selectedCategoryName = trim(
            $business->screenshot_display_category ?? ''
        );

        /*
    |--------------------------------------------------------------------------
    | 1. Specific category selected in Business Edit
    |--------------------------------------------------------------------------
    */
        if (!empty($selectedCategoryName)) {

            $selectedCategory = collect(
                $business->screenshot_categories ?? []
            )->first(function ($category) use ($selectedCategoryName) {

                return strcasecmp(
                    trim($category['name'] ?? ''),
                    $selectedCategoryName
                ) === 0;
            });

            return $selectedCategory['images'] ?? [];
        }

        /*
    |--------------------------------------------------------------------------
    | 2. Business uses DEFAULT
    |--------------------------------------------------------------------------
    */
        $defaultCategory = collect(
            $business->screenshot_categories ?? []
        )->first(function ($category) {

            return !empty($category['is_default']);
        });

        if ($defaultCategory) {
            return $defaultCategory['images'] ?? [];
        }

        /*
    |--------------------------------------------------------------------------
    | 3. No default category
    |--------------------------------------------------------------------------
    */
        return $business->business_images ?? [];
    }


    public function openEditCategory($index)
    {
        if (!isset($this->screenshotCategories[$index])) {
            return;
        }

        $this->editingCategoryIndex = $index;
        $this->editingCategoryName = $this->screenshotCategories[$index]['name'] ?? '';

        $this->showEditCategoryModal = true;

        $this->dispatch('show-edit-category-modal');
    }

    public function updateScreenshotCategory()
    {
        $categoryName = trim($this->editingCategoryName);

        if (empty($categoryName)) {
            return;
        }

        if ($this->editingCategoryIndex === null) {
            return;
        }

        $oldCategoryName =
            $this->screenshotCategories[$this->editingCategoryIndex]['name'] ?? '';

        // Prevent duplicate category names
        foreach ($this->screenshotCategories as $index => $category) {
            if (
                $index !== $this->editingCategoryIndex &&
                strcasecmp($category['name'] ?? '', $categoryName) === 0
            ) {
                return;
            }
        }

        // Update category name
        $this->screenshotCategories[$this->editingCategoryIndex]['name'] = $categoryName;

        // Update selected category reference if this category was selected
        if ($this->selectedScreenshotCategory === $oldCategoryName) {
            $this->selectedScreenshotCategory = $categoryName;
        }

        $this->editingCategoryIndex = null;
        $this->editingCategoryName = '';

        $this->dispatch('hide-edit-category-modal');
    }


    public function deleteScreenshotCategory($index)
    {
        // Check whether the category exists
        if (!isset($this->screenshotCategories[$index])) {
            return;
        }

        // Store category information before deleting
        $deletedCategory = $this->screenshotCategories[$index];

        // Remove the category
        unset($this->screenshotCategories[$index]);

        // Re-index array
        $this->screenshotCategories = array_values($this->screenshotCategories);

        // If deleted category was currently selected
        if (
            $this->selectedScreenshotCategory ===
            ($deletedCategory['name'] ?? null)
        ) {
            $this->selectedScreenshotCategory = null;
            $this->screenshotUrls = ['', '', '', '', ''];
        }

        // If deleted category was default,
        // make the first remaining category default
        $hasDefault = collect($this->screenshotCategories)
            ->contains(function ($category) {
                return !empty($category['is_default']);
            });

        if (!$hasDefault && !empty($this->screenshotCategories)) {
            $this->screenshotCategories[0]['is_default'] = true;
        }

        // Save updated categories to database
        $business = Business::findOrFail($this->selectedBusinessId);

        $business->update([
            'screenshot_categories' => $this->screenshotCategories,
        ]);

        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'Screenshot category deleted successfully.'
        );
    }

    public function openDeleteCategoryModal($index)
    {
        // Check whether the category exists
        if (!isset($this->screenshotCategories[$index])) {
            return;
        }

        // Store the category index
        $this->categoryToDeleteIndex = $index;

        // Store the category name for confirmation message
        $this->categoryToDeleteName =
            $this->screenshotCategories[$index]['name'] ?? '';

        // Open confirmation modal
        $this->dispatch('show-delete-category-modal');
    }

    private function captureRealScreenshot($savePath, $url)
    {
        $cleanUrl = trim($url);
        if (empty($cleanUrl)) return false;

        if (!preg_match("~^(?:f|ht)tps?://~i", $cleanUrl)) {
            $cleanUrl = "https://" . $cleanUrl;
        }

        // Standard browser headers to bypass datacenter blocking and bot detectors
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Referer' => 'https://www.google.com/',
        ];

        // HTTP timeout limit per API attempt
        $apiTimeout = 30;

        try {
            // 1st Attempt: Direct Embed redirect on Microlink
            $this->addConsoleLog("[Attempt 1] Calling Microlink Embed...");

            $embedUrl = "https://api.microlink.io/?url=" . urlencode($cleanUrl) . "&screenshot=true&meta=false&embed=screenshot.url";

            $embedResponse = Http::withoutVerifying()
                ->withHeaders($headers)
                ->timeout($apiTimeout)
                ->get($embedUrl);

            if ($embedResponse->successful() && strlen($embedResponse->body()) > 5000) {

                file_put_contents($savePath, $embedResponse->body());

                // Optimize screenshot using Imagick
                if ($this->optimizeScreenshot($savePath)) {
                    $this->addConsoleLog("[Attempt 1] Screenshot optimized successfully.");
                }

                $this->addConsoleLog("[Attempt 1] SUCCESS: Image fetched and saved via Microlink.");

                return true;
            } else {
                $this->addConsoleLog(
                    "[Attempt 1] FAILED: Microlink Embed status: " .
                        $embedResponse->status() .
                        ", Length: " .
                        strlen($embedResponse->body()) .
                        ", Response: " .
                        substr($embedResponse->body(), 0, 300)
                );
            }

            // 2nd Attempt: Microlink Screenshot API (JSON)
            $this->addConsoleLog("[Attempt 2] Calling Microlink API (JSON)...");

            $apiUrl = "https://api.microlink.io/?url=" . urlencode($cleanUrl) . "&screenshot=true&meta=false";

            $response = Http::withoutVerifying()
                ->withHeaders($headers)
                ->timeout($apiTimeout)
                ->get($apiUrl);

            if ($response->successful()) {

                $json = $response->json();

                $screenshotUrl = $json['data']['screenshot']['url'] ?? null;

                if ($screenshotUrl) {

                    $this->addConsoleLog(
                        "[Attempt 2] Microlink returned screenshot URL: " .
                            $screenshotUrl .
                            ". Fetching image..."
                    );

                    $imgResponse = Http::withoutVerifying()
                        ->withHeaders($headers)
                        ->timeout($apiTimeout)
                        ->get($screenshotUrl);

                    if ($imgResponse->successful() && strlen($imgResponse->body()) > 5000) {

                        file_put_contents($savePath, $imgResponse->body());

                        // Optimize screenshot using Imagick
                        if ($this->optimizeScreenshot($savePath)) {
                            $this->addConsoleLog("[Attempt 2] Screenshot optimized successfully.");
                        }

                        $this->addConsoleLog("[Attempt 2] SUCCESS: Image fetched and saved via Microlink.");

                        return true;
                    } else {
                        $this->addConsoleLog(
                            "[Attempt 2] FAILED: Image fetch failed. Status: " .
                                $imgResponse->status()
                        );
                    }
                } else {
                    $this->addConsoleLog(
                        "[Attempt 2] FAILED: JSON response did not contain screenshot URL."
                    );
                }
            } else {
                $this->addConsoleLog(
                    "[Attempt 2] FAILED: Microlink API status: " .
                        $response->status()
                );
            }
        } catch (\Exception $e) {

            $this->addConsoleLog(
                "[Exception] Screenshot generation failed: " .
                    $e->getMessage()
            );
        }

        return false;
    }

    private function optimizeScreenshot($imagePath)
    {
        try {
            if (!file_exists($imagePath)) {
                return false;
            }

            $image = new \Imagick($imagePath);

            // Keep aspect ratio and make width maximum 800px
            if ($image->getImageWidth() > 800) {
                $image->resizeImage(
                    800,
                    0,
                    \Imagick::FILTER_LANCZOS,
                    1
                );
            }

            // Convert screenshot to JPEG
            $image->setImageFormat('jpeg');

            // Remove unnecessary metadata
            $image->stripImage();

            // Initial compression
            $image->setImageCompression(\Imagick::COMPRESSION_JPEG);
            $image->setImageCompressionQuality(75);

            $image->writeImage($imagePath);

            $image->clear();
            $image->destroy();

            return true;
        } catch (\Exception $e) {
            $this->addConsoleLog(
                "Image optimization failed: " . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Run server diagnostics on permission, PHP settings, DNS and outbound HTTPS connectivity.
     */
    private function runScreenshotDiagnostics($targetUrl)
    {
        $this->addConsoleLog("--- SCREENSHOT DIAGNOSTICS ---");
        $this->addConsoleLog("Target URL: " . $targetUrl);

        // 1. Storage write test
        $destinationDir = public_path('business_images');
        $this->addConsoleLog("Storage path: " . $destinationDir);
        $this->addConsoleLog("Directory exists: " . (File::exists($destinationDir) ? "Yes" : "No"));
        if (!File::exists($destinationDir)) {
            try {
                File::makeDirectory($destinationDir, 0775, true);
                $this->addConsoleLog("Directory created successfully.");
            } catch (\Exception $e) {
                $this->addConsoleLog("Failed to create directory: " . $e->getMessage());
            }
        }

        $isWritable = is_writable($destinationDir);
        $this->addConsoleLog("Directory writable: " . ($isWritable ? "Yes" : "No"));

        if (!$isWritable) {
            // Try to make it writable via chmod 775
            try {
                @chmod($destinationDir, 0775);
                $isWritable = is_writable($destinationDir);
                $this->addConsoleLog("Retried checking directory writable after chmod 775: " . ($isWritable ? "Yes" : "No"));
            } catch (\Exception $e) {
            }
        }

        if (!$isWritable) {
            // Try to make it writable via chmod 777
            try {
                @chmod($destinationDir, 0777);
                $isWritable = is_writable($destinationDir);
                $this->addConsoleLog("Retried checking directory writable after chmod 777: " . ($isWritable ? "Yes" : "No"));
            } catch (\Exception $e) {
            }
        }

        if (!$isWritable) {
            $this->addConsoleLog("ERROR: The directory public/business_images is not writable by the web server user.");
            $this->addConsoleLog("To fix this, please run one of these commands on your production server terminal:");
            $this->addConsoleLog("Command Option 1: chmod -R 775 " . $destinationDir);
            $this->addConsoleLog("Command Option 2: chmod -R 777 " . $destinationDir);
            $this->addConsoleLog("Command Option 3: sudo chown -R www-data:www-data " . $destinationDir);
        }

        // Test writing dummy file
        if ($isWritable) {
            $testFile = $destinationDir . '/test_write.txt';
            try {
                file_put_contents($testFile, "test write");
                $this->addConsoleLog("Test file write: SUCCESS");
                unlink($testFile);
            } catch (\Exception $e) {
                $this->addConsoleLog("Test file write: FAILED - " . $e->getMessage());
            }
        }

        // 2. PHP settings & Extensions
        $this->addConsoleLog("PHP Version: " . PHP_VERSION);
        $this->addConsoleLog("allow_url_fopen: " . (ini_get('allow_url_fopen') ? "Enabled" : "Disabled"));
        $this->addConsoleLog("curl extension: " . (extension_loaded('curl') ? "Loaded" : "Not Loaded"));
        $this->addConsoleLog("openssl extension: " . (extension_loaded('openssl') ? "Loaded" : "Not Loaded"));
        $this->addConsoleLog("gd extension: " . (extension_loaded('gd') ? "Loaded" : "Not Loaded"));

        // 3. DNS resolution test
        $domains = ['api.microlink.io', 'image.thum.io', 's0.wp.com', 'httpbin.org'];
        foreach ($domains as $domain) {
            $ip = gethostbyname($domain);
            $this->addConsoleLog("DNS Resolution check for {$domain}: " . ($ip !== $domain ? "Resolved to {$ip}" : "FAILED to resolve"));
        }

        // 4. Outbound HTTPS test via HTTP client (diagnosing blocked outbound traffic)
        try {
            $testUrl = "https://httpbin.org/ip";
            $headers = [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            ];
            $res = Http::withoutVerifying()->withHeaders($headers)->timeout(8)->get($testUrl);
            $this->addConsoleLog("Outbound HTTPS connection to httpbin.org status: " . $res->status());
            if ($res->successful()) {
                $this->addConsoleLog("Outbound HTTPS response body: " . $res->body());
            }
        } catch (\Exception $e) {
            $this->addConsoleLog("Outbound HTTPS test FAILED: " . $e->getMessage());
        }

        $this->addConsoleLog("--- END DIAGNOSTICS ---");
    }

    private function createDummyScreenshot($path, $url, $number)
    {
        // Quality & size optimization settings
        $width = 800;
        $height = 450;
        $img = imagecreatetruecolor($width, $height);

        $bgColor = imagecolorallocate($img, 30, 41, 59); // Dark slate
        $textColor = imagecolorallocate($img, 248, 250, 252);
        $accentColor = imagecolorallocate($img, 249, 99, 59); // Theme primary (#F9633B)

        imagefill($img, 0, 0, $bgColor);

        // Draw top browser bar style
        $barColor = imagecolorallocate($img, 15, 23, 42);
        imagefilledrectangle($img, 0, 0, $width, 40, $barColor);
        imagefilledellipse($img, 20, 20, 12, 12, imagecolorallocate($img, 239, 68, 68));
        imagefilledellipse($img, 40, 20, 12, 12, imagecolorallocate($img, 245, 158, 11));
        imagefilledellipse($img, 60, 20, 12, 12, imagecolorallocate($img, 34, 197, 94));

        // Draw URL text box
        imagefilledrectangle($img, 90, 10, $width - 20, 30, imagecolorallocate($img, 30, 41, 59));
        imagestring($img, 3, 105, 12, substr($url, 0, 75), $textColor);

        // Main canvas label
        imagestring($img, 5, 50, 150, "Automated Optimized Screenshot #" . $number, $accentColor);
        imagestring($img, 4, 50, 190, "Source: " . substr($url, 0, 60), $textColor);
        imagestring($img, 3, 50, 230, "Resolution: 800x450 | Format: WebP (Optimized)", imagecolorallocate($img, 148, 163, 184));

        // Export as WebP for optimal quality/compression
        if (function_exists('imagewebp')) {
            imagewebp($img, $path, 80);
        } else {
            imagejpeg($img, $path, 80);
        }
        imagedestroy($img);
    }

public function saveImages()
{
    $business = Business::findOrFail($this->selectedBusinessId);

    // Current images after removing images with X button
    $finalImages = $this->currentImages;

    /*
    |--------------------------------------------------------------------------
    | Process newly uploaded manual images
    |--------------------------------------------------------------------------
    */
    if (!empty($this->newUploads)) {
        foreach ($this->newUploads as $file) {

            $savedPath = $this->storeOptimizedImage($file);

            if ($savedPath) {
                $finalImages[] = $savedPath;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Limit total images to maximum 5
    |--------------------------------------------------------------------------
    */
    $finalImages = array_slice($finalImages, 0, 5);

    /*
    |--------------------------------------------------------------------------
    | Determine where these images should be saved
    |--------------------------------------------------------------------------
    */
    $selectedCategoryName = trim(
        $business->screenshot_display_category ?? ''
    );

    $categories = $business->screenshot_categories ?? [];

    /*
    |--------------------------------------------------------------------------
    | 1. Specific category selected in Business Edit
    |--------------------------------------------------------------------------
    */
    if (!empty($selectedCategoryName)) {

        foreach ($categories as $index => $category) {

            if (
                strcasecmp(
                    trim($category['name'] ?? ''),
                    $selectedCategoryName
                ) === 0
            ) {

                $categories[$index]['images'] = $finalImages;

                break;
            }
        }

        $business->screenshot_categories = $categories;
        $business->save();
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Business Edit is using "Default (No Category)"
    |    -> Save images inside current DEFAULT category
    |--------------------------------------------------------------------------
    */
    else {

        $defaultCategoryIndex = null;

        foreach ($categories as $index => $category) {

            if (!empty($category['is_default'])) {

                $defaultCategoryIndex = $index;

                break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DEFAULT category found
        |--------------------------------------------------------------------------
        */
        if ($defaultCategoryIndex !== null) {

            $categories[$defaultCategoryIndex]['images'] =
                $finalImages;

            $business->screenshot_categories = $categories;

            $business->save();
        }

        /*
        |--------------------------------------------------------------------------
        | No DEFAULT category found
        | -> Legacy business_images fallback
        |--------------------------------------------------------------------------
        */
        else {

            $business->business_images = $finalImages;

            $business->save();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Livewire state
    |--------------------------------------------------------------------------
    */
    $this->currentImages = $finalImages;
    $this->newUploads = [];

    $this->showImageModal = false;

    /*
    |--------------------------------------------------------------------------
    | Reload business list so the updated images appear immediately
    |--------------------------------------------------------------------------
    */
    $this->loadBusinesses();

    $this->dispatch('hide-manage-images-modal');

    $this->dispatch(
        'show-toast',
        type: 'success',
        message: 'Business images updated successfully.'
    );
}

    private function storeOptimizedImage($file)
    {
        if (!$file || !$file->isValid()) return null;

        $destinationPath = public_path('business_images');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $fullPath = $destinationPath . '/' . $filename;

        $contents = file_get_contents($file->getRealPath());
        file_put_contents($fullPath, $contents);

        return 'business_images/' . $filename;
    }

    public function abortImageModal()
    {
        $this->showImageModal = false;
        $this->currentImages = [];
        $this->newUploads = [];
        $this->dispatch('hide-manage-images-modal');
    }

    // --- Screenshot URLs Modal Actions ---

    public function openUrlModal($businessId)
    {
        $business = Business::with('translations')->findOrFail($businessId);

        $this->selectedBusinessId = $businessId;

        $this->selectedBusinessName = $business->translations->firstWhere('lang_id', $this->lang_id)->name
            ?? $business->translations->first()?->name
            ?? 'Business #' . $businessId;

        // Load screenshot categories from database
        $this->screenshotCategories = $business->screenshot_categories ?? [];

        // Find default category
        $defaultCategory = collect($this->screenshotCategories)
            ->firstWhere('is_default', true);

        if ($defaultCategory) {
            $this->selectedScreenshotCategory = $defaultCategory['name'];
            $urls = $defaultCategory['urls'] ?? [];
        } else {
            // Backward compatibility with existing screenshot_urls
            $this->selectedScreenshotCategory = null;

            $urls = [];
            if (\Illuminate\Support\Facades\Schema::hasColumn('businesses', 'screenshot_urls')) {
                $urls = $business->screenshot_urls ?? [];
            }
        }

        // Ensure array has at least 5 slots
        while (count($urls) < 5) {
            $urls[] = '';
        }

        $this->screenshotUrls = $urls;

        $this->showUrlModal = true;

        $this->dispatch('show-url-modal');
    }


    public function addScreenshotCategory()
    {
        $categoryName = trim($this->newScreenshotCategory);

        // Empty category name
        if (empty($categoryName)) {
            return;
        }

        // Prevent duplicate category names
        foreach ($this->screenshotCategories as $category) {
            if (strcasecmp($category['name'] ?? '', $categoryName) === 0) {

                $this->addError(
                    'newScreenshotCategory',
                    'This category already exists.'
                );

                return;
            }
        }

        // Create new category
        $newCategory = [
            'name' => $categoryName,
            'urls' => [],
            'is_default' => empty($this->screenshotCategories),
        ];

        // Add category to current Livewire state
        array_unshift($this->screenshotCategories, $newCategory);
        $this->categoryPage = 1;

        // Save categories to database
        $business = Business::findOrFail($this->selectedBusinessId);

        $business->update([
            'screenshot_categories' => $this->screenshotCategories,
        ]);

        // Select newly added category
        $this->selectedScreenshotCategory = $categoryName;

        // Clear input field
        $this->newScreenshotCategory = '';
        $this->resetErrorBag('newScreenshotCategory'); // clear after successful save too

        // Success message
        session()->flash('category_success', 'Screenshot category added successfully.');
    }


    public function updatedNewScreenshotCategory()
    {
        $this->resetErrorBag('newScreenshotCategory');
    }

    public function nextCategoryPage()
    {
        $totalPages = max(
            1,
            (int) ceil(count($this->screenshotCategories) / $this->categoriesPerPage)
        );

        if ($this->categoryPage < $totalPages) {
            $this->categoryPage++;
        }
    }

    public function previousCategoryPage()
    {
        if ($this->categoryPage > 1) {
            $this->categoryPage--;
        }
    }


    public function updatedSelectedScreenshotCategory($categoryName)
    {
        // Find selected category
        $category = collect($this->screenshotCategories)
            ->first(function ($item) use ($categoryName) {
                return ($item['name'] ?? '') === $categoryName;
            });

        // If category was not found
        if (!$category) {
            $this->screenshotUrls = ['', '', '', '', ''];
            return;
        }

        // Load URLs belonging to this category
        $urls = $category['urls'] ?? [];

        // Reset URL fields first
        $this->screenshotUrls = [];

        // Put saved URLs into the fields
        foreach ($urls as $url) {
            if (!empty(trim($url))) {
                $this->screenshotUrls[] = $url;
            }
        }

        // Keep minimum 5 fields
        while (count($this->screenshotUrls) < 5) {
            $this->screenshotUrls[] = '';
        }

        // Load Region settings
        $this->categoryTargetMode = $category['target_regions_mode'] ?? 'all';
        $this->categoryTargetRegions = $category['target_regions'] ?? [];
        $this->searchLanguage = '';
    }

    public function confirmDeleteScreenshotCategory()
    {
        // Check if a category is selected for deletion
        if ($this->categoryToDeleteIndex === null) {
            return;
        }

        $index = $this->categoryToDeleteIndex;

        // Check if the category still exists
        if (!isset($this->screenshotCategories[$index])) {
            return;
        }

        // Remove the category
        unset($this->screenshotCategories[$index]);

        // Re-index the array
        $this->screenshotCategories = array_values($this->screenshotCategories);
        $this->categoryPage = 1;
        // Save updated categories to database
        $business = Business::findOrFail($this->selectedBusinessId);

        $business->update([
            'screenshot_categories' => $this->screenshotCategories,
        ]);

        // Reset delete-related variables
        $this->categoryToDeleteIndex = null;
        $this->categoryToDeleteName = '';

        // Close delete modal
        $this->dispatch('hide-delete-category-modal');

        // Show success message
        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'Screenshot category deleted successfully.'
        );
    }

    public function editScreenshotCategory($index)
    {
        if (!isset($this->screenshotCategories[$index])) {
            return;
        }

        $this->editingScreenshotCategoryIndex = $index;

        $this->editingScreenshotCategoryName =
            $this->screenshotCategories[$index]['name'] ?? '';
    }

    public function addUrlSlot()
    {
        $this->screenshotUrls[] = '';
    }

    public function removeUrlSlot($index)
    {
        unset($this->screenshotUrls[$index]);
        $this->screenshotUrls = array_values($this->screenshotUrls);
    }

    public function saveScreenshotUrls()
    {
        $business = Business::findOrFail($this->selectedBusinessId);

        // Remove empty URLs
        $cleanUrls = array_values(array_filter($this->screenshotUrls, function ($url) {
            return !empty(trim($url));
        }));

        // If a category is selected, save URLs inside that category
        if (!empty($this->selectedScreenshotCategory)) {

            foreach ($this->screenshotCategories as $index => $category) {

                if (($category['name'] ?? '') === $this->selectedScreenshotCategory) {

                    $this->screenshotCategories[$index]['urls'] = $cleanUrls;
                    $this->screenshotCategories[$index]['target_regions_mode'] = $this->categoryTargetMode;
                    $this->screenshotCategories[$index]['target_regions'] = $this->categoryTargetMode === 'specific' ? $this->categoryTargetRegions : [];

                    break;
                }
            }

            // Save all categories back to database
            $business->update([
                'screenshot_categories' => $this->screenshotCategories,
            ]);
        } else {

            // No category selected
            // Keep the existing default screenshot_urls behaviour
            $business->update([
                'screenshot_urls' => $cleanUrls,
            ]);
        }

        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'Screenshot URLs saved successfully.'
        );
    }

    public function closeModals()
    {
        $this->showImageModal = false;
        $this->showUrlModal = false;
    }
}
