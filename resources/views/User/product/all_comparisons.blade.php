@extends('user_layout.master')

@section('content')
    <section class="banner_sec help-cntr-bnr inr-bnr dark lg_Bnr" style="background-color: #003F7D;">
        <div class="bubble-wrp">
            <img src="{{ asset('front/img/small-bnnr-bg.png') ?? '' }}" alt="">
        </div>
    </section>

    @php
        $bName = $business->translations->first()->name ?? 'Business';
        $catTrans = $business->category->translation ?? null;
        $catName = $catTrans->name ?? 'providers';
        $compSlug = $catTrans->comparison_slug ?? 'compare';
    @endphp

    <section class="all_comparisons_sec p_120 light" style="background-color: #f9fafb !important;">
        <div class="container">
            <div class="hd_text mb-4" data-aos="fade-up" data-aos-duration="1000">
                <h2 style="font-size: 32px; font-weight: 700; color: #1e3050; margin-bottom: 8px;">
                    Compare {{ $bName }}
                </h2>
                <p style="font-size: 16px; color: #64748b; margin: 0;">
                    See how {{ $bName }} compares to other {{ $catName }} providers.
                </p>
            </div>

            <div class="row g-4" data-aos="fade-up" data-aos-duration="1000">
                @forelse($peerComparisons as $peer)
                    @php
                        $peerName = $peer->translations->first()->name ?? 'Business';
                        $peerRating = $peer->average_rating ?? 0;
                        $seoUrl = route('product-comparison.seo', [
                            'locale' => app()->getLocale(),
                            'comparison_slug' => $compSlug,
                            'comparison_businesses' => Str::slug($bName) . '-vs-' . Str::slug($peerName)
                        ]);
                    @endphp
                    <div class="col-lg-6 col-md-6 col-12">
                        <a href="{{ $seoUrl }}" class="comparison-card-link text-decoration-none" style="display: block; color: inherit;">
                            <div class="comparison-box p-3 bg-white rounded-3 border" style="border-radius: 12px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 2px 4px rgba(0,0,0,0.03); transition: all 0.2s ease;" onmouseover="this.style.boxShadow='0 6px 12px rgba(0,0,0,0.08)'; this.style.borderColor='#cbd5e1';" onmouseout="this.style.boxShadow='0 2px 4px rgba(0,0,0,0.03)'; this.style.borderColor='#e2e8f0';">
                                <div class="d-flex align-items-center justify-content-between">
                                    <!-- Business A -->
                                    <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                                        <img src="{{ asset($business->icon_id) }}" alt="{{ $bName }}" class="rounded-circle flex-shrink-0" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div style="min-width: 0;">
                                            <div class="fw-semibold text-dark text-truncate" style="font-size: 15px; color: #1e293b !important;">{{ $bName }}</div>
                                            <div class="d-flex align-items-center gap-1" style="font-size: 13px; color: #64748b;">
                                                <i class="fas fa-star text-warning" style="font-size: 12px;"></i>
                                                <span>{{ number_format($businessRating, 1) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- VS -->
                                    <div class="px-3 fw-normal text-muted flex-shrink-0" style="font-size: 24px; font-family: sans-serif; color: #000000 !important;">VS</div>

                                    <!-- Business B -->
                                    <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                                        <img src="{{ asset($peer->icon_id) }}" alt="{{ $peerName }}" class="rounded-circle flex-shrink-0" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div style="min-width: 0;">
                                            <div class="fw-semibold text-dark text-truncate" style="font-size: 15px; color: #1e293b !important;">{{ $peerName }}</div>
                                            <div class="d-flex align-items-center gap-1" style="font-size: 13px; color: #64748b;">
                                                <i class="fas fa-star text-warning" style="font-size: 12px;"></i>
                                                <span>{{ number_format($peerRating, 1) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-muted">No comparisons found.</div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-5">
                {{ $peerComparisons->links() }}
            </div>
        </div>
    </section>
@endsection
