@extends('admin_layout.master')
@section('content')
<style>
    #DataTables_Table_0_wrapper .drodown .dropdown-menu.dropdown-menu-end ul.link-list-opt li a {
    cursor: pointer;
}
</style>
    <div class="nk-block nk-block-lg business-categories">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Categories</h3>
                </div>
                {{-- Show the Country Dropdown --}}
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <div class="toggle-expand-content" data-content="pageMenu">

                            {{-- <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                            @if(getCurrentLanguageID() === 1)
                                            <a href="{{ route('add-category') }}"
                                                class="btn btn-primary d-none d-md-inline-flex btn-localio"><em
                                                class=""></em><span>Add Categories</span></a>
                                            @endif
                                </li>
                            </ul> --}}
                            

                                <!-- Language Dropdown Section -->
                                <li>
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle dropdown-indicator btn btn-outline-light btn-white" data-bs-toggle="dropdown">
                                            {{ session('category_lang_name', 'United States - English') }}
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <ul class="link-list-opt no-bdr">
                                                @php
                                                    $languages = \App\Models\Language::where('status', 1)->get();
                                                @endphp
                                                @foreach ($languages as $language)
                                                    <li class="{{ session('category_lang_code') == $language->lang_code ? 'active' : '' }}">
                                                        <a href="{{ route('set-category-language', ['lang_id' => $language->id]) }}">
                                                            <span>{{ $language->name }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </li>

                            {{-- End Dropdown --}}

                        </div>
                    </div>
                </div>
                {{-- End Country Dropdown  --}}

                {{-- Show the Add Categories Button --}}
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                            @if(getCurrentLanguageID() === 1)
                                            <a href="{{ route('add-category') }}"
                                                class="btn btn-primary d-none d-md-inline-flex btn-localio"><em
                                                class=""></em><span>Add Categories</span></a>
                                            @endif
                                        </li>
                                    </ul>

                                <!-- Language Dropdown Section -->
                                {{-- <li>
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle dropdown-indicator btn btn-outline-light btn-white" data-bs-toggle="dropdown">
                                            {{ session('category_lang_name', 'United States - English') }}
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <ul class="link-list-opt no-bdr">
                                                @php
                                                    $languages = \App\Models\Language::where('status', 1)->get();
                                                @endphp
                                                @foreach ($languages as $language)
                                                    <li class="{{ session('category_lang_code') == $language->lang_code ? 'active' : '' }}">
                                                        <a href="{{ route('set-category-language', ['lang_id' => $language->id]) }}">
                                                            <span>{{ $language->name }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </li> --}}

                            {{-- End Dropdown --}}

                        </div>
                    </div>
                </div>
                {{-- End Add Categories Button --}}
            </div>
        </div>
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                    @if ($categories->isEmpty())
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
                            @foreach ($categories as $category)
                                <tr class="nk-tb-item">
                                    <td class="nk-tb-col">
                                        <div class="user-card">
                                            <div class="user-info">
                                                <span class="tb-lead">{{ $category->name ?? '' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="nk-tb-col nk-tb-col-tools">
                                        <ul class="nk-tb-actions gx-1">
                                            <li>
                                                <div class="drodown">
                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                        data-bs-toggle="dropdown" ><em class="icon ni ni-more-h"></em></a>
                                                    <div class="dropdown-menu dropdown-menu-end"
                                                        style="list-style: none; padding: 0; margin: 0; height:auto; overflow:hidden !important;">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li><a
                                                                    href="{{route('add-category',$category->id)}}"><em
                                                                        class="icon ni ni-edit-fill" ></em><span>Edit</span></a>
                                                            </li>

                                                            <li><a
                                                                href="{{route('add-topic-category',$category->category->id)}}"><em class="icon ni ni-contact"></em>
                                                                <span>Business Topics</span></a>
                                                            </li>

                                                            <li class="removeConfermation"
                                                            data-url="{{  route('admin-remove-categories',$category->id) }}">
                                                                <a
                                                                href="{{ route('admin-remove-categories',$category->id)}}"><em
                                                                class="icon ni ni-trash-fill"></em><span>Delete</span></a>
                                                              </li>

                                                        {{-- Add Transalate --}}
                                                         {{-- @php
                                                            dd($category->lang_id);
                                                        @endphp --}}

                                                        <li>
                                                            <a onclick="openCategoryTranslateModal({{ $category->category->id }}, '{{ $category->name }}')">
                                                                <em class="icon ni ni-globe"></em> Translations
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

    <!-- Translate Business Category Modal -->
    <div class="modal fade" id="translateCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Translate Business Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Category Name</label>
                        <input type="text" id="modalCategoryName" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Source Language</label>
                        <select id="modalSourceLanguage" class="form-select">
                            @foreach($languages as $lang)
                                <option value="{{ $lang['id'] }}">{{ $lang['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Target Languages</label>
                        <div class="form-check mb-2">
                            <input type="checkbox" id="selectAllLanguages" class="form-check-input">
                            <label class="form-check-label fw-bold" for="selectAllLanguages">Select All</label>
                        </div>
                        <div class="row">
                            @foreach($languages as $lang)
                                <div class="col-md-4 col-sm-6 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input target-language"
                                               value="{{ $lang['id'] }}" id="lang_{{ $lang['id'] }}">
                                        <label class="form-check-label" for="lang_{{ $lang['id'] }}">{{ $lang['name'] }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnStartTranslation">Start Translation</button>
                </div>
            </div>
        </div>
    </div>



 <script>
   let currentCategoryId = null;

function openCategoryTranslateModal(categoryId, categoryName) {
    currentCategoryId = categoryId;
    $('#modalCategoryName').val(categoryName);

    $('#selectAllLanguages').prop('checked', false);
    $('.target-language').prop('checked', false);

    $('#translateCategoryModal').modal('show');
}

$('#btnStartTranslation').on('click', function () {
    const targetLanguages = $('.target-language:checked').map(function () {
        return $(this).val();
    }).get();
    const sourceLanguageId = $('#modalSourceLanguage').val();

    if (!currentCategoryId) {
                NioApp.Toast('No category selected.', 'error', { position: 'top-right' });
                return;
            }
    if (targetLanguages.length === 0) {
        NioApp.Toast('Please select at least one target language.', 'error', { position: 'top-right' });
                return;
    }

    let formData = new FormData();
    formData.append('category_id', currentCategoryId);
    formData.append('source_lang_id', sourceLanguageId);
    targetLanguages.forEach(lang => formData.append('target_lang_ids[]', lang));

    $.ajax({
        url: "{{ route('admin.save-category-translation') }}",
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                // alert('Translation saved successfully!');
                $('#translateCategoryModal').modal('hide');
                // Show Toastr Success
            NioApp.Toast('Translation saved successfully!', 'success', {
                        position: 'top-right'
                    });
            } else {
                // Show Toastr Error
                NioApp.Toast(data.message || 'Failed to save translation.', 'error', {
                    position: 'top-right'
                });
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
           NioApp.Toast('Something went wrong. Check console.', 'error', {
                        position: 'top-right'
           });
        }
    });
});
</script>




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

@endsection
