<div class="dropdown-flex-container d-flex">

    <!-- Left Side: Product List -->
    <div class="product-list col-md-9 pe-2">
        <div class="dropdown_content">
            <ul class="hdr_dropul" style="list-style: none; padding: 0; margin: 0;">
                @if ($businesses->isNotEmpty())
                @foreach ($businesses as $business)
                    @php
                        $translation = $business->translations->first();
                    @endphp
                    @if ($translation)
                        <li class="dropdown_item-1">
                            <a href="javascript:void(0);"
                                onclick="changeProducts('{{ $translation->slug }}')"
                                style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                                <span class="ab_img" style="flex-shrink: 0; align-items: center">
                                    <img src="{{ asset($business->icon_id) }}" class="header_img" alt="{{ $translation->name }}">
                                </span>
                                <span class="hdr_insdiecont">
                                    {{ $translation->name }}
                                </span>
                            </a>
                        </li>
                    @endif
                @endforeach
            @else
                    <li class="dropdown_item-1">
                        <a href="javascript:void(0);">No Businesses Found</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
    <!-- Right Side: Search Input -->
    <div class="search-input search_box col-md-3 ps-2">
        <div class="dropdown_content">
            <div class="inside_dropdown_cont">
                <div class="header_drop_inpt d-flex align-items-center" onclick="event.stopPropagation()" style="position: relative;">
                    <div class="inside_text flex-grow-1">
                        <input type="text" wire:model="searchTerm" wire:keydown="performSearch" placeholder="Search product..."
                            class="form-control" onclick="event.stopPropagation()">
                    </div>
                    <div class="drop_serach_btn ms-2"
                    style="background-color: #F9633B; border-radius: 50px;"
                    onmouseover="this.style.backgroundColor = '#002347';"
                    onmouseout="this.style.backgroundColor = '#F9633B';">
                   <i class="fa-solid fa-magnifying-glass"></i>
               </div>
                </div>
                @if (!empty($searchResults) || $searchTerm)
                <div class="search-results "
                    style="position: absolute; width:100%; background-color: white; border-radius: 8px; border: 1px solid #ccc; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); z-index: 10;  overflow-y: auto;">
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @forelse ($searchResults as $result)
                            <li wire:click="redirectToProduct({{ $result['business_id'] }})"
                                style="width: auto;padding: 10px 15px; display: flex; align-items: center; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background-color 0.2s ease-in-out; color: black;"
                                onmouseover="this.style.backgroundColor='#f4f4f4'"
                                onmouseout="this.style.backgroundColor='white'">
                                <div>
                                    <i class="fa-solid fa-magnifying-glass" ></i>
                                    </div>
                                {{ $result['name'] }}
                            </li>
                        @empty
                            <li style="padding: 10px 15px; color: #888; font-size: 14px;  width:100%;">
                                No results found for "{{ $searchTerm }}"
                            </li>
                        @endforelse
                    </ul>
                </div>
            @endif
            </div>
        </div>

        @php

// Get top 3 most viewed businesses (by user_id or session_id)
$topBusinessIds = App\Models\UserActivity::whereNotNull('business_id')
    // ->where('activity_type', 'view_business') // optional filter
    ->select('business_id')
    ->selectRaw('COUNT(*) as total')
    ->groupBy('business_id')
    ->orderByDesc('total')

    ->limit(3)
    ->pluck('business_id');

    // dd($topBusinessIds);
    $langId = getCurrentLanguageID();
// Fetch those businesses with translations
$trendingProducts = App\Models\Business::whereIn('id', $topBusinessIds)

->with(['translations' => function ($query) use ($langId) {
        $query->where('lang_id', $langId);
    }])
    ->get()
    // maintain the order of IDs as per popularity
    ->sortBy(function ($business) use ($topBusinessIds) {
        return array_search($business->id, $topBusinessIds->toArray());
    })
    ->map(function ($business) {
        return optional($business->translations->first())->name;
    })
    ->filter();

    @endphp

        <div class="mt-3 ps-1">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="trending-label">Trending:</span>
                @foreach ($trendingProducts as $business)
                    <button type="button" onclick="changeProducts('{{ strtolower(str_replace(' ', '-', $business)) }}')"
                        class="trending-category-btn">
                        {{ $business }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>
