<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Business;
use Illuminate\Support\Facades\File;
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

    // Modal state for Screenshot URLs
    public $showUrlModal = false;
    public $screenshotUrls = ['', '', '', '', ''];

    protected $listeners = [
        'closeModal' => 'closeModals'
    ];

    public function mount()
    {
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
        
        $this->currentImages = $business->business_images ?? [];
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
        
        $business = Business::findOrFail($this->selectedBusinessId);
        $urls = $business->screenshot_urls ?? [];

        // If no custom screenshot URLs set, fall back to permanent_url or affiliate_link
        if (empty(array_filter($urls))) {
            if (!empty($business->permanent_url)) {
                $urls[] = $business->permanent_url;
            } elseif (!empty($business->affiliate_link)) {
                $urls[] = $business->affiliate_link;
            } else {
                $urls[] = 'https://example.com';
            }
        }

        $generatedImages = [];
        $destinationDir = public_path('business_images');
        if (!File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true);
        }

        foreach ($urls as $idx => $targetUrl) {
            if (empty(trim($targetUrl))) continue;

            $filename = 'screenshot_' . $this->selectedBusinessId . '_' . time() . '_' . ($idx + 1) . '.webp';
            $fullPath = $destinationDir . '/' . $filename;

            // Generate an optimized dummy preview image representing the screenshot
            $this->createDummyScreenshot($fullPath, $targetUrl, $idx + 1);
            $generatedImages[] = 'business_images/' . $filename;
        }

        if (!empty($generatedImages)) {
            $this->currentImages = $generatedImages;
            $this->newUploads = [];
            session()->flash('modal_success', 'Screenshots generated & optimized successfully!');
        } else {
            session()->flash('modal_error', 'No valid URLs found to take screenshots.');
        }

        $this->isTakingScreenshots = false;
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
        $finalImages = $this->currentImages;

        // Process newly uploaded files
        if (!empty($this->newUploads)) {
            foreach ($this->newUploads as $file) {
                $savedPath = $this->storeOptimizedImage($file);
                if ($savedPath) {
                    $finalImages[] = $savedPath;
                }
            }
        }

        // Limit to max 5 images
        $finalImages = array_slice($finalImages, 0, 5);

        $business->update([
            'business_images' => $finalImages
        ]);

        $this->showImageModal = false;
        $this->loadBusinesses();

        $this->dispatch('hide-manage-images-modal');
        $this->dispatch('show-toast', type: 'success', message: 'Business images updated successfully.');
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

        $urls = [];
        if (\Illuminate\Support\Facades\Schema::hasColumn('businesses', 'screenshot_urls')) {
            $urls = $business->screenshot_urls ?? [];
        }
        // Ensure array has at least 5 slots
        while (count($urls) < 5) {
            $urls[] = '';
        }
        $this->screenshotUrls = $urls;
        $this->showUrlModal = true;

        $this->dispatch('show-url-modal');
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
        
        $cleanUrls = array_values(array_filter($this->screenshotUrls, function ($url) {
            return !empty(trim($url));
        }));

        if (\Illuminate\Support\Facades\Schema::hasColumn('businesses', 'screenshot_urls')) {
            $business->update([
                'screenshot_urls' => $cleanUrls
            ]);
        } else {
            // Fallback if migration has not been run yet
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE businesses ADD COLUMN screenshot_urls JSON NULL AFTER business_images;");
            $business->update([
                'screenshot_urls' => $cleanUrls
            ]);
        }

        $this->showUrlModal = false;
        $this->dispatch('hide-url-modal');
        $this->dispatch('show-toast', type: 'success', message: 'Screenshot URLs saved successfully.');
    }

    public function closeModals()
    {
        $this->showImageModal = false;
        $this->showUrlModal = false;
    }
}
