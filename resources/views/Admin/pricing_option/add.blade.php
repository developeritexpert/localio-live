@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">
                    {{ isset($pricing_data) ? 'Update Pricing Option' : 'Add Pricing Option' }}
                </h4>
            </div>
            <div>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('priceoptionsAddprocess') }}" class="form-validate" novalidate="novalidate" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pricing_option_id" value="{{ isset($pricing_data) ? $pricing_data->id : '' }}" />
                    <input type="hidden" name="lang_id" value="{{ $lang_id ?? 1 }}" />

                    <div class="row g-gs">
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="form-label" for="languageSelect">Country / Language</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" id="languageSelect" onchange="location = this.value;">
                                        @foreach($languages as $lang)
                                            @php
                                                $editUrl = isset($pricing_data) 
                                                    ? route('priceoptionsAdd', ['id' => $pricing_data->id, 'lang' => $lang->lang_code])
                                                    : route('priceoptionsAdd', ['lang' => $lang->lang_code]);
                                            @endphp
                                            <option value="{{ $editUrl }}" {{ ($langCode ?? '') == $lang->lang_code ? 'selected' : '' }}>
                                                {{ $lang->name }} ({{ $lang->lang_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="name">Name</label>
                                <div class="form-control-wrap">
                                    @php
                                        $editName = '';
                                        if (isset($pricing_data)) {
                                            $targetLangId = $lang_id ?? 1;
                                            $targetTrans = $pricing_data->translations->where('lang_id', $targetLangId)->first();
                                            $engTrans = $pricing_data->translations->where('lang_id', 1)->first();
                                            $editName = $targetTrans->name ?? ($engTrans->name ?? $pricing_data->slug);
                                        }
                                    @endphp
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $editName) }}" />
                                </div>
                                @error('name')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label d-block" for="status-toggle">Status</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="status-toggle" name="status"
                                        {{ isset($pricing_data) ? ($pricing_data->status ? 'checked' : '') : 'checked' }}>
                                    <label class="custom-control-label" for="status-toggle" id="status-label">
                                        {{ isset($pricing_data) && !$pricing_data->status ? 'Inactive' : 'Active' }}
                                    </label>
                                </div>
                                @error('status')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-12 mt-5">
                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-primary btn-localio">{{ isset($pricing_data) ? 'Update Pricing Option' : 'Save Pricing Option' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('status-toggle');
            const label = document.getElementById('status-label');

            function updateLabel() {
                if (toggle.checked) {
                    label.textContent = 'Active';
                } else {
                    label.textContent = 'Inactive';
                }
            }

            toggle.addEventListener('change', updateLabel);

            // Initialize on page load
            updateLabel();
        });
    </script>
@endsection
