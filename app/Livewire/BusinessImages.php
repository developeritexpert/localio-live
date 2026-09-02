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

    private $logCollector = [];

    private function addConsoleLog($msg)
    {
        $this->logCollector[] = $msg;
        Log::info($msg);
    }

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

        // 1. First priority: Configured screenshot URLs
        if (!empty($business->screenshot_urls) && is_array($business->screenshot_urls)) {
            $urls = array_values(array_filter($business->screenshot_urls, function ($u) {
                return !empty(trim($u));
            }));
        }

        // 2. Second priority: affiliate_link
        if (empty($urls) && !empty(trim($business->affiliate_link ?? ''))) {
            $urls[] = trim($business->affiliate_link);
        }

        // 3. Third priority: Country / regional website URLs
        if (empty($urls) && $business->websites && $business->websites->isNotEmpty()) {
            foreach ($business->websites as $web) {
                if (!empty(trim($web->website_url ?? '')) && !in_array(trim($web->website_url), $urls)) {
                    $urls[] = trim($web->website_url);
                }
            }
        }

        // 4. Fourth priority: Business permanent_url
        if (empty($urls) && !empty(trim($business->permanent_url ?? ''))) {
            $urls[] = trim($business->permanent_url);
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

            $filename = 'screenshot_' . $this->selectedBusinessId . '_' . time() . '_' . ($idx + 1) . '.png';
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

        if (!empty($generatedImages)) {
            $this->currentImages = array_merge($this->currentImages, $generatedImages);
            $this->currentImages = array_slice($this->currentImages, 0, 5);
            $this->newUploads = [];
            session()->flash('modal_success', count($generatedImages) . ' website screenshot(s) generated successfully!');
        } else {
            $this->addConsoleLog("All screenshot capture attempts failed. Running self-diagnostics...");
            $this->runScreenshotDiagnostics($urlsToCapture[0] ?? 'None');
            session()->flash('modal_error', 'Could not capture screenshots. Please verify the URL or try uploading images manually.');
        }

        $this->addConsoleLog("Automated Screenshot capture process completed.");
        $this->dispatch('print-console-logs', logs: $this->logCollector);
        $this->isTakingScreenshots = false;
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
        $apiTimeout = 30; // Increased to 30s because rendering screenshots can take time

        try {
            // 1st Attempt: Direct Embed redirect on Microlink (Most efficient, returns image directly)
            $this->addConsoleLog("[Attempt 1] Calling Microlink Embed...");
            $embedUrl = "https://api.microlink.io/?url=" . urlencode($cleanUrl) . "&screenshot=true&meta=false&embed=screenshot.url";
            $embedResponse = Http::withoutVerifying()
                ->withHeaders($headers)
                ->timeout($apiTimeout)
                ->get($embedUrl);
            if ($embedResponse->successful() && strlen($embedResponse->body()) > 5000) {
                file_put_contents($savePath, $embedResponse->body());
                $this->addConsoleLog("[Attempt 1] SUCCESS: Image fetched and saved via Microlink.");
                return true;
            } else {
                $this->addConsoleLog("[Attempt 1] FAILED: Microlink Embed status: " . $embedResponse->status() . ", Length: " . strlen($embedResponse->body()) . ", Response: " . substr($embedResponse->body(), 0, 300));
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
                    $this->addConsoleLog("[Attempt 2] Microlink returned screenshot URL: " . $screenshotUrl . ". Fetching image...");
                    $imgResponse = Http::withoutVerifying()
                        ->withHeaders($headers)
                        ->timeout($apiTimeout)
                        ->get($screenshotUrl);
                    if ($imgResponse->successful() && strlen($imgResponse->body()) > 5000) {
                        file_put_contents($savePath, $imgResponse->body());
                        $this->addConsoleLog("[Attempt 2] SUCCESS: Image fetched and saved via Microlink.");
                        return true;
                    } else {
                        $this->addConsoleLog("[Attempt 2] FAILED: Image fetch failed. Status: " . $imgResponse->status());
                    }
                } else {
                    $this->addConsoleLog("[Attempt 2] FAILED: JSON response did not contain screenshot URL.");
                }
            } else {
                $this->addConsoleLog("[Attempt 2] FAILED: Microlink API status: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->addConsoleLog("[Exception] Screenshot generation failed: " . $e->getMessage());
        }

        return false;
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
