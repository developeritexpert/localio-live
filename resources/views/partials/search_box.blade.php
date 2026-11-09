@php
    $placeholder = $placeholder ?? 'Search digital products & services';
@endphp
<div class="search-box global-search-container" style="position: relative;">
    <input type="text" class="global-search-input" autocomplete="off" placeholder="{{ $placeholder }}">
    <i class="fa-solid fa-magnifying-glass"></i>
    <div class="search-results global-search-results"
        style="position: absolute; width: 100%; background-color: #ffffff; border-radius: 0 0 8px 8px; border: 1px solid #ccc; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); z-index: 1050; max-height: 250px; overflow-y: auto; display: none;">
        <ul class="global-search-list" style="list-style: none; padding: 0; margin: 0;"></ul>
    </div>
</div>

