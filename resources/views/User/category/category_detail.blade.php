@extends('user_layout.master')
@section('meta_title', $metaTitle ?? 'Category Page')
@section('meta_description', $metaDescription ?? '')
@section('content')

@livewire('category-page', ['slug' => $slug, 'feature_slug' => $feature_slug ?? null, 'initialPage' => $page ?? 1], key('category-'.$slug.'-'.($feature_slug ?? 'all')))
@endsection
