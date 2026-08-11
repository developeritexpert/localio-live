@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg edit-legal-document">
    <div class="nk-block-head d-flex justify-content-between">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">Edit Legal Document: {{ $document->title }}</h4>
        </div>
        <div class="nk-block-head-content">
            <a href="{{ route('admin.legal_documents') }}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                <em class="icon ni ni-arrow-left"></em><span>Back</span>
            </a>
        </div>
    </div>

    <div class="card card-bordered">
        <div class="card-inner">
            <div class="nk-block">
                <form action="{{ route('admin.legal_documents.update', ['slug' => $document->key]) }}" class="form-validate" method="POST">
                    @csrf
                    <div class="card border">
                        <div class="card-body">
                            <div class="form-group col-lg-12 mb-3">
                                <label class="form-label" for="doc_title">Document Title</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="doc_title" name="title"
                                        value="{{ old('title', $document->title) }}" placeholder="Document Title" required />
                                </div>
                                @error('title')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group col-lg-12">
                                <label class="form-label" for="doc_description">Document Content (Upload full legal text in this single textarea)</label>
                                <div class="form-control-wrap">
                                    <textarea name="description" class="description" id="doc_description" cols="50" rows="15" placeholder="Enter Full Document Text">{{ old('description', $document->description) }}</textarea>
                                </div>
                                @error('description')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12 mt-4 p-3">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary text-center btn-localio">
                                    <span>Save Document</span>
                                </button>
                                <a href="{{ route('admin.legal_documents') }}" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
