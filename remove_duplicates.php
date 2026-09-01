<?php
$filePath = '/home/ubuntu/projects/localio-live/resources/views/Admin/categories/add.blade.php';
$content = file_get_contents($filePath);

$duplicate = <<<HTML
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="compare_alternatives_subtext">Compare Alternatives Subtext</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="compare_alternatives_subtext" name="compare_alternatives_subtext" rows="3" placeholder="Enter subtext displayed below the Compare Alternatives headline">{{ isset(\$category_data) ? (\$category_data['compare_alternatives_subtext'] ?? '') : old('compare_alternatives_subtext') }}</textarea>
                                </div>
                                <small class="text-muted d-block mt-1">Short text placed immediately below the "Compare {Category} alternatives" Headline.</small>
                                @error('compare_alternatives_subtext')
                                    <div class="error text-danger">{{ \$message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="community_says_subtext">Community Says Subtext</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="community_says_subtext" name="community_says_subtext" rows="3" placeholder="Enter subtext displayed below the What the community says headline">{{ isset(\$category_data) ? (\$category_data['community_says_subtext'] ?? '') : old('community_says_subtext') }}</textarea>
                                </div>
                                <small class="text-muted d-block mt-1">Short text placed immediately below the "What the Localio community says" Headline.</small>
                                @error('community_says_subtext')
                                    <div class="error text-danger">{{ \$message }}</div>
                                @enderror
                            </div>
                        </div>
HTML;

// Find the position of the first occurrence
$pos = strpos($content, $duplicate);
if ($pos !== false) {
    // Find the position of the second occurrence starting from after the first
    $pos2 = strpos($content, $duplicate, $pos + strlen($duplicate));
    if ($pos2 !== false) {
        // Replace the second occurrence with empty string
        $content = substr_replace($content, '', $pos2, strlen($duplicate));
        file_put_contents($filePath, $content);
        echo "Removed duplicate block.";
    } else {
        echo "Second occurrence not found. Line endings issue?";
    }
} else {
    echo "First occurrence not found.";
}
