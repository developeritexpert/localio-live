@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg offer-options">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Offer Options</h3>
                </div>
                {{-- Show the Country Dropdown --}}
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em
                                class="icon ni ni-more-v"></em></a>
                        <div class="toggle-expand-content" data-content="pageMenu">

                            {{-- <ul class="nk-block-tools g-3">

                                <li class="nk-block-tools-opt">
                                    <a href="#" data-target="addProduct"
                                        class="toggle btn btn-icon btn-primary d-md-none"><em
                                            class="icon ni ni-plus"></em></a>
                                            @if(getCurrentLanguageID() === 1)
                                            <a href="{{ route('priceoptionsAdd') }}"
                                                class="btn btn-primary d-none d-md-inline-flex btn-localio"><em
                                            class=""></em><span>Add Pricing Options</span></a>
                                            @endif
                                </li>
                            </ul> --}}

                            {{-- @php
                                $countries = \App\Models\Country::where('status', 1)->get();
                            @endphp --}}

                            {{-- <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    <!-- Mobile Toggle Button -->
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
                            </ul> --}}

                                <!-- Dropdown shown always below the button -->

                                {{-- <div class="mt-2">
                                    <select class="form-select" onchange="location = this.value;">
                                        <option selected disabled>Select Country</option>
                                        @foreach($countries as $country)
                                            <option value="{{ route('priceoptionsAdd', ['country' => $country->code]) }}">
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div> --}}
                            {{-- @endif --}}

                            <div class="mt-2">
                                <select class="form-select" id="countrySelect" >
                                    <option selected disabled>Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->lang_code }}" {{ $langCode == $country->lang_code ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                        </div>
                    </div>
                </div>
                {{-- End Country Dropdown --}}

                {{-- Show the Button Add Pricing Options--}}
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em
                                class="icon ni ni-more-v"></em></a>
                        <div class="toggle-expand-content" data-content="pageMenu">

                            {{-- <ul class="nk-block-tools g-3">

                                <li class="nk-block-tools-opt">
                                    <a href="#" data-target="addProduct"
                                        class="toggle btn btn-icon btn-primary d-md-none"><em
                                            class="icon ni ni-plus"></em></a>
                                            @if(getCurrentLanguageID() === 1)
                                            <a href="{{ route('priceoptionsAdd') }}"
                                                class="btn btn-primary d-none d-md-inline-flex btn-localio"><em
                                            class=""></em><span>Add Pricing Options</span></a>
                                            @endif
                                </li>
                            </ul> --}}

                            {{-- @php
                                $countries = \App\Models\Country::where('status', 1)->get();
                            @endphp --}}

                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    <!-- Mobile Toggle Button -->
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

                                <!-- Dropdown shown always below the button -->

                                {{-- <div class="mt-2">
                                    <select class="form-select" onchange="location = this.value;">
                                        <option selected disabled>Select Country</option>
                                        @foreach($countries as $country)
                                            <option value="{{ route('priceoptionsAdd', ['country' => $country->code]) }}">
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div> --}}
                            @endif

                            {{-- <div class="mt-2">
                                <select class="form-select" id="countrySelect" >
                                    <option selected disabled>Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->lang_code }}" {{ $langCode == $country->lang_code ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> --}}


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
                                <th class="nk-tb-col"><span class="sub-text">Name</span></th>
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
                                             <span class="tb-lead">{{ $price_option->translations->first()->name ?? '' }}</span>

                                            </div>
                                        </div>
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
                                                                    href="{{route('priceoptionsAdd',$price_option->id) }}"><em
                                                                        class="icon ni ni-edit-fill"></em><span>Edit</span></a>
                                                            </li>
                                                            <li class="removeConfermation"
                                                            data-url="{{ route('priceoptionsremove',$price_option->id) }}">
                                                                <a
                                                                href="{{ route('priceoptionsremove',$price_option->id)}}"><em
                                                                class="icon ni ni-trash-fill"></em><span>Remove</span></a>
                                                         </li>
                                                         <li>
                                                            <a onclick="openOfferTranslateModal({{ $price_options->first()->id }}, '{{ $price_options->first()->translations->first()->name ?? $price_options->first()->slug }}')">
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

{{-- Transalation Model in Offer Option --}}
     <div class="modal fade" id="translateOfferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Translate Offer Option</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Offer Name</label>
                        <input type="text" id="modalOfferName" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Source Language</label>
                        <select id="modalOfferSourceLanguage" class="form-select">
                            @foreach($languages as $lang)
                                <option value="{{ $lang['id'] }}">{{ $lang['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Target Languages</label>
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
    $('#countrySelect').on('change', function () {
        let langCode = $('#countrySelect').val();

        $.ajax({
            url: '{{ route("priceoptions") }}',
            type: 'GET',
            data: {
                lang: langCode
            },
            success: function (response) {
                let tbody = '';
                if (response.price_options.length === 0) {
                    tbody = `<tr><td colspan="2" class="text-center"><button class="btn btn-primary btn-localio">No data found</button></td></tr>`;
                } else {
                    response.price_options.forEach(option => {
                        tbody += `
                            <tr class="nk-tb-item">
                                <td class="nk-tb-col">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span class="tb-lead">${option.name ?? ''}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="nk-tb-col nk-tb-col-tools">
                                    <ul class="nk-tb-actions gx-1">
                                        <li>
                                            <div class="drodown">
                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-more-h"></em>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end edit-btn">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li>
                                                            <a href="/admin-dashboard/price-options/add/${option.id}">
                                                                <em class="icon ni ni-edit-fill"></em><span>Edit</span>
                                                            </a>
                                                        </li>
                                                        <li class="removeConfermation" data-url="/admin-dashboard/price-options/remove/${option.id}">
                                                            <a href="/admin-dashboard/price-options/remove/${option.id}">
                                                                <em class="icon ni ni-trash-fill"></em><span>Remove</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        `;
                    });
                }

                $('table tbody').html(tbody);
            },
            error: function (xhr) {
                console.error(xhr);
                alert('Something went wrong.');
            }
        });
    });
</script>
{{-- End Script --}}

{{-- Transalaton Model Script in Offer Option --}}
<script>
    let currentOfferId = null;

    function openOfferTranslateModal(offerId, offerName) {
        currentOfferId = offerId;
        $('#modalOfferName').val(offerName);
        console.log(offerName);

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
        const sourceLanguageId = $('#modalOfferSourceLanguage').val();

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
