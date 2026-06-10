<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Google\Cloud\Translate\V2\TranslateClient;

class TranslateContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translate:contents {--source-lang-id=1 : Source language ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create translated content entries for all languages based on source language content';

    /**
     * Tables containing content to translate
     *
     * @var array
     */
    protected $contentTables = [
        'category_page_contents',
        'footer_contents',
        'header_contents',
        'home_contents',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Set Google Translate credentials
        putenv('GOOGLE_APPLICATION_CREDENTIALS=google_secret1.json');

        // Get source language ID
        $sourceLangId = $this->option('source-lang-id');
        $sourceLanguage = DB::table('languages')->where('id', $sourceLangId)->first();

        if (!$sourceLanguage) {
            $this->error("Source language with ID '{$sourceLangId}' not found in the database!");
            return 1;
        }

        // Get all active languages except source language
        $languages = DB::table('languages')
            ->where('id', '!=', $sourceLangId)
            ->get();

        if ($languages->isEmpty()) {
            $this->error('No active target languages found for translation!');
            return 1;
        }

        // Initialize Google Translate client
        try {
            $translate = new TranslateClient();
        } catch (\Exception $e) {
            $this->error('Failed to initialize Google Translate client: ' . $e->getMessage());
            return 1;
        }

        $this->info('Starting translation process...');

        // Track statistics
        $stats = [
            'tables_processed' => 0,
            'entries_created' => 0,
            'entries_skipped' => 0,
            'failures' => 0
        ];

        // Process each language
        foreach ($languages as $language) {
            $this->info("\nProcessing language: {$language->name} ({$language->lang_code})");
            $targetLangCode = $this->getGoogleTranslateCode($language->lang_code);
            $sourceLangCode = $this->getGoogleTranslateCode($sourceLanguage->lang_code);

            // Skip if source and target languages are the same (to avoid Google Translate errors)
            if ($targetLangCode === $sourceLangCode) {
                $this->warn("  Skipping translation as source and target language codes are the same: {$sourceLangCode}");
                continue;
            }

            // Process each content table
            foreach ($this->contentTables as $table) {
                $this->info("  Processing table: {$table}");

                // Check if table exists
                if (!Schema::hasTable($table)) {
                    $this->error("  Table '{$table}' does not exist!");
                    continue;
                }

                // Get all columns for this table to make sure we include all required fields
                $columns = Schema::getColumnListing($table);

                // Get source content
                $sourceContents = DB::table($table)
                    ->where('lang_id', $sourceLangId)
                    ->get();

                if ($sourceContents->isEmpty()) {
                    $this->warn("    No source content found in '{$table}' for language ID {$sourceLangId}");
                    continue;
                }

                // Get existing meta keys for this language to avoid duplicates
                $existingKeys = DB::table($table)
                    ->where('lang_id', $language->id)
                    ->pluck('meta_key')
                    ->toArray();

                $keysToCreate = [];
                foreach ($sourceContents as $content) {
                    if (!in_array($content->meta_key, $existingKeys)) {
                        $keysToCreate[] = $content->meta_key;
                    }
                }

                if (empty($keysToCreate)) {
                    $this->line("    All content already exists for language ID {$language->id} in table '{$table}'");
                    $stats['entries_skipped'] += count($sourceContents);
                    continue;
                }

                $this->line("    Creating " . count($keysToCreate) . " new entries for language ID {$language->id}");

                // Create entries for missing keys
                $batch = [];
                foreach ($sourceContents as $content) {
                    if (!in_array($content->meta_key, $keysToCreate)) {
                        continue; // Skip if already exists
                    }

                    // Clone all fields from the source content
                    $newContent = (array) $content;

                    // Remove the primary key if it exists
                    if (isset($newContent['id'])) {
                        unset($newContent['id']);
                    }

                    // Set the new language ID
                    $newContent['lang_id'] = $language->id;

                    // Translate the meta_value if needed
                    if (is_string($content->meta_value) && !empty(trim($content->meta_value))) {
                        try {
                            // Skip translation for non-translatable content
                            if ($this->shouldTranslate($content->meta_value, $content->meta_key)) {
                                $translation = $translate->translate(
                                    $content->meta_value,
                                    [
                                        'source' => $sourceLangCode,
                                        'target' => $targetLangCode
                                    ]
                                );
                                $newContent['meta_value'] = $translation['text'];
                            }
                            // else keep the original meta_value which is already set

                            // Update timestamps if they exist
                            if (in_array('created_at', $columns)) {
                                $newContent['created_at'] = now();
                            }
                            if (in_array('updated_at', $columns)) {
                                $newContent['updated_at'] = now();
                            }

                            $batch[] = $newContent;

                        } catch (\Exception $e) {
                            $this->error("    Failed to translate content ({$content->meta_key}): " . $e->getMessage());
                            $stats['failures']++;
                        }
                    } else {
                        // Update timestamps if they exist
                        if (in_array('created_at', $columns)) {
                            $newContent['created_at'] = now();
                        }
                        if (in_array('updated_at', $columns)) {
                            $newContent['updated_at'] = now();
                        }

                        $batch[] = $newContent;
                    }
                }

                // Insert the translated content
                if (!empty($batch)) {
                    try {
                        DB::table($table)->insert($batch);
                        $this->info("    Successfully created " . count($batch) . " entries in '{$table}'");
                        $stats['entries_created'] += count($batch);
                    } catch (\Exception $e) {
                        $this->error("    Failed to insert content: " . $e->getMessage());

                        // Try inserting one by one to identify problematic records
                        $this->line("    Trying to insert records individually...");
                        $successCount = 0;

                        foreach ($batch as $record) {
                            try {
                                DB::table($table)->insert([$record]);
                                $successCount++;
                            } catch (\Exception $innerE) {
                                $this->error("    Failed to insert record with meta_key '{$record['meta_key']}': " . $innerE->getMessage());
                                $stats['failures']++;
                            }
                        }

                        if ($successCount > 0) {
                            $this->info("    Successfully created {$successCount} entries individually");
                            $stats['entries_created'] += $successCount;
                        }
                    }
                }

                $stats['tables_processed']++;
            }
        }

        $this->newLine();
        $this->info('Translation process completed!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Tables Processed', $stats['tables_processed']],
                ['Entries Created', $stats['entries_created']],
                ['Entries Skipped (Already Exist)', $stats['entries_skipped']],
                ['Failures', $stats['failures']]
            ]
        );

        return 0;
    }

    /**
     * Convert language code to Google Translate format
     *
     * @param string $langCode
     * @return string
     */
    protected function getGoogleTranslateCode($langCode)
    {
        // Convert codes like "en-us" to "en" for Google Translate
        $parts = explode('-', strtolower($langCode));
        return $parts[0];
    }

    /**
     * Determine if content should be translated
     *
     * @param string $text
     * @param string $metaKey
     * @return boolean
     */
    protected function shouldTranslate($text, $metaKey)
    {
        // Always skip translation for keys that likely contain non-translatable content
        $nonTranslatableKeys = [
            'image', 'logo', 'icon', 'url', 'link', 'email', 'phone',
            'background', 'bg', 'color', 'css', 'js', 'script', 'style',
            'header_image', 'header_bg_image', 'footer_logo'
        ];

        foreach ($nonTranslatableKeys as $key) {
            if (stripos($metaKey, $key) !== false) {
                return false;
            }
        }

        // Skip translation for content that appears to be:
        // - URLs
        // - File paths
        // - Email addresses
        // - JSON
        // - HTML tags only
        // - Numbers only

        // Check if it's a URL or email
        if (filter_var($text, FILTER_VALIDATE_URL) || filter_var($text, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Check if it's a file path
        if (preg_match('/^[\/\w\-\.]+\.(png|jpg|jpeg|gif|svg|css|js|pdf|ico)$/i', $text)) {
            return false;
        }

        // Check if it's JSON
        json_decode($text);
        if (json_last_error() === JSON_ERROR_NONE) {
            return false;
        }

        // Check if it's just HTML tags with no content
        $strippedText = strip_tags($text);
        if (empty(trim($strippedText))) {
            return false;
        }

        // Check if it's only numbers and special characters
        if (preg_match('/^[\d\s\.\,\-\+\$\%\#\@\!\?\:\;\(\)]+$/', $text)) {
            return false;
        }

        return true;
    }
}
