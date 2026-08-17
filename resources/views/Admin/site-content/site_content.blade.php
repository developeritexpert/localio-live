@extends('admin_layout.master')
@section('content')
<style>
    span#select2-countrySelect-container {
        padding-right: 3rem;
        /* padding-top: 0rem; */
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
