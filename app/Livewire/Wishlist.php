<?php

namespace App\Livewire;

use Livewire\Component;

class Wishlist extends Component
{
    public $productId;
    public $isInWishlist = false;

    protected $listeners = [
        'wishlistUpdated' => 'refreshWishlistState',
    ];

    public function mount($productId)
    {
        $this->productId = $productId;
        $this->checkWishlistState();
    }
    protected function checkWishlistState()
    {
        if (auth()->check()) {
            $this->isInWishlist = auth()->user()
                ->wishlists()
                ->where('business_id', $this->productId)
                ->exists();
        }
    }
    public function toggleWishlist()
    {
        if (!auth()->check()) {
            $this->dispatch('show-login-modal');
            return;
        }
        $user = auth()->user();
        $wishlist = $user->wishlists()
            ->where('business_id', $this->productId)
            ->first();
        if ($wishlist) {
            $wishlist->delete();
            $this->isInWishlist = false;
            $this->dispatch('wishlist:removed');
        } else {
            $user->wishlists()->create(['business_id' => $this->productId]);
            $this->isInWishlist = true;
            $this->dispatch('wishlist:added');
        }
        $this->dispatch('wishlistUpdated', productId: $this->productId, isInWishlist: $this->isInWishlist);
    }
    public function refreshWishlistState($productId, $isInWishlist)
    {
        if ($this->productId == $productId) {
            $this->isInWishlist = $isInWishlist;
        }
    }

    public function render()
    {
        return view('livewire.wishlist');
    }
}
