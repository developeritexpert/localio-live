@extends('user_layout.master')

@section('content')
@php
    $lang_id = getCurrentLanguageID();
    $catTrans = $business->category->translation ?? null;
    $parentCatTrans = $business->category->parent->translation ?? null;
    $catName = $catTrans->name ?? '';
    $catSlug = $catTrans->slug ?? $business->category->slug ?? null;
    $parentCatName = $parentCatTrans->name ?? '';
    $parentCatSlug = $parentCatTrans->slug ?? $business->category->parent->slug ?? null;
    $bName = $business->translations->first()->name ?? 'Business';
    $stFaqSub = static_text('business_faqs_subheadline');
    if (!empty($stFaqSub) && $stFaqSub !== 'business_faqs_subheadline') {
        $subHeadline = $stFaqSub . ' ' . $bName . '.';
    } else {
        $subHeadline = 'Frequently asked questions and answers about ' . $bName . '.';
    }
@endphp

<!-- Upper Header Section (identical to review details page header) -->
<section class="help-cntr-bnr inr-bnr dark asn_main_sec asn_main_sec_2 user_revew_sec" style="background-color: #fdfdfd; color: #1e3050; padding: 25px 0 35px 0; border-bottom: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Breadcrumb & Social Share Row -->
        <div class=" d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3" style="background-color: #fdfdfd;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb " style="background: transparent; padding: 0; font-size: 13px; margin-bottom:0;">
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
                            <a href="{{ route('product.details', ['locale' => app()->getLocale(), 'slug' => $business->translations->first()->slug ?? '']) }}" style="color: #64748b; text-decoration: none;">{{ $bName }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page" style="color: #1e3050; font-weight: 500;">
                        FAQs
                    </li>
                </ol>
            </nav>
            <div class="inside_sec_text">
                <x-social-icon />
            </div>
        </div>

        <!-- Business Header Row -->
        <div class="row align-items-center justify-content-between">
            <div class="col-md-8 col-12">
                <div class=" top_head d-flex align-items-center gap-3">
                    <!-- Business Icon -->
                    <div class="asn-img" style="width: 55px; height: 55px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.06); flex-shrink: 0; overflow: hidden; border: 1px solid #e2e8f0;">
                        <img src="{{ asset($business->icon_id ?? 'no-image.png') }}" alt="{{ $bName }}" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <div>
                        <div class="an_lkd d-flex align-items-center gap-2 flex-wrap">
                            <h1 style="font-size: 28px; font-weight: 700; color: #1e3050; margin: 0; line-height: 1.2;">
                                {{ $bName }} FAQs
                            </h1>
                            <livewire:wishlist :product-id="$business->id" :wire:key="'wishlist-'.$business->id" />
                        </div>
                        <p style="font-size: 15px; color: #64748b; margin: 4px 0 0 0;">
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

<section class="all_faqs_sec p_120 light" style="background-color: #ffffff !important; padding: 50px 0;">
    <div class="container">

            <!-- FAQs Accordion -->
            <div class="row" data-aos="fade-up" data-aos-duration="1000">
                <div class="col-lg-12">
                    <div class="faq-accor">
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
            </div>
        </div>
    </section>
@endsection
