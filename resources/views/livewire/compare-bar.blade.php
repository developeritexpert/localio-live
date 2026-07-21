<div>
    @if ($errorMessage)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ $errorMessage }}
        <button type="button" class="btn-close" wire:click="$set('errorMessage', '')"></button>
    </div>
    @endif

    @if (count($comparedProducts) > 0)
    <div class="fixed-bottom bg-light p-3 shadow-lg" id="compareBar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ count($comparedProducts) }}</strong> product(s) selected for comparison
                </div>
                <button class="btn btn-primary" wire:click="goToComparison" @if(count($comparedProducts) < 2) disabled title="Please select 2 products to compare" style="opacity: 0.6; cursor: not-allowed;" @endif>
                    Compare Now
                </button>
            </div>
        </div>
    </div>
    @endif
    </div>
