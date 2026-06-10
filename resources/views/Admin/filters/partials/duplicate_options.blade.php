@if($filters->count())
    <div class="form-group">
        @foreach($filters as $filter)
            <div class="form-check">
                <input type="checkbox" class="form-check-input duplicate-filter" value="{{ $filter->id }}" id="filter_{{ $filter->id }}">
                <label class="form-check-label" for="filter_{{ $filter->id }}">
                    {{ $filter->translations->first()->name ?? $filter->name }}
                    <small class="text-muted">({{ $filter->filterType->name }})</small>
                </label>
            </div>
        @endforeach
    </div>
@else
    <p>No filters found for this category.</p>
@endif
