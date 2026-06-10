@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg globals-header">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add Header Content</h4>
            </div>
        </div>
        <?php

        $lang_code = getCurrentLocale();
        ?>
        @if (isset($headerContents))
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="nk-block">
                        <form action="{{ url('admin-dashboard/header-page-update') ?? '' }}" class="form-validate"
                            novalidate="novalidate" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="card border">
                                    <div class="card-header mt-3">
                                        Header Logo Section
                                    </div>
                                    <div class="card-body">
                                        <div class="col-md-12">
                                            <form method="POST" action="{{ url('admin-dashboard/header-page-update') }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-group">
                                                    <label class="form-label" for="header_logo">Header Logo</label>
                                                    <input type="file" class="form-control" name="header_logo[{{ $headerLogo->id ?? 'new' }}]" required>

                                                    @if (!empty($headerLogo->meta_value) && file_exists(public_path($headerLogo->meta_value)))
                                                        <img src="{{ asset($headerLogo->meta_value) }}" alt="{{ $headerLogo->meta_key ?? '' }}"
                                                            style="width: 100px; height: auto;" class="mt-2">
                                                    @endif

                                                    <button type="submit" class="btn btn-sm btn-primary mt-3">Update Header Logo</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>


                                <div class="card border">
                                    <div class="card-header mt-3">
                                        Favicon Icon Section
                                    </div>
                                    <div class="card-body">
                                        <div class="col-md-12">
                                            <form method="POST" action="{{ url('admin-dashboard/header-page-update') }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-group">
                                                    <label class="form-label" for="favicon_icon">Favicon Icon</label>
                                                    <input type="file" class="form-control" name="favicon_icon[{{ $favicon->id ?? 'new' }}]" required>

                                                    @if (!empty($favicon->meta_value) && file_exists(public_path($favicon->meta_value)))
                                                        <img src="{{ asset($favicon->meta_value) }}" alt="{{ $favicon->meta_key ?? '' }}"
                                                            style="width: 100px; height: auto;" class="mt-2">
                                                    @endif

                                                    <button type="submit" class="btn btn-sm btn-primary mt-3">Update Favicon</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>


                                    @php
                                    // Define default meta keys and labels
                                        $defaultMetaKeys = [
                                            'header_search_placeholder' => 'Search Bar Placeholder Text',
                                            'login_btn_lable' => 'Login Button Label',
                                            'code_at_beginning_of_head_tag'=> 'Code at Beginning of <head> Tag',
                                            'code_at_end_of_head_tag' => 'Code before Closing </head> Tag',
                                            'sign_up_btn_lable' => 'Sign Up Button Label',
                                            'sign_out_btn_lable' => 'Sign Out Button Label',
                                            'categories' => 'Categories Text',
                                            'exclusive' => 'Exclusive Deal Text',
                                            'top_rated_product' => 'Top Rated Product Text',
                                            'expert_guide' => 'Expert Guides Text',
                                            'help_center' => 'Help Center Text',
                                        ];

                                        // Create an associative array from database records
                                        $contentMap = $headerContents->keyBy('meta_key');
                                    @endphp

                                @foreach ($defaultMetaKeys as $metaKey => $label)
                                @php
                                    // Get existing record or create an empty object with default type 'text'
                                    $content = $contentMap[$metaKey] ?? (object) ['id' => 'new', 'meta_value' => '', 'type' => 'text'];
                                @endphp

                            <div class="card border">
                                <div class="card-header mt-3">
                                    {{ $label }}
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="form-label" for="{{ $metaKey }}">{{ $label }}</label>
                                        <div class="input-group">
                                            @if ($content->type === 'textarea')
                                                <textarea class="form-control site_text_input"
                                                          rows="4"
                                                          id="{{ $metaKey }}"
                                                          name="{{ $metaKey }}[{{ $content->id }}]"
                                                          data-id="{{ $content->id }}"
                                                          data-key="{{ $metaKey }}">{{ $content->meta_value }}</textarea>
                                            @else
                                                <input type="text"
                                                       class="form-control site_text_input"
                                                       id="{{ $metaKey }}"
                                                       name="{{ $metaKey }}[{{ $content->id }}]"
                                                       data-id="{{ $content->id }}"
                                                       data-key="{{ $metaKey }}"
                                                       value="{{ $content->meta_value }}">
                                            @endif

                                            <div class="input-group-append ms-2 d-flex align-items-center">
                                                <button type="button" class="btn btn-sm btn-primary save_btn">Save</button>
                                                <div class="spinner-border spinner-border-sm text-primary ms-2 d-none" role="status"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @endforeach



                            </div>
                            <div class="col-md-12 mt-4">
                                <div class="form-group">
                                    {{-- <button class="addCategory btn btn-primary  btn-localio text-center"><em
                                            class=""></em><span>Update Content</span></button> --}}
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        @endif
    </div>


    <script>
        $(document).ready(function () {
            $(".site_text_input").on('keyup', function (e) {
                e.preventDefault();
                let input = $(this);

                // Only append Save button and spinner once
                if (input.next('.save_btn').length === 0) {
                    const buttonElement = $('<button>', {
                        text: 'Save',
                        class: 'btn btn-primary mt-2 save_btn',
                        type: 'button'
                    });

                    const spinner = $('<div>', {
                        class: 'spinner-border spinner-border-sm text-primary mt-2 ms-2 d-none',
                        role: 'status'
                    });

                    input.after(buttonElement);
                    buttonElement.after(spinner);
                }
            });

            // Save button click
            $(document).on('click', '.save_btn', function (e) {
                e.preventDefault();

                const btn = $(this);
                const spinner = btn.next('.spinner-border');
                const input = btn.siblings('.site_text_input');
                const nameAttr = input.attr('name');
                const inputValue = input.val();

                // Show spinner, hide button
                btn.hide();
                spinner.removeClass('d-none');

                // Dynamically create and submit a mini form
                const tempForm = $('<form>', {
                    method: 'POST',
                    action: '{{ url("admin-dashboard/header-page-update") }}'
                });

                const tokenInput = $('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: '{{ csrf_token() }}'
                });

                const fieldInput = $('<input>', {
                    type: 'hidden',
                    name: nameAttr,
                    value: inputValue
                });

                tempForm.append(tokenInput, fieldInput);
                $('body').append(tempForm);
                tempForm.submit();
            });
        });
    </script>



@endsection
