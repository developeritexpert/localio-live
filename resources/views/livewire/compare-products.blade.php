<div>
    @if ($errorMessage)
        <div class="alert alert-warning alert-dismissible fade show mt-2" role="alert">
            {{ $errorMessage }}
            <button type="button" class="btn-close" wire:click="$set('errorMessage', '')"></button>
        </div>
    @endif

    <div class="blue-chkbox">
        <input type="checkbox"
               id="compare{{ $item->id }}"
               wire:model.live="isInCompare"
               wire:click="toggleCompare({{ $item->id }})">
        <label for="compare{{ $item->id }}">Compare Products</label>
    </div>
    </div>
