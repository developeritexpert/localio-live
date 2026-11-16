@extends('user_layout.master')
@section('meta_title', $metaTitle ?? 'Category Page')
@section('meta_description', $metaDescription ?? '')
@section('og_type', 'website')

@section('structured_data')
@php
    $matchedLang = \App\Models\Language::where('lang_code', request()->segment(1))->first();
    $cLangId = $matchedLang ? $matchedLang->id : getCurrentLanguageID();
    $currCat = \App\Models\Category::whereHas('translations', function($q) use ($slug, $cLangId) {
        $q->where('slug', $slug)->where('lang_id', $cLangId);
    })->orWhereHas('translations', function($q) use ($slug) {
        $q->where('slug', $slug);
    })->first();

    $catBreadcrumbs = [
        ['name' => 'Home', 'url' => url('/' . app()->getLocale())],
    ];
    if ($currCat && $currCat->parent) {
        $parentTrans = $currCat->parent->translation()->first();
        if ($parentTrans) {
            $catBreadcrumbs[] = ['name' => $parentTrans->name, 'url' => route('category.detail', ['locale' => app()->getLocale(), 'slug' => $parentTrans->slug])];
        }
    }
    $catBreadcrumbs[] = ['name' => $currCat ? (optional($currCat->translation()->first())->name ?? $metaTitle) : $metaTitle, 'url' => url()->current()];
@endphp
<script type="application/ld+json">
{!! \App\Services\SeoService::getBreadcrumbsSchema($catBreadcrumbs) !!}
</script>
@if($currCat)
<script type="application/ld+json">
{!! \App\Services\SeoService::getCategorySchema($currCat, $metaTitle, $metaDescription) !!}
</script>
@endif
@endsection

@section('content')

@livewire('category-page', ['slug' => $slug, 'feature_slug' => $feature_slug ?? null, 'initialPage' => $page ?? 1], key('category-'.$slug.'-'.($feature_slug ?? 'all')))
@endsection
