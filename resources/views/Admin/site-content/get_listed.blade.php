@extends('admin_layout.master')

@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Get Listed</h4>
            </div>
        </div>
        <form action="{{ route('admin.get-listed-update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="nk-block">
                            </div>
                            <div>
                                <div>
                                    <label for="heading" class="form-label">Heading</label>
                                    <input type="text" name="heading"
                                        class="form-control @error('heading') is-invalid @enderror"
                                        value="{{ old('heading', $getListed->heading ?? '') }}">

                                    @error('heading')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Sub Heading -->
                                <div class="mb-3">
                                    <label for="sub_heading" class="form-label">Sub Heading</label>
                                    <textarea name="sub_heading" class="form-control @error('sub_heading') is-invalid @enderror">{{ old('sub_heading', $getListed->sub_heading ?? '') }}</textarea>

                                    @error('sub_heading')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Claim Profile Checkbox -->
                                <div class="mb-3">
                                    <label class="form-check-label">Claim Profile</label>
                                    {{-- <input type="text" name="claim_profile" class="form-check-input"> --}}
                                    <input type="text" name="claim_profile"
                                    class="form-control @error('claim_profile') is-invalid @enderror"
                                    value="{{ old('claim_profile', $getListed->claim_profile ?? '') }}">
                                </div>

                                <!-- Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" name="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title', $getListed->title ?? '') }}">

                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control description @error('description') is-invalid @enderror">{{ old('description', $getListed->description ?? '') }}</textarea>

                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Second Title -->
                                <div class="mb-3">
                                    <label for="title2" class="form-label">Second Title</label>
                                    <input type="text" name="title2"
                                        class="form-control @error('title2') is-invalid @enderror"
                                        value="{{ old('title2', $getListed->title2 ?? '') }}">

                                    @error('title2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Second Description -->
                                <div class="mb-3">
                                    <label for="description2" class="form-label">Second Description</label>
                                    <textarea name="description2" class="form-control description @error('description2') is-invalid @enderror">{{ old('description2', $getListed->description2 ?? '') }}</textarea>

                                    @error('description2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="section_title" class="form-label">section Title</label>
                                    <input type="text" name="section_title"
                                        class="form-control @error('section_title') is-invalid @enderror"
                                        value="{{ old('section_title', $getListed->section_title ?? '') }}">

                                    @error('section_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="card border">
                                        <div class="card-header mt-3">
                                            <strong> Section </strong>
                                            <button type="button" class="btn btn-success btn-sm float-end"
                                                id="add-get-listed-item">Add
                                                Item</button>
                                        </div>
                                        <div class="card-body">

                                            <div class="form-group col-lg-12">
                                                <label class="form-label" for="image">Image</label>
                                                <div class="form-control-wrap">
                                                    <input type="file" class="form-control" id="get_listed_image" />

                                                </div>
                                            </div>

                                            <div class="form-group col-lg-12">
                                                <label class="form-label" for="description">Description</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control site_text_input"
                                                        id="get_listed_desc" placeholder="Enter Here Description..." />

                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                                <div id="get_listed-items-list" class="get_listed-items-container">
                                    <!-- Popular items will be appended here -->
                                </div>

                                <div id="get_listed-items" class="get_listed-items-container">
                                    <div class="col-md-12 mt-4">
                                        <div class="card border">
                                            <div class="card-header">
                                                Education items
                                            </div>
                                            <div class="card-body">


                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>S.no</th>
                                                            <th>Image</th>
                                                            <th>Description</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    <tbody>
                                                        @forelse ($pageTileTranslationEducation as $index => $pageTile)
                                                            @php
                                                                $firstTranslation = $pageTile->translations->first();
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>
                                                                    @if ($firstTranslation && $firstTranslation->image)
                                                                        <img src="{{ asset($firstTranslation->image) }}"
                                                                            alt="Item Image"
                                                                            style="width: 100px; height: auto;">
                                                                    @else
                                                                        No Image
                                                                    @endif
                                                                </td>
                                                                <td>{{ $firstTranslation?->description ?? 'No Description' }}
                                                                </td>
                                                                <td>
                                                                    <a class="btn btn-danger btn-sm"
                                                                        href="{{ route('admin.page_tile_translation.delete', $pageTile->id) }}">Delete</a>
                                                                </td>
                                                                <td>
                                                                    @if ($firstTranslation)
                                                                        <button type="button"
                                                                            class="btn btn-success btn-sm update-education-item"
                                                                            data-id="{{ $firstTranslation->id ?? '' }}"
                                                                            data-des="{{ $firstTranslation->description ?? '' }}"
                                                                            data-image="{{ $firstTranslation->image ? asset($firstTranslation->image) : '' }}">
                                                                            Edit
                                                                        </button>
                                                                    @else
                                                                        <span class="text-muted">No Data</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6">No records found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>


                                                </table>
                                            </div>
                                        </div>

                                            <div id="updated-get-item-details" class="mt-4" style="display: none;">
                                                <p><strong>Title:</strong></p>
                                                <input type="hidden" class="form-control" id="updated-gl-id" name="GL[id]"
                                                    value="" />

                                                <p><strong>Description:</strong></p>
                                                <input type="text" class="form-control" id="updated-gl-description"
                                                    name="GL[description]"
                                                    value="" />


                                                <p><strong>Image:</strong></p>
                                                <img id="updated-es-image" style="width: 100px; height: auto;" />
                                                <input type="file" id="update-gl-image-input" name="GL[image]" />

                                                <button type="button" id="save-gl-button"
                                                    style="display: none;">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                    <br>

                                @php
                                $currentLangId = App\Models\Language::where('lang_code', getCurrentLocale())->value('id');
                                $defaultLangData = App\Models\GetListed::where('lang_id', 1)->first();
                            @endphp

                            <div class="row">
                                @php
                                    $imageFields = [
                                        'left_image' => 'Left Image',
                                        'right_image' => 'Right Image',
                                        'bottom_image' => 'Bottom Image',
                                        'second_left_image' => 'Second Left Image',
                                        'second_right_image' => 'Second Right Image',
                                    ];
                                @endphp

                                @foreach ($imageFields as $field => $label)
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">{{ $label }}</label>

                                        @if ($currentLangId == 1)
                                            <input type="file" name="{{ $field }}" class="form-control">
                                        @endif

                                        @php
                                            // Get image from the current language or fallback to lang_id = 1
                                            $imagePath = $getListed->$field ?? ($defaultLangData->$field ?? null);
                                        @endphp

                                        {{-- <p>Debug: Image Path -> {{ $imagePath }}</p> --}}

                                        @if ($imagePath && file_exists(public_path($imagePath)))
                                            <div class="mt-2">
                                                <img src="{{ asset($imagePath) }}" class="img-thumbnail" width="150">
                                            </div>
                                        @else
                                            <p class="text-muted">No image available</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>


                           </div>
                       </div>
                   </div>
               </div>
                    <div class="col-lg-4">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="card border">
                                            <div class="nk-block">
                                                <div class="col-md-12 mt-1 d-flex justify-content-between">
                                                    <a href="your-view-page-url" class="btn btn-link text-center">
                                                        <span><b>View Page</b></span>
                                                    </a>
                                                    <button type="submit"
                                                    class="addCategory btn btn-primary btn-localio text-center"><em
                                                        class=""></em><span>Update
                                                        Content</span></button>
                                                </div>

                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="meta_title" class="form-label fw-bold">Meta Title</label>
                                                    <input type="text" id="meta_title" name="meta_title"
                                                        class="form-control" placeholder="Enter meta title"
                                                        value="{{ $getListed->meta_title ?? '' }}">
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="meta_description" class="form-label fw-bold">Meta
                                                        Description</label>
                                                    <textarea id="meta_description" name="meta_description" class="form-control" rows="3"
                                                        placeholder="Enter meta description">{{ $getListed->meta_description ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>
    <script>
     $(document).ready(function () {
    let educationItems = [];

    $('#add-get-listed-item').on('click', function () {
        const description = $('#get_listed_desc').val();
        const imageInput = $('#get_listed_image')[0];
        const imageFile = imageInput.files[0];

        if (description && imageFile) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const itemCard = $('<div class="item-card mt-2 p-2 border">').html(`
                    <p>${description}</p>
                    <img src="${e.target.result}" alt="Image" style="width: 100px; height: auto;">
                    <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                `);

                $('#get_listed-items-list').append(itemCard);

                // Create hidden inputs for form submission
                const hiddenDesc = $('<input type="hidden" name="get_listed_item[description][]" value="' + description + '">');
                const hiddenImage = $('<input type="hidden" name="get_listed_item[image][]" value="' + e.target.result + '">');

                $('#get_listed-items-list').append(hiddenDesc, hiddenImage);

                educationItems.push({ description, image: e.target.result, card: itemCard, hiddenInputs: [hiddenDesc, hiddenImage] });

                clearForm();
            };
            reader.readAsDataURL(imageFile);
        } else {
            alert('Please fill in all fields and select an image.');
        }
    });

    $('#get_listed-items').on('click', '.update-education-item', function () {
        let itemId = $(this).data('id');
        let description = $(this).data('des');
        let imageSrc = $(this).data('image');

        $('#updated-gl-id').val(itemId);
        $('#updated-gl-description').val(description);
        $('#updated-es-image').attr('src', imageSrc);

        $('#updated-get-item-details').show();
        $('#save-gl-button').show();
    });

    $('#save-gl-button').on('click', function () {
        let itemId = $('#updated-gl-id').val();
        let description = $('#updated-gl-description').val().trim();
        let imageFile = $('#update-gl-image-input')[0].files[0];

        let formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('id', itemId);
        formData.append('des', description);

        if (imageFile) {
            formData.append('image', imageFile);
        }

        $.ajax({
            url: '{{ route('admin.page_get_listed_translation.update') }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                alert(response.success);
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('There was an error updating the item.');
            }
        });
    });

    $('#update-gl-image-input').on('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function () {
                $('#updated-es-image').attr('src', reader.result);
            };
            reader.readAsDataURL(file);
        }
    });

    $('#get_listed-items-list').on('click', '.remove-item', function () {
        const itemCard = $(this).closest('.item-card');
        const itemIndex = educationItems.findIndex(item => item.card[0] === itemCard[0]);

        if (itemIndex > -1) {
            educationItems.splice(itemIndex, 1);
            itemCard.remove();
        }
    });

    function clearForm() {
        $('#get_listed_desc').val('');
        $('#get_listed_image').val('');
    }
});

    </script>

@endsection
