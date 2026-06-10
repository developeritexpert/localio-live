<div class="preview-filter">
    <h6 class="mb-3">{{ $name }}</h6>

    @if($type === 'dropdown')
        <select class="form-select">
            @foreach($options as $index => $opt)
                <option {{ isset($default_options[$index]) ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>

    @elseif($type === 'checkbox')
        @foreach($options as $index => $opt)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" {{ isset($default_options[$index]) ? 'checked' : '' }}>
                <label class="form-check-label">{{ $opt }}</label>
            </div>
        @endforeach

    @elseif($type === 'radio')
        @foreach($options as $index => $opt)
            <div class="form-check">
                <input class="form-check-input" type="radio" name="preview_radio" {{ isset($default_options[$index]) ? 'checked' : '' }}>
                <label class="form-check-label">{{ $opt }}</label>
            </div>
        @endforeach

    @elseif($type === 'slider')
        <div class="mb-2">
            <label class="form-label">{{ $min }} {{ $unit }} - {{ $max }} {{ $unit }}</label>
            <input type="range" class="form-range" min="{{ $min }}" max="{{ $max }}" value="{{ $default_range ?? $min }}">
        </div>
        <p class="text-muted">Default: {{ $default_range ?? $min }} {{ $unit }}</p>

    @elseif($type === 'toggle')
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" {{ $default_toggle ? 'checked' : '' }}>
            <label class="form-check-label">{{ $default_toggle ? $on_label : $off_label }}</label>
        </div>
    @endif
</div>
