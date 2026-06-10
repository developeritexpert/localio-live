<div class="dropdown-flex-container d-flex">
    <!-- Left Side: Category List -->
    <div class="category-list col-md-9 pe-2">
        <div class="dropdown_content">
            <ul class="hdr_dropul category-list_a" style="list-style: none; padding: 0; margin: 0;">
                @if ($categories->isNotEmpty())
                    @foreach ($categories as $category)
                        <li class="dropdown_item-1" wire:click="redirectToCategory({{ $category['id'] }})"
                            onmouseover="
                                this.querySelector('span.hdr_insdiecont').style.color = '#F9633B';
                                this.querySelector('img.header_img').style.transform = 'scale(1.1)';
                                this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
                            "
                            onmouseout="
                                this.querySelector('span.hdr_insdiecont').style.color = '';
                                this.querySelector('img.header_img').style.transform = 'scale(1)';
                                this.style.boxShadow = '';
                            "
                            style="padding: 8px 0;">
                                <span class="ab_img" style="flex-shrink: 0; align-items: center">
                                    <img src="{{ $category->media ? asset($category->media->dir_path . '/' . $category->media->file_name) : asset('images/no-image.png') }}"
                                        class="header_img" alt="{{ $category->translations->name ?? 'Category' }}"
                                        style="width: 40px; height: 40px; object-fit: contain;">
                                </span>
                                <span class="hdr_insdiecont"
                                    style="font-size: 1.1rem; font-weight: 500; margin-top: 1px">
                                    {{ $category->translations->name ?? '' }}
                                </span>
                        </li>
                    @endforeach
                @else
                    <li class="dropdown_item-1">
                        <a href="javascript:void(0);">No Category Found</a>
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
                        <input type="text" wire:model="searchTerm" wire:keydown="performSearch" placeholder="Search Category..."
                            class="form-control" onclick="event.stopPropagation()">
                    </div>
                    <div class="drop_serach_btn ms-2"
                        style="background-color: #F9633B; border-radius: 50px;"
                        onmouseover="this.style.backgroundColor = '#002347';"
                        onmouseout="this.style.backgroundColor = '#F9633B';">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                </div>

                <!-- Search Results Dropdown -->
                @if (!empty($searchResults) || $searchTerm)
                    <div class="search-results"
                        style="position: absolute; width:100%; background-color: white; border-radius: 8px; border: 1px solid #ccc; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); z-index: 10;  overflow-y: auto;">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            @forelse ($searchResults as $result)
                            <li wire:key="cat-{{ $category['id'] }}"  class="dropdown_item-1" wire:click="redirectToCategory({{ $category['id'] }})"                                    style="width: auto; padding: 10px 15px; display: flex; align-items: center; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background-color 0.2s ease-in-out; color: black;"
                                    onmouseover="this.style.backgroundColor='#f4f4f4'"
                                    onmouseout="this.style.backgroundColor='white'">
                                    <div>
                                        <i class="fa-solid fa-magnifying-glass" ></i>
                                        </div>
                                    {{ $result['name'] }}

                                </li>
                            @empty
                                <li style="padding: 10px 15px; color: #888; font-size: 14px; width:100%;">
                                    No results found for "{{ $searchTerm }}"
                                </li>
                            @endforelse
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        @php
            $trendingCategories = $categories->take(3);
        @endphp

        <div class="mt-3 ps-1">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="trending-label">Trending:</span>
                @foreach ($trendingCategories as $category)
                    <button type="button" onclick="changeCategory('{{ $category->translations->slug ?? '' }}')"
                        class="trending-category-btn">
                        {{ $category->translations->name ?? '' }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.dropdown-toggle-trigger').forEach(function (el) {
                el.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const parent = el.closest('.dropdown');
                    parent.classList.toggle('body_hiden');
                });
            });
        });
    </script> --}}
</div>
