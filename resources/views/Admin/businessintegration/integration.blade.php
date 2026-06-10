@extends('admin_layout.master')

@section('content')
<div class="container business-integration">
    <h3>Business Integration Section</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.integration.save', $business->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Hidden -->
        <input type="hidden" name="business_id" value="{{ $business->id }}">

        <!-- Title -->
        <div class="mb-3">
            <label class="form-label">Main Title</label>
            <input type="text" name="title" class="form-control"
                   value="{{ old('title', $integration->title ?? '') }}" required>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-control" required>{{ old('description', $integration->description ?? '') }}</textarea>
        </div>

        <!-- Sub Heading -->
        <div class="mb-3">
            <label class="form-label">Sub Heading</label>
            <input type="text" name="sub_heading" class="form-control"
                   value="{{ old('sub_heading', $integration->sub_heading ?? '') }}">
        </div>

        <hr>

        <h5>Business Icons</h5>
        <div id="icon-repeater">
            @php
                // Decode JSON items or use old input
                $items = old('items', []);
                $business_images = $business->business_images ?? [];

                if(empty($items) && isset($integration->items) && is_string($integration->items)) {
                    $items = json_decode($integration->items, true) ?? [];
                }
            @endphp

            @forelse($items as $index => $item)
                <div class="icon-item border p-3 mb-3 rounded">
                    {{-- Business Icon --}}
                    <div class="mb-2">
                        <label class="form-label">Business Icon</label>
                        <input type="file" name="icon[{{ $index }}]" class="form-control">

                        @php
                            $icon_path = $item['icon'] ?? null;

                            // Check if file exists in public folder
                            if($icon_path && file_exists(public_path($icon_path))){
                                $icon_url = asset($icon_path);
                            } else {
                                $icon_url = asset('images/placeholder.png');
                            }
                        @endphp

                        <img src="{{ $icon_url }}" alt="Icon" width="60" class="mt-2">

                        @if($icon_path && file_exists(public_path($icon_path)))
                            <input type="hidden" name="existing_icon[{{ $index }}]" value="{{ $icon_path }}">
                        @endif
                    </div>

                    {{-- Business Name --}}
                    <div class="mb-2">
                        <label class="form-label">Business Name</label>
                        <input type="text" name="name[{{ $index }}]" class="form-control" value="{{ $item['name'] ?? '' }}">
                    </div>

                    {{-- Business Link --}}
                    <div class="mb-2">
                        <label class="form-label">Business Link</label>
                        <input type="url" name="link[{{ $index }}]" class="form-control" value="{{ $item['link'] ?? '' }}">
                    </div>

                    <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                </div>
            @empty
                {{-- Default empty item --}}
                <div class="icon-item border p-3 mb-3 rounded">
                    <div class="mb-2">
                        <label class="form-label">Business Icon</label>
                        <input type="file" name="icon[0]" class="form-control">
                        <img src="{{ asset('images/placeholder.png') }}" alt="No Icon" width="60" class="mt-2">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Business Name</label>
                        <input type="text" name="name[0]" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Business Link</label>
                        <input type="url" name="link[0]" class="form-control">
                    </div>
                </div>
            @endforelse
        </div>



        <button type="button" id="add-item" class="btn btn-secondary mb-3">+ Add More</button>
        <button type="submit" class="btn btn-primary">Save Integration</button>
    </form>
</div>

<script>
document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('icon-repeater');
    const index = container.querySelectorAll('.icon-item').length;

    const newItem = `
    <div class="icon-item border p-3 mb-3 rounded">
        <input type="file" name="icon[${index}]" class="form-control mb-2">
        <input type="text" name="name[${index}]" class="form-control mb-2" placeholder="Business Name">
        <input type="url" name="link[${index}]" class="form-control mb-2" placeholder="Business Link">
        <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
    </div>`;

    container.insertAdjacentHTML('beforeend', newItem);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item')) {
        e.target.closest('.icon-item').remove();
    }
});
</script>
@endsection
