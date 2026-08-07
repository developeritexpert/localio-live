<div class="blue-chkbox d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
    <div class="">
        <input type="checkbox"
               id="compare{{ $item->id }}"
               {{ $isInCompare ? 'checked' : '' }}
               wire:click="toggleCompare({{ $item->id }})"
               @if($isDisabled) disabled title="You must uncheck a product first to compare another." @endif>
        <label for="compare{{ $item->id }}">Compare</label>
    </div>

    @php
        $activeReviews = $item->reviews ? $item->reviews->where('status', 'active') : collect();
        $totalRevCount = $activeReviews->count();
        $recCount = $activeReviews->where('recommend', 1)->count();
        $recPercent = $totalRevCount > 0 ? round(($recCount / $totalRevCount) * 100) : 0;
    @endphp

    @if($totalRevCount > 0)
        <div class="d-flex align-items-center ms-auto" style="color: #002347; font-size: 13px; font-weight: 600;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#002347" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 5px; flex-shrink: 0;"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
            <span>{{ $recPercent }}% of users recommend this</span>
        </div>
    @endif

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
