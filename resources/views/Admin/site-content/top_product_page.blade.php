@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add Top Product Content</h4>
            </div>
        </div>
        <?php

        $lang_code = getCurrentLocale();
        ?>
        @if (isset($productFiles) && !$productFiles->isEmpty())
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="nk-block">
                        <form action="{{ url('admin-dashboard/product-page-update') ?? '' }}" class="form-validate"
                            novalidate="novalidate" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                @if ($lang_code == 'en')
                                    <div class="card border">
                                        <div class="card-header mt-3">
                                            Top Product Header Banner Section
                                        </div>
                                        <div class="card-body">
                                            @foreach ($productFiles as $content)
                                                @if ($content->meta_key == 'banner_image')
                                                    <div class="col-md-12 mt-3">
                                                        <div class="form-group">
                                                            <label class="form-label" for="image">Header Image</label>
                                                            <div class="dz-message">
                                                                <input type="file" class="form-control"
                                                                    name="top_pro_banner_image[{{ $content->id }}]"
                                                                    id="metaValue" value="{{ $content->value ?? '' }}">
                                                            </div>
                                                            @error('top_pro_banner_image')
                                                                <div class="error text-danger">{{ $message }}</div>
                                                            @enderror
                                                            @if (!empty($content->meta_value) && file_exists(public_path($content->meta_value)))
                                                                <img src="{{ asset($content->meta_value) }}" class="mt-3"
                                                                    alt="{{ $content->meta_key }}"
                                                                    style="width: 100px; height: auto;">
                                                            @endif
                                                        </div>

                                                    </div>
                                                @elseif($content->meta_key == 'banner_bg_image')
                                                    <div class="col-md-12 mt-3">
                                                        <div class="form-group">
                                                            <label class="form-label" for="image">Background
                                                                Image</label>
                                                            <div class="dz-message">
                                                                <input type="file" class="form-control"
                                                                    name="top_pro_banner_bg_image[{{ $content->id }}]"
                                                                    id="metaValue" value="{{ $content->value ?? '' }}">
                                                            </div>
                                                            @error('top_pro_banner_bg_image')
                                                                <div class="error text-danger">{{ $message }}</div>
                                                            @enderror
                                                            @if (!empty($content->meta_value) && file_exists(public_path($content->meta_value)))
                                                                <img src="{{ asset($content->meta_value) }}" class="mt-3"
                                                                    alt="{{ $content->meta_key }}"
                                                                    style="width: 100px; height: auto;">
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($content->meta_key == 'facebook_icon')
                                                    <div class="col-md-12 mt-3">
                                                        <div class="form-group">
                                                            <label class="form-label" for="image">Facebook Image</label>
                                                            <div class="dz-message">
                                                                <input type="file" class="form-control"
                                                                    name="top_pro_facebook_icon[{{ $content->id }}]"
                                                                    id="metaValue" value="{{ $content->value ?? '' }}">
                                                            </div>
                                                            @error('top_pro_facebook_icon')
                                                                <div class="error text-danger">{{ $message }}</div>
                                                            @enderror
                                                            @if (!empty($content->meta_value) && file_exists(public_path($content->meta_value)))
                                                                <img src="{{ asset($content->meta_value) }}" class="mt-1"
                                                                    alt="{{ $content->meta_key }}"
                                                                    style="width: 100px; height: auto;">
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($content->meta_key == 'pinterest_icon')
                                                    <div class="col-md-12 mt-3">
                                                        <div class="form-group">
                                                            <label class="form-label" for="image">Pinterest Image</label>
                                                            <div class="dz-message">
                                                                <input type="file" class="form-control"
                                                                    name="top_pro_pinterest_icon[{{ $content->id }}]"
                                                                    id="metaValue" value="{{ $content->value ?? '' }}">
                                                            </div>
                                                            @error('top_pro_pinterest_icon')
                                                                <div class="error text-danger">{{ $message }}</div>
                                                            @enderror
                                                            @if (!empty($content->meta_value) && file_exists(public_path($content->meta_value)))
                                                                <img src="{{ asset($content->meta_value) }}" class="mt-1"
                                                                    alt="{{ $content->meta_key }}"
                                                                    style="width: 100px; height: auto;">
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($content->meta_key == 'twitter_icon')
                                                    <div class="col-md-12 mt-3">
                                                        <div class="form-group">
                                                            <label class="form-label" for="image">Twitter Image</label>
                                                            <div class="dz-message">
                                                                <input type="file" class="form-control"
                                                                    name="top_pro_twitter_icon[{{ $content->id }}]"
                                                                    id="metaValue" value="{{ $content->value ?? '' }}">
                                                            </div>
                                                            @error('top_pro_twitter_icon')
                                                                <div class="error text-danger">{{ $message }}</div>
                                                            @enderror
                                                            @if (!empty($content->meta_value) && file_exists(public_path($content->meta_value)))
                                                                <img src="{{ asset($content->meta_value) }}" class="mt-1"
                                                                    alt="{{ $content->meta_key }}"
                                                                    style="width: 100px; height: auto;">
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($content->meta_key == 'copylink_icon')
                                                    <div class="col-md-12 mt-3">
                                                        <div class="form-group">
                                                            <label class="form-label" for="image">CopyLink Image</label>
                                                            <div class="dz-message">
                                                                <input type="file" class="form-control"
                                                                    name="top_pro_copylink_icon[{{ $content->id }}]"
                                                                    id="metaValue" value="{{ $content->value ?? '' }}">
                                                            </div>
                                                            @error('top_pro_copylink_icon')
                                                                <div class="error text-danger">{{ $message }}</div>
                                                            @enderror
                                                            @if (!empty($content->meta_value) && file_exists(public_path($content->meta_value)))
                                                                <img src="{{ asset($content->meta_value) }}"
                                                                    class="mt-1" alt="{{ $content->meta_key }}"
                                                                    style="width: 100px; height: auto;">
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($content->meta_key == 'more_icon')
                                                    <div class="col-md-12 mt-3">
                                                        <div class="form-group">
                                                            <label class="form-label" for="image">More Image</label>
                                                            <div class="dz-message">
                                                                <input type="file" class="form-control"
                                                                    name="top_pro_more_icon[{{ $content->id }}]"
                                                                    id="metaValue" value="{{ $content->value ?? '' }}">
                                                            </div>
                                                            @error('top_pro_more_icon')
                                                                <div class="error text-danger">{{ $message }}</div>
                                                            @enderror
                                                            @if (!empty($content->meta_value) && file_exists(public_path($content->meta_value)))
                                                                <img src="{{ asset($content->meta_value) }}"
                                                                    class="mt-1" alt="{{ $content->meta_key }}"
                                                                    style="width: 100px; height: auto;">
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <!-- new image add -->
                                                @elseif($content->meta_key == 'mail_image')
                                                    <div class="col-md-12 mt-3">
                                                        <div class="form-group">
                                                            <label class="form-label" for="image">Mail Image</label>
                                                            <div class="dz-message">
                                                                <input type="file" class="form-control"
                                                                    name="top_pro_mail_image[{{ $content->id }}]"
                                                                    id="metaValue" value="{{ $content->value ?? '' }}">
                                                            </div>
                                                            @error('top_pro_mail_image')
                                                                <div class="error text-danger">{{ $message }}</div>
                                                            @enderror
                                                            @if (!empty($content->meta_value) && file_exists(public_path($content->meta_value)))
                                                                <img src="{{ asset($content->meta_value) }}"
                                                                    class="mt-1" alt="{{ $content->meta_key }}"
                                                                    style="width: 50px; height: 50px;">
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($content->meta_key == 'green_tick_img')
                                                    <div class="col-md-12 mt-3">
                                                        <div class="form-group">
                                                            <label class="form-label" for="image">Green Tick
                                                                Image</label>
                                                            <div class="dz-message">
                                                                <input type="file" class="form-control"
                                                                    name="top_pro_green_tick_img[{{ $content->id }}]"
                                                                    id="metaValue" value="{{ $content->value ?? '' }}">
                                                            </div>
                                                            @error('top_pro_green_tick_img')
                                                                <div class="error text-danger">{{ $message }}</div>
                                                            @enderror
                                                            @if (!empty($content->meta_value) && file_exists(public_path($content->meta_value)))
                                                                <img src="{{ asset($content->meta_value) }}"
                                                                    class="mt-1" alt="{{ $content->meta_key }}"
                                                                    style="width: 50px; height: 50px;">
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                @if (isset($topProductContents) && !$topProductContents->isEmpty())
                                    @foreach ($topProductContents as $key => $val)
                                        @if ($val->meta_key === 'header_title')
                                            <div class="card border">
                                                <div class="card-header mt-3">
                                                    Top Product Header Title Section
                                                </div>
                                                <div class="card-body">

                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label"
                                                            for="{{ $key }}">Automotive</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="header_title[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />

                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'header_sub_title')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">How to find
                                                            text</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="header_sub_title[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />

                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'header_bottom_text')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Localio
                                                            Provides Independent Text</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="header_bottom_text[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />

                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'learn_more')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Learn
                                                            More</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="learn_more[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="card border">
                                                <div class="card-header mt-3">
                                                    Top Product Left Menu Section
                                                </div>
                                                <div class="card-body">
                                                @elseif($val->meta_key === 'search_placeholder')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Seach Product
                                                            Placeholder</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="search_placeholder[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />

                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'user_rating')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">User
                                                            Rating</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="user_rating[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />

                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'price')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Price</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="price[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'price_option')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Pricing
                                                            Options</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="price_option[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'features')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label"
                                                            for="{{ $key }}">Features</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="features[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'show_more')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Show
                                                            more</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="show_more[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'deployment')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label"
                                                            for="{{ $key }}">Deployment</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="deployment[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'company_size')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Company
                                                            Size</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="company_size[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border">
                                                <div class="card-header mt-3">
                                                    Top Product Features Main Menu Section
                                                </div>
                                                <div class="card-body">
                                                @elseif($val->meta_key === 'drop_down_text')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Top Rated
                                                            Products</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="drop_down_text[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'visit_website')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Visit
                                                            Website</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="visit_website[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'read_more')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Read
                                                            more</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="read_more[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'key_features')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Key
                                                            Features</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="key_features[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'starting_price')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Starting
                                                            Price</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="starting_price[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'month')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label"
                                                            for="{{ $key }}">Month</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="month[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'compare_products')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Compare
                                                            Products</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="compare_products[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'rating')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label"
                                                            for="{{ $key }}">Rating</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="rating[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'footer_title')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Top-rated
                                                            Products of</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="footer_title[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'footer_sub_title')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Fill out the
                                                            form</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="footer_sub_title[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'email_placeholder')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Email
                                                            Placeholder</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="email_placeholder[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'subscribe_lable')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Subscribe
                                                            Lable</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="subscribe_lable[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'you_agree')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">By
                                                            proceeding, you agree to our</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="you_agree[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'terms_of_use')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">terms Of
                                                            Use</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="terms_of_use[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'privacy_policy')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">Privacy
                                                            Policy</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="privacy_policy[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                @elseif($val->meta_key === 'and')
                                                    <div class="form-group col-lg-12">
                                                        <label class="form-label" for="{{ $key }}">And</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control"
                                                                id="{{ $key }}"
                                                                name="and[{{ $val->id }}]"
                                                                value="{{ $val->meta_value ?? '' }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                                <div class="col-md-12 mt-4">
                                    <div class="form-group">
                                        <button class="addCategory btn btn-primary btn-localio text-center"><em
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
