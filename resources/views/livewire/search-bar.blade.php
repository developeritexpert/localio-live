<div id="myID" class="search-box" style="position: relative;">
    <input type="text" wire:model="query" wire:keydown="performSearch" placeholder="{{ $placeholder }}" />
    <i class="fa fa-search"></i>

    @if (!empty($results) || $query)
    <div class="search-results"
    style="position: absolute; width: 100%; background-color: white;   border-radius: 0px 0px 25px 25px; border: 1px solid #ccc; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); z-index: 10; max-height: 250px; overflow-y: auto;">
            <ul style="list-style: none; padding: 0; margin: 0;">
                @forelse ($results as $result)
                    <li wire:click="redirectToProduct({{ $result['business_id'] }})"
                    style="padding: 10px 15px; display: flex; align-items: center; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background-color 0.2s ease-in-out; color: black;"
                    onmouseover="this.style.backgroundColor='#f4f4f4'"
                        onmouseout="this.style.backgroundColor='white'">
                        <div>
                        <i class="fa-solid fa-magnifying-glass" ></i>
                        </div>
                        {{ $result['name'] }}
                    </li>
                @empty
                    <li style="padding: 10px 15px; color: #888; font-size: 14px;">
                        No results found for "{{ $query }}"
                    </li>
                @endforelse
            </ul>
        </div>
    @endif
</div>
