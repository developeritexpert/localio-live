@props(['business' => null])

@php
    $business = $business ?? ($page ?? null);
    $shareUrl = request()->url();
    $shareTitle = isset($business) && isset($business->name) ? $business->name : (isset($page) ? $page->title : config('app.name'));
@endphp

<style>
    .share-trigger-btn, .action-icon-btn {
        background: transparent !important;
        border: 0 !important;
        outline: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 32px !important;
        height: 32px !important;
        border-radius: 50% !important;
        color: #94a3b8 !important;
        cursor: pointer !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        transition: background-color 0.2s ease, color 0.2s ease !important;
    }
    .share-trigger-btn:hover, .action-icon-btn:hover {
        background-color: #f1f5f9 !important;
        color: #1e293b !important;
        
    }
    a.action-icon-btn:hover,button.action-icon-btn:hover {
    background: #003f7d !important;
    color: #fff !important;
}
button.action-icon-btn.share-trigger-btn:hover{
     background: #ff5722!important;
    color: #fff !important;

}
</style>

<div class="inside_sec_text inside_sec_text_2">
    <div class="sharing_icons" style="display: flex; align-items: center; gap: 8px;">
        @if(isset($business) && isset($business->id))
            <div class="sharing_ul social_wishlist_btn">
                <div wire:key="wishlist-container-{{ $business->id }}">
                    @livewire('wishlist', ['productId' => $business->id], key('wishlist-' . $business->id))
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
