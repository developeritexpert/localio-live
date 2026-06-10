<div class="wishlist">
    <a href="javascript:void(0)" wire:click="toggleWishlist">
        @if ($isInWishlist)
            <i class="fas fa-heart text-red-600 hover:text-red-700"></i>
        @else
            <i class="far fa-heart text-gray-600 hover:text-red-600"></i>
        @endif
    </a>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        window.addEventListener('wishlist:added', () => {
            Swal.fire({
                icon: 'success',
                title: 'Added to Wishlist',
                toast: true,
                position: 'top-end',
                timer: 2000,
                showConfirmButton: false,
            });
        });

        window.addEventListener('wishlist:removed', () => {
            Swal.fire({
                icon: 'info',
                title: 'Removed from Wishlist',
                toast: true,
                position: 'top-end',
                timer: 2000,
                showConfirmButton: false,
            });
        });
    </script>
</div>
