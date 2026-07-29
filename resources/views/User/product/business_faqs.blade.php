@extends('user_layout.master')

@section('content')
    <section class="banner_sec help-cntr-bnr inr-bnr dark lg_Bnr" style="background-color: #003F7D;">
        <div class="bubble-wrp">
            <img src="{{ asset('front/img/small-bnnr-bg.png') ?? '' }}" alt="">
        </div>
    </section>

    @php
        $bName = $business->translations->first()->name ?? 'Business';
        $bSlug = $business->translations->first()->slug ?? '';
        $detailUrl = route('product.details', ['locale' => app()->getLocale(), 'slug' => $bSlug]);
    @endphp

    <section class="all_faqs_sec p_120 light" style="background-color: #f9fafb !important; padding: 50px 0;">
        <div class="container">
            <!-- Breadcrumbs / Back Link -->
            <div class="mb-4">
                <a href="{{ $detailUrl }}" class="text-decoration-none text-muted fw-semibold" style="font-size: 14px;">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to {{ $bName }}
                </a>
            </div>

            <!-- Page Header -->
            <div class="hd_text mb-4 d-flex align-items-center gap-3 flex-wrap" data-aos="fade-up" data-aos-duration="1000">
                <img src="{{ asset($business->icon_id ?? 'front/img/sftare-img1.svg') }}" alt="{{ $bName }}"
                     class="rounded" style="width: 54px; height: 54px; object-fit: cover;">
                <div>
                    <h1 style="font-size: 28px; font-weight: 700; color: #1e3050; margin-bottom: 4px;">
                        {{ $bName }} FAQs
                    </h1>
                    <p style="font-size: 15px; color: #64748b; margin: 0;">
                        Frequently asked questions and answers about {{ $bName }}.
                    </p>
                </div>
            </div>

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
