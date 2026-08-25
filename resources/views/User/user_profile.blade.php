@extends('user_layout.master')
@section('content')
<style>
    body { background-color: #f9f9f9; }
    .profile-header { background: #fff; padding: 40px 0; text-align: center; border-bottom: 1px solid #eaeaea; }
    .profile-avatar-large { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; background: #003f7d; color: #fdfdfd; display: inline-flex; align-items: center; justify-content: center; font-size: 36px; font-weight: bold; margin-bottom: 16px; }
    .profile-name { font-size: 28px; font-weight: 700; color: #1e3050; margin-bottom: 8px; }
    .profile-meta { font-size: 14px; color: #64748b; margin-bottom: 4px; }
    
    .reviews-section { max-width: 800px; margin: 40px auto; padding: 0 20px; }
    .reviews-title { font-size: 24px; font-weight: 700; color: #1e3050; margin-bottom: 24px; }
    
    .review-business-link { font-size: 14px; color: #64748b; margin-bottom: 8px; display: block; }
    .review-business-link a { color: #007bff; text-decoration: none; font-weight: 500; }
    .review-business-link a:hover { text-decoration: underline; }
    
    .review-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; margin-bottom: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
    .review-card-header { display: flex; align-items: center; margin-bottom: 16px; }
    .review-avatar-small { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background: #ffe4e6; color: #1e3050; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; font-weight: bold; margin-right: 12px; }
    
    .review-user-name { font-size: 14px; font-weight: 700; color: #1e3050; margin: 0; }
    .review-user-meta { font-size: 12px; color: #64748b; margin: 0; }
    
    .review-rating-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    .review-stars { color: #00b67a; font-size: 16px; } /* Trustpilot style stars color, or use site yellow #ffb829 */
    .review-date { font-size: 13px; color: #64748b; }
    
    .review-title { font-size: 18px; font-weight: 700; color: #1e3050; margin-bottom: 8px; }
    .review-content { font-size: 15px; color: #333; line-height: 1.5; margin-bottom: 16px; }
    
    .review-tags { display: flex; gap: 8px; }
    .review-tag { background: #f1f1f1; color: #555; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 500; }

    .action-icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; color: #002347; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .action-icon-btn:hover { background-color: #f1f5f9; color: #002347; }
</style>

<!-- Profile Header -->
<div class="profile-header " style="margin-top: 8rem !important;">
    <div class="container">
        @if($user->profile_image)
            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->displayName() }}" class="profile-avatar-large">
        @else
            <div class="profile-avatar-large">
                {{ strtoupper(substr($user->displayName(), 0, 1)) }}
            </div>
        @endif
        
        <h1 class="profile-name">{{ $user->displayName() }}</h1>
        
        @if($badge)
            <div class="user-contributor-badge mb-2" style="font-size: 15px; color: #1e3050; font-weight: 600;">
                {!! $badge->icon !!} {{ $badge->name }}
            </div>
        @endif
        
        <div class="profile-meta">
            @if($user->country)
                {{ $user->country->name ?? 'United States' }} &bull; 
            @endif
            {{ $reviews->total() }} reviews
        </div>
        
        @if($user->job_title || $user->industry)
            <div class="profile-meta mt-2">
                {{ $user->job_title }} {{ $user->industry ? ' at ' . $user->industry : '' }}
            </div>
        @endif
    </div>
</div>

<!-- Reviews Section -->
<div class="reviews-section">
    <h2 class="reviews-title">Reviews</h2>
    
    @forelse($reviews as $review)
        <div class="review-business-link" style="margin-bottom: 8px;">
            <span style="color: #64748b; font-size: 14px;">Review of</span> 
            <a href="{{ route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $review->business->translations->first()->slug ?? $review->business->slug]) }}" style="font-weight: 600; color: #002347; text-decoration: none;">
                {{ $review->business->translations->first()->name ?? $review->business->name }}
            </a>
        </div>
        
        <div class="review-card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
            <!-- Card Header -->
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-start">
                    <!-- Avatar -->
                    @if($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->displayName() }}" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; margin-right: 16px;">
                    @else
                        <div style="width: 48px; height: 48px; border-radius: 50%; background-color: #002347; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                            <span style="color: white; font-weight: bold; font-size: 20px;">
                                {{ strtoupper(substr($user->displayName(), 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    
                    <!-- Name and Meta -->
                    <div>
                        <h6 style="margin: 0; font-size: 16px; font-weight: 600; color: #002347;">{{ $user->displayName() }}</h6>
                        @if($user->job_title)
                            <p style="margin: 2px 0 0 0; font-size: 13px; color: #64748b;">{{ $user->job_title }}</p>
                        @endif
                        @if($user->industry)
                            <p style="margin: 0; font-size: 13px; color: #64748b;">{{ $user->industry }}</p>
                        @endif
                    </div>
                </div>
                
                <!-- Date -->
                <div style="font-size: 13px; color: #64748b;">
                    {{ $review->created_at->diffForHumans() }}
                </div>
            </div>
            
            <!-- Title -->
            <h5 style="font-size: 18px; font-weight: 600; color: #002347; margin-bottom: 8px; margin-top: 16px;">{{ $review->title ?? $review->translations->first()->title ?? '' }}</h5>
            
            <!-- Stars -->
            <div style="color: #ffb829; font-size: 14px; margin-bottom: 12px;">
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= floor($review->rating))
                        <i class="fas fa-star"></i>
                    @elseif ($i - 0.5 <= $review->rating)
                        <i class="fas fa-star-half-alt"></i>
                    @else
                        <i class="far fa-star"></i>
                    @endif
                @endfor
            </div>
            
            <!-- Content -->
            <p style="font-size: 15px; color: #4a5568; line-height: 1.6; margin-bottom: 24px;">{{ strip_tags($review->review ?? $review->translations->first()->description ?? 'No Description Available') }}</p>
            
            <!-- Bottom Actions -->
            <div class="review-actions-bottom-right" style="display:flex; justify-content: end; align-items: center; gap: 6px;">
                <a href="mailto:support@example.com?subject=Report Review ID: {{ $review->id }}" class="action-icon-btn" title="Report this review">
                    <i class="fas fa-flag" style="font-size: 13px;"></i>
                </a>
                <button type="button" class="action-icon-btn" style="background: transparent; border: none; padding: 0; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; color: #002347; cursor: pointer; outline: none; box-shadow: none; appearance: none; -webkit-appearance: none;" onclick="window.openShareModal('{{ url()->current() }}#review-{{ $review->id }}', 'Review on {{ config('app.name') }}', event)" title="Share this review" aria-label="Share this review">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="18" cy="5" r="3"></circle>
                        <circle cx="6" cy="12" r="3"></circle>
                        <circle cx="18" cy="19" r="3"></circle>
                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                    </svg>
                </button>
            </div>
        </div>
    @empty
        <div class="alert alert-light text-center">
            This user hasn't published any reviews yet.
        </div>
    @endforelse

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $reviews->links() }}
    </div>
</div>
@endsection
