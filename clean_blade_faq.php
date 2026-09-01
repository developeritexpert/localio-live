<?php
$filePath = '/home/ubuntu/projects/localio-live/resources/views/Admin/categories/add.blade.php';
$content = file_get_contents($filePath);

$pattern = '/<!-- FAQ Page Content & Settings Section -->.*?<\/div>\s*<\/div>\s*<\/div>/s';
preg_match_all($pattern, $content, $matches);

if (isset($matches[0]) && count($matches[0]) > 1) {
    // Keep only the first occurrence, remove others
    $firstOccurrence = $matches[0][0];
    // Remove all occurrences
    $cleanedContent = preg_replace($pattern, '', $content);
    // Put back exactly one occurrence right after top_rated_description block
    $search = '</label>
                                @error(\'top_rated_description\')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>';
    $search_n = str_replace("\r\n", "\n", $search);
    
    if (strpos($cleanedContent, $search) !== false) {
        $cleanedContent = str_replace($search, $search . "\n\n" . $firstOccurrence, $cleanedContent);
    } elseif (strpos($cleanedContent, $search_n) !== false) {
        $cleanedContent = str_replace($search_n, $search_n . "\n\n" . $firstOccurrence, $cleanedContent);
    }
    file_put_contents($filePath, $cleanedContent);
    echo "Cleaned duplicate FAQ section in add.blade.php.\n";
} else {
    echo "Count of matches: " . (isset($matches[0]) ? count($matches[0]) : 0) . "\n";
}
