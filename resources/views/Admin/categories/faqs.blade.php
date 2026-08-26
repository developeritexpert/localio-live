@extends('admin_layout.master')
@section('content')

<?php $locale = getCurrentLocale(); ?>
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">
                Category FAQs - {{ $translation->name ?? $category->category_id ?? 'Unknown Category' }}
            </h3>
        </div>
        <div class="nk-block-head-content">
            <a href="{{ route('categories') }}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                <em class="icon ni ni-arrow-left"></em><span>Back</span>
            </a>
        </div>
    </div>
</div>

<div class="nk-block nk-block-lg">
    <div class="row g-4">
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                @foreach (\App\Models\Language::where('status', 'active')->get() as $lang)
                    <li class="nav-item">
                        <a class="nav-link {{ $lang->lang_code === $locale ? 'active' : '' }}" 
                           href="{{ route('set-category-language', $lang->id) }}">
                           {{ $lang->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        
        <div class="col-md-12 mt-3">
            @livewire('category-faqs-form', ['categoryId' => $category->id])
        </div>
    </div>
</div>
@endsection
