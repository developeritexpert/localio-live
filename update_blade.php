<?php
$filePath = '/home/ubuntu/projects/localio-live/resources/views/Admin/categories/add.blade.php';
$content = file_get_contents($filePath);

$search = <<<HTML
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="top_rated_listing_subtext">Top-Rated Listing Subtext</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="top_rated_listing_subtext" name="top_rated_listing_subtext" rows="3" placeholder="Enter subtext displayed below the H2 headline on the Top Rated page">{{ isset(\$category_data) ? (\$category_data['top_rated_listing_subtext'] ?? '') : old('top_rated_listing_subtext') }}</textarea>
                                </div>
                                <small class="text-muted d-block mt-1">Short text placed immediately below the Top-Rated Listing Headline.</small>
                                @error('top_rated_listing_subtext')
                                    <div class="error text-danger">{{ \$message }}</div>
                                @enderror
                            </div>
                        </div>
HTML;

$replace = <<<HTML
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="top_rated_listing_subtext">Top-Rated Listing Subtext</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="top_rated_listing_subtext" name="top_rated_listing_subtext" rows="3" placeholder="Enter subtext displayed below the H2 headline on the Top Rated page">{{ isset(\$category_data) ? (\$category_data['top_rated_listing_subtext'] ?? '') : old('top_rated_listing_subtext') }}</textarea>
                                </div>
                                <small class="text-muted d-block mt-1">Short text placed immediately below the Top-Rated Listing Headline.</small>
                                @error('top_rated_listing_subtext')
                                    <div class="error text-danger">{{ \$message }}</div>
                                @enderror
                            </div>
                        </div>

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

$content = str_replace($search, $replace, $content);
// Attempt 2 in case of line endings
$search2 = str_replace("\r\n", "\n", $search);
$replace2 = str_replace("\r\n", "\n", $replace);
$content = str_replace($search2, $replace2, $content);

file_put_contents($filePath, $content);
echo "Updated add.blade.php.\n";
