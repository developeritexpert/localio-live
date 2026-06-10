<div class="col-lg-9 p-0">
    <div class="user_content">
        <div class="edit-listing">
            <div class="d-flex justify-content-between mb-2">

                <h2>{{ __('file.edit-listing') }}</h2>

                <div class="form-group">
                        <label class="form-label font-weight-bold">Country-Region</label>
                        <select class="form-control" wire:model.live="lang_id" style="height: auto">
                            @foreach (\App\Models\Language::where('status', 1)->get() as $language)
                                <option value="{{ $language->id }}">{{ $language->name }}</option>
                            @endforeach
                        </select>

                </div>
            </div>

            <div class="edit-boxs">

                @php
                    $currentTranslation = $business->translations->firstWhere('lang_id', $lang_id);
                @endphp


                {{-- first header --}}
                <div class="edit-box" style="background-color:#06498B1A; border: 0px;">

                        <div class="edit-icon">
                            @if($editingField === 'name_and_icon')
                                <button type="button" wire:click="saveAllFields('name_and_icon')"
                                        style="background: none; border: none; padding: 0; cursor: pointer;"
                                        wire:key="save-icon">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            @else
                                {{-- <button type="button" wire:click="startEditing('name_and_icon')"
                                        style="background: none; border: none; padding: 0; cursor: pointer;"
                                        wire:key="edit-icon">
                                    <i class="fa-solid fa-pencil"></i>
                                </button> --}}

                                <!-- Pencil button opens modal -->
                                <button type="button"
                                style="background: none; border: none; padding: 0; cursor: pointer;"
                                data-bs-toggle="modal"
                                data-bs-target="#editBusinessModal">
                                <i class="fa-solid fa-pencil"></i>
                                </button>

                            @endif
                        </div>

                    <div class="editable-content mt-15">
                        <div class="user-info">
                            <div class="asn-img">
                                @if($editingField === 'name_and_icon')
                                    <div wire:click.stop class="image-upload-container" style="position: relative; display: inline-block;">
                                        <!-- Hidden file input -->
                                        <input type="file" wire:model="newIcon" id="logo-upload"
                                            style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; z-index: 2;"
                                            accept="image/*">

                                            @error('newIcon')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        <!-- Current logo with upload overlay -->
                                        <div style="position: relative; display: inline-block;">
                                            <img src="{{ asset($business->icon_id) }}" alt="Business Icon" class="logo"
                                                style="cursor: pointer; opacity: 0.8; border: 2px dashed #007bff; border-radius: 8px;">

                                            <!-- Upload icon overlay -->
                                            <div class="upload-overlay" style="position: absolute; top: 50%; left: 50%;
                                                                            transform: translate(-50%, -50%);
                                                                            background: rgba(0,123,255,0.9);
                                                                            color: white; padding: 6px;
                                                                            border-radius: 50%; font-size: 16px;">
                                                <i class="fa-solid fa-camera"></i>
                                            </div>
                                        </div>

                                        <!-- Upload feedback -->
                                        @if ($newIcon)
                                            <div style="position: absolute; bottom: -25px; left: 0; right: 0;
                                                        text-align: center; font-size: 11px; color: #28a745;">
                                                <i class="fa-solid fa-check-circle"></i> New image selected
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <img src="{{ asset($business->icon_id) }}" alt="Business Icon" class="logo" style="cursor: pointer;">
                                @endif
                            </div>

                            <div class="user-data">
                                <div class="user-name d-flex">
                                    @if($editingField === 'name_and_icon')
                                        <input type="text" wire:model.defer="name" class="form-control business-name-input"
                                            style="width: auto; min-width: 200px; font-size: 1.5rem; font-weight: 600;
                                                    border: 2px solid #007bff; border-radius: 6px; padding: 8px 12px;
                                                    background: #fff; box-shadow: 0 2px 4px rgba(0,123,255,0.1);
                                                    transition: all 0.2s ease;"
                                            placeholder="Enter business name">

                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    @else
                                    <h4>{{ $currentTranslation?->name ?? 'N/A' }}</h4>
                                        {{-- <i class="fa-regular fa-heart"></i> --}}
                                    @endif
                                </div>

                                <p class="size20 m-0">
                                    Pricing, Features, Alternatives & User Ratings
                                </p>
                            </div>
                        </div>

                        <div class="rgt-edit-data">
                            <div class="edit-btn">
                                <a href="javascript:void(0)" class="btn unq_btn">{{ __('file.visit_Website') }}
                                    <img src="{{ asset('vender_dashboard/img/arrowdown.png') }}" alt="">
                                </a>

                                <div class="rating">
                                    @php
                                    $ratingCount = $business->reviews->where('status', 'active')->count();

                                    @endphp

                                    {{-- <div class="tp-btm d-flex flex-col-mob">
                                        <div class="inn_ul">
                                            <div class="rating-stars">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <=floor($averageRating))
                                                    <i class="fas fa-star text-warning"></i>
                                                    @elseif ($i - 0.5 <= $averageRating)
                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                        @else
                                                        <i class="far fa-star text-warning"></i>
                                                        @endif
                                                        @endfor
                                            </div>
                                        </div>
                                        <div class="rate_box">
                                            {{ number_format($averageRating, 1) }} {{ $ratingCount }} ratings
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- learn more --}}
                {{-- <div class="edit-box" style="background-color:#06498B1A; border: 0px;">
                    <div class="lcl_text">
                        <p class="sml_text">{{ static_text('localio_commissions_message') }}
                            <a class="big-bld" type="button" onclick="openModal()">Learn more</a>
                        </p>
                    </div>
                </div> --}}


                {{-- Key Feature --}}
                <div class="edit-box">
                    <div class="edit-icon">
                        <!-- Pencil button opens modal -->
                        <button type="button"
                        style="background: none; border: none; padding: 0; cursor: pointer;"
                        data-bs-toggle="modal"
                        data-bs-target="#editFeaturesModal">
                        <i class="fa-solid fa-pencil"></i>
                    </button>
                    </div>

                    <div class="editable-content-2 mt-15">
                        <div class="features">

                            {{-- Features --}}
                            <div class="feature-box blue-border">
                                <ul>
                                    @if(!empty($features))
                                        @foreach($features as $feature)
                                            <li>{{ $feature }}</li>
                                        @endforeach
                                    @else
                                        <li>Free domain & SSL certificate</li>
                                        <li>Customizable automatic updates</li>
                                        <li>Scalable performance management</li>
                                        <li>DDoS & malware protection</li>
                                    @endif
                                </ul>
                            </div>

                            <div class="feature-box">
                                <div class="price-box">
                                    <p>{{ __('file.Starting_Price') }}</p>
                                    <p class="price">{{ $currency }}{{ $startingPrice ?? '9' }} {{ __('file./Month') }}</p>
                                </div>
                            </div>

                            <div class="feature-box">
                                <div class="check-img">
                                    <img src="{{ asset('vender_dashboard/img/check.png') }}" alt="">
                                </div>
                                <h6>{{ $freeTrialText ?? __('file.free_trail') }} <br>{{ __('file.available') }}</h6>
                                <a href="javascript:void(0)" class="btn blue-btn">{{ __('file.claim_Now') }}</a>
                            </div>


                        </div>
                    </div>
                </div>


                {{-- Description --}}
                <div class="edit-box">

                    <div class="edit-icon">
                        @if($editingField === 'description')
                        <button type="button" wire:click="saveAllFields('description_and_images')"
                                style="background: none; border: none; padding: 0; cursor: pointer;"
                                wire:key="save-description">
                            <i class="fa-solid fa-check"></i>
                        </button>
                        @else
                            <button type="button" wire:click="startEditing('description')"
                                    style="background: none; border: none; padding: 0; cursor: pointer;"
                                    wire:key="edit-description">
                                <i class="fa-solid fa-pencil"></i>
                            </button>
                        @endif

                    </div>

                    <div class="editable-content-2 mt-15">
                        <div class="row align-items-center">
                            {{-- LEFT COLUMN --}}
                            <div class="col-lg-7">
                                <div class="about">
                                    <div class="edit-heading">
                                        <h3 style="color: #002347;">What is {{ $currentTranslation?->name }}</h3>

                                        @if($editingField === 'description')
                                            <textarea wire:model.defer="description"
                                                class="form-control"
                                                rows="6"
                                                style="border: 2px solid #007bff; border-radius: 6px; background: #fff;
                                                        padding: 10px; font-size: 16px; resize: vertical; box-shadow: 0 2px 4px rgba(0,123,255,0.1);">
                                            </textarea>

                                            @error('description')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        @else
                                            <p>{!! $currentTranslation?->description !!}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>


                            {{-- RIGHT COLUMN --}}
                            <div class="col-lg-5">
                                <div class="is-asana-rgt">

                                    @if ($editingField === 'description')
                                    <div class="row g-3">

                                        {{-- Upload UI --}}
                                        <div class="col-12">
                                            <label class="form-label fw-bold mb-2">Upload New Images</label>
                                            <div wire:click.stop class="image-upload-container">
                                                <input type="file" multiple wire:model="newImages" accept="image/*">
                                                <div>
                                                    {{-- <img src="{{ asset('front/img/upload-placeholder.png') }}" alt="Upload"
                                                        class="img-fluid">
                                                     <div class="upload-overlay">
                                                        <i class="fa-solid fa-camera"></i>
                                                    </div> --}}
                                                </div>
                                            </div>

                                            @error('newImages.*')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Preview Thumbnails for New Uploads --}}
                                        @if ($newImages)
                                            <div class="col-12 mt-2">
                                                <div class="row g-2">
                                                    @foreach ($newImages as $image)
                                                        <div class="col-4">
                                                            <img src="{{ $image->temporaryUrl() }}" class="img-fluid rounded border" alt="Preview">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Existing Images with Replace & Delete --}}
                                        @if ($business->business_images && count($business->business_images))
                                            <div class="col-12 mt-4">
                                                <label class="form-label fw-bold mb-2">Existing Images</label>
                                                <div class="row g-3">
                                                    @foreach ($business->business_images as $index => $img)
                                                        @if(!in_array($index, $deleteImageIndexes))
                                                            <div class="col-md-6">
                                                                <div class="card">
                                                                    <img src="{{ asset($img) }}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="Image {{ $index + 1 }}">
                                                                    <div class="card-body p-2">
                                                                        {{-- Replace --}}
                                                                        <div class="mb-2">
                                                                            <label class="form-label small mb-1">Replace</label>
                                                                            <input type="file" wire:model.defer="replaceImages.{{ $index }}" accept="image/*" class="form-control form-control-sm">
                                                                            @error("replaceImages.$index")
                                                                                <div class="text-danger small">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>

                                                                        {{-- Delete --}}
                                                                        <button type="button" class="btn btn-danger btn-sm w-100" wire:click="deleteImage({{ $index }})">
                                                                            <i class="fa fa-trash"></i> Delete
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach

                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                        @else
                                        {{-- View Mode Image Slider --}}
                                        @if ($business->business_images && count($business->business_images))
                                            <div class="is-asana-rgt">
                                                <div class="is-asan-slider">
                                                    {{-- <div class="asan-slider slider-for">
                                                        @foreach ($business->business_images as $img)
                                                            <div class="asan-slider-inr">
                                                                <img src="{{ asset($img) }}" alt="Business Image">
                                                            </div>
                                                        @endforeach
                                                    </div> --}}
                                                    <div class="asan-slider asan-slider-btm slider-nav">
                                                        @foreach ($business->business_images as $img)
                                                            <div>
                                                                <img src="{{ asset($img) }}" alt="Thumb">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-muted mt-4">No images uploaded yet.</p>
                                        @endif
                                    @endif


                                    <div class="card p-3 mb-3 bg-light rounded d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="fw-bold mb-0">Thread Theory Atelier</h5>
                                            <small class="text-muted">Pricing, Features, Alternatives & User Ratings</small>
                                        </div>
                                        <div>
                                            <a href="#" class="btn btn-danger rounded-pill">Visit Website →</a>
                                            <!-- Pencil icon triggers toggle -->
                                            <i class="fa fa-pencil ms-3 text-dark" role="button" data-bs-toggle="collapse" data-bs-target="#editBusinessForm"></i>
                                        </div>
                                    </div>

                                    <!-- Inline Popup Form (collapsible) -->
                                    {{--
                                    <div class="collapse mt-3" id="editBusinessForm">
                                        <div class="card card-body">
                                            <h5>Edit Business</h5>
                                            <div class="mb-3">
                                                <label class="form-label">Business Name</label>
                                                <input type="text" class="form-control" wire:model.defer="name">
                                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Website URL</label>
                                                <input type="text" class="form-control" wire:model.defer="website_url">
                                                @error('website_url') <span class="text-danger">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="d-flex justify-content-end">
                                                <button type="button" class="btn btn-secondary me-2" data-bs-toggle="collapse" data-bs-target="#editBusinessForm">Cancel</button>
                                                <button type="button" class="btn btn-primary" wire:click="saveAllFields('name_and_icon')" data-bs-toggle="collapse" data-bs-target="#editBusinessForm">
                                                    Save changes
                                                </button>
                                            </div>
                                        </div>
                                    </div> --}}



                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- product section --}}
                <div class="edit-box">
                    <div class="edit-icon">
                    </div>
                    <div class="editable-content-2 editable-content-3 editable-content-l9 mt-15">
                        <section class="product_pricing_sec p_50 pb-0" id="section3">
                            <div class="section_title text-center mb-4" data-aos-duration="1000">
                                <h3>{{ $name }} Pricing Plans</h3>
                            </div>

                            <div class="pricing_plans_row d-flex justify-content-center align-items-center">
                                <div class="row table_st_1 justify-content-center">
                                    @forelse ($business->products as $product)
                                        @php
                                            $price = $product->prices->first();
                                            $displayPrice = null;
                                            $originalPrice = null;
                                            $timeUnit = 'month';
                                            $isDiscounted = false;

                                            if ($price) {
                                                $today = \Carbon\Carbon::now()->startOfDay();
                                                $timeUnit = $price->time_unit;

                                                if ($price->discount_price && (!$price->discount_expiration_date || \Carbon\Carbon::parse($price->discount_expiration_date)->endOfDay() >= $today)) {
                                                    $displayPrice = $price->discount_price;
                                                    $originalPrice = $price->price;
                                                    $timeUnit = $price->discount_time_units ?? $price->time_unit;
                                                    $isDiscounted = true;
                                                } elseif ($price->renewal_price) {
                                                    $displayPrice = $price->renewal_price;
                                                    $timeUnit = $price->renewal_time_units ?? $price->time_unit;
                                                } else {
                                                    $displayPrice = $price->price;
                                                }

                                                $timeUnitDisplay = match ($timeUnit) {
                                                    'one_time' => 'One-Time',
                                                    'quarter' => '3 months',
                                                    default => ucfirst($timeUnit),
                                                };
                                            }
                                        @endphp

                                        <div class="col-md-6 col-lg-6 mb-4"  data-aos-duration="1000">
                                            <div class="pricing_card">
                                                <div class="pricing_header">
                                                    <h6>{{ $product->translations->first()?->name ?? 'Plan' }}</h6>
                                                </div>
                                                <div class="pricing_amount">
                                                    @if ($displayPrice !== null)
                                                        <h3 class="blue-text">
                                                            {{ $price->currency }}
                                                            {{ number_format($displayPrice, 2) }}
                                                            @if (!empty($timeUnitDisplay))
                                                                <span class="big_text">/ {{ $timeUnitDisplay }}</span>
                                                            @endif
                                                        </h3>

                                                        @if ($isDiscounted && $originalPrice)
                                                            <p class="original_price">
                                                                <s>{{ $price->currency }} {{ number_format($originalPrice, 2) }}</s>
                                                                <span class="discount_badge">Sale</span>
                                                            </p>
                                                        @endif

                                                        @if (!empty($price->additional_info))
                                                            <p class="additional_info">{{ $price->additional_info }}</p>
                                                        @endif
                                                    @else
                                                        <h3 class="blue-text">Contact for Pricing</h3>
                                                    @endif
                                                </div>

                                                @if ($isDiscounted && $price->discount_expiration_date)
                                                    <div class="discount_timer">
                                                        <p>Offer ends:
                                                            {{ \Carbon\Carbon::parse($price->discount_expiration_date)->format('M d, Y') }}
                                                        </p>
                                                    </div>
                                                @endif

                                                <div class="pricing_action d-flex justify-content-center">
                                                    <a href="{{ $product->product_link ?? $business->affiliate_link ?? '#' }}"
                                                       data-track="{{ json_encode([
                                                           'type' => 'click',
                                                           'product_id' => $product->id,
                                                           'business_id' => $business->id,
                                                           'action' => 'try_for_free',
                                                           'label' => 'Try for free',
                                                       ]) }}"
                                                       target="_blank"
                                                       class="cta cta_orange" style="border: none;">
                                                        Try for free
                                                        <span class="right-arw">
                                                            <img src="{{ asset('front/img/right-arrw.svg') }}" alt="">
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center">
                                            <p>No pricing plans available for this product.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="lcl_text mt-4">
                                <p class="sml_text">* Prices may vary. Visit the official website for the most current pricing.</p>
                            </div>
                        </section>
                    </div>
                </div>

                {{-- pros and Cons --}}
                <div class="edit-box">
                    <div class="edit-icon">

                    </div>
                    <div class="editable-content-2 editable-content-3 editable-content-l9 mt-15">
                            <section class="pros-cons p_50 light" id="section4">

                                <div>

                                    <div class="atelier">
                                        <h3>
                                        {{$business->translations->first()->name ?? 'Business'}} Pros & Cons
                                        </h3>

                                        {{-- User Avatars ds--}}
                                        <div class="theory">
                                            {{-- @for($i = 1; $i <= 5; $i++)
                                                <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #f9ca24, #6c5ce7); border: 2px solid white; margin-left: -5px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">
                                                {{ chr(64 + $i) }} --}}
                                        {{-- </div> --}}
                                        {{-- @endfor --}}
                                    </div>

                                    {{-- Description --}}
                                    <p>
                                        To find out what users like and dislike about monday.com, our team used advanced techniques
                                        to analyze the most common positive and negative aspects mentioned about monday.com
                                        within user reviews. These are the most frequently mentioned pros and cons by our users; we've
                                        sorted the resulting list based on the percentage of users who expressed positive sentiment
                                        towards the given topic in their reviews.
                                    </p>
                                </div>

                                {{-- Pros Section --}}
                                <div class="pros-rated">
                                    <h3>
                                        Pros rated by users
                                    </h3>

                                    {{-- Productivity Enhancement --}}
                                    <div class="pro-enhan">
                                        <div class="otr_pickr">
                                            <div class="greentickicon">
                                                <i class="fa-solid fa-check"></i>
                                            </div>
                                        <h4>Productivity enhancement</h4>

                                        </div>
                                        <p >Of the 215 reviews that mention productivity, 95% are positive</p>
                                        <p>
                                            Users appreciate monday.com's productivity features, which offer functionality for automating
                                            tasks and integrations with other platforms. Additionally, they provide users with visual aids
                                            such as timelines and boards to keep teams aligned and efficient.
                                        </p>
                                    </div>

                                    {{-- Project Management Efficiency --}}
                                    <div class="Pro-manage">
                                        <div class="otr_pickr1">
                                            <div class="greentickicon1">
                                                <i class="fa-solid fa-check"></i>
                                            </div>
                                            <h4>Project management efficiency</h4>
                                        </div>
                                        <p>
                                            Of the 2,235 reviews that talk about efficiency, 94% of them are positive
                                        </p>
                                        <p>
                                            Users like monday.com's ability to streamline project management by allowing them to organize
                                            projects, assign tasks, and track progress in one place, which enhances team collaboration and
                                            accountability.
                                        </p>
                                    </div>
                                </div>

                                {{-- Cons Section --}}
                                <div>
                                    <h3>
                                        Cons rated by users
                                    </h3>

                                    {{-- Email Notifications Overload --}}
                                    <div class="cons-rated">
                                        <div class="pickr">
                                            <div class="redboxicon">
                                                <i class="fa-solid fa-minus"></i>
                                            </div>
                                            <h4>Email notifications overload</h4>
                                        </div>
                                        <p>
                                            Of the 319 reviews that talk about email notifications, 39% are negative
                                        </p>
                                        <p>
                                            Users frequently report that monday.com's email notifications can be overwhelming, with users
                                            receiving too many notifications leading to cluttered inboxes and difficulty in managing and
                                            prioritizing important updates.
                                        </p>
                                    </div>

                                    {{-- Performance Improvements --}}
                                    <div class="prform-imp">
                                        <div class="picker">
                                            <div class="redboxicon">
                                                <i class="fa-solid fa-minus"></i>
                                            </div>
                                            <h4>Need for performance improvements</h4>
                                        </div>
                                        <p>
                                            Out of the 231 reviews talking about performance, 38% are negative
                                        </p>
                                        <p>
                                            Many users report occasional lags and slow response times, especially when handling large
                                            datasets or multiple tasks, which can disrupt workflows and reduce productivity.
                                        </p>
                                    </div>
                                </div>
                                </div>
                            </section>
                    </div>
                </div>

                {{-- alternate --}}
                {{-- <div class="edit-box ">
                    <div class="edit-icon">

                    </div>
                    <div class="editable-content-4 mt-15">
                        <section class="populr-alternative  light p_50" id="section5">


                            <div class="populr-alter-wrp" data-aos-duration="1000">
                                <h3 class="text-center">Compare With A Popular Alternative
                                </h3>
                                <div class="altr-wrp-inr-txt d-flex"  data-aos-duration="1000">
                                    <div class="altr-lft-div">
                                        {{-- <div class="altr-mid-hd b_btm">
                                        </div> --}}
                                        {{-- <ul class="list-unstyled  m-0">
                                            <li class="b_btm h_80 fw_700">Starting Price</li>
                                            <li class="b_btm h_80 fw_700">Pricing Options</li>
                                            <li class="b_btm h_80 fw_700">Ease Of Use</li>
                                            <li class="b_btm h_80 fw_700">Value For Money</li>
                                            <li class="b_btm h_80 fw_700">Customer Service</li>
                                            <li class="b_btm h_80 fw_700">Exclusives Services</li>
                                        </ul>
                                    </div>
                                    <div class="altr-mid-div">
                                        <div class="altr-mid-hd d-flex">
                                            <div class="poplr-img">
                                                <img src="{{ asset($business->icon_id ?? 'front/img/poplr-zero.svg') }}"
                                                    alt="">
                                            </div>
                                            <div class="poplr-txt">
                                                <h6 class="fw_700 h6_26">{{ $business->translations->firstWhere('lang_id', $lang_id)?->name }}</h6>
                                                @php
                                                $ratingCount = $business->reviews
                                                ->where('status', 'active')
                                                ->count();
                                                @endphp

                                                <div class="tp-btm d-flex flex-col-mob">
                                                    <div class="inn_ul">
                                                        <div class="rating-stars">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <=floor($averageRating))
                                                                <i class="fas fa-star text-warning"></i>
                                                                @elseif ($i - 0.5 <= $averageRating)
                                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                                    @else
                                                                    <i class="far fa-star text-warning"></i>
                                                                    @endif
                                                                    @endfor
                                                        </div>
                                                    </div>
                                                    <div class="rate_box">
                                                        {{ number_format($averageRating, 1) }} | {{ $ratingCount }}
                                                        ratings
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="poplr-span-txt h_80">
                                            <p class="m-0"><span>{{ $currency }}{{ $startingPrice }}</span>
                                                /{{ $timeUnit }}
                                            </p>
                                        </div>
                                        @if (!empty($business->pricingOptions) && $business->pricingOptions->isNotEmpty())
                                        <div class="poplr-vrsion-trial h_80">
                                            <ul class="list-unstyled d-flex m-0 flex-wrap">
                                                @foreach ($business->pricingOptions as $option)
                                                @if ($option->translations->isNotEmpty())
                                                <li>
                                                    <img src="{{ asset('front/img/tik-mrk.svg') }}">
                                                    {{ $option->translations->first()->name }}
                                                </li>
                                                @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                        @else
                                        <div class="poplr-vrsion-trial h_80">
                                            <ul class="list-unstyled d-flex m-0 flex-wrap">
                                                <li>
                                                    NA
                                                </li>
                                            </ul>
                                        </div>
                                        @endif
                                        <div class="poplr-progress">
                                            <ul class="list-unstyled m-0">
                                                <li class="h_80">
                                                    <div class="prgs_br d-flex">
                                                        <progress class="progress-bar" value="{{ $easeOfUseAvg * 20 }}"
                                                            max="100"></progress>
                                                        <output>{{ $easeOfUseAvg }}/5</output>
                                                    </div>
                                                </li>
                                                <li class="h_80">
                                                    <div class="prgs_br d-flex">
                                                        <progress class="progress-bar"
                                                            value="{{ $valueForMoneyAvg * 20 }}"
                                                            max="100"></progress>
                                                        <output>{{ $valueForMoneyAvg }}/5</output>
                                                    </div>
                                                </li>
                                                <li class="h_80">
                                                    <div class="prgs_br d-flex">
                                                        <progress class="progress-bar"
                                                            value="{{ $customerServiceAvg * 20 }}"
                                                            max="100"></progress>
                                                        <output>{{ $customerServiceAvg }}/5</output>
                                                    </div>
                                                </li>
                                                <li class="h_80">
                                                    <div class="prgs_br d-flex">
                                                        <progress class="progress-bar"
                                                            value="{{ $exclusiveFeatureAvg * 20 }}"
                                                            max="100"></progress>
                                                        <output>{{ $exclusiveFeatureAvg }}/5</output>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="poplr-visit-btn">
                                            <a href="{{ $link }} "
                                                class="cta cta_orange d-flex align-items-center">
                                                Visit Website
                                                <svg width="17" height="12" viewBox="0 0 17 12" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M1.2647 5.84154C7.08595 5.84154 7.42894 5.84154 13.2205 5.84154C13.2304 5.81429 13.2403 5.78704 13.2403 5.75979C13.1017 5.6962 12.9631 5.63262 12.8245 5.55995C10.9435 4.62434 9.91386 3.14372 9.52775 1.25434C9.50795 1.18167 9.59705 1.09084 9.63665 1C9.72575 1.0545 9.86436 1.09992 9.89406 1.18167C10.0525 1.6086 10.1317 2.06278 10.3198 2.47154C11.1712 4.34275 12.6859 5.45095 14.8738 5.78704C15.0124 5.80521 15.1114 5.97779 15.25 6.0868C14.4778 6.32297 13.7947 6.45922 13.1908 6.72265C11.4286 7.49475 10.4089 8.82095 9.99306 10.5559C9.94356 10.7739 9.97326 11.11 9.51785 10.9647C9.87426 8.82095 10.6861 7.79451 13.3096 6.21397C13.0324 6.21397 12.8443 6.21397 12.6562 6.21397C7.20123 6.21397 7.18494 6.21397 1.7201 6.20488C1.5122 6.20488 1.1756 6.32297 1.2647 5.84154Z"
                                                        fill="white" stroke="white" stroke-width="0.8" />
                                                    <!-- Code injected by live-server -->
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                    @if (count($alternativeBusiness) > 0)

                                    <div class="altr-rgt-div">
                                        <div class="altr-mid-hd d-flex b_btm ">
                                            <div class="poplr-img">
                                                <img
                                                    src="{{ asset($alternativeBusiness[0]->icon_id ?? 'front/img/lyt-rd-grey.svg') }}">
                                            </div>
                                            <div class="poplr-txt">
                                                @if (isset($alternativeBusiness[0]) && $alternativeBusiness[0]->translations->isNotEmpty())
                                                <h6 class="fw_700 h6_26">
                                                    {{ $alternativeBusiness[0]->translations->first()->name }}
                                                </h6>
                                                @endif
                                                @php
                                                $ratingCount = $alternativeBusiness[0]->reviews
                                                ->where('status', 'active')
                                                ->count();
                                                $altAverageRating =
                                                $alternativeBusiness[0]->reviews->count() > 0
                                                ? $alternativeBusiness[0]->reviews->avg('rating')
                                                : 0;
                                                @endphp

                                                <div class="tp-btm d-flex flex-col-mob">
                                                    <div class="inn_ul">
                                                        <div class="rating-stars">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <=floor($altAverageRating))
                                                                <i class="fas fa-star text-warning"></i>
                                                                @elseif ($i - 0.5 <= $altAverageRating)
                                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                                    @else
                                                                    <i class="far fa-star text-warning"></i>
                                                                    @endif
                                                                    @endfor
                                                        </div>
                                                    </div>
                                                    <div class="rate_box">
                                                        {{ number_format($altAverageRating, 1) }} | {{ $ratingCount }}
                                                        ratings
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                        $price = getBusinessesWithStartingPrice($alternativeBusiness[0]);
                                        // dd($price);
                                        if (!empty($price) && isset($price[0]['starting_price'])) {
                                        $businessprice = $price[0]['starting_price'];
                                        $startingPrice = $businessprice['amount'];
                                        $currency = $businessprice['currency'] ?? '$';
                                        $timeUnit = ucfirst($businessprice['time_unit'] ?? 'month');
                                        }
                                        // dd($alternativeBusiness->id);
                                        // Get reviews for the current altbusiness
                                        $reviews = \App\Models\Review::where(
                                        'business_id',
                                        $alternativeBusiness[0]->id,
                                        )->get();

                                        $altEaseOfUse = round($reviews->avg('ease_of_use_rating'), 1);
                                        $altValueForMoney = round($reviews->avg('value_for_money_rating'), 1);
                                        $altCustomerService = round(
                                        $reviews->avg('customer_service_rating'),
                                        1,
                                        );
                                        $altExclusiveFeature = round(
                                        $reviews->avg('exclusive_service_rating'),
                                        1,
                                        );
                                        @endphp
                                        <div class="poplr-span-txt b_btm h_80 ">
                                            <p class="m-0"><span>{{ $currency }}{{ $startingPrice }}</span>
                                                /{{ $timeUnit }}</p>
                                        </div>
                                        @if (!empty($alternativeBusiness[0]->pricingOptions) && $alternativeBusiness[0]->pricingOptions->isNotEmpty())
                                        <div class="poplr-vrsion-trial b_btm h_80 ">
                                            <ul class="list-unstyled d-flex m-0 flex-wrap">
                                                @foreach ($alternativeBusiness[0]->pricingOptions as $option)
                                                @if ($option->translations->isNotEmpty())
                                                <li>
                                                    <img src="{{ asset('front/img/tik-mrk.svg') }}">
                                                    {{ $option->translations->first()->name }}
                                                </li>
                                                @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                        @else
                                        <div class="poplr-vrsion-trial h_80">
                                            <ul class="list-unstyled d-flex m-0 flex-wrap">
                                                <li>
                                                    NA
                                                </li>
                                            </ul>
                                        </div>
                                        @endif


                                        <div class="poplr-progress">
                                            <ul class="list-unstyled m-0">
                                                <li class="b_btm h_80 ">
                                                    <div class="prgs_br d-flex">
                                                        <progress class="progress-bar"
                                                            value="{{ ($altEaseOfUse ?? 0) * 20 }}" max="100">
                                                            {{ ($altEaseOfUse ?? 0) * 20 }}%
                                                        </progress>
                                                        <output>{{ $altEaseOfUse ?? 0 }}/5</output>
                                                    </div>
                                                </li>
                                                <li class="b_btm h_80 ">
                                                    <div class="prgs_br d-flex">
                                                        <progress class="progress-bar"
                                                            value="{{ ($altCustomerService ?? 0) * 20 }}"
                                                            max="100">
                                                            {{ ($altCustomerService ?? 0) * 20 }}%
                                                        </progress>
                                                        <output>{{ $altCustomerService ?? 0 }}/5</output>
                                                    </div>
                                                </li>
                                                <li class="b_btm h_80 ">
                                                    <div class="prgs_br d-flex">
                                                        <progress class="progress-bar"
                                                            value="{{ ($altExclusiveFeature ?? 0) * 20 }}"
                                                            max="100">
                                                            {{ ($altExclusiveFeature ?? 0) * 20 }}%
                                                        </progress>
                                                        <output>{{ $altExclusiveFeature ?? 0 }}/5</output>
                                                    </div>
                                                </li>
                                                <li class="b_btm h_80 ">
                                                    <div class="prgs_br d-flex">
                                                        <progress class="progress-bar"
                                                            value="{{ ($altValueForMoney ?? 0) * 20 }}"
                                                            max="100">
                                                            {{ ($altValueForMoney ?? 0) * 20 }}%
                                                        </progress>
                                                        <output>{{ $altValueForMoney ?? 0 }}/5</output>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>

                                    </div>
                                    @endif
                                </div>
                            </div>


                        </section>
                    </div>
                </div> --}}


                {{-- top review --}}
                {{-- <div class="edit-box white-box">
                    <div class="edit-icon">
                    </div>
                    <div class="editable-content-3 editable-content-5 mt-15">
                        <section class="review_sec p_50 " id="section6">
                            <div class="container">

                                <div class="review_content"  data-aos-duration="1000">
                                    <h2>Top Reviews</h2>
                                    @if ($topReviews->isNotEmpty())
                                        @foreach ($topReviews as $review)
                                            <div class="review_detl">
                                                <div class="reviw_hd">
                                                    <div class="ans_lft">
                                                        <div class="asn-img">
                                                            @if ($review->user && $review->user->profile_image)
                                                                <img src="{{ asset($review->user->profile_image) }}"
                                                                    class="img-fluid profile-circle"
                                                                    style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                                    alt="User Image">
                                                            @else
                                                                <img src="{{ asset($default_image) }}"
                                                                    class="img-fluid profile-circle"
                                                                    style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                                    alt="Default Image">
                                                            @endif
                                                        </div>
                                                        <div class="asn-rating">
                                                            <h6>
                                                                @if ($review->user && $review->user->user_type === 'admin')
                                                                    {{ $review->public_name ?? 'Public' }}
                                                                @else
                                                                    {{ $review->user->first_name ?? 'Anonymous' }}
                                                                @endif
                                                            </h6>
                                                            <div class="rating light">
                                                                <span
                                                                    class="rating-score size18">{{ number_format($review->rating, 1) }}</span>
                                                                <div class="rating_str">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        @if ($i <= floor($review->rating))
                                                                            <i class="fas fa-star text-warning"></i>
                                                                        @elseif ($i - 0.5 <= $review->rating)
                                                                            <i class="fas fa-star-half-alt text-warning"></i>
                                                                        @else
                                                                            <i class="far fa-star text-warning"></i>
                                                                        @endif
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <p class="m-0">{{ $review->created_at->diffForHumans() }}</p>
                                                </div>
                                                <div class="review_text size18">
                                                    <p class="size22 big-bld">{{ $review->translations->first()->title ?? 'Review' }}
                                                    </p>
                                                    <p>{{ strip_tags($review->translations->first()->description ?? 'No Description Available') }}
                                                    </p>

                                                    {{-- Show The Pros and Cons --}}
                                                    {{-- <div class="pros-cons-box row mt-4">
                                                            <div class="col-md-6">
                                                                <div class="pros-box p-3 border border-success rounded bg-light">
                                                                    <h6 class="text-success mb-2"><i class="fas fa-thumbs-up me-1"></i> Pros</h6>
                                                                    <p class="mb-0">{{ $review->translations->first()->pros }}</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mt-3 mt-md-0">
                                                                <div class="cons-box p-3 border border-danger rounded bg-light">
                                                                    <h6 class="text-danger mb-2"><i class="fas fa-thumbs-down me-1"></i> Cons</h6>
                                                                    <p class="mb-0">{{ $review->translations->first()->cons }}</p>
                                                                </div>
                                                            </div>
                                                    </div> --}}
                                                {{-- </div>
                                            </div>
                                        @endforeach

                                    <div class="btm-bttn light">
                                        <a class="cta cta_white" href="{{ route('ReviewShow', ['locale' => getCurrentLocale(), 'slug' => $business->translations->where('lang_id', getCurrentLanguageID())->first()?->slug]) }}">View All Reviews</a>
                                    </div>
                                    @else
                                    <p>No reviews found for this business.</p>
                                @endif
                                </div>
                            </div>

                        </section>
                    </div>
                </div> --}}


                {{-- worth it --}}
                <div class="edit-box white-box">
                    <div class="edit-icon">
                    </div>
                    <div class="editable-content-3 editable-content-5 mt-15">
                        <section class="worth-it p_50 light" id="section7">
                            <h3>
                                Is Monday.com Worth It?
                            </h3>
                            <p style="color: #666; line-height: 1.6; margin-bottom: 15px;">
                                Monday.com is worth it for teams that need a comprehensive project management solution with strong visual organization capabilities. The platform excels in workflow automation, team collaboration, and customizable dashboards that can adapt to various business needs.
                            </p>
                            <p style="color: #666; line-height: 1.6; margin-bottom: 15px;">
                                However, the value depends on your team size and complexity of projects. Small teams might find it overwhelming, while larger organizations will benefit from its scalability and advanced features. The pricing can be steep for basic users, but the ROI becomes apparent with increased productivity and streamlined processes.
                            </p>
                            <p style="color: #666; line-height: 1.6;">
                                Overall, if you're looking for a robust platform that can grow with your business and don't mind investing in a premium solution, Monday.com is definitely worth considering.
                            </p>
                        </section>
                    </div>
                </div>

                {{-- best for  --}}
                <div class="edit-box white-box">
                    <div class="edit-icon">
                    </div>
                    <div class="editable-content-3 editable-content-5 mt-15">
                        <section class="best-for p_50 light pt-0" id="section8">
                            <h3>
                                Who Is Monday.com Best For?
                            </h3>

                            <div style="margin-bottom: 25px;">
                            <h4>
                                    Perfect for:
                                </h4>
                                <ul style="color: #666; line-height: 1.6;" class="m-0">
                                    <li style="margin-bottom: 8px;">Medium to large teams (10+ members) managing complex projects</li>
                                    <li style="margin-bottom: 8px;">Marketing agencies tracking multiple client campaigns</li>
                                    <li style="margin-bottom: 8px;">Product development teams needing visual roadmaps</li>
                                    <li style="margin-bottom: 8px;">Operations teams requiring workflow automation</li>
                                    <li style="margin-bottom: 8px;">Companies wanting comprehensive reporting and analytics</li>
                                </ul>
                            </div>

                            <div>
                                <h4>
                                    Not ideal for:
                                </h4>
                                <ul style="color: #666; line-height: 1.6; " class="m-0">
                                    <li style="margin-bottom: 8px;">Solo entrepreneurs or very small teams (under 5 people)</li>
                                    <li style="margin-bottom: 8px;">Budget-conscious startups looking for basic task management</li>
                                    <li style="margin-bottom: 8px;">Teams preferring simple, minimalist interfaces</li>
                                    <li style="margin-bottom: 8px;">Organizations with very basic project management needs</li>
                                </ul>
                            </div>
                        </section>
                    </div>
                </div>

                {{--software type --}}
                {{-- Comments This All Section --}}
                {{-- <div class="edit-box blue-edit-box">
                    <div class="edit-icon">

                    </div>
                    <div class="editable-content-3 editable-content-5 mt-15">
                        <section class="software-like p_50 dark " id="section9">
                            <div class="sftwre-like-innr">
                                <div class="sftwre-asana-hd text-center"
                                data-aos-duration="1000">
                                    <h3>Software like {{ $business->translations->firstWhere('lang_id', $lang_id)?->name }}</h3>
                                    <p>Based on other buyer's searches, these are the products that could be a good fit for you.
                                    </p>
                                </div>
                                <div class="sft_ware_test" style="display: flex; justify-content:center; align-items: center;">

                                    <div class="sftware-alternative d-flex"  data-aos-duration="1000">
                                        <div class="sftware-alternative-pck"  data-aos-duration="1000">
                                            <div class="ans_lft p_top_btm_sftwre pt-0">
                                                <div class="asn-img">
                                                    <img src="{{ asset($business->icon_id ?? 'front/img/sftare-img1.svg') }}">
                                                </div>
                                                <div class="asn-rating">
                                                    <h6 class="m-0">{{ $business->translations->firstWhere('lang_id', $lang_id)?->name }}</h6>
                                                </div>
                                            </div>
                                            <div class="overall-rate-sftwre p_top_btm_sftwre">
                                                <h6 class="fw_700">Overall Rating:</h6>
                                                @php
                                                $ratingCount = $business->reviews->where('status', 'active')->count();

                                                @endphp

                                                <div class="tp-btm d-flex flex-col-mob">
                                                    <div class="inn_ul">
                                                        <div class="rating-stars">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <=floor($averageRating))
                                                                <i class="fas fa-star text-warning"></i>
                                                                @elseif ($i - 0.5 <= $averageRating)
                                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                                    @else
                                                                    <i class="far fa-star text-warning"></i>
                                                                    @endif
                                                                    @endfor
                                                        </div>
                                                    </div>
                                                    <div class="rate_box">
                                                        {{ number_format($averageRating, 1) }} | {{ $ratingCount }} ratings
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="over-rate-progress p_top_btm_sftwre">
                                                <div class="ovr-progrs-div d-flex">
                                                    <p class="m-0">Ease of Use</p>
                                                    <div class="prgs_br d-flex align-items-center">
                                                        <progress class="progress-bar" value="{{ $easeOfUseAvg * 20 }}"
                                                            max="100"></progress>
                                                        <output>{{ $easeOfUseAvg }}/5</output>
                                                    </div>
                                                </div>

                                                <div class="ovr-progrs-div d-flex">
                                                    <p class="m-0">Customer Service</p>
                                                    <div class="prgs_br d-flex align-items-center">
                                                        <progress class="progress-bar" value="{{ $customerServiceAvg * 20 }}"
                                                            max="100"></progress>
                                                        <output>{{ $customerServiceAvg }}/5</output>
                                                    </div>
                                                </div>

                                                <div class="ovr-progrs-div d-flex">
                                                    <p class="m-0">Features</p>
                                                    <div class="prgs_br d-flex align-items-center">
                                                        <progress class="progress-bar" value="{{ $exclusiveFeatureAvg * 20 }}"
                                                            max="100"></progress>
                                                        <output>{{ $exclusiveFeatureAvg }}/5</output>
                                                    </div>
                                                </div>

                                                <div class="ovr-progrs-div d-flex">
                                                    <p class="m-0">Value for Money</p>
                                                    <div class="prgs_br d-flex align-items-center">
                                                        <progress class="progress-bar" value="{{ $valueForMoneyAvg * 20 }}"
                                                            max="100"></progress>
                                                        <output>{{ $valueForMoneyAvg }}/5</output>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="start-from p_top_btm_sftwre">
                                                <h6>Starting From:</h6>
                                                <p class="m-0">
                                                    <span>{{ $currency }}{{ $startingPrice }}</span>/{{ $timeUnit }}
                                                </p>
                                            </div>
                                            <div class="pricing-model">
                                                <h6>Pricing Model:</h6>
                                                <span>{{ $additionalInfo }}</span>
                                            </div>
                                        </div>

                                        @foreach ($alternativeBusiness as $altbusiness)
                                        <div class="sftware-alternative-pck"  data-aos-duration="1000">
                                            @php
                                            $price = getBusinessesWithStartingPrice($altbusiness);
                                            // dd($price);
                                            if (!empty($price) && isset($price[0]['starting_price'])) {
                                            $businessprice = $price[0]['starting_price'];
                                            $startingPrice = $businessprice['amount'];
                                            $currency = $businessprice['currency'] ?? '$';
                                            $timeUnit = ucfirst($businessprice['time_unit'] ?? 'month');
                                            $additionalInfo = $businessprice['additional_info'] ?? 'NA';
                                            }

                                            // Get reviews for the current altbusiness
                                            $altReviews = \App\Models\Review::where(
                                            'business_id',
                                            $altbusiness->id,
                                            )->get();

                                            $altEaseOfUseAvg = round($altReviews->avg('ease_of_use_rating'), 1);
                                            $altValueForMoneyAvg = round($altReviews->avg('value_for_money_rating'), 1);
                                            $altCustomerServiceAvg = round(
                                            $altReviews->avg('customer_service_rating'),
                                            1,
                                            );
                                            $altExclusiveFeatureAvg = round(
                                            $altReviews->avg('exclusive_service_rating'),
                                            1,
                                            );

                                            @endphp

                                            <div class="ans_lft p_top_btm_sftwre pt-0">
                                                <div class="asn-img">
                                                    <img src="{{ asset($altbusiness->icon_id ?? 'front/img/top-rate-img2.svg') }}"
                                                        alt="">
                                                </div>
                                                <div class="asn-rating">
                                                    @if ($altbusiness->translations->isNotEmpty())
                                                    <h6 class="m-0">{{ $altbusiness->translations->first()->name }}
                                                    </h6>
                                                    @else
                                                    <h6 class="m-0">Name not available</h6>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="overall-rate-sftwre p_top_btm_sftwre">
                                                <h6 class="fw_700">Overall Rating:</h6>
                                                <div class="tp-btm d-flex flex-col-mob">
                                                    <div class="inn_ul">
                                                        <div class="rating-stars">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <=floor($altbusiness->reviews->avg('rating')))
                                                                <i class="fas fa-star text-warning"></i>
                                                                @elseif ($i - 0.5 <= $altbusiness->reviews->avg('rating'))
                                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                                    @else
                                                                    <i class="far fa-star text-warning"></i>
                                                                    @endif
                                                                    @endfor
                                                        </div>
                                                    </div>
                                                    <div class="rate_box">
                                                        {{ number_format($altbusiness->reviews->avg('rating'), 1) }} |
                                                        {{ $altbusiness->reviews->where('status', 'active')->count() }}
                                                        ratings
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="over-rate-progress p_top_btm_sftwre">
                                                <div class="ovr-progrs-div d-flex">
                                                    <p class="m-0">Ease of Use</p>
                                                    <div class="prgs_br d-flex align-items-center">
                                                        <progress class="progress-bar"
                                                            value="{{ ($altEaseOfUseAvg ?? 0) * 20 }}" max="100">
                                                            {{ ($altEaseOfUseAvg ?? 0) * 20 }}%
                                                        </progress>
                                                        <output>{{ $altEaseOfUseAvg ?? 0 }}/5</output>
                                                    </div>
                                                </div>
                                                <div class="ovr-progrs-div d-flex">
                                                    <p class="m-0">Customer Service</p>
                                                    <div class="prgs_br d-flex align-items-center">
                                                        <progress class="progress-bar"
                                                            value="{{ ($altCustomerServiceAvg ?? 0) * 20 }}" max="100">
                                                            {{ ($altCustomerServiceAvg ?? 0) * 20 }}%
                                                        </progress>
                                                        <output>{{ $altCustomerServiceAvg ?? 0 }}/5</output>
                                                    </div>
                                                </div>
                                                <div class="ovr-progrs-div d-flex">
                                                    <p class="m-0">Features</p>
                                                    <div class="prgs_br d-flex align-items-center">
                                                        <progress class="progress-bar"
                                                            value="{{ ($altExclusiveFeatureAvg ?? 0) * 20 }}"
                                                            max="100">
                                                            {{ ($altExclusiveFeatureAvg ?? 0) * 20 }}%
                                                        </progress>
                                                        <output>{{ $altExclusiveFeatureAvg ?? 0 }}/5</output>
                                                    </div>
                                                </div>
                                                <div class="ovr-progrs-div d-flex">
                                                    <p class="m-0">Value for Money</p>
                                                    <div class="prgs_br d-flex align-items-center">
                                                        <progress class="progress-bar"
                                                            value="{{ ($altValueForMoneyAvg ?? 0) * 20 }}" max="100">
                                                            {{ ($altValueForMoneyAvg ?? 0) * 20 }}%
                                                        </progress>
                                                        <output>{{ $altValueForMoneyAvg ?? 0 }}/5</output>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="start-from p_top_btm_sftwre">
                                                <h6>Starting From:</h6>
                                                <p class="m-0">
                                                    <span>{{ $currency }}{{ $startingPrice }}</span>/{{ $timeUnit }}
                                                </p>
                                            </div>
                                            <div class="pricing-model  p_top_btm_sftwre pt-0">
                                                <h6>Pricing Model:</h6>
                                                <span>{{ $additionalInfo }}</span>
                                            </div>
                                            <div class="sftwre-alt-btn">
                                                <a href="{{ $altbusiness->websites->first()->website_url ?? ($altbusiness->affiliate_link ?? ($altbusiness->permanent_url ?? '#')) }}"
                                                    class="cta cta_orange d-flex align-items-center justify-content-center">
                                                    Visit Website
                                                    <svg width="17" height="12" viewBox="0 0 17 12" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M1.2647 5.84154C7.08595 5.84154 7.42894 5.84154 13.2205 5.84154C13.2304 5.81429 13.2403 5.78704 13.2403 5.75979C13.1017 5.6962 12.9631 5.63262 12.8245 5.55995C10.9435 4.62434 9.91386 3.14372 9.52775 1.25434C9.50795 1.18167 9.59705 1.09084 9.63665 1C9.72575 1.0545 9.86436 1.09992 9.89406 1.18167C10.0525 1.6086 10.1317 2.06278 10.3198 2.47154C11.1712 4.34275 12.6859 5.45095 14.8738 5.78704C15.0124 5.80521 15.1114 5.97779 15.25 6.0868C14.4778 6.32297 13.7947 6.45922 13.1908 6.72265C11.4286 7.49475 10.4089 8.82095 9.99306 10.5559C9.94356 10.7739 9.97326 11.11 9.51785 10.9647C9.87426 8.82095 10.6861 7.79451 13.3096 6.21397C13.0324 6.21397 12.8443 6.21397 12.6562 6.21397C7.20123 6.21397 7.18494 6.21397 1.7201 6.20488C1.5122 6.20488 1.1756 6.32297 1.2647 5.84154Z"
                                                            fill="white" stroke="white" stroke-width="0.8" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>



                                </div>
                            </div>
                            {{-- <div class="sft_btm">
                                <a class="cta"
                                    onclick="changeCategory('{{ $business->category->translation()->first()->slug }}')">View
                                    All
                                    Alternatives</a>
                            </div> --}}

                        {{-- </section>
                    </div>
                </div> --}}


                {{-- compare to competitor --}}
                {{-- <div class="edit-box white-box">
                    <div class="edit-icon">

                    </div>
                    <div class="editable-content-3 editable-content-5 mt-15">
                        <section class="compare-to-competitors p_50 light" id="section10">
                            <h3>
                                How Does Monday.com Compare to Competitors?
                            </h3>

                            <div style="margin-bottom: 30px;">
                                <h4>vs. Asana</h4>
                                <p style="color: #666; line-height: 1.6; margin-bottom: 10px;">
                                    Monday.com offers more visual customization and advanced automation features, while Asana provides a cleaner interface and better free tier. Monday.com is better for complex workflows, Asana for simpler project tracking.
                                </p>
                            </div>

                            <div style="margin-bottom: 30px;">
                                <h4>vs. Trello</h4>
                                <p style="color: #666; line-height: 1.6; margin-bottom: 10px;">
                                    Monday.com is significantly more feature-rich and scalable compared to Trello's simple Kanban approach. Trello wins on simplicity and affordability, while Monday.com excels in advanced project management capabilities and team collaboration tools.
                                </p>
                            </div>

                            <div style="margin-bottom: 30px;">
                                <h4>vs. Notion</h4>
                                <p style="color: #666; line-height: 1.6; margin-bottom: 10px;">
                                    Monday.com focuses specifically on project management with pre-built templates and workflows, while Notion offers more flexibility as an all-in-one workspace. Monday.com is better for structured project management, Notion for customizable knowledge management.
                                </p>
                            </div>

                            <div>
                                <h4> vs. Jira</h4>
                                <p style="color: #666; line-height: 1.6;">
                                    Jira is more technical and suited for software development teams, while Monday.com is more user-friendly and versatile across different industries. Monday.com offers better visual appeal and ease of use, while Jira provides deeper issue tracking and agile development features.
                                </p>
                            </div>
                        </section>
                    </div>
                </div> --}}

                {{-- Integrations & Compatibility --}}
                {{-- <div class="edit-box white-box mb_40">
                    <div class="edit-icon">

                    </div>
                    <div class="editable-content-6 mt-15">
                        <section class="integrations-compatibility p_50 light" id="section12">
                            <h3>Integrations & Compatibility</h3>

                            <p style="color: #666; line-height: 1.6; margin-bottom: 20px;">
                                FlowSync Tech integrates seamlessly with over 100+ platforms and software solutions to streamline your workflow:
                            </p>

                            <ul style="color: #666; line-height: 1.8; margin-left: 20px; flex-wrap: wrap;" class="d-flex glass m-0">
                                <li>Salesforce CRM</li>
                                <li>HubSpot Marketing Hub</li>
                                <li>Microsoft Office 365</li>
                                <li>Google Workspace</li>
                                <li>Slack Team Communication</li>
                                <li>Zoom Video Conferencing</li>
                                <li>Shopify E-commerce</li>
                                <li>QuickBooks Accounting</li>
                                <li>Mailchimp Email Marketing</li>
                                <li>Trello Project Management</li>
                                <li>GitHub Code Repository</li>
                                <li>Adobe Creative Cloud</li>
                                <li>Dropbox File Storage</li>
                                <li>Stripe Payment Processing</li>

                                <li>Zendesk Customer Support</li>
                                <li>Jira Issue Tracking</li>
                                <li>Asana Task Management</li>
                                <li>LinkedIn Sales Navigator</li>
                                <li>Oracle Database Systems</li>
                                <li>SAP Enterprise Software</li>
                                <li>Tableau Data Visualization</li>
                                <li>Power BI Business Intelligence</li>
                                <li>AWS Cloud Services</li>
                                <li>Microsoft Azure</li>
                                <li>Docker Container Platform</li>
                                <li>Jenkins CI/CD Pipeline</li>
                                <li>Kubernetes Orchestration</li>
                                <li>MySQL Database</li>
                                <li>PostgreSQL Database</li>
                            </ul>

                            <p style="color: #666; line-height: 1.6;" class="m-0 pt-2">
                                FlowSync Tech also provides REST API and webhook support for custom integrations, along with native SDKs for popular programming languages including Python, JavaScript, Java, and C#.
                            </p>

                        </section>
                    </div>
                </div> --}}


                {{-- Security & Compliance Section --}}
                {{-- <div class="edit-box white-box mb_40">
                    <div class="edit-icon">

                    </div>
                    <div class="editable-content mt-15">
                        <section class="Security-Compliance  pt-0 light" id="section13">
                            <h3>
                                Security & Compliance
                            </h3>

                            <div style="margin-bottom: 30px;">
                                <h4>
                                    Data Protection & Encryption
                                </h4>
                                <ul class="m-0" style="color: #666; line-height: 1.8;   ">
                                    <li>End-to-end AES-256 encryption for data in transit and at rest</li>
                                    <li>Multi-factor authentication (MFA) required for all user accounts</li>
                                    <li>Regular security audits and penetration testing by third-party firms</li>
                                    <li>Zero-trust security architecture with role-based access controls</li>
                                    <li>Automated backup systems with 99.9% data recovery guarantee</li>
                                    <li>SSL/TLS 1.3 encryption for all web communications</li>
                                    <li>Advanced threat detection and real-time monitoring</li>
                                    <li>Secure API authentication using OAuth 2.0 and JWT tokens</li>
                                </ul>
                            </div>

                            <div style="margin-bottom: 30px;">
                                <h3>
                                    Compliance Certifications
                                </h3>
                                <div class="blade_uppadte" style="display: grid; grid-template-columns: auto auto; gap: 20px;">
                                    <div>
                                    <h4>GDPR Compliance</h4>
                                        <p style="color: #666; line-height: 1.5; font-size: 14px;">
                                            ✅ Fully compliant with EU General Data Protection Regulation<br>
                                            ✅ Data Processing Agreements available<br>
                                            ✅ Right to be forgotten implementation
                                        </p>
                                    </div>

                                    <div>
                                        <h4>CCPA Compliance</h4>
                                        <p style="color: #666; line-height: 1.5; font-size: 14px;">
                                            ✅ California Consumer Privacy Act compliant<br>
                                            ✅ Transparent data collection practices<br>
                                            ✅ Consumer rights management portal
                                        </p>
                                    </div>

                                    <div>
                                        <h4>SOC 2 Type II</h4>
                                        <p style="color: #666; line-height: 1.5; font-size: 14px;">
                                            ✅ Certified for security, availability, and confidentiality<br>
                                            ✅ Annual audits by independent CPA firms<br>
                                            ✅ Reports available upon request
                                        </p>
                                    </div>

                                    <div>
                                        <h4>ISO 27001</h4>
                                        <p style="color: #666; line-height: 1.5; font-size: 14px;">
                                            ✅ Information Security Management certified<br>
                                            ✅ Risk management framework implemented<br>
                                            ✅ Continuous improvement processes
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-bottom: 30px;">
                                <h3>
                                    Additional Security Features
                                </h3>
                                <ul style="color: #666; line-height: 1.8;">
                                    <li>Single Sign-On (SSO) integration with SAML 2.0 and OpenID Connect</li>
                                    <li>IP whitelisting and geolocation restrictions</li>
                                    <li>Detailed audit logs and activity monitoring</li>
                                    <li>Data residency options in US, EU, and APAC regions</li>
                                    <li>24/7 security operations center (SOC) monitoring</li>
                                    <li>Incident response team with <4 hour response time </li>
                                    <li>Regular employee security training and background checks</li>
                                    <li>Bug bounty program with responsible disclosure policy</li>
                                </ul>
                            </div>


                        </section>
                    </div>
                </div> --}}


                {{-- Faq --}}
                <div class="edit-box white-box">
                    <div class="edit-icon">

                    </div>
                    <div class="editable-content mt-15">

                        <section class="faq-section  faq-section_1 p_50 pt-2 light" id="section15">
                            <div class="container">
                                <div class="faq-inner">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="d-flex flex-column w-auto">
                                                <h2>
                                                    Frequently Asked Questions (FAQs)
                                                </h2>
                                                <p>
                                                    Find quick answers to the most common questions about using Localio to discover, filter, and connect with the best local businesses and products.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="faq-accor">
                                                <div class="accordion" id="accordionExample">
                                                    <div class="accordion" id="accordionExample">

                                                        <!-- FAQ 1 -->
                                                        <div class="accordion-item"  data-aos-duration="1000">
                                                            <h2 class="accordion-header" id="headingOne1">
                                                                <button class="accordion-button" type="button"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseOne1"
                                                                    aria-expanded="true"
                                                                    aria-controls="collapseOne1">
                                                                    <span>What is Thread Theory Atelier?</span>
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOne1" class="accordion-collapse collapse show"
                                                                aria-labelledby="headingOne1"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    An independent menswear studio that offers precision tailoring and tech-assisted fitting for a refined, contemporary wardrobe.
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- FAQ 2 -->
                                                        <div class="accordion-item"  data-aos-duration="1000">
                                                            <h2 class="accordion-header" id="headingOne2">
                                                                <button class="accordion-button collapsed" type="button"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseOne2"
                                                                    aria-expanded="false"
                                                                    aria-controls="collapseOne2">
                                                                    <span>Do I need an appointment for a fitting?</span>
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOne2" class="accordion-collapse collapse"
                                                                aria-labelledby="headingOne2"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    Yes, fittings are by appointment only to ensure personalized service and accurate measurements.
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- FAQ 3 -->
                                                        <div class="accordion-item"  data-aos-duration="1000">
                                                            <h2 class="accordion-header" id="headingOne3">
                                                                <button class="accordion-button collapsed" type="button"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseOne3"
                                                                    aria-expanded="false"
                                                                    aria-controls="collapseOne3">
                                                                    <span>What types of garments do you offer?</span>
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOne3" class="accordion-collapse collapse"
                                                                aria-labelledby="headingOne3"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    We specialize in suits, blazers, shirts, trousers, and formalwear — all customizable to fit your style and measurements.
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                </div>
                            </div>
                        </section>
                    </div>
                </div>


                {{-- Breakdown bar --}}
                {{-- <div class="edit-box white-box mb_40">
                    <div class="edit-icon">

                    </div>
                    <div class="editable-content-6 mt-15">

                        <div class="crm-review-innr crm-review-innr_2 " data-aos-duration="1000">
                            <div class="box_crmlft">
                                <div class="crem_flex">
                                    <div class="sales-crm-pack crm-pack-lft">
                                        <div class="inn_sl_hed">
                                            <div class="sli_img choice_img">
                                                <img class="slider_img"
                                                    src="{{ asset($business->icon_id ?? 'front/img/big-asana.png') }}"
                                                    alt="">
                                            </div>
                                            <div class="sl_h">
                                                <div class="inn_h d-flex align-items-center">
                                                    <h6 class="head">{{ $business->translations->firstWhere('lang_id', $lang_id)?->name }}
                                                    </h6>
                                                    <livewire:wishlist :product-id="$business->id" :wire:key="'wishlist-'.$business->id" />
                                                </div>
                                                <div class="tp-btm d-flex flex-col-mob pt-2">
                                                    <div class="inn_ul">
                                                        <div class="rating-stars">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <=floor($averageRating))
                                                                <i class="fas fa-star text-warning"></i>
                                                                @elseif ($i - 0.5 <= $averageRating)
                                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                                    @else
                                                                    <i class="far fa-star text-warning"></i>
                                                                    @endif
                                                                    @endfor
                                                        </div>
                                                    </div>
                                                    <div class="rate_box">
                                                        {{ number_format($averageRating, 1) }} | {{ $ratingCount }}
                                                        ratings
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="crem_flex">
                                    <div class="sales-crm-pack">
                                        <div class="feture_box">
                                            <h6 class="size22 big-bld">Localio Review Breakdown</h6>
                                            <ul class="p-0 m-0">
                                                <li class="d-flex justify-content-between">
                                                    <span class="lyt-text">Ease of Use</span>
                                                    <div class="prgs_br">
                                                        <progress class="progress-bar"
                                                            value="{{ $easeOfUseAvg * 20 }}"
                                                            max="100"></progress>
                                                        <output>{{ $easeOfUseAvg }}/5</output>
                                                    </div>
                                                </li>
                                                <li class="d-flex justify-content-between">
                                                    <span class="lyt-text">Customer Service</span>
                                                    <div class="prgs_br">
                                                        <progress class="progress-bar"
                                                            value="{{ $customerServiceAvg * 20 }}"
                                                            max="100"></progress>
                                                        <output>{{ $customerServiceAvg }}/5</output>
                                                    </div>
                                                </li>
                                                <li class="d-flex justify-content-between">
                                                    <span class="lyt-text">Features</span>
                                                    <div class="prgs_br">
                                                        <progress class="progress-bar"
                                                            value="{{ $exclusiveFeatureAvg * 20 }}"
                                                            max="100"></progress>
                                                        <output>{{ $exclusiveFeatureAvg }}/5</output>
                                                    </div>
                                                </li>
                                                <li class="d-flex justify-content-between">
                                                    <span class="lyt-text">Value for Money</span>
                                                    <div class="prgs_br">
                                                        <progress class="progress-bar"
                                                            value="{{ $valueForMoneyAvg * 20 }}"
                                                            max="100"></progress>
                                                        <output>{{ $valueForMoneyAvg }}/5</output>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>

                                    </div>
                                </div>
                                <div class="crem_flex">
                                    <div class="sales-crm-pack">
                                        <div class="fre_trail feture_box size22">
                                            <div class="grn_check_big">
                                                <img src="{{ asset('front/img/new-grn-chk.png') }}">
                                            </div>
                                            <h6 class="blue-text big-bld">Free Trial <br>
                                                Available
                                            </h6>
                                            <div class="accor-btn p-0">
                                                <a class="cta cta_white">Claim Now</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}

                {{-- review section --}}
                {{-- <div class="edit-box white-box mb_40">

                    <div class="editable-content mt-15">
                        <section class="crm_sec " id="section14">

                            <div class="crm_content"  data-aos-duration="1000">
                                <div class="crm_hd">
                                    <div class="crm_lft">
                                        <h2>Localio {{ $business->translations->firstWhere('lang_id', $lang_id)?->name }} Reviews</h2>
                                    </div>
                                    <div class="crm-ryt">
                                        <div class="ryt-rvw-btn" style="cursor: pointer;">
                                            <a class="cta cta_orange"
                                                @auth
                                                onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }} })"
                                                @else
                                                onclick="window.location.href = '/login'" @endauth>Write
                                                Review</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="crm_review_box review_sec" id="all-reviews">
                                <nav class="d-flex">
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <button class="nav-link cta active" id="nav-home-tab" data-bs-toggle="tab"
                                            data-bs-target="#nav-home" type="button" role="tab"
                                            aria-controls="nav-home" aria-selected="true">All Reviews</button>
                                        @auth
                                        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                            data-bs-target="#nav-profile" type="button" role="tab"
                                            aria-controls="nav-profile" aria-selected="false">Our
                                            Reviews</button>
                                        @endauth
                                        <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab"
                                            data-bs-target="#nav-contact" type="button" role="tab"
                                            aria-controls="nav-contact" aria-selected="false">Trustpilot
                                            Reviews</button>
                                    </div>
                                    <div class="selct_box">
                                        <form method="GET" id="sort-form">
                                            <label for="rating-select">Sort by:</label>
                                            <select id="rating-select" name="sort" onchange="this.form.submit()">
                                                <option value="best" {{ request('sort') == 'best' ? 'selected' : '' }}>Best
                                                    Rating
                                                </option>
                                                <option value="high-to-low"
                                                    {{ request('sort') == 'high-to-low' ? 'selected' : '' }}>High
                                                    to Low</option>
                                                <option value="low-to-high"
                                                    {{ request('sort') == 'low-to-high' ? 'selected' : '' }}>Low
                                                    to High</option>
                                            </select>
                                        </form>
                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade active show" id="nav-home" role="tabpanel"
                                        aria-labelledby="nav-home-tab">
                                        @foreach ($allReviews as $review)
                                        <div class="review_detl populr-alternative"  data-aos-duration="1000">
                                            <div class="reviw_hd">
                                                <div class="ans_lft">
                                                    <div class="asn-img">
                                                        @if ($review->user && $review->user->profile_image)
                                                        <img src="{{ asset($review->user->profile_image) }}"
                                                            class="img-fluid profile-circle"
                                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                            alt="User Image">
                                                        @else
                                                        <img src="{{ asset($default_image) }}"
                                                            class="img-fluid profile-circle"
                                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                            alt="Default Image">
                                                        @endif
                                                    </div>
                                                    <div class="asn-rating">
                                                        <h6>
                                                            @if ($review->user && $review->user->user_type === 'admin')
                                                            {{ $review->public_name ?? 'Public' }}
                                                            @else
                                                            {{ $review->user->first_name ?? 'Anonymous' }}
                                                            @endif
                                                        </h6>
                                                        <div class="rating light">
                                                            <span
                                                                class="rating-text size18">{{ number_format($review->rating, 1) }}</span>
                                                            <div class="rating_str">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    @if ($i <=floor($review->rating))
                                                                    <i class="fas fa-star text-warning"></i>
                                                                    @elseif ($i - 0.5 <= $review->rating)
                                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                                        @else
                                                                        <i class="far fa-star text-warning"></i>
                                                                        @endif
                                                                @endfor
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p>{{ $review->created_at->diffForHumans() }}</p>
                                            </div>
                                            <div class="review_text size18">
                                                <p class="size22 big-bld">
                                                    {{ $review->translations->first()->title ?? '' }}
                                                </p>
                                                <p>{{ strip_tags($review->translations->first()->description ?? 'No Description Available') }}
                                                </p>
                                            </div>
                                        </div>
                                        @endforeach
                                        <div class="btm-bttn light">
                                            @php
                                            $translation = $business->translations->where('lang_id', getCurrentLanguageID())->first();
                                        @endphp

                                        @if ($translation && $translation->slug)
                                            <a class="cta cta_white"
                                               href="{{ route('ReviewShow', ['locale' => getCurrentLocale(), 'slug' => $translation->slug]) }}">
                                                View All Reviews
                                            </a>
                                        @endif
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="nav-profile" role="tabpanel"
                                        aria-labelledby="nav-profile-tab">
                                        @foreach ($ourReviews as $review)
                                        @if ($review->user)
                                        <div class="review_detl"  data-aos-duration="1000">
                                            <div class="reviw_hd">
                                                <div class="ans_lft">
                                                    <div class="asn-img">
                                                        @if ($review->user && $review->user->profile_image)
                                                        <img src="{{ asset($review->user->profile_image) }}"
                                                            class="img-fluid profile-circle"
                                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                            alt="User Image">
                                                        @else
                                                        <img src="{{ asset($default_image) }}"
                                                            class="img-fluid profile-circle"
                                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                            alt="Default Image">
                                                        @endif
                                                    </div>
                                                    <div class="asn-rating">
                                                        <h6>
                                                            @if ($review->user && $review->user->user_type === 'admin')
                                                            {{ $review->public_name ?? 'Public' }}
                                                            @else
                                                            {{ $review->user->first_name ?? 'Anonymous' }}
                                                            @endif
                                                        </h6>
                                                        <div class="rating light">
                                                            <span
                                                                class="rating-score size18">{{ number_format($review->rating, 1) }}</span>
                                                            <div class="rating_str">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    @if ($i <=floor($review->rating))
                                                                    <i class="fas fa-star text-warning"></i>
                                                                    @elseif ($i - 0.5 <= $review->rating)
                                                                        <i
                                                                            class="fas fa-star-half-alt text-warning"></i>
                                                                        @else
                                                                        <i class="far fa-star text-warning"></i>
                                                                        @endif
                                                                        @endfor
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p>{{ $review->created_at->diffForHumans() }}</p>
                                            </div>
                                            <div class="review_text size18">
                                                <p class="size22 big-bld">
                                                    {{ $review->translations->first()->title ?? '' }}
                                                </p>
                                                <p>{{ strip_tags($review->translations->first()->description ?? 'No Description Available') }}
                                                </p>
                                            </div>
                                        </div>
                                        @endif
                                        @endforeach
                                        <div class="btm-bttn light">
                                            @php
                                            $translation = $business->translations->where('lang_id', getCurrentLanguageID())->first();
                                        @endphp

                                        @if ($translation && $translation->slug)
                                            <a class="cta cta_white"
                                               href="{{ route('ReviewShow', ['locale' => getCurrentLocale(), 'slug' => $translation->slug]) }}">
                                                View All Reviews
                                            </a>
                                        @endif
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="nav-contact" role="tabpanel"
                                        aria-labelledby="nav-contact-tab">
                                        @foreach ($trustpilotReviews as $review)
                                        <div class="review_detl"  data-aos-duration="1000">
                                            <div class="reviw_hd">
                                                <div class="ans_lft">
                                                    <div class="asn-img">
                                                        @if ($review->user && $review->user->profile_image)
                                                        <img src="{{ asset($review->user->profile_image) }}"
                                                            class="img-fluid profile-circle"
                                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                            alt="User Image">
                                                        @else
                                                        <img src="{{ asset($default_image) }}"
                                                            class="img-fluid profile-circle"
                                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%;"
                                                            alt="Default Image">
                                                        @endif
                                                    </div>
                                                    <div class="asn-rating">
                                                        <h6>
                                                            @if ($review->user && $review->user->user_type === 'admin')
                                                            {{ $review->public_name ?? 'Public' }}
                                                            @else
                                                            {{ $review->user->first_name ?? 'Anonymous' }}
                                                            @endif
                                                        </h6>
                                                        <div class="rating light">
                                                            <span
                                                                class="rating-text size18">{{ number_format($review->rating, 1) }}</span>
                                                            <div class="rating_str">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    @if ($i <=floor($review->rating))
                                                                    <i class="fas fa-star text-warning"></i>
                                                                    @elseif ($i - 0.5 <= $review->rating)
                                                                        <i class="fas fa-star-half-alt text-warning"></i>
                                                                        @else
                                                                        <i class="far fa-star text-warning"></i>
                                                                        @endif
                                                                        @endfor
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p>{{ $review->created_at->diffForHumans() }}</p>
                                            </div>
                                            <div class="review_text size18">
                                                <p class="size22 big-bld">
                                                    {{ $review->translations->first()->title ?? '' }}
                                                </p>
                                                <p>{{ strip_tags($review->translations->first()->description ?? 'No Description Available') }}
                                                </p>
                                            </div>
                                        </div>
                                        @endforeach
                                        <div class="btm-bttn light">
                                            @php
                                            $translation = $business->translations->where('lang_id', getCurrentLanguageID())->first();
                                        @endphp

                                        @if ($translation && $translation->slug)
                                            <a class="cta cta_white"
                                               href="{{ route('ReviewShow', ['locale' => getCurrentLocale(), 'slug' => $translation->slug]) }}">
                                                View All Reviews
                                            </a>
                                        @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </section>

                    </div>
                </div> --}}

                {{-- new letter --}}
                {{-- <div class="edit-box">
                    <div class="edit-icon">

                    </div>
                    <div class="editable-content-7 mt-15">
                        <section class="subs_sec light p_50" id="section16">
                            <div class="container">
                                <!-- Live as if you were to die tomorrow. Learn as if you were to live forever. - Mahatma Gandhi -->
                                    <div class="subs_content">
                                        <h2 data-aos-duration="1000">Get the Best Picks in Your Inbox</h2>
                                        <p data-aos-duration="1000">Drop your email to receive trusted software
                                            picks, all
                                            recommended by actual users.
                                        </p>
                                        <div class="mail_field" data-aos-duration="1000">
                                            <div class="email_box">
                                                <input type="email" id="email" name="email" placeholder="Email Address*">
                                            </div>
                                            <div class="accor-btn sbs_bttn">
                                                <a href="" class="cta cta_white">Subscribe</a>
                                            </div>
                                        </div>
                                        <div data-aos-duration="1000">
                                            <label>
                                                <input type="checkbox" name="agreement" required>
                                                I agree to receive promotional emails from Localio and accept the
                                                <a href="{{ route('privacy-policy', ['locale' => session('lang_code', 'en-us')]) }}" style="text-decoration: underline;">Privacy Policy</a>
                                                and
                                                <a href="{{ route('terms-condition', ['locale' => session('lang_code', 'en-us')]) }}" style="text-decoration: underline;">Terms and
                                                    Conditions</a>.
                                            </label>
                                        </div>

                                    </div>

                            </div>
                        </section>
                    </div>
                </div> --}}
            </div>


            <!-- Edit Business Modal Pencile-->
            <!-- Modal -->
            <div class="modal fade" id="editBusinessModal" tabindex="-1" aria-labelledby="editBusinessModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog modal-dialog-centered"> <!-- Centered modal -->
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                    <h5 class="modal-title" id="editBusinessModalLabel">Edit Business</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Business Name</label>
                        <input type="text" class="form-control" wire:model.defer="name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Website URL</label>
                        <input type="text" class="form-control" wire:model.defer="website_url">
                        @error('website_url') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Business Image -->
                    {{-- <div class="mb-3">
                        <label class="form-label">Business Image (Logo/Icon)</label>
                        <input type="file" class="form-control" wire:model="business_images">
                        @error('business_images') <span class="text-danger">{{ $message }}</span> @enderror

                        <!-- Preview uploaded image -->
                        @if ($business_images)
                            <div class="mt-2">
                                <img src="{{ $business_images->temporaryUrl() }}"
                                    alt="Preview"
                                    class="img-fluid rounded shadow"
                                    style="max-height: 120px;">
                            </div>
                        @elseif (!empty($current_image))
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $current_image) }}"
                                    alt="Current Image"
                                    class="img-fluid rounded shadow"
                                    style="max-height: 120px;">
                            </div>
                        @endif
                    </div> --}}

<!-- Business Image -->
<div class="mb-3">
    <label class="form-label">Business Image (Logo/Icon)</label>
    <!-- Bind to newImages[] to match Livewire property -->
    <input type="file" class="form-control" wire:model="newImages" multiple>
    @error('newImages') <span class="text-danger">{{ $message }}</span> @enderror

    <!-- Preview uploaded images -->
    @if (!empty($newImages))
        <div class="mt-2 d-flex flex-wrap gap-2">
            @foreach ($newImages as $image)
                <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="img-fluid rounded shadow" style="max-height: 120px;">
            @endforeach
        </div>
    @elseif (!empty($current_image))
        <div class="mt-2">
            <img src="{{ asset('storage/' . $current_image) }}" alt="Current Image" class="img-fluid rounded shadow" style="max-height: 120px;">
        </div>
    @endif
</div>


                </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="saveAllFields('name_and_icon')" data-bs-dismiss="modal">
                        Save changes
                    </button>
                    </div>

                </div>
                </div>
            </div>
            <!-- End Edit Business Modal -->

        <!-- Modal -->
        <!-- Correct Modal for Features/Price/Trial -->
        <div class="modal fade" id="editFeaturesModal" tabindex="-1" aria-labelledby="editFeaturesModalLabel" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="editFeaturesModalLabel">{{ __('Edit Features, Price & Trial') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveFeatureAndPricing('features_price_trial')">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Features') }}</label>
                            <textarea class="form-control" rows="3" wire:model="featuresText"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Starting Price') }}</label>
                            <input type="text" class="form-control" wire:model="startingPrice">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Free Trial Text') }}</label>
                            <input type="text" class="form-control" wire:model="freeTrialText">
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                    </form>
                </div>
            </div>
            </div>
        </div>


        </div>
    </div>
</div>

