@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add FAQ Category</h4>
            </div>
        </div>

        <div class="card card-bordered">
            <div class="card-inner">
                <div class="nk-block">
                    <form action="{{ route('category-add-procc') ?? '' }}" class="form-validate" novalidate="novalidate"
                        method="post">
                        @csrf
                        @if (isset($faq))
                            <input type="hidden" name="faq_id" value="{{ $faq->id }}">
                        @endif
                        <input type="hidden" name="lang_id" value="{{ getCurrentLanguageID() }}" />

                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="name">Name</label>
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" name="name" id="name"
                                                value="{{ old('name', isset($faq) && $faq->translation ? $faq->translation->name : '') }}">
                                        </div>
                                    </div>
                                    @error('name')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="form-group">
                                    <label class="form-label" for="description">Description</label>
                                    <div class="form-control-wrap">
                                        <textarea class="description" name="description" id="description" rows="4" cols="50">{{ old('description', isset($faq) && $faq->translation ? strip_tags($faq->translation->description) : '') }}</textarea>
                                    </div>
                                    @error('description')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="form-group">
                                    <label class="form-label" for="status">Status</label> {{-- static label --}}
                                    <div class="form-control-wrap">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="status"
                                                name="status" value="1"
                                                {{ old('status', $faq->status ?? 0) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="status"></label>
                                            <span id="status-text" class="ml-2">
                                                {{ old('status', $faq->status ?? 0) ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                    @error('status')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="form-group">
                                <button class="addCategory btn btn-primary btn-localio text-center">
                                    <em class=""></em>
                                    <span>{{ isset($faq) ? 'Update Category' : 'Save Category' }}</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusToggle = document.getElementById('status');
            const statusText = document.getElementById('status-text');

            function updateStatusLabel() {
                statusText.textContent = statusToggle.checked ? 'Active' : 'Inactive';
            }

            // Set initial state
            updateStatusLabel();

            // Update on change
            statusToggle.addEventListener('change', updateStatusLabel);
        });
    </script>
@endsection
