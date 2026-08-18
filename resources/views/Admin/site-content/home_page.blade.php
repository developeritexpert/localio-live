@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg pages-home">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Home Page Content</h4>
            </div>
        </div>
        <?php
            use Illuminate\Support\Facades\Redis;
            $lang_code = Redis::get('admin_lang_code') ?? getCurrentLocale();
        ?>
        @if (isset($homeContents) && isset($allHomeContents))
        <form action="{{ route('home-content-update') }}" class="form-validate" novalidate="novalidate" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            {{-- Left Column: Main Content & Homepage Categories --}}
            <div class="col-lg-8">
                <div class="row g-3">
                    {{-- 1. Home Banner Section --}}
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header mt-3">
                                Home Banner Section
                            </div>
                            <div class="card-body">
                                @foreach ($homeContents as $content)
                                    @if ($content->meta_key == 'header_img')
                                        <div class="form-group mb-3">
                                            <label class="form-label" for="image">Header Image</label>
                                            <div class="dz-message">
                                                <input type="file" class="form-control" name="header_image[{{ $content->id }}]" id="metaValue" value="{{ $content->value ?? '' }}">
                                            </div>
                                            @error('header_image')
                                                <div class="error text-danger">{{ $message }}</div>
                                            @enderror
                                            @if (!empty($content->meta_value))
                                                <img src="{{ asset($content->meta_value) }}" alt="{{ $content->meta_key }}" class="mt-2 rounded" style="width: 100px; height: auto;">
                                            @endif
                                        </div>
                                    @elseif($content->meta_key == 'header_background_img')
                                        <div class="form-group mb-3">
                                            <label class="form-label" for="image">Header Background Image</label>
                                            <div class="dz-message">
                                                <input type="file" class="form-control" name="header_backgound_image[{{ $content->id }}]" id="metaValue" value="{{ $content->value ?? '' }}">
                                            </div>
                                            @error('header_backgound_image')
                                                <div class="error text-danger">{{ $message }}</div>
                                            @enderror
                                            @if (!empty($content->meta_value))
                                                <img src="{{ asset($content->meta_value) }}" alt="{{ $content->meta_key }}" class="mt-2 rounded" style="width: 100px; height: auto;">
                                            @endif
                                        </div>
                                    @endif
                                @endforeach

                                @foreach ($allHomeContents as $key => $val)
                                    @if ($val->meta_key == 'header_title')
                                        <div class="form-group col-lg-12 mb-3">
                                            <label class="form-label" for="{{ $key }}">Heading</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="{{ $key }}" name="header_title[{{ $val->id }}]" value="{{ $val->meta_value ?? '' }}" />
                                            </div>
                                        </div>
                                    @elseif($val->meta_key === 'header_description')
                                        <div class="form-group col-lg-12 mb-3">
                                            <label class="form-label" for="{{ $key }}">Description</label>
                                            <div class="form-control-wrap">
                                                <textarea class="form-control" id="{{ $key }}" name="header_description[{{ $val->id }}]" rows="3">{{ $val->meta_value ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    @elseif($val->meta_key === 'placeholder_text')
                                        <div class="form-group col-lg-12 mb-3">
                                            <label class="form-label" for="{{ $key }}">Home Page Search Placeholder</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="{{ $key }}" name="placeholder_text[{{ $val->id }}]" value="{{ $val->meta_value ?? '' }}" />
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- 2. Homepage Categories Management Card --}}
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header mt-3">
                                Homepage Categories & Custom "View All" Side Link Names
                            </div>
                            <div class="card-body">
                                {{-- Section Title Input --}}
                                @php
                                    $mostPopularContent = $allHomeContents->firstWhere('meta_key', 'most_popular');
                                @endphp
                                @if($mostPopularContent)
                                    <div class="form-group mb-3 pb-3 border-bottom">
                                        <label class="form-label fw-bold" for="most_popular_title">Section Title</label>
                                        <div class="form-control-wrap">
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="most_popular_title" 
                                                   name="most_popular[{{ $mostPopularContent->id }}]" 
                                                   value="{{ $mostPopularContent->meta_value ?? 'Most popular categories' }}" 
                                                   placeholder="Most popular categories" />
                                        </div>
                                        <small class="text-muted">This title is displayed on the homepage sidebar for this section (defaults to <em>"Most popular categories"</em>).</small>
                                    </div>
                                @endif

                                <p class="text-muted small mb-3">Select which categories appear on the homepage, specify their display order, product limit, and custom side link text (e.g. <em>"View all project management software"</em>, <em>"View web hosting services"</em>).</p>

                                <!-- Category Selector / Picker -->
                                <div class="row g-2 mb-3 align-items-center bg-light p-2 rounded">
                                    <div class="col-md-8">
                                        <select id="category_select_picker" class="form-select form-control">
                                            <option value="">-- Choose Category or Subcategory to Add to Homepage --</option>
                                            @if(isset($allCategories))
                                                @php
                                                    $mainCategories = $allCategories->filter(fn($c) => empty($c->parent_id) || $c->parent_id == 0)->sortBy(function($c) {
                                                        $t = $c->translation ?? $c->translations ?? ($c->categoryTranslations ? $c->categoryTranslations->first() : null);
                                                        return $t ? $t->name : '';
                                                    }, SORT_NATURAL|SORT_FLAG_CASE);

                                                    $subCategories = $allCategories->filter(fn($c) => !empty($c->parent_id) && $c->parent_id > 0)->sortBy(function($c) {
                                                        $t = $c->translation ?? $c->translations ?? ($c->categoryTranslations ? $c->categoryTranslations->first() : null);
                                                        return $t ? $t->name : '';
                                                    }, SORT_NATURAL|SORT_FLAG_CASE);
                                                @endphp

                                                @if($mainCategories->isNotEmpty())
                                                    <optgroup label="-- Main Categories --">
                                                        @foreach($mainCategories as $mainCat)
                                                            @php
                                                                $mTrans = $mainCat->translation ?? $mainCat->translations ?? ($mainCat->categoryTranslations ? $mainCat->categoryTranslations->first() : null);
                                                                $mName = $mTrans->name ?? 'Category #' . $mainCat->id;
                                                            @endphp
                                                            <option value="{{ $mainCat->id }}" data-name="{{ e($mName) }}">
                                                                {{ $mName }} (Main Category)
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif

                                                @if($subCategories->isNotEmpty())
                                                    <optgroup label="-- Subcategories --">
                                                        @foreach($subCategories as $subCat)
                                                            @php
                                                                $sTrans = $subCat->translation ?? $subCat->translations ?? ($subCat->categoryTranslations ? $subCat->categoryTranslations->first() : null);
                                                                $sName = $sTrans->name ?? 'Category #' . $subCat->id;
                                                                $pTrans = $subCat->parent?->translation ?? $subCat->parent?->translations ?? ($subCat->parent?->categoryTranslations ? $subCat->parent->categoryTranslations->first() : null);
                                                                $parentName = $pTrans ? $pTrans->name : null;
                                                            @endphp
                                                            <option value="{{ $subCat->id }}" data-name="{{ e($sName) }}">
                                                                {{ $sName }} {{ $parentName ? "({$parentName})" : '' }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-primary w-100" id="btn_add_category_to_homepage">
                                            <i class="fa fa-plus me-1"></i> Add Category to Homepage
                                        </button>
                                    </div>
                                </div>

                                <!-- Active Homepage Categories Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0" id="homepage_categories_table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Category Name</th>
                                                <th style="width: 100px;">Order</th>
                                                <th style="width: 100px;">Limit</th>
                                                <th>Custom Side Link Name ("View All" Text)</th>
                                                <th style="width: 70px;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="homepage_categories_tbody">
                                            @if(isset($homepageCategories) && $homepageCategories->isNotEmpty())
                                                @foreach($homepageCategories as $cat)
                                                    @php
                                                        $catTranslation = $cat->translation ?? $cat->translations ?? ($cat->categoryTranslations ? $cat->categoryTranslations->first() : null);
                                                        $catName = $catTranslation->name ?? 'Category #' . $cat->id;
                                                    @endphp
                                                    <tr id="cat_row_{{ $cat->id }}">
                                                        <td>
                                                            <strong>{{ $catName }}</strong>
                                                            <input type="hidden" name="category_config[{{ $cat->id }}][id]" value="{{ $cat->id }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control form-control-sm"
                                                                name="category_config[{{ $cat->id }}][homepage_order]"
                                                                value="{{ $cat->homepage_order ?? 0 }}" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control form-control-sm"
                                                                name="category_config[{{ $cat->id }}][homepage_product_limit]"
                                                                value="{{ $cat->homepage_product_limit ?? 6 }}" min="1" max="50">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm"
                                                                name="category_config[{{ $cat->id }}][homepage_link_text]"
                                                                value="{{ $catTranslation->homepage_link_text ?? '' }}"
                                                                placeholder="View all {{ strtolower($catName) }} software">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-cat-btn" onclick="removeHomepageCategoryRow({{ $cat->id }})">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr id="empty_cats_row">
                                                    <td colspan="5" class="text-center py-4 text-muted">
                                                        <i class="fa fa-info-circle text-info me-1"></i> No homepage categories selected yet. Select a category from the dropdown above and click "Add Category to Homepage".
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Language, URL & Meta Tag Info --}}
            <div class="col-lg-4">
                <div class="row g-3">
                    {{-- Action & Settings Card --}}
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <a href="{{ url('/') }}" target="_blank" class="btn btn-link text-primary p-0">
                                        <span><b>View Page</b></span>
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-localio text-center">
                                        <span>Update Content</span>
                                    </button>
                                </div>

                                <div class="form-group mb-3">
                                    @php
                                        $languages = \App\Models\Language::where('status', 1)->get();
                                    @endphp
                                    <label class="form-label font-weight-bold">Country-Region</label>
                                    <select class="form-control" name="language" id="languageDropdown">
                                        @foreach ($languages as $language)
                                            <option value="{{ $language->id }}" {{ $lang_code == $language->lang_code ? 'selected' : '' }}>
                                                {{ $language->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                @php
                                    $permanentUrl = $allHomeContents->firstWhere('meta_key', 'permanent_url')?->meta_value ?? '';
                                @endphp
                                <div class="form-group">
                                    <label class="form-label permanent_url">Permanent Link</label>
                                    <input type="text"
                                           name="permanent_url"
                                           class="form-control"
                                           placeholder="Page-URL"
                                           value="{{ old('permanent_url', $permanentUrl) }}" />
                                    @error('permanent_url')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Meta Tag Info Card --}}
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header mt-3">
                                Meta Tag Info
                            </div>
                            <div class="card-body">
                                @foreach ($allHomeContents as $key => $val)
                                    @if ($val->meta_key == 'meta_home_title')
                                        <div class="form-group col-lg-12 mb-3">
                                            <label class="form-label" for="{{ $key }}">Meta Title</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control"
                                                    id="{{ $key }}"
                                                    name="meta_home_title[{{ $val->id }}]"
                                                    value="{{ $val->meta_value ?? '' }}" />
                                            </div>
                                        </div>
                                    @elseif($val->meta_key === 'Meta_home_description')
                                        <div class="form-group col-lg-12 mb-3">
                                            <label class="form-label" for="{{ $key }}">Meta Description</label>
                                            <div class="form-control-wrap">
                                                <textarea class="form-control" id="{{ $key }}" name="Meta_home_description[{{ $val->id }}]" rows="4">{{ $val->meta_value ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var addBtn = document.getElementById('btn_add_category_to_homepage');
            var selectPicker = document.getElementById('category_select_picker');
            var tbody = document.getElementById('homepage_categories_tbody');

            if (addBtn && selectPicker && tbody) {
                addBtn.addEventListener('click', function() {
                    var selectedId = selectPicker.value;
                    if (!selectedId) {
                        alert('Please select a category first.');
                        return;
                    }

                    var selectedOption = selectPicker.options[selectPicker.selectedIndex];
                    var catName = selectedOption.getAttribute('data-name') || selectedOption.text;

                    if (document.getElementById('cat_row_' + selectedId)) {
                        alert('This category is already added to the homepage list below.');
                        return;
                    }

                    var emptyRow = document.getElementById('empty_cats_row');
                    if (emptyRow) {
                        emptyRow.remove();
                    }

                    var tr = document.createElement('tr');
                    tr.id = 'cat_row_' + selectedId;
                    tr.innerHTML = `
                        <td>
                            <strong>` + catName + `</strong>
                            <input type="hidden" name="category_config[` + selectedId + `][id]" value="` + selectedId + `">
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm"
                                name="category_config[` + selectedId + `][homepage_order]"
                                value="0" min="0">
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm"
                                name="category_config[` + selectedId + `][homepage_product_limit]"
                                value="6" min="1" max="50">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm"
                                name="category_config[` + selectedId + `][homepage_link_text]"
                                value=""
                                placeholder="View all ` + catName.toLowerCase() + ` software">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeHomepageCategoryRow(` + selectedId + `)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    `;

                    tbody.appendChild(tr);
                    selectPicker.value = '';
                });
            }
        });

        function removeHomepageCategoryRow(catId) {
            var row = document.getElementById('cat_row_' + catId);
            if (row) {
                row.remove();
            }
            var tbody = document.getElementById('homepage_categories_tbody');
            if (tbody && tbody.children.length === 0) {
                tbody.innerHTML = `
                    <tr id="empty_cats_row">
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fa fa-info-circle text-info me-1"></i> No homepage categories selected yet. Select a category from the dropdown above and click "Add Category to Homepage".
                        </td>
                    </tr>
                `;
            }
        }

        $(document).ready(function () {
            $('#languageDropdown').on('change', function () {
                var langId = $(this).val();
                var csrfToken = "{{ csrf_token() }}";

                $.ajax({
                    url: "{{ route('admin.getContentByLanguage') }}",
                    type: "POST",
                    data: {
                        language_id: langId,
                        _token: csrfToken
                    },
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", xhr.responseText);
                        alert("Error occurred: " + xhr.responseText);
                    }
                });
            });
        });
    </script>
@endsection
