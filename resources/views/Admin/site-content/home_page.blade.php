@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg pages-home">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add Home Content</h4>
            </div>
        </div>
        <?php
            use Illuminate\Support\Facades\Redis;
            $lang_code = Redis::get('admin_lang_code') ?? getCurrentLocale();
        ?>
        {{-- <p>Stored Language Code: {{ $lang_code }}</p> --}}
        @if (isset($homeContents) && isset($allHomeContents))
        <form action="{{route('home-content-update') }}" class="form-validate"
                                novalidate="novalidate" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-bordered">
                    {{-- <div class="card-inner"> --}}
                        <div class="nk-block">

                                @csrf

                        </div>


                                    <div class="col-md-12">
                                        <div class="card border">
                                            <div class="card-header mt-3">
                                                Home Banner Section
                                            </div>
                                            <div class="card-body">
                                                {{-- @if ($lang_code == 'en-us') --}}

                                                    @foreach ($homeContents as $content)
                                                        @if ($content->meta_key == 'header_img')
                                                            <div class="form-group">
                                                                <label class="form-label" for="image">Header Image</label>
                                                                <div class="dz-message">
                                                                    <input type="file" class="form-control"
                                                                        name="header_image[{{ $content->id }}]" id="metaValue"
                                                                        value="{{ $content->value ?? '' }}">
                                                                </div>
                                                                @error('header_image')
                                                                    <div class="error text-danger">{{ $message }}</div>
                                                                @enderror
                                                                @if (isset($content->meta_key))
                                                                    <img src="{{ asset($content->meta_value) }}"
                                                                        alt="{{ $content->meta_key }}"
                                                                        style="width: 100px; height: auto;">
                                                                @endif
                                                            </div>
                                                        @elseif($content->meta_key == 'header_background_img')
                                                            <div class="form-group">
                                                                <label class="form-label" for="image">Header Background
                                                                    Image</label>
                                                                <div class="dz-message">
                                                                    <input type="file" class="form-control"
                                                                        name="header_backgound_image[{{ $content->id }}]"
                                                                        id="metaValue" value="{{ $content->value ?? '' }}">
                                                                </div>
                                                                @error('header_backgound_image')
                                                                    <div class="error text-danger">{{ $message }}</div>
                                                                @enderror
                                                                @if (isset($content->meta_key))
                                                                    <img src="{{ asset($content->meta_value) }}"
                                                                        alt="{{ $content->meta_key }}"
                                                                        style="width: 100px; height: auto;">
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                {{-- @endif --}}
                                                @foreach ($allHomeContents as $key => $val)
                                                    @if ($val->meta_key == 'header_title')
                                                        <div class="form-group col-lg-12">
                                                            <label class="form-label" for="{{ $key }}">Heading</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control"
                                                                    id="{{ $key }}"
                                                                    name="header_title[{{ $val->id }}]"
                                                                    value="{{ $val->meta_value ?? 'not data found' }}" />
                                                            </div>
                                                        </div>
                                                    @elseif($val->meta_key === 'header_description')
                                                        <div class="form-group col-lg-12">
                                                            <label class="form-label"
                                                                for="{{ $key }}">Description</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control"
                                                                    id="{{ $key }}"
                                                                    name="header_description[{{ $val->id }}]"
                                                                    value="{{ $val->meta_value ?? '' }}" />
                                                            </div>
                                                        </div>
                                                    @elseif($val->meta_key === 'placeholder_text')
                                                        <div class="form-group col-lg-12">
                                                            <label class="form-label" for="{{ $key }}">Home Page
                                                                Search
                                                                Placeholder</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control"
                                                                    id="{{ $key }}"
                                                                    name="placeholder_text[{{ $val->id }}]"
                                                                    value="{{ $val->meta_value ?? '' }}" />
                                                            </div>
                                                        </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="card border">
                                            <div class="card-header mt-3">
                                                Trusted Brands Section
                                            </div>
                                            <div class="card-body">
                                                {{-- @if ($lang_code == 'en-us') --}}
                                                    @foreach ($homeContents as $content)
                                                        @if ($content->meta_key == 'trusted_brands_img')
                                                            <div class="form-group">
                                                                <label class="form-label" for="image">Trusted brand
                                                                    images</label>
                                                                <div class="dz-message">
                                                                    <!-- Allow multiple file uploads -->
                                                                    <input type="file" class="form-control"
                                                                        name="brand_image[{{ $content->id }}][]"
                                                                        id="metaValue" value="{{ $content->meta_value ?? '' }}"
                                                                        multiple>
                                                                </div>
                                                                @error('brand_image')
                                                                    <div class="error text-danger">{{ $message }}</div>
                                                                @enderror
                                                                @if (isset($content->meta_value) && !empty($content->meta_value))
                                                                    @php
                                                                        $imageIds = json_decode($content->meta_value);
                                                                    @endphp
                                                                    @if (is_array($imageIds))
                                                                        @foreach ($imageIds as $imageId)
                                                                            @php
                                                                                $media = \App\Models\HomeContentMedia::find(
                                                                                    $imageId,
                                                                                ); // Retrieve image by ID
                                                                            @endphp
                                                                            @if ($media)
                                                                                <img src="{{ asset($media->file_path) }}"
                                                                                    alt="{{ $content->meta_key }}"
                                                                                    style="width: 100px; height: auto;">
                                                                            @endif
                                                                        @endforeach
                                                                    @else
                                                                        <img src="{{ asset($content->meta_value) }}"
                                                                            alt="{{ $content->meta_key }}"
                                                                            style="width: 100px; height: auto;">
                                                                    @endif
                                                                @endif

                                                            </div>
                                                        @endif
                                                    @endforeach
                                                {{-- @endif --}}
                                            @elseif($val->meta_key === 'trusted_brands_text')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">Trusted Brands
                                                        Text</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control " id="{{ $key }}"
                                                            name="trusted_brand[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @elseif($val->meta_key === 'most_popular')
                                    <div class="col-md-12">
                                        <div class="card border">
                                            <div class="card-header mt-3">
                                                Most Popular Section
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">Most Popular</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $key }}"
                                                            name="most_popular[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />

                                                    </div>
                                                </div>
                                            @elseif($val->meta_key === 'campare_business')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">Compare Software
                                                        Button
                                                        Lable</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $key }}"
                                                            name="campare_business[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />
                                                    </div>
                                                </div>
                                            @elseif($val->meta_key === 'visit_website')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">Visit Website
                                                        Text</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control site_text_input"
                                                            id="{{ $key }}"
                                                            name="visit_website[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="card border">
                                            <div class="card-header mt-3">
                                                Exclusive deals
                                                Section
                                            </div>
                                        @elseif($val->meta_key === 'exclusive_deals')
                                            <div class="card-body">
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">Exclusive Deals
                                                        Text</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control site_text_input"
                                                            id="{{ $key }}"
                                                            name="exclusive_deals[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />

                                                    </div>
                                                </div>
                                            @elseif($val->meta_key === 'all_exclusive')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">All Exclusive
                                                        Lable</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control site_text_input"
                                                            id="{{ $key }}"
                                                            name="all_exclusive[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />

                                                    </div>
                                                </div>
                                            @elseif($val->meta_key === 'get_this_deal')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">Get Deal
                                                        Lable</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control site_text_input"
                                                            id="{{ $key }}"
                                                            name="get_this_deal[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="card border">
                                            <div class="card-header mt-3">
                                                AI Search Section
                                            </div>
                                            <div class="card-body">
                                                {{-- @if ($lang_code == 'en-us') --}}
                                                    @foreach ($homeContents as $content)
                                                        @if ($content->meta_key == 'ai_section_left_img')
                                                            <div class="form-group">

                                                                <label class="form-label" for="image">Ai Section Left
                                                                    Image</label>
                                                                <div class="dz-message">
                                                                    <input type="file" class="form-control"
                                                                        name="ai_left_image[{{ $content->id }}]"
                                                                        id="metaValue"
                                                                        value="{{ $content->meta->value ?? '' }}">
                                                                </div>
                                                                @error('ai_left_image')
                                                                    <div class="error text-danger">{{ $message }}</div>
                                                                @enderror

                                                                @if (isset($content->meta_key))
                                                                    <img src="{{ asset($content->meta_value) }}"
                                                                        alt="{{ $content->meta_key }}"
                                                                        style="width: 100px; height: auto;">
                                                                @endif
                                                            </div>
                                                        @elseif($content->meta_key == 'ai_section_right_img')
                                                            <div class="form-group">

                                                                <label class="form-label" for="image">Ai Section Right
                                                                    Image</label>
                                                                <div class="dz-message">
                                                                    <input type="file" class="form-control"
                                                                        name="ai_right_image[{{ $content->id }}]"
                                                                        id="metaValue"
                                                                        value="{{ $content->meta_value ?? '' }}">
                                                                </div>
                                                                @error('ai_right_image')
                                                                    <div class="error text-danger">{{ $message }}</div>
                                                                @enderror

                                                                @if (isset($content->meta_key))
                                                                    <img src="{{ asset($content->meta_value) }}"
                                                                        alt="{{ $content->meta_key }}"
                                                                        style="width: 100px; height: auto;">
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                {{-- @endif --}}
                                            @elseif($val->meta_key === 'ai_title')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">AI Search
                                                        Text</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $key }}"
                                                            name="ai_title[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />

                                                    </div>
                                                </div>
                                            @elseif($val->meta_key === 'ai_description')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">AI Search
                                                        Description</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $key }}"
                                                            name="ai_description[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />
                                                    </div>
                                                </div>
                                            @elseif($val->meta_key === 'ai_placeholder')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">AI
                                                        Placeholder</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $key }}"
                                                            name="ai_placeholder[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />
                                                    </div>
                                                </div>
                                                {{-- @if ($lang_code == 'en-us') --}}
                                                    @foreach ($homeContents as $content)
                                                        @if ($content->meta_key == 'ai_send_img')
                                                            <div class="form-group">

                                                                <label class="form-label" for="image">AI Send Message
                                                                    Button
                                                                    Image</label>
                                                                <div class="dz-message">
                                                                    <input type="file" class="form-control"
                                                                        name="ai_send_img[{{ $content->id }}]"
                                                                        id="metaValue"
                                                                        value="{{ $content->meta->value ?? '' }}">
                                                                </div>
                                                                @error('ai_send_img')
                                                                    <div class="error text-danger">{{ $message }}</div>
                                                                @enderror

                                                                @if (isset($content->meta_key))
                                                                    <img src="{{ asset($content->meta_value) }}"
                                                                        alt="{{ $content->meta_key }}"
                                                                        style="width: 30px; height: 50px;">
                                                                @endif
                                                            </div>
                                                            <!-- add here ai image -->
                                                        @endif
                                                    @endforeach
                                                {{-- @endif --}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="card border">
                                            <div class="card-header mt-3">
                                                Top Product Section
                                            </div>
                                            <div class="card-body">
                                            @elseif($val->meta_key === 'top_product')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">Top Product
                                                        Text</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $key }}"
                                                            name="top_product[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />

                                                    </div>
                                                </div>
                                            @elseif($val->meta_key === 'all_top_product')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">All Product
                                                        Lable</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $key }}"
                                                            name="all_top_product[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="card border">
                                            <div class="card-header mt-3">
                                                Latest Review Section
                                            </div>
                                            <div class="card-body">
                                            @elseif($val->meta_key === 'latest_reviews')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">Latest Reviews
                                                        Text</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control site_text_input"
                                                            id="{{ $key }}"
                                                            name="latest_reviews[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />

                                                    </div>
                                                </div>
                                            @elseif($val->meta_key === 'write_review')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">Write Review
                                                        Lable</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control site_text_input"
                                                            id="{{ $key }}"
                                                            name="write_review[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="card border">
                                            <div class="card-header mt-3">
                                                Read Article Section
                                            </div>
                                            <div class="card-body">
                                                @if ($lang_code == 'en')
                                                    @foreach ($homeContents as $content)
                                                        @if ($content->meta_key == 'review_section_right_img')
                                                            <div class="form-group">
                                                                <label class="form-label" for="image">Review Section Right
                                                                    Image</label>
                                                                <div class="dz-message">
                                                                    <input type="file" class="form-control"
                                                                        name="review_section_right_img[{{ $content->id }}]"
                                                                        id="metaValue" value="{{ $content->value ?? '' }}">
                                                                </div>
                                                                @error('review_section_right_img')
                                                                    <div class="error text-danger">{{ $message }}</div>
                                                                @enderror
                                                                @if (isset($content->meta_key))
                                                                    <img src="{{ asset($content->meta_value) }}"
                                                                        alt="{{ $content->meta_key }}"
                                                                        style="width: 100px; height: auto;">
                                                                @endif
                                                            </div>
                                                        @elseif($content->meta_key == 'review_section_left_img')
                                                            <div class="form-group">
                                                                <label class="form-label" for="image">Review Section Left
                                                                    Image</label>
                                                                <div class="dz-message">
                                                                    <input type="file" class="form-control"
                                                                        name="review_section_left_img[{{ $content->id }}]"
                                                                        id="metaValue" value="{{ $content->value ?? '' }}">
                                                                </div>
                                                                @error('review_section_left_img')
                                                                    <div class="error text-danger">{{ $message }}</div>
                                                                @enderror
                                                                @if (isset($content->meta_key))
                                                                    <img src="{{ asset($content->meta_value) }}"
                                                                        alt="{{ $content->meta_key }}"
                                                                        style="width: 50px; height: 50px;">
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @elseif($val->meta_key === 'read_article')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">Read Article
                                                        Text</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control site_text_input"
                                                            id="{{ $key }}"
                                                            name="read_article[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />
                                                    </div>
                                                </div>
                                            @elseif($val->meta_key === 'view_all_article')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">View Article
                                                        Lable</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control site_text_input"
                                                            id="{{ $key }}"
                                                            name="view_all_article[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? '' }}" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="card border">
                                            <div class="card-header mt-3">
                                                Find Right Tool Section
                                            </div>
                                            <div class="card-body">
                                                <div class="col-md-12">
                                                    @if ($lang_code == 'en')
                                                        @foreach ($homeContents as $content)
                                                            @if ($content->meta_key == 'find_tool_left_img')
                                                                <div class="form-group">
                                                                    <label class="form-label" for="image">Find Tool Section
                                                                        Left Image</label>
                                                                    <div class="dz-message">
                                                                        <input type="file" class="form-control"
                                                                            name="find_tool_left_image[{{ $content->id }}]"
                                                                            id="metaValue"
                                                                            value="{{ $content->value ?? '' }}">
                                                                    </div>
                                                                    @error('find_tool_left_image')
                                                                        <div class="error text-danger">{{ $message }}</div>
                                                                    @enderror
                                                                    @if (isset($content->meta_key))
                                                                        <img src="{{ asset($content->meta_value) }}"
                                                                            alt="{{ $content->meta_key }}"
                                                                            style="width: 100px; height: auto;">
                                                                    @endif
                                                                </div>
                                                            @elseif($content->meta_key == 'find_tool_right_img')
                                                                <div class="form-group">
                                                                    <label class="form-label" for="image">Find Tool Section
                                                                        Right Image</label>
                                                                    <div class="dz-message">
                                                                        <input type="file" class="form-control"
                                                                            name="find_tool_right_image[{{ $content->id }}]"
                                                                            id="metaValue"
                                                                            value="{{ $content->value ?? '' }}">
                                                                    </div>
                                                                    @error('find_tool_right_image')
                                                                        <div class="error text-danger">{{ $message }}</div>
                                                                    @enderror
                                                                    @if (isset($content->meta_key))
                                                                        <img src="{{ asset($content->meta_value) }}"
                                                                            alt="{{ $content->meta_key }}"
                                                                            style="width: 50px; height: 50px;">
                                                                    @endif
                                                                </div>

                                                                <!-- new -->
                                                            @elseif($content->meta_key == 'user_reviews_img')
                                                                <div class="form-group">
                                                                    <label class="form-label" for="image">Verified Reviews
                                                                        Image</label>
                                                                    <div class="dz-message">
                                                                        <input type="file" class="form-control"
                                                                            name="verified_reviews_image[{{ $content->id }}]"
                                                                            id="metaValue"
                                                                            value="{{ $content->value ?? '' }}">
                                                                    </div>
                                                                    @error('verified_reviews_image')
                                                                        <div class="error text-danger">{{ $message }}</div>
                                                                    @enderror
                                                                    @if (isset($content->meta_key))
                                                                        <img src="{{ asset($content->meta_value) }}"
                                                                            class="himg mt-2" alt="{{ $content->meta_key }}"
                                                                            style="width: 50px; height: 50px;">
                                                                    @endif
                                                                </div>
                                                            @elseif($content->meta_key == 'price_compare_img')
                                                                <div class="form-group">
                                                                    <label class="form-label" for="image">Feature Price
                                                                        Image</label>
                                                                    <div class="dz-message">
                                                                        <input type="file" class="form-control"
                                                                            name="feature_price_image[{{ $content->id }}]"
                                                                            id="metaValue"
                                                                            value="{{ $content->value ?? '' }}">
                                                                    </div>
                                                                    @error('feature_price_image')
                                                                        <div class="error text-danger">{{ $message }}</div>
                                                                    @enderror
                                                                    @if (isset($content->meta_key))
                                                                        <img src="{{ asset($content->meta_value) }}"
                                                                            class="himg mt-2" alt="{{ $content->meta_key }}"
                                                                            style="width: 50px; height: 50px;">
                                                                    @endif
                                                                </div>
                                                            @elseif($content->meta_key == 'independent_img')
                                                                <div class="form-group">
                                                                    <label class="form-label" for="image">Independent
                                                                        Image</label>
                                                                    <div class="dz-message">
                                                                        <input type="file" class="form-control"
                                                                            name="independ_image[{{ $content->id }}]"
                                                                            id="metaValue"
                                                                            value="{{ $content->value ?? '' }}">
                                                                    </div>
                                                                    @error('independ_image')
                                                                        <div class="error text-danger">{{ $message }}</div>
                                                                    @enderror
                                                                    @if (isset($content->meta_key))
                                                                        <img src="{{ asset($content->meta_value) }}"
                                                                            class="himg mt-2" alt="{{ $content->meta_key }}"
                                                                            style="width: 50px; height: 50px;">
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                @elseif($val->meta_key === 'find_tool')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Find Tool
                                                            Text</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="find_tool[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'verify_user_review')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Verified User
                                                            Reviews Text</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="verify_user_review[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />

                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'verify_review_description')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Read Real
                                                            feedback
                                                            Text</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="verify_review_description[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'feature_price')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Feature And
                                                            Price
                                                            Comparision
                                                            Text</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="feature_price[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'feature_price_description')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Easily Compare
                                                            Software Text</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="feature_price_description[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'independent')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Independent
                                                            Insights</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="independent[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'independent_description')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Access Unbiased
                                                            Data
                                                            Text</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="independent_description[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'get_button_lable')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Get Button
                                                            Lable</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="get_button_lable[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                @endif
                                @endforeach
                                </div>
                                </div>
                                </div>

                        </div>
                    {{-- </div> --}}
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
                                            <button class="addCategory btn btn-primary btn-localio text-center"><em
                                                class=""></em><span>Update
                                                Content</span></button>

                                </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                                    @php
                                                        $languages = \App\Models\Language::where('status', 1)->get();
                                                    @endphp

                                                    <label class="form-label font-weight-bold">Country-Region</label>
                                                    <select class="form-control" name="language"  id="languageDropdown" >
                                                        @foreach ($languages as $language)
                                                            <option value="{{ $language->id }}" {{ $lang_code == $language->lang_code ? 'selected' : '' }}>

                                                                {{ $language->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                        </div>
                                        @php
                                        // Retrieve the value for 'permanent_url' if available from the HomeContent model
                                        $permanentUrl = $allHomeContents->firstWhere('meta_key', 'permanent_url')?->meta_value ?? '';
                                                // dd($permanentUrl);
                                    @endphp

                                    <div class="form-group mt-3">
                                        <label class="form-label permanent_url">Permanent Link</label>
                                        <input type="text"
                                               name="permanent_url"
                                               class="form-control"
                                               placeholder="Page-URL"
                                               value="{{ old('permanent_url', $permanentUrl) }}" />
                                        @error('permanent_url')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>


                                        {{-- @if ($lang_code == 'en-us') --}}
                                            @foreach ($homeContents as $content)
                                                @if ($content->meta_key == 'header_img')
                                                    <div class="form-group">
                                                        <label class="form-label" for="image">Header Image</label>
                                                        <div class="dz-message">
                                                            <input type="file" class="form-control"
                                                                name="header_image[{{ $content->id }}]" id="metaValue"
                                                                value="{{ $content->value ?? '' }}">
                                                        </div>
                                                        @error('header_image')
                                                            <div class="error text-danger">{{ $message }}</div>
                                                        @enderror
                                                        @if (isset($content->meta_key))
                                                            <img src="{{ asset($content->meta_value) }}"
                                                                alt="{{ $content->meta_key }}"
                                                                style="width: 100px; height: auto;">
                                                        @endif
                                                    </div>
                                                @elseif($content->meta_key == 'header_background_img')
                                                    <div class="form-group">
                                                        <label class="form-label" for="image">Header Background
                                                            Image</label>
                                                        <div class="dz-message">
                                                            <input type="file" class="form-control"
                                                                name="header_backgound_image[{{ $content->id }}]"
                                                                id="metaValue" value="{{ $content->value ?? '' }}">
                                                        </div>
                                                        @error('header_backgound_image')
                                                            <div class="error text-danger">{{ $message }}</div>
                                                        @enderror
                                                        @if (isset($content->meta_key))
                                                            <img src="{{ asset($content->meta_value) }}"
                                                                alt="{{ $content->meta_key }}"
                                                                style="width: 100px; height: auto;">
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        {{-- @endif --}}
                                        @foreach ($allHomeContents as $key => $val)
                                            @if ($val->meta_key == 'meta_home_title')

                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">Meta Title</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control"
                                                            id="{{ $key }}"
                                                            name="meta_home_title[{{ $val->id }}]"
                                                            value="{{ $val->meta_value ?? 'not data found' }}" />
                                                    </div>
                                                </div>

                                                @elseif($val->meta_key === 'Meta_home_description')
                                                <div class="form-group col-lg-12">
                                                    <label class="form-label" for="{{ $key }}">Description</label>
                                                    <div class="form-control-wrap">
                                                        <textarea class="form-control" id="{{ $key }}" name="Meta_home_description[{{ $val->id }}]" rows="4">{{ $val->meta_value ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                    </div>
                                </div>
                            </div>

                    </div>
                </div>
            </div>

        </div>
        </form>
        @endif
    </div>

    <script>
        $(document).ready(function () {
            $('#languageDropdown').on('change', function () {
                var langId = $(this).val(); // Get selected language ID
                var csrfToken = "{{ csrf_token() }}"; // Get CSRF token

                $.ajax({
                    url: "{{ route('admin.getContentByLanguage') }}", // Correct route
                    type: "POST",
                    data: {
                        language_id: langId,
                        _token: csrfToken
                    },
                    success: function (response) {


                    // Reload page after successful response
                    if (response.success) {
                        location.reload();
                    }
                    },
                    error: function (xhr, status, error) {
                    console.error("AJAX Error:", xhr.responseText);
                    alert("Error occurred: " + xhr.responseText);
                }
                });
                // alert(langId);
            });
        });
    </script>

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
