<div>
    <div class="blue-chkbox">
        <input type="checkbox"
               id="compare{{ $item->id }}"
               wire:model.live="isInCompare"
               wire:click="toggleCompare({{ $item->id }})"
               @if($isDisabled) disabled title="You must uncheck a product first to compare another." @endif>
        <label for="compare{{ $item->id }}">Compare</label>
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
    </script>
    @endscript
</div>
