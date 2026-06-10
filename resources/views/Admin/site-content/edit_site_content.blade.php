@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg">
    <div class="nk-block-head d-flex justify-content-between align-items-center">
        <h4 class="title nk-block-title">Manage Static Site Content</h4>
    </div>

    <form action="{{ route('admin-static-content-update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
        @csrf
        {{-- <input type="hidden" name="type" value="{{ request()->input('type', 'config') }}"> --}}

        <div class="card card-bordered card-preview">
            <div class="card-inner row">
                @foreach($data as $item)
                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="{{ $item->key }}">{{ $item->name ?? ucfirst(str_replace('_', ' ', $item->key)) }}</label>

                            @if($item->field_type === 'file')
                                <input type="file" class="form-control" id="{{ $item->key }}" name="{{ $item->key }}">

                                @if($item->value)
                                    <div class="mt-2">
                                        <img src="{{ asset($item->value) }}" alt="Preview" height="60">
                                    </div>
                                @endif

                            @elseif($item->field_type === 'textarea')
                                <textarea class="form-control" id="{{ $item->key }}" name="{{ $item->key }}" rows="4">{{ $item->value }}</textarea>

                            @else
                                <input type="{{ $item->field_type }}" class="form-control" id="{{ $item->key }}" name="{{ $item->key }}" value="{{ old($item->key, $item->value) }}">
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-3">
            <button class="btn btn-primary" type="submit">Save</button>
        </div>
    </form>
</div>

@endsection