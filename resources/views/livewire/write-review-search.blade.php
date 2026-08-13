<div class="position-relative w-100" style="max-width: 800px; margin: 0 auto;">
    <div class="search-input-wrap position-relative">
        <input type="text" 
               wire:model.live.debounce.300ms="query" 
               placeholder="Search for a brand, product or service to review... " 
               class="form-control py-3 px-4 shadow-sm border-0" 
               style="border-radius: 30px; font-size: 16px; padding-right: 50px; background-color: #fff;" />
        <i class="fa fa-search position-absolute" style="right: 20px; top: 50%; transform: translateY(-50%); color: #7a8ea8; font-size: 18px;"></i>
    </div>

    @if(!empty($results))
        <div class="search-results-dropdown position-absolute w-100 shadow-lg mt-2 bg-white" style="border-radius: 12px; z-index: 1000; max-height: 400px; overflow-y: auto; border: 1px solid #e2e8f0;">
            @foreach($results as $business)
                <a href="/{{ app()->getLocale() }}/{{ $business['slug'] }}?write_review=1" class="d-flex align-items-center p-3 text-decoration-none hover-bg-light border-bottom" style="transition: background-color 0.2s; border-color: #f1f5f9;">
                    <div style="width: 40px; height: 40px; border-radius: 6px; overflow: hidden; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;" class="me-3">
                        <img src="{{ asset($business['icon']) }}" alt="{{ $business['name'] }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    <span style="font-weight: 600; color: #1e3050; font-size: 15px;">{{ $business['name'] }}</span>
                </a>
            @endforeach
        </div>
    @elseif(strlen($query) >= 2)
        <div class="search-results-dropdown position-absolute w-100 shadow-lg mt-2 bg-white p-3 text-center text-muted" style="border-radius: 12px; z-index: 1000; border: 1px solid #e2e8f0;">
            No businesses found matching "{{ $query }}"
        </div>
    @endif

    <style>
        .hover-bg-light:hover {
            background-color: #f8fafc;
        }
    </style>
</div>
