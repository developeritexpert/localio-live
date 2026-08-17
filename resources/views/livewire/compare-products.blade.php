<div class="blue-chkbox d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
    <div class="">
        <input type="checkbox" id="compare{{ $item->id }}" {{ $isInCompare ? 'checked' : '' }}
            wire:click="toggleCompare({{ $item->id }})" @if($isDisabled) disabled
            title="You must uncheck a product first to compare another." @endif>
        <label for="compare{{ $item->id }}">Compare</label>
    </div>


    <div class="blue-heart" wire:key="wishlist-container-{{ $item->id }}">
        @livewire('wishlist', ['productId' => $item->id], key('wishlist-' . $item->id))
    </div>
    @script
    <script>
    $wire.on('show-toastr-error', (event) => {
        let msg = event.message || (event[0] && event[0].message) || 'Error';
        if (typeof toastr !== 'undefined') {
            toastr.error(msg);
        } else {
            alert(msg);
        }
    });

    Livewire.on('toggleCompareProduct', () => {
        $wire.call('refreshComparedProducts');
    });
    </script>
    @endscript
</div>