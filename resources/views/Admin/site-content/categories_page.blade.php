@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add Category Content</h4>
            </div>
        </div>
        <?php
        $lang_code = getCurrentLocale();
        ?>
        @if (isset($categoryPageContents))
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="nk-block">
                        <form action="{{ url('admin-dashboard/category-page-update') ?? '' }}" class="form-validate"
                            novalidate="novalidate" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                @if ($lang_code == 'en-us')
                                    <div class="card border">
                                        <div class="card-header mt-3">
                                            Category Header Banner Section
                                        </div>
                                        <div class="card-body">
                                            <div class="col-md-12">
                                                @foreach ($categoryPageContents as $content)
                                                    @if ($content->meta_key == 'header_image')
                                                        <div class="form-group">
                                                            <label class="form-label" for="image">Header Image</label>
                                                            <div class="dz-message">
                                                                <input type="file" class="form-control"
                                                                    name="category_header_image[{{ $content->id }}]"
                                                                    id="metaValue" value="{{ $content->value ?? '' }}">
                                                            </div>
                                                            @error('category_header_image')
                                                                <div class="error text-danger">{{ $message }}</div>
                                                            @enderror
                                                            @if (!empty($content->meta_value) && file_exists(public_path($content->meta_value)))
                                                                <img src="{{ asset($content->meta_value) }}"
                                                                    alt="{{ $content->meta_key }}"
                                                                    style="width: 100px; height: auto;">
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <div class="col-md-12">
                                                @foreach ($categoryPageContents as $content)
                                                    @if ($content->meta_key == 'header_bg_image')
                                                        <div class="form-group">
                                                            <label class="form-label" for="image">Background
                                                                Image</label>
                                                            <div class="dz-message">
                                                                <input type="file" class="form-control"
                                                                    name="category_background_image[{{ $content->id }}]"
                                                                    id="metaValue" value="{{ $content->value ?? '' }}">
                                                            </div>
                                                            @error('category_background_image')
                                                                <div class="error text-danger">{{ $message }}</div>
                                                            @enderror
                                                            @if (!empty($content->meta_value) && file_exists(public_path($content->meta_value)))
                                                                <img src="{{ asset($content->meta_value) }}"
                                                                    alt="{{ $content->meta_key }}"
                                                                    style="width: 100px; height: auto;">
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="card border">
                                    <div class="card-header mt-3">
                                        Category Heading Section
                                    </div>
                                    <div class="card-body">
                                        @foreach ($categoryPageContents as $key => $val)
                                            @if ($val->meta_key == 'heading')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">Heading</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $key }}"
                                                            name="heading[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />
                                                    </div>
                                                </div>
                                            @elseif($val->meta_key === 'description')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">Sub Heading</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $key }}"
                                                            name="description[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />
                                                    </div>
                                                </div>
                                            @elseif($val->meta_key === 'search_placeholder_text')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">Search Placeholder
                                                        text</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $key }}"
                                                            name="search_placeholder_text[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />
                                                    </div>
                                                </div>
                                            @elseif($val->meta_key === 'main_heading')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">Category Page
                                                        Heading</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $key }}"
                                                            name="main_heading[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-12 mt-4">
                                    <div class="form-group">
                                        <button class="addCategory btn btn-primary  text-center btn-localio"><em
                                                class=""></em><span>Update Content</span></button>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        $(document).ready(function() {
            $(".site_text_input").on('keyup', function(e) {
                e.preventDefault();
                var buttonElement = $('<button>', {
                    'text': 'save',
                    'class': 'btn btn-primary mt-2 save_btn'
                });
                thisObj = $(this);
                // console.log(thisObj.next('button').length < 1);
                if (thisObj.next('button').length < 1) {
                    thisObj.after(buttonElement);
                }
            });
            $(document).on('click', '.save_btn', function(e) {
                e.preventDefault();
                btnObj = $(this);
                let textVal = btnObj.siblings('.site_text_input').val();
                let textID = btnObj.siblings('.site_text_input').attr('id');
                btnObj.siblings('.spinner-border').show();
                btnObj.hide();
                $.ajax({
                    url: '{{ url('/admin-dashboard/update-lang-file') }}',
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        data: {
                            'textVal': textVal,
                            'textID': textID,
                        }
                    },
                    success: function(response) {

                        btnObj.siblings('.spinner-border').hide();
                        btnObj.remove();
                    }
                });
            });
        });
    </script>

@endsection
