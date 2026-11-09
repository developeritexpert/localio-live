@props(['business' => null])

@php
    $business = $business ?? ($page ?? null);
    $shareUrl = request()->url();
    $shareTitle = isset($business) && isset($business->name) ? $business->name : (isset($page) ? $page->title : config('app.name'));
    $isInWishlist = false;
    if (isset($business) && isset($business->id) && auth()->check()) {
        $isInWishlist = auth()->user()->wishlists()->where('business_id', $business->id)->exists();
    }
@endphp

<div class="inside_sec_text inside_sec_text_2">
    <div class="sharing_icons" style="display: flex; align-items: center; gap: 8px;">
        @if(isset($business) && isset($business->id))
            <div class="sharing_ul social_wishlist_btn">
                <div class="wishlist">
                    <a href="javascript:void(0)"
                       class="business-wishlist-toggle-btn"
                       data-business-id="{{ $business->id }}"
                       onclick="toggleBusinessWishlistGlobal({{ $business->id }}, this)"
                       aria-label="Save to wishlist"
                       title="Save to wishlist">
                        @if ($isInWishlist)
                            <i class="fas fa-heart text-red-600"></i>
                        @else
                            <i class="far fa-heart text-gray-600"></i>
                        @endif
                    </a>
                </div>
            </div>
        @endif
        <div class="sharing_ul">
            <button type="button" class="action-icon-btn share-trigger-btn" aria-label="Share this" title="Share this"
                style="background: transparent; border: none; padding: 0; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; color: #94a3b8; cursor: pointer; outline: none; box-shadow: none; appearance: none; -webkit-appearance: none;"
                onclick="window.openShareModal('{{ $shareUrl }}', '{{ addslashes($shareTitle) }}', event)">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="18" cy="5" r="3"></circle>
                    <circle cx="6" cy="12" r="3"></circle>
                    <circle cx="18" cy="19" r="3"></circle>
                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                </svg>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    if (typeof window.toggleBusinessWishlistGlobal === 'undefined') {
        window.toggleBusinessWishlistGlobal = function(businessId, btn) {
            @guest
                if (typeof openLoginModal === 'function') {
                    openLoginModal();
                } else {
                    window.location.href = "{{ route('login', ['locale' => app()->getLocale()]) }}";
                }
                return;
            @endguest

            fetch("{{ route('business.wishlist.toggle', ['locale' => app()->getLocale()]) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ business_id: businessId })
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                const icon = btn.querySelector('i');
                if (data.status === 'added') {
                    if (icon) icon.className = 'fas fa-heart text-red-600';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Added to Wishlist',
                            toast: true,
                            position: 'top-end',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } else if (data.status === 'removed') {
                    if (icon) icon.className = 'far fa-heart text-gray-600';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Removed from Wishlist',
                            toast: true,
                            position: 'top-end',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }
            })
            .catch(function(err) {
                console.error('Wishlist toggle error:', err);
            });
        };
    }
</script>
@endpush
