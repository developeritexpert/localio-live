<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Business;
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
        
        $business = Business::with('websites')->findOrFail($this->selectedBusinessId);
        $urls = [];

        // 1. First priority: affiliate_link
        if (!empty(trim($business->affiliate_link ?? ''))) {
            $urls[] = trim($business->affiliate_link);
        }

        // 2. Second priority: Country / regional website URLs (websites)
        if (empty($urls) && $business->websites && $business->websites->isNotEmpty()) {
            foreach ($business->websites as $web) {
                if (!empty(trim($web->website_url ?? '')) && !in_array(trim($web->website_url), $urls)) {
                    $urls[] = trim($web->website_url);
                }
            }
        }

        // 3. Third priority: Configured screenshot URLs (from Screenshot URLs modal)
        if (empty($urls) && !empty($business->screenshot_urls) && is_array($business->screenshot_urls)) {
            $urls = array_values(array_filter($business->screenshot_urls, function ($u) {
                return !empty(trim($u));
            }));
        }

        // 4. Fourth priority: Business permanent_url
        if (empty($urls) && !empty(trim($business->permanent_url ?? ''))) {
            $urls[] = trim($business->permanent_url);
        }

        if (empty($urls)) {
            session()->flash('modal_error', 'No website or screenshot URL found for this business. Please configure Screenshot URLs first.');
            $this->isTakingScreenshots = false;
            return;
        }

        $generatedImages = [];
        $destinationDir = public_path('business_images');
        if (!File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true);
        }

        // Capture up to 5 URLs
        $urlsToCapture = array_slice($urls, 0, 5);

        foreach ($urlsToCapture as $idx => $targetUrl) {
            $targetUrl = trim($targetUrl);
            if (empty($targetUrl)) continue;

            if (!preg_match("~^(?:f|ht)tps?://~i", $targetUrl)) {
                $targetUrl = "https://" . $targetUrl;
            }

            $filename = 'screenshot_' . $this->selectedBusinessId . '_' . time() . '_' . ($idx + 1) . '.png';
            $fullPath = $destinationDir . '/' . $filename;

            $captured = $this->captureRealScreenshot($fullPath, $targetUrl);
            if ($captured) {
                $generatedImages[] = 'business_images/' . $filename;
            }
        }

        if (!empty($generatedImages)) {
            $this->currentImages = array_merge($this->currentImages, $generatedImages);
            $this->currentImages = array_slice($this->currentImages, 0, 5);
            $this->newUploads = [];
            session()->flash('modal_success', count($generatedImages) . ' website screenshot(s) generated successfully via Microlink!');
        } else {
            session()->flash('modal_error', 'Could not capture screenshots. Please verify the URL or try uploading images manually.');
        }

        $this->isTakingScreenshots = false;
    }

    private function captureRealScreenshot($savePath, $url)
    {
        $cleanUrl = trim($url);
        if (empty($cleanUrl)) return false;

        if (!preg_match("~^(?:f|ht)tps?://~i", $cleanUrl)) {
            $cleanUrl = "https://" . $cleanUrl;
        }

        try {
            // Dedicated Provider: Microlink Screenshot API
            $apiUrl = "https://api.microlink.io/?url=" . urlencode($cleanUrl) . "&screenshot=true&meta=false&waitForTimeout=1500";
            
            $response = Http::withoutVerifying()->timeout(35)->get($apiUrl);
            if ($response->successful()) {
                $json = $response->json();
                $screenshotUrl = $json['data']['screenshot']['url'] ?? null;
                if ($screenshotUrl) {
                    $imgResponse = Http::withoutVerifying()->timeout(35)->get($screenshotUrl);
                    if ($imgResponse->successful() && strlen($imgResponse->body()) > 5000) {
                        file_put_contents($savePath, $imgResponse->body());
                        return true;
                    }
                }
            }

            // Fallback: direct embed redirect on Microlink
            $embedUrl = "https://api.microlink.io/?url=" . urlencode($cleanUrl) . "&screenshot=true&meta=false&embed=screenshot.url";
            $embedResponse = Http::withoutVerifying()->timeout(35)->get($embedUrl);
            if ($embedResponse->successful() && strlen($embedResponse->body()) > 5000) {
                file_put_contents($savePath, $embedResponse->body());
                return true;
            }
        } catch (\Exception $e) {
            Log::warning("Microlink screenshot failed for {$cleanUrl}: " . $e->getMessage());
        }

        return false;
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
