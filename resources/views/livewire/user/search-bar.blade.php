
<div class="form" style="position: relative;">
    <input type="search" class="search-box" wire:model="query" wire:keydown="performSearch" placeholder="{{ $placeholder }}">
    
    @if(!empty($query))
        <button type="button" class="clear-search-btn" wire:click="clearSearch" style="position: absolute; right: 50px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #999; font-size: 18px; cursor: pointer; z-index: 5;">
            {{-- <i class="fa-solid fa-times"></i> --}}
        </button>
    @endif
    
    <button class="btn cta_dark active"><i class="fa-solid fa-magnifying-glass"></i></button>

    @if (!empty($results) || $query)
    <ul class="search-results-dropdown">
        @forelse ($results as $result)
            <li 
                @if($result['type'] == 'product')
                    wire:click="redirectToProduct({{ $result['business_id'] }})"
                @else
                    wire:click="redirectToCategory({{ $result['category_id'] }})"
                @endif
                class="search-result-item"
            >
                <i class="fa-solid fa-search search-icon"></i>
                <span class="result-text">{{ $result['name'] }}</span>
            </li>
        @empty
            <li class="no-results-item">
                <i class="fa-solid fa-search-minus no-results-icon"></i>
                <div class="no-results-content">
                    <span class="no-results-title">No results found</span>
                    <span class="no-results-subtitle">Try searching for "{{ $query }}" with different keywords</span>
                </div>
            </li>
        @endforelse
    </ul>
    @endif
</div>