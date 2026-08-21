@extends('admin_layout.master')
@section('content')
<style>
    span#select2-countrySelect-container {
        padding-right: 3rem;
    }
    .select2-container--default .select2-selection--single {
        border-color: #dbdfea;
        background-color: #fff;
        border-radius: 4px;
        height: calc(2.125rem + 2px);
    }
</style>

<div class="nk-block nk-block-lg site-content">
    <div class="nk-block-head">
        <div class="nk-block-between align-items-center">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Manage Text Content</h4>
            </div>
            {{-- Country/Region Selector --}}
            <div class="nk-block-head-content">
                <div class="form-group mb-0">
                    <div class="form-control-wrap">
                        <select class="form-select js-select2" data-placeholder="Select Country/Region" id="countrySelect" onchange="filterContentByCountry(this.value)">
                            @if(isset($languages))
                                @foreach ($languages as $language)
                                    <option value="{{ $language->lang_code }}" {{ (isset($langCode) && $langCode == $language->lang_code) || (isset($langId) && $langId == $language->id) ? 'selected' : '' }}>
                                        {{ $language->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-bordered">
        <div class="card-inner">
            <div class="row g-4">
                {{-- 1. URL Slugs / Page URL Translations Section --}}
                <div class="col-12">
                    <form action="{{ route('admin.text-content.update') }}" class="form-validate" method="POST">
                        @csrf
                        <input type="hidden" name="lang_id" value="{{ $langId }}">

                        <div class="card border w-100">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Page URL Slugs</h6>
                                <button type="submit" class="btn btn-sm btn-primary btn-localio">
                                    Update Page URL Slugs
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="faq_slug">FAQ URL Slug (e.g. faqs, preguntas-frecuentes)</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="faq_slug" name="slugs[faq_slug]"
                                                    value="{{ old('slugs.faq_slug', $currentLanguage->faq_slug ?? 'faqs') }}" placeholder="faqs" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="alternatives_slug">Alternatives URL Slug (e.g. alternatives, alternativas)</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="alternatives_slug" name="slugs[alternatives_slug]"
                                                    value="{{ old('slugs.alternatives_slug', $currentLanguage->alternatives_slug ?? 'alternatives') }}" placeholder="alternatives" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="reviews_slug">Reviews URL Slug (e.g. reviews, resenas, all-review)</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="reviews_slug" name="slugs[reviews_slug]"
                                                    value="{{ old('slugs.reviews_slug', $currentLanguage->reviews_slug ?? 'reviews') }}" placeholder="reviews" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="comparisons_slug">Comparisons URL Slug (e.g. comparisons, comparativas)</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="comparisons_slug" name="slugs[comparisons_slug]"
                                                    value="{{ old('slugs.comparisons_slug', $currentLanguage->comparisons_slug ?? 'comparisons') }}" placeholder="comparisons" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-primary btn-localio">
                                        Update Page URL Slugs
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- 2. Other Text Content Sections --}}
                @foreach ($sections as $sectionKey => $section)
                    <div class="col-12">
                        <form action="{{ route('admin.text-content.update') }}" class="form-validate" method="POST">
                            @csrf
                            <input type="hidden" name="lang_id" value="{{ $langId }}">

                            <div class="card border w-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ $section['title'] }}</h6>
                                    <button type="submit" class="btn btn-sm btn-primary btn-localio">
                                        Update {{ $section['title'] }}
                                    </button>
                                </div>
                                <div class="card-body">
                                    @if ($sectionKey === 'rating_labels')
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 20%;">Rating Range</th>
                                                        <th style="width: 25%;">Min Threshold (0.0 - 5.0)</th>
                                                        <th style="width: 55%;">Word Label for Current Language ({{ $currentLanguage->name ?? 'English' }})</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $tiers = [
                                                            ['range' => '4.5 – 5.0', 'label_key' => 'rating_label_excellent', 'thresh_key' => 'rating_threshold_excellent', 'default_word' => 'Excellent', 'default_t' => '4.5'],
                                                            ['range' => '4.0 – 4.4', 'label_key' => 'rating_label_great', 'thresh_key' => 'rating_threshold_great', 'default_word' => 'Great', 'default_t' => '4.0'],
                                                            ['range' => '3.0 – 3.9', 'label_key' => 'rating_label_good', 'thresh_key' => 'rating_threshold_good', 'default_word' => 'Good', 'default_t' => '3.0'],
                                                            ['range' => '2.0 – 2.9', 'label_key' => 'rating_label_satisfactory', 'thresh_key' => 'rating_threshold_satisfactory', 'default_word' => 'Satisfactory', 'default_t' => '2.0'],
                                                            ['range' => '1.0 – 1.9', 'label_key' => 'rating_label_poor', 'thresh_key' => 'rating_threshold_poor', 'default_word' => 'Poor', 'default_t' => '1.0'],
                                                        ];
                                                    @endphp
                                                    @foreach ($tiers as $tier)
                                                        @php
                                                            $lblObj = $keys[$tier['label_key']] ?? null;
                                                            $lblVal = '';
                                                            if ($lblObj) {
                                                                if ($langId == 1) {
                                                                    $lblVal = $lblObj->default_value ?? $tier['default_word'];
                                                                } else {
                                                                    $trans = $lblObj->translations->first();
                                                                    $lblVal = $trans ? $trans->value : ($lblObj->default_value ?? $tier['default_word']);
                                                                }
                                                            } else {
                                                                $lblVal = $tier['default_word'];
                                                            }

                                                            $thrObj = $keys[$tier['thresh_key']] ?? null;
                                                            $thrVal = $thrObj->default_value ?? $tier['default_t'];
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <span class="badge bg-primary fs-13px">{{ $tier['range'] }}</span>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.1" min="0" max="5" class="form-control"
                                                                    name="texts[{{ $tier['thresh_key'] }}]" value="{{ $thrVal }}" placeholder="{{ $tier['default_t'] }}" />
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control fw-bold"
                                                                    name="texts[{{ $tier['label_key'] }}]" value="{{ $lblVal }}" placeholder="{{ $tier['default_word'] }}" />
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                    <div class="row g-3">
                                        @foreach ($section['keys'] as $key)
                                            @php
                                                $keyObj = $keys[$key] ?? null;
                                                $value = '';
                                                if ($keyObj) {
                                                    if ($langId == 1) {
                                                        $value = $keyObj->default_value ?? '';
                                                    } else {
                                                        $trans = $keyObj->translations->first();
                                                        $value = $trans ? $trans->value : ($keyObj->default_value ?? '');
                                                    }
                                                }
                                            @endphp
                                            <div class="form-group col-lg-12">
                                                <label class="form-label" for="{{ $key }}">
                                                    {{ ucwords(str_replace('_', ' ', $key)) }}
                                                </label>
                                                <div class="form-control-wrap">
                                                    @if (str_contains($key, '_des') || str_contains($key, 'mail_below_text') || str_contains($key, '_desc') || str_contains($key, '_message'))
                                                        <textarea class="form-control" id="{{ $key }}"
                                                            name="texts[{{ $key }}]" rows="3">{{ $value }}</textarea>
                                                    @else
                                                        <input type="text" class="form-control"
                                                            id="{{ $key }}" name="texts[{{ $key }}]" value="{{ $value }}" />
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @endif
                                    <div class="text-end mt-3">
                                        <button type="submit" class="btn btn-primary btn-localio">
                                            Update {{ $section['title'] }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    function filterContentByCountry(langCode) {
        if (!langCode) return;
        let url = new URL(window.location.href);
        url.searchParams.set('lang', langCode);
        window.location.href = url.toString();
    }

    $(document).ready(function () {
        $('#countrySelect').on('change select2:select', function () {
            let langCode = $(this).val();
            filterContentByCountry(langCode);
        });
    });
</script>
@endsection
