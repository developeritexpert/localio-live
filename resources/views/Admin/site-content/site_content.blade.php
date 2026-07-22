@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg site-content">
        <div class="nk-block-head d-flex justify-content-between align-items-center">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Manage Text Content</h4>
            </div>
        </div>

        <div class="card card-bordered">
            <div class="card-inner">
                <div class="nk-block">
                    <form action="{{ route('admin.text-content.update') }}" class="form-validate" novalidate="novalidate"
                        method="post">
                        @csrf
                        <input type="hidden" name="lang_id" value="{{ $langId }}">

                        <div class="row g-3">
                            @foreach ($sections as $sectionKey => $section)
                            <form action="{{ route('admin.text-content.update') }}" class="form-validate mb-4" method="POST">
                                @csrf
                                <input type="hidden" name="lang_id" value="{{ $langId }}">

                                <div class="card border w-100">
                                    <div class="card-header mt-3">
                                        {{ $section['title'] }}
                                    </div>
                                    <div class="card-body">
                                        @foreach ($section['keys'] as $key)
                                            @php
                                                $value = isset($keys[$key]) ? ($keys[$key]->translations->first()->value ?? ($keys[$key]->default_value ?? '')) : '';
                                            @endphp
                                            <div class="form-group col-lg-12">
                                                <label class="form-label" for="{{ $key }}">
                                                    {{ ucwords(str_replace('_', ' ', $key)) }}
                                                </label>
                                                <div class="form-control-wrap">
                                                    @if (str_contains($key, '_des') || str_contains($key, 'mail_below_text') || str_contains($key, '_desc') || str_contains($key, '_message'))
                                                        <textarea class="form-control site_text_input" id="{{ $key }}"
                                                            name="texts[{{ $key }}]" rows="3">{{ $value }}</textarea>
                                                    @else
                                                        <input type="text" class="form-control site_text_input"
                                                            id="{{ $key }}" name="texts[{{ $key }}]" value="{{ $value }}" />
                                                    @endif
                                                    <div class="spinner-border spinner-border-sm" role="status" style="display: none;">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="text-end mt-3">
                                            <button type="submit" class="btn btn-primary btn-localio">
                                                Update {{ $section['title'] }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endforeach


                            {{-- <div class="col-md-12 mt-4">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-localio text-center">
                                        <span>Update Content</span>
                                    </button>
                                </div>
                            </div> --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
