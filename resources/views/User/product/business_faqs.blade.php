@extends('user_layout.master')

@section('content')
@php
    $lang_id = getCurrentLanguageID();
    $bTranslation = $business->translations->first();
    $catTrans = $business->category->translation ?? null;
    $parentCatTrans = $business->category->parent->translation ?? null;
    $catName = $catTrans->name ?? '';
    $catSlug = $catTrans->slug ?? $business->category->slug ?? null;
    $parentCatName = $parentCatTrans->name ?? '';
    $parentCatSlug = $parentCatTrans->slug ?? $business->category->parent->slug ?? null;
    $bName = $bTranslation->name ?? 'Business';
    $stFaqSub = static_text('business_faqs_subheadline');
    if (!empty($stFaqSub) && $stFaqSub !== 'business_faqs_subheadline') {
        $subHeadline = $stFaqSub . ' ' . $bName . '.';
    } else {
        $subHeadline = 'Frequently asked questions and answers about ' . $bName . '.';
    }

    $faqTitle1 = $bTranslation->faqs_title ?? ('Frequently Asked Questions about ' . $bName);
    $faqDesc1  = $bTranslation->faqs_description ?? '';
    $faqTitle2 = $bTranslation->faqs_title_2 ?? '';
    $faqDesc2  = $bTranslation->faqs_description_2 ?? '';
@endphp

<!-- Upper Header Section -->
<section class="help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 user_revew_sec" style="background-color: #f7f9fb; color: #1e3050;  border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb & Social Share Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3" >
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="background: transparent; padding: 0; font-size: 13px; margin-bottom:0;">
                    @if($parentCatName)
                        <li class="breadcrumb-item">
                            @if($parentCatSlug)
                                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $parentCatSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $parentCatName }}</a>
                            @else
                                <span style="color: #64748b;">{{ $parentCatName }}</span>
                            @endif
                        </li>
                    @endif
                    @if($catName)
                        <li class="breadcrumb-item">
                            @if($catSlug)
                                <a href="{{ route('category.detail', ['locale' => app()->getLocale(), 'slug' => $catSlug]) }}" style="color: #64748b; text-decoration: none;">{{ $catName }}</a>
                            @else
                                <span style="color: #64748b;">{{ $catName }}</span>
                            @endif
                        </li>
                    @endif
                    @if($business)
                        <li class="breadcrumb-item">
                            <a href="{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $bTranslation->slug ?? '']) }}" style="color: #64748b; text-decoration: none;">{{ $bName }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page" style="color: #1e3050; font-weight: 500;">
                        FAQs
                    </li>
                </ol>
            </nav>
            <div class="inside_sec_text">
                <x-social-icon :business="$business" />
            </div>
        </div>

        <!-- Business Header Row -->
        <div class="row align-items-center justify-content-between">
            <div class="col-md-8 col-12">
                <div class="top_head d-flex align-items-center gap-2">
                    <!-- Business Icon -->
                    <div class="asn-img" style="width: 55px; height: 55px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.06); flex-shrink: 0; overflow: hidden; border: 1px solid #e2e8f0;">
                        <img src="{{ asset($business->icon_id ?? 'no-image.png') }}" alt="{{ $bName }}" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <div>
                        <div class="an_lkd d-flex align-items-center gap-2 flex-wrap">
                            <h1 style="font-size: 28px; font-weight: 700;  margin: 0; line-height: 1;">
                                {{ $bName }} FAQs
                            </h1>
                            <livewire:wishlist :product-id="$business->id" :wire:key="'wishlist-'.$business->id" />
                        </div>
                        <p style="font-size: 16px; color: #444; margin: 0; font-weight:400;">
                            {{ $subHeadline }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-12 text-md-end text-start mt-md-0 mt-3">
                <a href="{{ $business->getTrackedUrl() }}" target="_blank" class="btn" style="background-color: #ff5722; color: #ffffff; font-weight: 600; font-size: 15px; padding: 12px 28px; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none" onmouseover="this.style.backgroundColor='#e64a19';" onmouseout="this.style.backgroundColor='#ff5722';">
                    Visit website <i class="fas fa-external-link-alt" style="font-size: 13px;"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Content & Right Sidebar Section -->
<section class="all_faqs_sec py-5 common_detail_sec" style="background-color: #ffffff !important;">
    <div class="container">
        <div class="row g-4">
            <!-- Left Side: FAQs Title 1 & Description 1, Title 2 & Description 2, and FAQs Accordion -->
            <div class="col-lg-8 col-12">
                <!-- Section 1: Title 1 & Description 1 -->
                @if($faqTitle1)
                    <div class="mb-4">
                        <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin-bottom: 12px;">
                            {{ $faqTitle1 }}
                        </h2>
                        @if($faqDesc1)
                            <div class="content_box mb-4" style="font-size: 15px; color: #4a5568; line-height: 1.6;">
                                {!! $faqDesc1 !!}
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Section 2: Title 2 & Description 2 -->
                @if($faqTitle2 || $faqDesc2)
                    <div class="mb-4">
                        @if($faqTitle2)
                            <h2 style="font-size: 24px; font-weight: 700; color: #002347; margin-bottom: 12px;">
                                {{ $faqTitle2 }}
                            </h2>
                        @endif
                        @if($faqDesc2)
                            <div class="content_box mb-4" style="font-size: 15px; color: #4a5568; line-height: 1.6;">
                                {!! $faqDesc2 !!}
                            </div>
                        @endif
                    </div>
                @endif

                <!-- FAQs Accordion -->
                <div class="faq-accor mt-4" style="width:100%;">
                    <div class="accordion" id="businessFaqAccordion">
                        @forelse ($business->faqs as $index => $faq)
                            @php $translation = $faq->translations->first(); @endphp
                            @if ($translation)
                                <div class="accordion-item mb-3 border rounded-3 bg-white" style="border-radius: 8px !important; overflow: hidden; border: 1px solid #e2e8f0 !important;">
                                    <h2 class="accordion-header" id="headingFaq{{ $index }}">
                                        <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseFaq{{ $index }}"
                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                aria-controls="collapseFaq{{ $index }}"
                                                style="font-weight: 600; color: #002347; font-size: 16px; padding: 16px 20px;">
                                            <span>{{ $translation->question }}</span>
                                        </button>
                                    </h2>
                                    <div id="collapseFaq{{ $index }}"
                                         class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                         aria-labelledby="headingFaq{{ $index }}"
                                         data-bs-parent="#businessFaqAccordion">
                                        <div class="accordion-body" style="font-size: 15px; color: #444; line-height: 1.6; padding: 20px;">
                                            {{ $translation->answer }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="p-4 text-center text-muted bg-white rounded border">
                                No FAQs available for {{ $bName }} at this time.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Side: Sidebar Widgets matching Product Details Page -->
            <div class="col-lg-4 col-12 d-flex flex-column gap-4">
                <!-- 1. USPs List Widget -->
                @if($business->usps && $business->usps->count() > 0)
                    <div class="p-4 bg-white rounded-3 border" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 14px; color: #2d3748;">
                            @foreach($business->usps as $usp)
                                @php $uText = $usp->text ?? $usp->usp_text ?? ''; @endphp
                                @if(!empty($uText))
                                    <li class="d-flex align-items-center gap-2" style="font-size: 16px; color: #000; font-weight:500;">
                                        <i class="fas fa-check text-success" style="font-size: 16px; "></i>
                                        <span>{{ $uText }}</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- 2. Rating Breakdown Widget -->
                <div class="p-4 bg-white rounded-3 border" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <div class="d-flex justify-content-between align-items-start mb-3 pb-3" style="border-bottom: 1px solid #f0f0f0;">
                        <div>
                            <div style="font-size: 42px; font-weight: 700; color: #002347; line-height: 1;">
                                {{ number_format($averageRating, 1) }}
                            </div>
                            <div style="margin-top: 8px; margin-bottom: 4px; display: flex; gap: 4px;">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($averageRating))
                                        <i class="fas fa-star text-warning" style="font-size: 18px;"></i>
                                    @elseif ($i - 0.5 <= $averageRating)
                                        <i class="fas fa-star-half-alt text-warning" style="font-size: 18px;"></i>
                                    @else
                                        <i class="far fa-star text-warning" style="font-size: 18px;"></i>
                                    @endif
                                @endfor
                            </div>
                            <div style="color: #718096; font-size: 14px;">{{ number_format($totalReviews) }} {{ $totalReviews == 1 ? 'review' : 'reviews' }}</div>
                        </div>
                        <a href="{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $bTranslation->slug ?? '']) }}#section14" class="view-review-link" style="color: #06498b; font-weight: 600; font-size: 14px; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                            View all reviews
                        </a>
                    </div>

                    @if(isset($criteria) && count($criteria) > 0)
                        <h6 style="font-size: 14px; font-weight: 600; color: #002347; margin-bottom: 12px;">Review breakdown</h6>
                        <div class="mb-3">
                            @foreach ($criteria as $criterion)
                                <div class="ovr-progrs-div d-flex align-items-center justify-content-between mb-2">
                                    <p class="m-0" style="font-size: 12px; font-weight: 500; color: #444;">{{ $criterion->name }}</p>
                                    <div class="prgs_br d-flex align-items-center" style="flex: 1; max-width: 60%; justify-content: flex-end;">
                                        <progress class="progress-bar w-100" value="{{ $criterion->average_rating * 20 }}" max="100" style="height: 8px;"></progress>
                                        <span style="font-size: 12px; font-weight: 600; color: #444; margin-left: 8px; min-width: 35px; text-align: right;">{{ number_format($criterion->average_rating, 1) }}/5</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center pt-2 mt-2" style="border-top: 1px solid #f0f0f0;">
                        <span style="font-weight: 600; color: #002347; font-size: 14px;">Recommended by users</span>
                        <strong style="color: #002347; font-size: 14px;">{{ $recommendPercent }}%</strong>
                    </div>

                    <div class="do-you-recommend mt-3 pt-3" style="border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 600; color: #1e3050; font-size: 14px;">Do you recommend {{ $bName }}?</span>
                        <div style="display: flex; gap: 8px;">
                            @auth
                                <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 28px; height: 28px; border-radius: 50%; background-color: #174889; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#ff5722';" onmouseout="this.style.backgroundColor='#174889';">
                                    <i class="fas fa-thumbs-up" style="font-size: 12px;"></i>
                                </a>
                                <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 28px; height: 28px; border-radius: 50%; background-color: #174889; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#ff5722';" onmouseout="this.style.backgroundColor='#174889';">
                                    <i class="fas fa-thumbs-down" style="font-size: 12px;"></i>
                                </a>
                            @else
                                <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: true })" style="width: 28px; height: 28px; border-radius: 50%; background-color: #174889; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#ff5722';" onmouseout="this.style.backgroundColor='#174889';">
                                    <i class="fas fa-thumbs-up" style="font-size: 12px;"></i>
                                </a>
                                <a href="javascript:void(0)" onclick="Livewire.dispatch('openReviewModal', { businessId: {{ $business->id }}, recommend: false })" style="width: 28px; height: 28px; border-radius: 50%; background-color: #174889; color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;" onmouseover="this.style.backgroundColor='#ff5722';" onmouseout="this.style.backgroundColor='#174889';">
                                    <i class="fas fa-thumbs-down" style="font-size: 12px;"></i>
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- 3. Pricing & Free Trial Card -->
                @if(!is_null($startingPrice))
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-3 bg-white rounded-3 border text-center h-100 d-flex flex-column justify-content-between" style="border-radius: 12px !important; border: 1px solid #e2e8f0 !important;">
                                <div style="font-size: 16px; color: #002347; font-weight: 600;">Starting price</div>
                                <div class="my-2" style="font-size: 26px; font-weight: 700; color: #002347;">{{ $currency }}{{ $startingPrice }}</div>
                                <a href="{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $bTranslation->slug ?? '']) }}#section6" style="font-size: 15px; color: #002347; font-weight: 600; text-decoration: none;" class="underline">View pricing</a>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-white rounded-3 border text-center h-100 d-flex flex-column justify-content-between" style="border-radius: 12px !important; border: 1px solid #e2e8f0 !important;">
                                <div class="mx-auto my-1" style="width: 32px; height: 32px; border-radius: 50%; background: #06498b; color: #fff; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-check" style="font-size: 14px;"></i>
                                </div>
                                <div style="font-size: 12.5px; font-weight: 700; color: #1e3050;">Free Trial Available</div>
                                <a href="{{ $business->getTrackedUrl() }}" target="_blank" class="btn btn-sm text-white w-100 mt-1" style="background-color: #174889; border-radius: 20px; font-weight: 600; font-size: 12px; transition:unset !important" onmouseover="this.style.backgroundColor='#ff5722';" onmouseout="this.style.backgroundColor='#174889';">Claim Now</a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 4. Highlighted Reviews Widget -->
                @if(isset($topReviews) && $topReviews->count() > 0)
                    <div class="p-4 bg-white rounded-3 border" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 1px solid #f0f0f0;">
                            <h6 style="font-size: 14px; font-weight: 700; color: #002347; margin: 0;">Highlighted reviews</h6>
                            <a href="{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $bTranslation->slug ?? '']) }}#section14" style="color: #06498b; font-weight: 600; font-size: 13px; text-decoration: none;" class="underline">View all reviews</a>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            @foreach($topReviews as $rev)
                                @php 
                                    $revTrans = $rev->translations->first(); 
                                    $u = $rev->user;
                                    if ($u && $u->user_type === 'admin') {
                                        $displayName = $rev->public_name ?? 'Public';
                                        $initial = strtoupper(substr($displayName, 0, 1));
                                    } elseif ($u) {
                                        $displayName = $u->displayName();
                                        $initial = strtoupper(substr($u->first_name ?? $u->name ?? 'A', 0, 1));
                                    } else {
                                        $displayName = 'Anonymous';
                                        $initial = 'A';
                                    }
                                @endphp
                                <div class="pb-3" style="border-bottom: 1px solid #f1f5f9;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-start gap-2">
                                            <div style="width: 38px; height: 38px; border-radius: 50%; background: #002347; color: #fff; font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                {{ $initial }}
                                            </div>
                                            <div>
                                                <div style="font-size: 14px; font-weight: 700; color: #002347; line-height: 1.2;">{{ $displayName }}</div>
                                                @if(!empty($u->job_title))
                                                    <div style="font-size: 12px; color: #718096; line-height: 1.3;">{{ $u->job_title }}</div>
                                                @endif
                                                @if(!empty($u->industry))
                                                    <div style="font-size: 12px; color: #718096; line-height: 1.3;">{{ $u->industry }}</div>
                                                @endif
                                                @if(!empty($u->company_size))
                                                    @php $compSizeText = static_text('company_size_' . $u->company_size); @endphp
                                                    <div style="font-size: 12px; color: #718096; line-height: 1.3;">{{ (!empty($compSizeText) && $compSizeText !== 'company_size_' . $u->company_size) ? $compSizeText : $u->company_size }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <small class="text-muted" style="font-size: 11px; white-space: nowrap;">{{ $rev->created_at ? $rev->created_at->diffForHumans() : '' }}</small>
                                    </div>
                                    @if($revTrans && !empty($revTrans->title))
                                        <h6 style="font-size: 14px; font-weight: 700; color: #002347; margin: 10px 0 6px 0;">{{ $revTrans->title }}</h6>
                                    @endif
                                    <div class="d-flex gap-1 mb-2">
                                        @for($s=1; $s<=5; $s++)
                                            <i class="fas fa-star {{ $s <= $rev->rating ? 'text-warning' : 'text-muted' }}" style="font-size: 12px;"></i>
                                        @endfor
                                    </div>
                                    @if($revTrans && !empty($revTrans->description))
                                        <p style="font-size: 13px; color: #4a5568; margin: 0; line-height: 1.4;">{{ Str::limit(strip_tags($revTrans->description), 120) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 5. Recent Discussions Widget -->
                <div class="p-4 bg-white rounded-3 border" style="border-radius: 14px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 1px solid #f0f0f0;">
                        <h6 style="font-size: 14px; font-weight: 700; color: #002347; margin: 0;">Recent discussions</h6>
                        <a href="{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $bTranslation->slug ?? '']) }}#sectionDiscussions" style="color: #06498b; font-weight: 600; font-size: 13px; text-decoration: none;" class="underline">View all discussions</a>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div class="pb-3" style="border-bottom: 1px solid #f1f5f9;">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #002347; color: #fff; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center;">M</div>
                                    <div>
                                        <div style="font-size: 13px; font-weight: 600; color: #1e3050;">Marc L.</div>
                                        <div style="font-size: 11px; color: #718096;">Product Manager • Small Business (1-50 emp.)</div>
                                    </div>
                                </div>
                                <span style="font-size: 11px; color: #a0aec0;">2 hours ago</span>
                            </div>
                            <h6 style="font-size: 13px; font-weight: 600; color: #1e3050; margin: 8px 0 4px 0;">Is there a free tier for API access or is it trial only?</h6>
                            <p style="font-size: 12.5px; color: #4a5568; margin: 0; line-height: 1.4;">We are looking to integrate this into our workflow and want to test the latency over a few weeks...</p>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #002347; color: #fff; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center;">S</div>
                                    <div>
                                        <div style="font-size: 13px; font-weight: 600; color: #1e3050;">Sarah J.</div>
                                        <div style="font-size: 11px; color: #718096;">CTO • Mid-Market (51-1000 emp.)</div>
                                    </div>
                                </div>
                                <span style="font-size: 11px; color: #a0aec0;">1 day ago</span>
                            </div>
                            <h6 style="font-size: 13px; font-weight: 600; color: #1e3050; margin: 8px 0 4px 0;">How does the performance compare to alternatives in large datasets?</h6>
                            <p style="font-size: 12.5px; color: #4a5568; margin: 0; line-height: 1.4;">We noticed some latency spikes during queries with more than 10k items. Anyone else facing this?</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@livewire('add-review')

@if(auth()->check() && session()->has('pending_review_business_id'))
    @php
        $pendingBusId = session('pending_review_business_id');
        $pendingRec = session('pending_review_recommend');
        session()->forget(['pending_review_business_id', 'pending_review_recommend']);
    @endphp
    <script>
        function triggerPendingReviewModal() {
            if (window.Livewire) {
                if (typeof Livewire.dispatch === 'function') {
                    Livewire.dispatch('openReviewModal', { businessId: {{ $pendingBusId }}, recommend: {{ json_encode($pendingRec) }} });
                } else if (typeof Livewire.emit === 'function') {
                    Livewire.emit('openReviewModal', {{ $pendingBusId }}, {{ json_encode($pendingRec) }});
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(triggerPendingReviewModal, 300);
        });
        document.addEventListener('livewire:load', function() {
            setTimeout(triggerPendingReviewModal, 300);
        });
    </script>
@endif
@endsection
