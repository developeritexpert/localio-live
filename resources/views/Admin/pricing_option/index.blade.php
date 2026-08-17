@extends('admin_layout.master')
@section('content')
<style>
    span#select2-countrySelect-container {
    padding-right: 3rem;
    padding-top: 0rem;
}
    .select2-container--default .select2-selection--single {
        border-color: #dbdfea;
        background-color: #fff;
        height: calc(2.125rem + 2px);
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #526484;
        line-height: calc(2.125rem + 2px);
        padding-left: 1rem;
        padding-right: calc(1rem + 16px);
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(2.125rem + 2px);
        position: absolute;
        top: 1px;
        right: 1px;
        width: 20px;
    }
</style>
<div class="nk-block nk-block-lg">
    <div class="nk-block-head">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h4 class="nk-block-title">Pricing Options</h4>
            </div>
            
            {{-- Add Pricing Options and Filter by Language --}}
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1"
                        data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                    <div class="toggle-expand-content" data-content="pageMenu">
                        <ul class="nk-block-tools g-3">
                            <li>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <div class="form-control-wrap">
                                        <select class="form-select js-select2" data-placeholder="Select Country" id="countrySelect" onchange="filterPricingByCountry(this.value)">
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->lang_code }}" {{ $country->lang_code == $langCode ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </li>
                            <li class="nk-block-tools-opt">
                                <a href="#" data-target="addProduct"
                                class="toggle btn btn-icon btn-primary d-md-none">
                                    <em class="icon ni ni-plus"></em>
                                </a>

                                @if(getCurrentLanguageID() === 1)
                                    <!-- Main Add Button -->
                                    <a href="{{ route('priceoptionsAdd') }}"
                                    class="btn btn-primary d-none d-md-inline-flex btn-localio">
                                        <em class=""></em><span>Add Pricing Options</span>
                                    </a>
                            </li>
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
            {{-- End Add Pricing Options --}}

        </div>
    </div>
    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                @if ($price_options->isEmpty())
                    <div class="text-center">
                        <button class="btn btn-primary btn-localio">No data found</button>
                    </div>
                @else
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col"><span class="sub-text">Translated Name</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Name (English)</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Translated Button Text</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Button Text (English)</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Scope / Categories</span></th>
                            <th class="nk-tb-col tb-tnx-action">
                                <span>Action</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($price_options as $price_option)
                            <tr class="nk-tb-item">
                                <td class="nk-tb-col">
                                    <div class="user-card">
                                        <div class="user-info">
                                            @php
                                                $trans = $price_option->translations->where('lang_id', $lang_id)->first();
                                                $eng = $price_option->translations->where('lang_id', 1)->first();
                                                $translatedName = $trans->name ?? ($price_option->slug ?? '');
                                                $englishName = $eng->name ?? ($price_option->slug ?? '');
                                                $translatedButtonText = $trans->button_text ?? ($eng->button_text ?? 'Claim now');
                                                $englishButtonText = $eng->button_text ?? 'Claim now';
                                            @endphp
                                            <span class="tb-lead">{{ $translatedName }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="nk-tb-col">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span class="tb-lead">{{ $englishName }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="nk-tb-col">
                                    <span class="badge bg-outline-primary">{{ $translatedButtonText }}</span>
                                </td>
                                <td class="nk-tb-col">
                                    <span class="badge bg-outline-secondary">{{ $englishButtonText }}</span>
                                </td>
                                <td class="nk-tb-col">
                                    @if($price_option->scope === 'category_specific')
                                        <span class="badge bg-info mb-1">Category Specific</span>
                                        <br>
                                        <small class="text-muted">
                                            @php
                                                $catNames = $price_option->categories->map(function($cat) use ($lang_id) {
                                                    $t = $cat->categoryTranslations->where('lang_id', $lang_id)->first()
                                                      ?? $cat->categoryTranslations->where('lang_id', 1)->first();
                                                    return $t->name ?? 'Category #' . $cat->id;
                                                })->join(', ');
                                            @endphp
                                            {{ $catNames ?: 'No categories assigned' }}
                                        </small>
                                    @else
                                        <span class="badge bg-success">Global</span>
                                    @endif
                                </td>
                                <td class="nk-tb-col nk-tb-col-tools">
                                    <ul class="nk-tb-actions gx-1">
                                        <li>
                                            <div class="drodown">
                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                    data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                <div class="dropdown-menu dropdown-menu-end edit-btn"
                                                    style="height:auto !important;">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li><a
                                                                href="{{ route('priceoptionsAdd', ['id' => $price_option->id]) }}"><em
                                                                     class="icon ni ni-edit-fill"></em><span>Edit</span></a>
                                                         </li>
                                                        <li class="removeConfermation"
                                                        data-url="{{ route('priceoptionsremove',$price_option->id) }}">
                                                            <a
                                                            href="{{ route('priceoptionsremove',$price_option->id)}}"><em
                                                            class="icon ni ni-trash-fill"></em><span>Remove</span></a>
                                                     </li>
                                                    <li>
                                                        <a onclick="openOfferTranslateModal({{ $price_option->id }}, '{{ addslashes($englishName) }}', '{{ addslashes($englishButtonText) }}')">
                                                            <em class="icon ni ni-globe"></em> <span>Translations</span>
                                                        </a>
                                                    </li>

                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                @endif
            </table>
        </div>
    </div>
</div>

{{-- Translation Modal in Offer Option --}}
 <div class="modal fade" id="translateOfferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Translate Offer Option</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Offer Name</label>
                        <input type="text" id="modalOfferName" class="form-control" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Button Text</label>
                        <input type="text" id="modalOfferButtonText" class="form-control" readonly>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Target Languages</label>
                    <div class="form-check mb-2">
                        <input type="checkbox" id="selectAllOfferLanguages" class="form-check-input">
                        <label class="form-check-label fw-bold" for="selectAllOfferLanguages">Select All</label>
                    </div>
                    <div class="row">
                        @foreach($languages as $lang)
                            <div class="col-md-4 col-sm-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input target-offer-language"
                                           value="{{ $lang['id'] }}" id="offer_lang_{{ $lang['id'] }}">
                                    <label class="form-check-label" for="offer_lang_{{ $lang['id'] }}">{{ $lang['name'] }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnStartOfferTranslation">Start Translation</button>
            </div>
        </div>
    </div>
</div>


    <script>
        $(document).ready(function() {
            $('#name').on('input', function() {
                let name = $(this).val().toLowerCase();
                let slug = name.replace(/\s+/g, "-");
                slug = slug.replace(/\//g, "-");
                $('#slug').val(slug);
            });
            $('#slug').on('change', function() {
                this.value = this.value.toLowerCase().replace(/\s+/g, '-').replace(/\//g, '-');
            });
        });
    </script>

{{-- pricing options script --}}
<script>
    function filterPricingByCountry(langCode) {
        if (!langCode) return;
        let url = new URL(window.location.href);
        url.searchParams.set('lang', langCode);
        window.location.href = url.toString();
    }

    $(document).ready(function () {
        $('#countrySelect').on('change select2:select', function () {
            let langCode = $(this).val();
            filterPricingByCountry(langCode);
        });
    });
</script>
{{-- End Script --}}

{{-- Translation Modal Script in Offer Option --}}
<script>
    let currentOfferId = null;

    function openOfferTranslateModal(offerId, offerName, buttonText) {
        currentOfferId = offerId;
        $('#modalOfferName').val(offerName);
        $('#modalOfferButtonText').val(buttonText || 'Claim now');

        $('#selectAllOfferLanguages').prop('checked', false);
        $('.target-offer-language').prop('checked', false);

        $('#translateOfferModal').modal('show');
    }

    $('#selectAllOfferLanguages').on('change', function () {
        $('.target-offer-language').prop('checked', this.checked);
    });

    $('#btnStartOfferTranslation').on('click', function () {
        const targetLanguages = $('.target-offer-language:checked').map(function () {
            return $(this).val();
        }).get();
        const sourceLanguageId = 1;

        if (!currentOfferId) {
            NioApp.Toast('No offer selected.', 'error', { position: 'top-right' });
            return;
        }

        if (targetLanguages.length === 0) {
            NioApp.Toast('Please select at least one target language.', 'error', { position: 'top-right' });
            return;
        }

        let formData = new FormData();
        formData.append('offer_id', currentOfferId);
        formData.append('source_lang_id', sourceLanguageId);
        targetLanguages.forEach(lang => formData.append('target_lang_ids[]', lang));

        $.ajax({
            url: "{{ route('admin.save-offer-translation') }}",
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#translateOfferModal').modal('hide');
                    NioApp.Toast('Translation saved successfully!', 'success', { position: 'top-right' });
                    // Refresh table
                    window.location.reload();
                } else {
                    NioApp.Toast(data.message || 'Failed to save translation.', 'error', { position: 'top-right' });
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                NioApp.Toast('Something went wrong. Check console.', 'error', { position: 'top-right' });
            }
        });
    });
</script>
@endsection
