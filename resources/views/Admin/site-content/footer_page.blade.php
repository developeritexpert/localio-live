@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg globals-footer">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add Footer Content</h4>
            </div>
        </div>
        @php
        $footerSections = [
            'logo' => [
                'title' => 'Footer Logo Section',
                'type' => 'file',
                'fields' => [
                    'footer_logo' => 'Footer Logo'
                ]
            ],
            'discover' => [
                'title' => 'Footer Discover Menu Section',
                'type' => 'text',
                'fields' => [
                    'discover' => 'Discover Text',
                    'categories' => 'Categories',
                    'top_rated_product' => 'Top Rated Product',
                    'exclusive_deal' => 'Exclusive Deal',
                ]
            ],
            'company' => [
                'title' => 'Footer Company Menu Section',
                'type' => 'text',
                'fields' => [
                    'company' => 'Company',
                    'who_we_are' => 'Who We Are',
                    'privacy_policy' => 'Privacy Policy',
                    'terms_and_conditions' => 'Terms and Conditions',
                ]
            ],
            'vendors' => [
                'title' => 'Footer Vendors Menu Section',
                'type' => 'text',
                'fields' => [
                    'vendors' => 'Vendors',
                    'get_listed' => 'Get Listed',
                    'vendor_login' => 'Vendor Login',
                ]
            ],
            'help' => [
                'title' => 'Footer Help Menu Section',
                'type' => 'text',
                'fields' => [
                    'help' => 'Help',
                    'expert_guides' => 'Expert Guides',
                    'help_center' => 'Help Center',
                    'contact' => 'Contact',
                ]
            ],
            'follow' => [
                'title' => 'Footer Follow Us Menu Section',
                'type' => 'text',
                'fields' => [
                    'follow_us' => 'Follow Us',
                    'facebook' => 'Facebook',
                    'instagram' => 'Instagram',
                    'twitter' => 'Twitter',
                    'footer_copyright_text' => 'Footer Copyright Text'
                ]
            ],
            'footer_code' => [
                'title' => 'Footer <footer> Section',
                'type' => 'textarea',
                'fields' => [
                    'code_at_beginning_of_footer_tag' => 'Code at Beginning of &lt;footer&gt; Tag',
                    'code_at_end_of_footer_tag' => 'Code before Closing &lt;/footer&gt; Tag',
                ]
            ]
        ];

        $contentMap = $footerContents->keyBy('meta_key');
        @endphp

        <form action="{{ route('footer-page-update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @foreach ($footerSections as $sectionKey => $section)
                <div class="card border">
                    <div class="card-header mt-3">
                        {!! $section['title'] !!}
                    </div>
                    <div class="card-body">
                        @foreach ($section['fields'] as $key => $label)
                            @php
                                $field = $contentMap[$key] ?? (object) ['id' => 'new', 'meta_value' => '', 'type' => $section['type']];
                            @endphp

                            <div class="form-group col-lg-12">
                                <label class="form-label">{!! $label !!}</label>

                                @if ($section['type'] === 'file')
                                    <input type="file" class="form-control" name="{{ $key }}[{{ $field->id }}]">
                                    @if (!empty($field->meta_value) && file_exists(public_path($field->meta_value)))
                                        <img class="mt-2" src="{{ asset($field->meta_value) }}" alt="{{ $key }}" style="width:100px;height:auto;">
                                    @endif
                                @elseif($section['type'] === 'textarea')
                                <textarea class="form-control" rows="4" style="min-height: 120px;" name="{{ $key }}[{{ $field->id }}]">{{ $field->meta_value }}</textarea>

                                @else
                                    <input type="text" class="form-control" name="{{ $key }}[{{ $field->id }}]" value="{{ $field->meta_value }}">
                                @endif
                            </div>
                        @endforeach
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Update {{ $section['title'] }}</button>
                        </div>
                    </div>
                </div>
            @endforeach

            @php
            $expectedFields = [
                'facebook_icon', 'instagram_icon', 'twitter_icon' ,'pinterest_icon' ,
                'facebook_url', 'instagram_url', 'twitter_url', 'pinterest_url',
            ];
            $footerFilesAssoc = collect($footerFiles ?? [])->keyBy('meta_key');
            @endphp

            <div class="card border">
                <div class="card-header mt-3">
                    Footer Icons & URLs
                </div>
                <div class="card-body">
                    @foreach ($expectedFields as $fieldKey)
                        @php
                            $file = $footerFilesAssoc[$fieldKey] ?? (object) ['id' => 'new', 'meta_value' => ''];
                            $isIcon = in_array($fieldKey, ['facebook_icon', 'instagram_icon', 'twitter_icon' , 'pinterest_icon']);
                        @endphp

                        <div class="form-group col-lg-12">
                            <label class="form-label" for="{{ $fieldKey }}">{{ ucfirst(str_replace('_', ' ', $fieldKey)) }}</label>
                            @if ($isIcon)
                                <input type="file" class="form-control" name="{{ $fieldKey }}[{{ $file->id }}]">
                                @if (!empty($file->meta_value) && file_exists(public_path($file->meta_value)))
                                    <img class="mt-2" src="{{ asset($file->meta_value) }}" style="width:100px;height:auto;">
                                @endif
                            @else
                                <input type="text" class="form-control" name="{{ $fieldKey }}[{{ $file->id }}]" value="{{ $file->meta_value }}">
                            @endif
                        </div>
                    @endforeach
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">Update Footer Icons & URLs</button>
                    </div>
                </div>
            </div>
        </form>


                            {{-- <div class="form-group mt-4">
                                <button class="btn btn-primary">Update All Footer Content</button>
                            </div> --}}





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
