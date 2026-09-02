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
                <h4 class="title nk-block-title">Manage Untranslated Content</h4>
                <p class="text-muted mb-0 mt-1" style="font-size: 13px;">Manage region-specific dynamic variables and global texts that are excluded from general translation and set manually per country/region.</p>
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
            <div class="row g-3">
                @foreach ($sections as $sectionKey => $section)
                    <div class="col-12 mb-3">
                        <form action="{{ route('admin.text-content.update') }}" class="form-validate" method="POST">
                            @csrf
                            <input type="hidden" name="lang_id" value="{{ $langId }}">

                            <div class="card border w-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">{{ $section['title'] }}</h6>
                                </div>
                                <div class="card-body">
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
                                            @php
                                                $keyLabels = [
                                                    'country1' => 'Country1 (e.g., U.S.)',
                                                    'country2' => 'Country2 (e.g., for the U.S.)',
                                                ];
                                                $displayLabel = $keyLabels[$key] ?? ucwords(str_replace('_', ' ', $key));
                                            @endphp
                                            <div class="form-group col-lg-12">
                                                <label class="form-label" for="{{ $key }}">
                                                    {{ $displayLabel }}
                                                </label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control"
                                                        id="{{ $key }}" name="texts[{{ $key }}]" value="{{ $value }}" />
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="submit" class="btn btn-primary btn-localio" style="background-color: #fd5602; border-color: #fd5602;">
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
        window.location.href = "{{ route('untranslated-site-page') }}?lang_code=" + langCode;
    }
</script>
@endsection
