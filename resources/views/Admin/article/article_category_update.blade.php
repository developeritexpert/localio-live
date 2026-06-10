@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg">
    <div class="nk-block-head d-flex justify-content-between">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">
                Update Article Category
                @if($articleTranslationCategory)
                    - <span>{{ $articleTranslationCategory->language->name ?? '' }}</span>
                @else
                    - <span>{{ Cookie::get('language_code', config('app.locale')) }}</span>
                @endif
                </h4>  
             
        </div>
        <?php 
         $locale = getCurrentLocale();
        ?>
     
    </div>
    <div class="card card-bordered">
        <div class="card-inner">
            <div class="nk-block">
                <form action="{{ url('admin-dashboard/article/category/update') ?? '' }}" class="form-validate" novalidate="novalidate" method="post" enctype="multipart/form-data">
                @csrf
                    <div class="row g-3">
                        <div class="col-md-9">
                            <div class="form-group">
                                <input type="hidden" name="article_tr_id" value="{{ $articleTranslationCategory->id ?? '' }}" id="id">


                                <label class="form-label" for="name">Article Categopry Name</label>
                                <div class="form-control-wrap">
                                    <!-- <input type="text" class="form-control" name="name" id="name" value="{{$articleCategory->name ?? ''}}"> -->
                                    <input type="text" class="form-control" name="name" id="name" 
                                    value="{{ old('name', $articleTranslationCategory->name ?? $articleCategory->name) }}">
                                </div>
                                @error('name')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @if( $articleTranslationCategory->language_id ?? '' )
                            <input type="hidden" class="form-control" id="handle" name="handle" value="{{  $articleTranslationCategory->language->handle ?? '' }}" />
                        @else
                            <input type="hidden" class="form-control" id="handle" name="handle" value="{{ Cookie::get('language_code', config('app.locale')) }}" />
                        @endif
                        <input type="hidden" name="article_ct_id" id="category_id" value="{{ $articleCategory->id ?? '' }}">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="form-label" for="description">Article Categopry Description</label>
                                <div class="form-control-wrap">
                                    <textarea class="description" name="description" id="description" rows="4" cols="50">{{ old('description', $articleTranslationCategory->description ?? $articleCategory->description) }}</textarea>
                                </div>
                                @error('description')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @if($locale == 'en')
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label class="form-label" for="image">Upload Image</label>
                                    <div class="dz-message">
                                        <input type="file" class="form-control" name="image" id="image">
                                    </div>
                                    @error('image')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                @if(isset($articleCategory->image))
                                <img src="{{ asset('ArticleCategoryImages/' . $articleCategory->image) }}" alt="{{ $articleCategory->name }}" style="width: 50px; height: auto;">
                                @else
                                    <span>No Image</span>
                                @endif
                            </div>
                        @endif
                        <div class="col-md-12 mt-4">
                            <div class="form-group">
                                <button class="addCategory btn btn-primary  text-center"><span>Update Article Category</span></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
